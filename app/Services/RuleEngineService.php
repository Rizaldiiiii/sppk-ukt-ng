<?php

namespace App\Services;

use App\Models\FaktorPenilaian;
use App\Models\GolonganUkt;
use App\Models\HasilUkt;
use App\Models\Mahasiswa;
use App\Models\ParameterPenilaian;
use App\Models\RincianPenilaian;
use App\Models\User;
use App\Models\VersiParameter;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

class RuleEngineService
{
    public function __construct(
        private readonly AuditService $auditService,
        private readonly NotificationService $notificationService,
    ) {}

    /**
     * Ambil versi parameter yang sedang aktif saat ini.
     */
    public function versiAktif(): ?VersiParameter
    {
        return VersiParameter::with(['parameterPenilaian.faktor'])
            ->where('aktif', true)
            ->orderByDesc('berlaku_mulai')
            ->orderByDesc('id')
            ->first();
    }

    /**
     * Evaluasi deterministik masukan terhadap versi parameter tertentu (KF-05, KF-01, KF-04).
     *
     * @param  array<string, mixed>  $masukan
     * @return array{
     *     status: string,
     *     versi_parameter_id: int,
     *     versi: string,
     *     sumber: string,
     *     faktor_kurang: array<int, string>,
     *     rincian: array<int, array<string, mixed>>,
     *     skor_total: float|null,
     *     golongan_simulasi: GolonganUkt|null,
     *     label_status: string,
     *     catatan_footer: string|null
     * }
     */
    public function hitung(
        array $masukan,
        ?VersiParameter $versiParameter = null,
        string $sumber = 'simulasi'
    ): array {
        $versi = $versiParameter ?? $this->versiAktif();

        if (! $versi) {
            throw new InvalidArgumentException('Belum ada versi parameter penilaian yang aktif.');
        }

        $versi->loadMissing(['parameterPenilaian.faktor']);

        $parameterList = $versi->parameterPenilaian
            ->filter(fn (ParameterPenilaian $p) => $p->faktor && $p->faktor->aktif)
            ->sortBy(fn (ParameterPenilaian $p) => $p->faktor->urutan)
            ->values();

        // Periksa kelengkapan masukan — bila tidak lengkap, jangan mengarang nilai (Blueprint §6.2)
        $faktorKurang = [];
        foreach ($parameterList as $param) {
            $kode = $param->faktor->kode;
            if (! array_key_exists($kode, $masukan) || $masukan[$kode] === null || $masukan[$kode] === '') {
                $faktorKurang[] = $param->faktor->nama;
            }
        }

        if ($faktorKurang !== []) {
            return [
                'status' => 'tidak_bisa_dihitung',
                'versi_parameter_id' => $versi->id,
                'versi' => $versi->versi,
                'sumber' => $sumber,
                'faktor_kurang' => $faktorKurang,
                'rincian' => [],
                'skor_total' => null,
                'golongan_simulasi' => null,
                'label_status' => 'Masukan belum lengkap — perhitungan belum dapat dijalankan',
                'catatan_footer' => $sumber === 'simulasi' ? $this->catatanFooterSimulasi($versi) : null,
            ];
        }

        $rincian = [];
        $skorTotal = 0.0;
        $tanggalBerlaku = $versi->berlaku_mulai?->locale('id')->translatedFormat('d M Y') ?? '-';

        foreach ($parameterList as $param) {
            /** @var FaktorPenilaian $faktor */
            $faktor = $param->faktor;
            $aturan = $param->aturan ?? [];
            $nilaiMentah = $masukan[$faktor->kode];

            $evaluasi = $this->evaluasiFaktor($faktor, $aturan, $nilaiMentah);
            $bobot = (float) ($aturan['bobot'] ?? 0);
            $skorDasar = (float) $evaluasi['skor'];
            $nilaiKomponen = round(($skorDasar * $bobot) / 100, 2);
            $skorTotal = round($skorTotal + $nilaiKomponen, 2);

            $nilaiMasukanFormatted = $this->formatNilaiMasukan($faktor, $nilaiMentah);

            $langkahPerhitungan = sprintf(
                'Faktor "%s" memakai masukan %s. Berdasarkan aturan parameter versi %s (berlaku sejak %s), masukan masuk kategori "%s" dengan tingkat kontribusi %s dari 5 dan porsi pertimbangan %s%%, sehingga menghasilkan poin komponen %s.',
                $faktor->nama,
                $nilaiMasukanFormatted,
                $versi->versi,
                $tanggalBerlaku,
                $evaluasi['label'],
                number_format($skorDasar, 0, ',', '.'),
                number_format($bobot, 0, ',', '.'),
                number_format($nilaiKomponen, 2, ',', '.')
            );

            $kalimatAlasan = $this->bangunKalimatAlasan(
                faktor: $faktor,
                nilaiMasukanFormatted: $nilaiMasukanFormatted,
                labelKategori: $evaluasi['label'],
                dampak: $evaluasi['dampak'],
                nilaiKomponen: $nilaiKomponen,
                sumber: $sumber
            );

            $rincian[] = [
                'faktor_id' => $faktor->id,
                'kode_faktor' => $faktor->kode,
                'nama_faktor' => $faktor->nama,
                'penjelasan_singkat' => $faktor->penjelasan_singkat,
                'status_konfirmasi' => $faktor->status_konfirmasi,
                'nilai_masukan' => $nilaiMasukanFormatted,
                'nilai_komponen' => $nilaiKomponen,
                'langkah_perhitungan' => $langkahPerhitungan,
                'kalimat_alasan' => $kalimatAlasan,
            ];
        }

        $golonganSimulasi = $this->tentukanGolonganDariSkor($skorTotal);

        return [
            'status' => 'berhasil',
            'versi_parameter_id' => $versi->id,
            'versi' => $versi->versi,
            'sumber' => $sumber,
            'faktor_kurang' => [],
            'rincian' => $rincian,
            'skor_total' => $skorTotal,
            'golongan_simulasi' => $golonganSimulasi,
            'label_status' => $sumber === 'resmi'
                ? 'Hasil penetapan resmi kampus'
                : 'Penjelasan rincian berbasis model simulasi berparameter',
            'catatan_footer' => $sumber === 'simulasi' ? $this->catatanFooterSimulasi($versi) : null,
        ];
    }

    /**
     * Simpan hasil perhitungan (simulasi atau resmi beserta rinciannya) ke database.
     *
     * @param  array<string, mixed>  $masukan
     */
    public function simpanHasil(
        Mahasiswa $mahasiswa,
        array $masukan,
        string $sumber = 'simulasi',
        ?VersiParameter $versiParameter = null,
        ?GolonganUkt $golonganResmi = null,
        ?int $nominalResmi = null,
        ?string $tanggalPenetapan = null,
        ?string $sumberPenetapan = null,
        ?User $ditetapkanOleh = null,
        ?string $catatan = null,
        bool $kirimNotifikasi = false
    ): HasilUkt {
        $evaluasi = $this->hitung($masukan, $versiParameter, $sumber);

        if ($evaluasi['status'] !== 'berhasil') {
            throw new InvalidArgumentException(
                'Data masukan belum lengkap untuk faktor: '.implode(', ', $evaluasi['faktor_kurang'])
            );
        }

        return DB::transaction(function () use (
            $mahasiswa,
            $evaluasi,
            $sumber,
            $golonganResmi,
            $nominalResmi,
            $tanggalPenetapan,
            $sumberPenetapan,
            $ditetapkanOleh,
            $catatan,
            $kirimNotifikasi
        ) {
            $golongan = $sumber === 'resmi'
                ? $golonganResmi
                : $evaluasi['golongan_simulasi'];

            $nominal = $sumber === 'resmi'
                ? $nominalResmi
                : ($golongan?->nominal_max ?? $golongan?->nominal_min);

            $hasil = HasilUkt::create([
                'mahasiswa_id' => $mahasiswa->id,
                'golongan_id' => $golongan?->id,
                'sumber' => $sumber,
                'versi_parameter_id' => $evaluasi['versi_parameter_id'],
                'nominal' => $nominal,
                'skor_total' => $evaluasi['skor_total'],
                'tanggal_penetapan' => $tanggalPenetapan ?? now()->toDateString(),
                'sumber_penetapan' => $sumber === 'resmi'
                    ? ($sumberPenetapan ?: 'Keputusan Penetapan UKT Universitas Negeri Gorontalo')
                    : 'Model Penjelasan Simulasi Berparameter ('.$evaluasi['versi'].')',
                'ditetapkan_oleh' => $ditetapkanOleh?->id,
                'catatan' => $catatan,
            ]);

            foreach ($evaluasi['rincian'] as $baris) {
                RincianPenilaian::create([
                    'hasil_id' => $hasil->id,
                    'faktor_id' => $baris['faktor_id'],
                    'nilai_masukan' => $baris['nilai_masukan'],
                    'nilai_komponen' => $baris['nilai_komponen'],
                    'langkah_perhitungan' => $baris['langkah_perhitungan'],
                    'kalimat_alasan' => $baris['kalimat_alasan'],
                ]);
            }

            if ($ditetapkanOleh) {
                $this->auditService->catat(
                    pelaku: $ditetapkanOleh,
                    aksi: $sumber === 'resmi' ? 'entri_hasil_resmi' : 'buat_hasil_simulasi',
                    subjekTipe: HasilUkt::class,
                    subjekId: $hasil->id,
                    nilaiSebelum: null,
                    nilaiSesudah: [
                        'mahasiswa_id' => $mahasiswa->id,
                        'sumber' => $sumber,
                        'golongan_id' => $golongan?->id,
                        'versi_parameter_id' => $evaluasi['versi_parameter_id'],
                        'skor_total' => $evaluasi['skor_total'],
                    ]
                );
            }

            if ($kirimNotifikasi && $mahasiswa->user) {
                $labelStatus = $sumber === 'resmi'
                    ? 'Hasil Penetapan Resmi UKT Tersedia ('.($golongan?->nama ?? 'Golongan UKT').')'
                    : 'Rincian Simulasi Penilaian UKT Diperbarui';

                $this->notificationService->kirimPemberitahuanStatus(
                    penerima: $mahasiswa->user,
                    judulPeristiwa: 'Pembaruan Hasil Peninjauan UKT',
                    statusBaru: $labelStatus,
                    tautanHalaman: url('/hasil-saya'),
                    pemicu: $ditetapkanOleh,
                    subjekTipe: HasilUkt::class,
                    subjekId: $hasil->id,
                );
            }

            return $hasil->load(['golongan', 'versiParameter', 'rincianPenilaian.faktor']);
        });
    }

    /**
     * Buat versi parameter penilaian baru tanpa mengubah versi lama maupun hasil perhitungan lama (KN-06).
     *
     * @param  array<int, array<string, mixed>>  $aturanPerFaktorId
     */
    public function buatVersiParameterBaru(
        User $petugas,
        string $kodeVersi,
        string $berlakuMulai,
        ?string $catatan,
        array $aturanPerFaktorId
    ): VersiParameter {
        return DB::transaction(function () use ($petugas, $kodeVersi, $berlakuMulai, $catatan, $aturanPerFaktorId) {
            $versiLama = $this->versiAktif();

            // Nonaktifkan status aktif pada versi sebelumnya tanpa mengubah aturan versi lama
            VersiParameter::where('aktif', true)->update(['aktif' => false]);

            $versiBaru = VersiParameter::create([
                'versi' => $kodeVersi,
                'berlaku_mulai' => $berlakuMulai,
                'dibuat_oleh' => $petugas->id,
                'catatan' => $catatan,
                'aktif' => true,
            ]);

            foreach ($aturanPerFaktorId as $faktorId => $aturan) {
                ParameterPenilaian::create([
                    'versi_parameter_id' => $versiBaru->id,
                    'faktor_id' => (int) $faktorId,
                    'aturan' => $aturan,
                ]);
            }

            $this->auditService->catat(
                pelaku: $petugas,
                aksi: 'buat_versi_parameter_baru',
                subjekTipe: VersiParameter::class,
                subjekId: $versiBaru->id,
                nilaiSebelum: $versiLama ? [
                    'versi_aktif_sebelumnya' => $versiLama->versi,
                    'versi_id_sebelumnya' => $versiLama->id,
                ] : null,
                nilaiSesudah: [
                    'versi_baru' => $versiBaru->versi,
                    'berlaku_mulai' => $berlakuMulai,
                    'catatan' => $catatan,
                ]
            );

            return $versiBaru->load('parameterPenilaian.faktor');
        });
    }

    /**
     * Penjelasan status seeder/simulasi yang ditempatkan khusus di footer halaman hasil (Blueprint §6.3).
     */
    public function catatanFooterSimulasi(?VersiParameter $versi = null): string
    {
        $bagianSeeder = implode('; ', config('sppk.bagian_seeder_simulasi', [
            'Nilai masukan contoh pada profil mahasiswa simulasi',
            'Parameter aturan, ambang skor, dan bobot komponen pada versi parameter simulasi',
            'Langkah perhitungan skor komponen dan estimasi golongan simulasi',
        ]));

        $infoVersi = $versi ? " (dihitung dengan parameter {$versi->versi})" : '';

        return "Data pada halaman ini adalah seeder/simulasi{$infoVersi}, bukan penetapan resmi kampus. "
            ."Bagian yang merupakan seeder/simulasi: {$bagianSeeder}. "
            .'Penetapan golongan UKT resmi tetap menjadi kewenangan Universitas Negeri Gorontalo.';
    }

    /**
     * @param  array<string, mixed>  $aturan
     * @return array{skor: float, label: string, dampak: string}
     */
    private function evaluasiFaktor(FaktorPenilaian $faktor, array $aturan, mixed $nilaiMentah): array
    {
        $tipe = $aturan['tipe'] ?? 'rentang_angka';
        $daftarRentang = $aturan['rentang'] ?? [];

        if ($tipe === 'rentang_angka') {
            $angka = is_numeric($nilaiMentah)
                ? (float) $nilaiMentah
                : (float) preg_replace('/[^0-9.]/', '', (string) $nilaiMentah);

            foreach ($daftarRentang as $item) {
                $min = isset($item['min']) ? (float) $item['min'] : null;
                $max = isset($item['max']) ? (float) $item['max'] : null;

                $cocokMin = $min === null || $angka >= $min;
                $cocokMax = $max === null || $angka <= $max;

                if ($cocokMin && $cocokMax) {
                    return [
                        'skor' => (float) ($item['skor'] ?? 1),
                        'label' => (string) ($item['label'] ?? 'Rentang terukur'),
                        'dampak' => (string) ($item['dampak'] ?? 'mempengaruhi perhitungan komponen sesuai rentang masukan'),
                    ];
                }
            }
        } else {
            $nilaiString = mb_strtolower(trim((string) $nilaiMentah));
            foreach ($daftarRentang as $item) {
                $kandidat = mb_strtolower(trim((string) ($item['nilai'] ?? '')));
                if ($kandidat !== '' && ($nilaiString === $kandidat || str_contains($nilaiString, $kandidat))) {
                    return [
                        'skor' => (float) ($item['skor'] ?? 1),
                        'label' => (string) ($item['label'] ?? $item['nilai']),
                        'dampak' => (string) ($item['dampak'] ?? 'mempengaruhi skor komponen berdasarkan kondisi yang tercatat'),
                    ];
                }
            }
        }

        // Fallback deterministik ke aturan terakhir bila ada
        $terakhir = end($daftarRentang) ?: [];

        return [
            'skor' => (float) ($terakhir['skor'] ?? 2),
            'label' => (string) ($terakhir['label'] ?? 'Kategori umum'),
            'dampak' => (string) ($terakhir['dampak'] ?? 'memberikan kontribusi menengah terhadap komponen penilaian'),
        ];
    }

    private function formatNilaiMasukan(FaktorPenilaian $faktor, mixed $nilaiMentah): string
    {
        if (is_numeric($nilaiMentah)) {
            $angka = (float) $nilaiMentah;
            if ($faktor->satuan === 'rupiah') {
                return 'Rp '.number_format($angka, 0, ',', '.').' / bulan';
            }
            if ($faktor->satuan === 'orang') {
                return number_format($angka, 0, ',', '.').' orang tanggungan';
            }
            if ($faktor->satuan === 'va') {
                return number_format($angka, 0, ',', '.').' VA';
            }
            if ($faktor->satuan === 'm2') {
                return number_format($angka, 0, ',', '.').' m²';
            }

            return number_format($angka, 0, ',', '.');
        }

        return trim((string) $nilaiMentah);
    }

    private function bangunKalimatAlasan(
        FaktorPenilaian $faktor,
        string $nilaiMasukanFormatted,
        string $labelKategori,
        string $dampak,
        float $nilaiKomponen,
        string $sumber
    ): string {
        $poinFormatted = number_format($nilaiKomponen, 2, ',', '.');

        if ($sumber === 'resmi') {
            return sprintf(
                'Komponen %s bernilai %s poin karena data tercatat sebesar %s berada pada kelompok "%s" yang %s.',
                $faktor->nama,
                $poinFormatted,
                $nilaiMasukanFormatted,
                $labelKategori,
                $dampak
            );
        }

        return sprintf(
            'Pada hasil simulasi ini, komponen %s menghasilkan %s poin karena masukan %s termasuk kategori "%s" yang %s.',
            $faktor->nama,
            $poinFormatted,
            $nilaiMasukanFormatted,
            $labelKategori,
            $dampak
        );
    }

    private function tentukanGolonganDariSkor(float $skorTotal): ?GolonganUkt
    {
        $daftarGolongan = GolonganUkt::where('aktif', true)
            ->orderBy('nominal_min')
            ->orderBy('id')
            ->get()
            ->values();

        if ($daftarGolongan->isEmpty()) {
            return null;
        }

        $jumlah = $daftarGolongan->count();
        // Skor berada di rentang 1.00 sampai 5.00; petakan secara proporsional & deterministik
        $rasio = max(0.0, min(1.0, ($skorTotal - 1.0) / 4.0));
        $indeks = (int) round($rasio * ($jumlah - 1));

        return $daftarGolongan->get($indeks) ?? $daftarGolongan->first();
    }
}

