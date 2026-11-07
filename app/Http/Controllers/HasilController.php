<?php

namespace App\Http\Controllers;

use App\Models\FaktorPenilaian;
use App\Models\HasilUkt;
use App\Models\Mahasiswa;
use App\Models\ParameterPenilaian;
use App\Models\PerkiraanWaktu;
use App\Models\User;
use App\Models\VersiParameter;
use App\Services\AuditService;
use App\Services\RuleEngineService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Validator;
use Illuminate\View\View;

class HasilController extends Controller
{
    public function __construct(
        private readonly RuleEngineService $ruleEngineService,
        private readonly AuditService $auditService,
    ) {}

    /**
     * Beranda mahasiswa (/beranda).
     */
    public function beranda(Request $request): View
    {
        /** @var User $user */
        $user = $request->user()->loadMissing('mahasiswa');
        $mahasiswa = $user->mahasiswa;

        $hasilTerbaru = null;
        if ($mahasiswa) {
            $hasilTerbaru = HasilUkt::with(['golongan', 'versiParameter', 'rincianPenilaian.faktor'])
                ->where('mahasiswa_id', $mahasiswa->id)
                ->latest('id')
                ->first();
        }

        $jumlahNotifikasiBelumDibaca = $user->unreadNotifications()->count();
        $notifikasiTerbaru = $user->notifications()->latest()->limit(3)->get();

        return view('mahasiswa.beranda', [
            'user' => $user,
            'mahasiswa' => $mahasiswa,
            'hasilTerbaru' => $hasilTerbaru,
            'jumlahNotifikasiBelumDibaca' => $jumlahNotifikasiBelumDibaca,
            'notifikasiTerbaru' => $notifikasiTerbaru,
        ]);
    }

    /**
     * Halaman hasil dan rincian komponen penilaian (/hasil-saya dan /hasil-saya/{hasil}) — KF-01, KF-02, KF-04, KF-05.
     * Mencakup bagian "Coba Kalau Dataku Berbeda" (Simulasi What-If di memori/sesi).
     */
    public function index(Request $request, ?HasilUkt $hasil = null): View
    {
        /** @var User $user */
        $user = $request->user()->loadMissing('mahasiswa');
        $mahasiswaSendiri = $user->mahasiswa;

        $hasilAktif = $this->ambilDanOtorisasiHasilMahasiswa($request, $user, $mahasiswaSendiri, $hasil);

        $catatanFooterSimulasi = null;
        if ($hasilAktif && $hasilAktif->isSimulasi()) {
            $catatanFooterSimulasi = $this->ruleEngineService->catatanFooterSimulasi($hasilAktif->versiParameter);
        }

        $perkiraanWaktu = PerkiraanWaktu::where('jenis', 'penetapan')
            ->where('aktif', true)
            ->latest('tanggal')
            ->first();

        $whatIf = $hasilAktif
            ? $this->siapkanDataWhatIf($request, $user, $mahasiswaSendiri, $hasilAktif)
            : null;

        return view('mahasiswa.hasil', [
            'user' => $user,
            'mahasiswa' => $mahasiswaSendiri,
            'hasil' => $hasilAktif,
            'catatanFooterSimulasi' => $catatanFooterSimulasi,
            'perkiraanWaktu' => $perkiraanWaktu,
            'whatIf' => $whatIf,
        ]);
    }

    /**
     * Proses perhitungan Simulasi What-If ("Coba Kalau Dataku Berbeda") pada halaman /hasil-saya (perluasan KF-02 & KF-05).
     * Dihitung di memori dan disimpan pada sesi pengguna saja — tidak mengubah database hasil_ukt maupun data_masukan mahasiswa.
     */
    public function simulasiWhatIf(Request $request, ?HasilUkt $hasil = null): RedirectResponse
    {
        /** @var User $user */
        $user = $request->user()->loadMissing('mahasiswa');
        $mahasiswaSendiri = $user->mahasiswa;

        $hasilAktif = $this->ambilDanOtorisasiHasilMahasiswa($request, $user, $mahasiswaSendiri, $hasil);

        if (! $hasilAktif || ! $mahasiswaSendiri) {
            return redirect()
                ->route('mahasiswa.hasil')
                ->withErrors(['what_if' => 'Data hasil penilaian awal Anda belum tersedia untuk disimulasikan.']);
        }

        $versiAktif = $this->ruleEngineService->versiAktif() ?? $hasilAktif->versiParameter;
        if (! $versiAktif) {
            return redirect()
                ->route('mahasiswa.hasil')
                ->withErrors(['what_if' => 'Versi parameter penilaian belum tersedia.']);
        }

        $skemaFaktor = $this->bangunSkemaFaktorWhatIf($versiAktif, $mahasiswaSendiri, $hasilAktif);
        $daftarKodeFaktor = array_keys($skemaFaktor);

        // Ambil input baik dari array what_if[...] maupun dari field tingkat atas
        $inputMentah = is_array($request->input('what_if'))
            ? $request->input('what_if')
            : $request->only($daftarKodeFaktor);

        $rules = [];
        $messages = [];
        $pilihanTervalidasi = [];

        foreach ($skemaFaktor as $kode => $infoFaktor) {
            $namaFaktor = $infoFaktor['nama'];
            $petaOpsi = $infoFaktor['peta_opsi_valid']; // lowercase key -> canonical label

            $rules[$kode] = [
                'required',
                'string',
                function (string $attribute, mixed $value, \Closure $fail) use ($namaFaktor, $petaOpsi) {
                    $kunci = mb_strtolower(trim((string) $value));
                    if ($kunci === '' || ! array_key_exists($kunci, $petaOpsi)) {
                        $fail("Pilihan pada faktor \"{$namaFaktor}\" tidak valid atau berada di luar kategori aturan penilaian. Silakan pilih salah satu kategori yang tersedia pada dropdown.");
                    }
                },
            ];

            $messages["{$kode}.required"] = "Kolom faktor \"{$namaFaktor}\" wajib dipilih (masukan tidak lengkap).";
            $messages["{$kode}.string"] = "Format pilihan pada faktor \"{$namaFaktor}\" tidak valid.";
        }

        $validator = Validator::make($inputMentah, $rules, $messages);

        if ($validator->fails()) {
            return redirect()
                ->to(route('mahasiswa.hasil', $hasil ? ['hasil' => $hasil->id] : []) . '#simulasi-what-if')
                ->withErrors($validator)
                ->withInput($inputMentah);
        }

        foreach ($skemaFaktor as $kode => $infoFaktor) {
            $kunci = mb_strtolower(trim((string) $inputMentah[$kode]));
            $pilihanTervalidasi[$kode] = $infoFaktor['peta_opsi_valid'][$kunci];
        }

        $request->session()->put(
            $this->kunciSesiWhatIf($user->id, $hasilAktif->id),
            $pilihanTervalidasi
        );

        return redirect()
            ->to(route('mahasiswa.hasil', $hasil ? ['hasil' => $hasil->id] : []) . '#simulasi-what-if')
            ->with('toast_success', 'Simulasi What-If berhasil dihitung di memori. Data dan hasil resmi Anda tidak berubah.');
    }

    /**
     * Kembalikan pilihan Simulasi What-If ke data asli mahasiswa (hapus sesi simulasi).
     */
    public function resetWhatIf(Request $request, ?HasilUkt $hasil = null): RedirectResponse
    {
        /** @var User $user */
        $user = $request->user()->loadMissing('mahasiswa');
        $mahasiswaSendiri = $user->mahasiswa;

        $hasilAktif = $this->ambilDanOtorisasiHasilMahasiswa($request, $user, $mahasiswaSendiri, $hasil);

        if ($hasilAktif) {
            $request->session()->forget($this->kunciSesiWhatIf($user->id, $hasilAktif->id));
        }

        return redirect()
            ->to(route('mahasiswa.hasil', $hasil ? ['hasil' => $hasil->id] : []) . '#simulasi-what-if')
            ->with('toast_success', 'Simulasi What-If dikembalikan ke data tercatat Anda.');
    }

    /**
     * Ambil hasil UKT mahasiswa dan tegakkan otorisasi kepemilikan (KF-12).
     */
    private function ambilDanOtorisasiHasilMahasiswa(
        Request $request,
        User $user,
        ?Mahasiswa $mahasiswaSendiri,
        ?HasilUkt $hasil = null
    ): ?HasilUkt {
        // Periksa bila ada percobaan manipulasi parameter query (nim atau mahasiswa_id) milik orang lain
        if ($request->filled('nim') && $mahasiswaSendiri && $request->string('nim')->toString() !== $mahasiswaSendiri->nim) {
            $targetMahasiswa = Mahasiswa::where('nim', $request->string('nim')->toString())->first();
            if ($targetMahasiswa) {
                Gate::authorize('viewMahasiswa', $targetMahasiswa);
            } else {
                $this->auditService->catatPenolakanAkses(
                    pelaku: $user,
                    subjekTipe: Mahasiswa::class,
                    subjekId: $request->string('nim')->toString(),
                    alasan: 'Percobaan mengakses hasil mahasiswa dengan NIM lain melalui parameter query'
                );
                abort(403, 'Akses ditolak. Anda hanya diizinkan melihat hasil milik Anda sendiri.');
            }
        }

        if ($request->filled('mahasiswa_id') && $mahasiswaSendiri && (int) $request->input('mahasiswa_id') !== (int) $mahasiswaSendiri->id) {
            $targetMahasiswa = Mahasiswa::find((int) $request->input('mahasiswa_id'));
            if ($targetMahasiswa) {
                Gate::authorize('viewMahasiswa', $targetMahasiswa);
            } else {
                $this->auditService->catatPenolakanAkses(
                    pelaku: $user,
                    subjekTipe: Mahasiswa::class,
                    subjekId: (int) $request->input('mahasiswa_id'),
                    alasan: 'Percobaan mengakses hasil mahasiswa dengan ID lain melalui parameter query'
                );
                abort(403, 'Akses ditolak. Anda hanya diizinkan melihat hasil milik Anda sendiri.');
            }
        }

        // Bila mengakses via rute /hasil-saya/{hasil}, periksa kepemilikan melalui HasilPolicy
        if ($hasil !== null) {
            Gate::authorize('view', $hasil);
            $hasil->loadMissing(['golongan', 'versiParameter', 'rincianPenilaian.faktor']);

            return $hasil;
        }

        // Query dibatasi ketat ke mahasiswa_id milik pengguna yang sedang login
        $hasilAktif = $mahasiswaSendiri
            ? HasilUkt::with(['golongan', 'versiParameter', 'rincianPenilaian.faktor'])
                ->where('mahasiswa_id', $mahasiswaSendiri->id)
                ->latest('id')
                ->first()
            : null;

        if ($hasilAktif) {
            Gate::authorize('view', $hasilAktif);
        }

        return $hasilAktif;
    }

    /**
     * Siapkan data formulir 5 faktor dan keluaran berdampingan Hasil Saya vs Hasil Simulasi What-If
     * menggunakan ulang RuleEngineService::hitung dan versi parameter aktif yang sama.
     *
     * @return array<string, mixed>|null
     */
    private function siapkanDataWhatIf(
        Request $request,
        User $user,
        ?Mahasiswa $mahasiswa,
        HasilUkt $hasilAktif
    ): ?array {
        $versiAktif = $this->ruleEngineService->versiAktif() ?? $hasilAktif->versiParameter;
        if (! $versiAktif || ! $mahasiswa) {
            return null;
        }

        $skemaFaktor = $this->bangunSkemaFaktorWhatIf($versiAktif, $mahasiswa, $hasilAktif);
        if ($skemaFaktor === []) {
            return null;
        }

        $pilihanSesi = $request->session()->get($this->kunciSesiWhatIf($user->id, $hasilAktif->id), []);
        $masukanSimulasi = [];
        $faktorForm = [];
        $jumlahFaktorDiubah = 0;

        foreach ($skemaFaktor as $kode => $info) {
            $labelTerpilih = $info['kategori_asli_label'];
            if (is_array($pilihanSesi) && isset($pilihanSesi[$kode])) {
                $kunciSesi = mb_strtolower(trim((string) $pilihanSesi[$kode]));
                if (isset($info['peta_opsi_valid'][$kunciSesi])) {
                    $labelTerpilih = $info['peta_opsi_valid'][$kunciSesi];
                }
            }

            $apakahFaktorIniDiubah = $labelTerpilih !== $info['kategori_asli_label'];
            if ($apakahFaktorIniDiubah) {
                $jumlahFaktorDiubah++;
            }

            // Bila kategori yang dipilih sama dengan kategori asli mahasiswa, pakai nilai mentah asli mahasiswa
            // sehingga komponen yang tidak diubah tetap identik 100%. Bila diubah, pakai nilai wakil kategori.
            $opsiTerpilih = $info['opsi_by_label'][$labelTerpilih] ?? reset($info['opsi_by_label']);
            $masukanSimulasi[$kode] = $apakahFaktorIniDiubah
                ? $opsiTerpilih['nilai_wakil']
                : $info['nilai_asli_mentah'];

            $faktorForm[] = [
                'faktor_id' => $info['faktor_id'],
                'kode' => $kode,
                'nama' => $info['nama'],
                'bobot' => $info['bobot'],
                'nilai_asli_formatted' => $info['nilai_asli_formatted'],
                'kategori_asli_label' => $info['kategori_asli_label'],
                'kategori_terpilih_label' => $labelTerpilih,
                'apakah_diubah' => $apakahFaktorIniDiubah,
                'opsi' => array_values($info['opsi_by_label']),
            ];
        }

        // Hitung di memori menggunakan RuleEngineService::hitung dan versi parameter aktif yang sama
        $evaluasiWhatIf = $this->ruleEngineService->hitung($masukanSimulasi, $versiAktif, 'simulasi');

        $rincianAsliByFaktorId = $hasilAktif->rincianPenilaian->keyBy('faktor_id');
        $perbandinganKomponen = [];

        foreach ($evaluasiWhatIf['rincian'] as $barisWhatIf) {
            $faktorId = $barisWhatIf['faktor_id'];
            $kodeFaktor = $barisWhatIf['kode_faktor'];
            $barisAsli = $rincianAsliByFaktorId->get($faktorId);
            $infoSkema = $skemaFaktor[$kodeFaktor] ?? null;

            $poinAsli = $barisAsli ? round((float) $barisAsli->nilai_komponen, 2) : 0.0;
            $poinWhatIf = round((float) $barisWhatIf['nilai_komponen'], 2);
            $selisihPoin = round($poinWhatIf - $poinAsli, 2);

            $labelTerpilih = $pilihanSesi[$kodeFaktor] ?? ($infoSkema['kategori_asli_label'] ?? '');
            $apakahDiubah = $infoSkema ? ($labelTerpilih !== $infoSkema['kategori_asli_label']) : (abs($selisihPoin) >= 0.001);

            $perbandinganKomponen[] = [
                'faktor_id' => $faktorId,
                'kode_faktor' => $kodeFaktor,
                'nama_faktor' => $barisWhatIf['nama_faktor'],
                'bobot' => $infoSkema['bobot'] ?? 0,
                'apakah_diubah' => $apakahDiubah,
                // Data Asli Mahasiswa
                'masukan_asli' => $barisAsli?->nilai_masukan ?? ($infoSkema['nilai_asli_formatted'] ?? '-'),
                'kategori_asli' => $infoSkema['kategori_asli_label'] ?? '-',
                'poin_asli' => $poinAsli,
                'langkah_asli' => $barisAsli?->langkah_perhitungan ?? '-',
                'alasan_asli' => $barisAsli?->kalimat_alasan ?? '-',
                // Data Simulasi What-If (dari RuleEngineService::hitung)
                'masukan_what_if' => $barisWhatIf['nilai_masukan'],
                'kategori_what_if' => $labelTerpilih,
                'poin_what_if' => $poinWhatIf,
                'langkah_what_if' => $barisWhatIf['langkah_perhitungan'],
                'alasan_what_if' => $barisWhatIf['kalimat_alasan'],
                // Selisih
                'selisih_poin' => $selisihPoin,
            ];
        }

        $skorTotalAsli = round((float) $hasilAktif->skor_total, 2);
        $skorTotalWhatIf = round((float) ($evaluasiWhatIf['skor_total'] ?? 0), 2);
        $selisihTotal = round($skorTotalWhatIf - $skorTotalAsli, 2);

        return [
            'versi_parameter' => $versiAktif->versi,
            'berlaku_mulai' => $versiAktif->berlaku_mulai?->locale('id')->translatedFormat('d M Y') ?? '-',
            'sedang_disimulasikan' => is_array($pilihanSesi) && $pilihanSesi !== [],
            'jumlah_faktor_diubah' => $jumlahFaktorDiubah,
            'faktor_form' => $faktorForm,
            'perbandingan_komponen' => $perbandinganKomponen,
            'skor_total_asli' => $skorTotalAsli,
            'skor_total_what_if' => $skorTotalWhatIf,
            'selisih_total' => $selisihTotal,
            'golongan_what_if' => $evaluasiWhatIf['golongan_simulasi'],
        ];
    }

    /**
     * Bangun skema 5 faktor beserta kategori dropdown dari aturan versi parameter aktif.
     *
     * @return array<string, array<string, mixed>>
     */
    private function bangunSkemaFaktorWhatIf(
        VersiParameter $versiAktif,
        Mahasiswa $mahasiswa,
        HasilUkt $hasilAktif
    ): array {
        $versiAktif->loadMissing(['parameterPenilaian.faktor']);

        $parameterList = $versiAktif->parameterPenilaian
            ->filter(fn (ParameterPenilaian $p) => $p->faktor && $p->faktor->aktif)
            ->sortBy(fn (ParameterPenilaian $p) => $p->faktor->urutan)
            ->values();

        $dataMasukanMahasiswa = is_array($mahasiswa->data_masukan) ? $mahasiswa->data_masukan : [];
        $rincianByFaktorId = $hasilAktif->rincianPenilaian->keyBy('faktor_id');

        $skema = [];

        foreach ($parameterList as $param) {
            /** @var FaktorPenilaian $faktor */
            $faktor = $param->faktor;
            $kode = $faktor->kode;
            $aturan = $param->aturan ?? [];
            $tipe = (string) ($aturan['tipe'] ?? 'rentang_angka');
            $bobot = (int) round((float) ($aturan['bobot'] ?? 0));
            $daftarRentang = $aturan['rentang'] ?? [];

            if ($daftarRentang === []) {
                continue;
            }

            $barisRincian = $rincianByFaktorId->get($faktor->id);
            $nilaiAsliMentah = $dataMasukanMahasiswa[$kode] ?? $barisRincian?->nilai_masukan;
            $nilaiAsliFormatted = $barisRincian?->nilai_masukan ?? (string) $nilaiAsliMentah;

            $labelKategoriAsli = $this->cocokkanLabelKategoriAsli($tipe, $daftarRentang, $nilaiAsliMentah);

            $opsiByLabel = [];
            $petaOpsiValid = [];

            foreach ($daftarRentang as $item) {
                $label = (string) ($item['label'] ?? ($item['nilai'] ?? 'Kategori'));
                $skor = (float) ($item['skor'] ?? 1);
                $poin = round(($skor * $bobot) / 100, 2);
                $apakahAsli = $label === $labelKategoriAsli;

                $nilaiWakil = $apakahAsli && $nilaiAsliMentah !== null
                    ? $nilaiAsliMentah
                    : $this->tentukanNilaiWakilKategori($faktor, $tipe, $item);

                $opsiByLabel[$label] = [
                    'label' => $label,
                    'nilai_opsi' => $label,
                    'skor' => (int) round($skor),
                    'poin' => $poin,
                    'nilai_wakil' => $nilaiWakil,
                    'apakah_asli' => $apakahAsli,
                ];

                $petaOpsiValid[mb_strtolower(trim($label))] = $label;
                if (! empty($item['nilai'])) {
                    $petaOpsiValid[mb_strtolower(trim((string) $item['nilai']))] = $label;
                }
            }

            $skema[$kode] = [
                'faktor_id' => $faktor->id,
                'kode' => $kode,
                'nama' => $faktor->nama,
                'bobot' => $bobot,
                'tipe' => $tipe,
                'nilai_asli_mentah' => $nilaiAsliMentah,
                'nilai_asli_formatted' => $nilaiAsliFormatted,
                'kategori_asli_label' => $labelKategoriAsli,
                'opsi_by_label' => $opsiByLabel,
                'peta_opsi_valid' => $petaOpsiValid,
            ];
        }

        return $skema;
    }

    /**
     * Cocokkan nilai asli mahasiswa ke label kategori pada aturan RuleEngine.
     *
     * @param  array<int, array<string, mixed>>  $daftarRentang
     */
    private function cocokkanLabelKategoriAsli(string $tipe, array $daftarRentang, mixed $nilaiMentah): string
    {
        if ($tipe === 'rentang_angka') {
            $angka = is_numeric($nilaiMentah)
                ? (float) $nilaiMentah
                : (float) preg_replace('/[^0-9.]/', '', (string) $nilaiMentah);

            foreach ($daftarRentang as $item) {
                $min = isset($item['min']) ? (float) $item['min'] : null;
                $max = isset($item['max']) ? (float) $item['max'] : null;

                if (($min === null || $angka >= $min) && ($max === null || $angka <= $max)) {
                    return (string) ($item['label'] ?? 'Rentang terukur');
                }
            }
        } else {
            $nilaiString = mb_strtolower(trim((string) $nilaiMentah));
            foreach ($daftarRentang as $item) {
                $kandidatNilai = mb_strtolower(trim((string) ($item['nilai'] ?? '')));
                $kandidatLabel = mb_strtolower(trim((string) ($item['label'] ?? '')));
                if (
                    ($kandidatNilai !== '' && ($nilaiString === $kandidatNilai || str_contains($nilaiString, $kandidatNilai)))
                    || ($kandidatLabel !== '' && $nilaiString === $kandidatLabel)
                ) {
                    return (string) ($item['label'] ?? $item['nilai']);
                }
            }
        }

        $terakhir = end($daftarRentang) ?: [];

        return (string) ($terakhir['label'] ?? ($terakhir['nilai'] ?? 'Kategori umum'));
    }

    /**
     * Tentukan nilai wakil deterministik di dalam rentang kategori agar RuleEngineService::hitung
     * memetakan ke kategori tersebut dengan format nilai masukan yang rapi.
     *
     * @param  array<string, mixed>  $item
     */
    private function tentukanNilaiWakilKategori(FaktorPenilaian $faktor, string $tipe, array $item): int|string
    {
        if ($tipe !== 'rentang_angka') {
            return (string) ($item['nilai'] ?? $item['label'] ?? '');
        }

        $min = (int) ($item['min'] ?? 0);
        $max = (int) ($item['max'] ?? $min);

        if ($faktor->satuan === 'rupiah') {
            if ($min <= 0) {
                return $max;
            }
            if ($max >= 100000000) {
                return $min + 999999; // Rp 9.000.000
            }

            return (int) round((($min - 1) + $max) / 2, -4);
        }

        if ($faktor->satuan === 'orang') {
            return $min >= 2 ? $min : $max;
        }

        if ($faktor->satuan === 'va') {
            return $max <= 2200 ? $max : 3500;
        }

        return (int) round(($min + $max) / 2);
    }

    private function kunciSesiWhatIf(int $userId, int $hasilId): string
    {
        return "sppk_what_if_{$userId}_{$hasilId}";
    }
}
