<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class AuditLog extends Model
{
    public const LABEL_NILAI_KOSONG = '(kosong)';

    public const MODE_RINGKAS = 'ringkas';
    public const MODE_DETAIL = 'detail';

    public const KATEGORI_KEBERATAN = 'Keberatan';
    public const KATEGORI_PERTANYAAN = 'Pertanyaan';
    public const KATEGORI_HASIL = 'Hasil';
    public const KATEGORI_KONTEN_PARAMETER = 'Konten & Parameter';
    public const KATEGORI_AKUN = 'Akun';
    public const KATEGORI_KEAMANAN = 'Keamanan';
    public const KATEGORI_SISTEM = 'Sistem';

    public const DAFTAR_KATEGORI = [
        self::KATEGORI_KEBERATAN,
        self::KATEGORI_PERTANYAAN,
        self::KATEGORI_HASIL,
        self::KATEGORI_KONTEN_PARAMETER,
        self::KATEGORI_AKUN,
        self::KATEGORI_KEAMANAN,
        self::KATEGORI_SISTEM,
    ];

    /**
     * Pemetaan mode tampil untuk tiap aksi audit:
     * - MODE_RINGKAS: hanya aksi perubahan data.
     * - MODE_DETAIL: aksi akses dan peristiwa sistem (termasuk penolakan_akses dan aksi tak dikenal).
     *
     * @var array<string, string>
     */
    public const PETA_MODE_AKSI = [
        // Perubahan data (tampil di mode ringkas & mode detail)
        'ajukan_keberatan' => self::MODE_RINGKAS,
        'ubah_status_keberatan' => self::MODE_RINGKAS,
        'lengkapi_perbaikan_keberatan' => self::MODE_RINGKAS,
        'buat_pertanyaan' => self::MODE_RINGKAS,
        'jawab_pertanyaan' => self::MODE_RINGKAS,
        'tutup_pertanyaan' => self::MODE_RINGKAS,
        'buka_kembali_pertanyaan' => self::MODE_RINGKAS,
        'tambah_faktor_penilaian' => self::MODE_RINGKAS,
        'ubah_faktor_penilaian' => self::MODE_RINGKAS,
        'tambah_golongan_ukt' => self::MODE_RINGKAS,
        'ubah_golongan_ukt' => self::MODE_RINGKAS,
        'buat_versi_parameter_baru' => self::MODE_RINGKAS,
        'ubah_perkiraan_waktu' => self::MODE_RINGKAS,
        'ubah_konten_publik' => self::MODE_RINGKAS,
        'ubah_visual_panduan' => self::MODE_RINGKAS,
        'entri_hasil_resmi' => self::MODE_RINGKAS,
        'buat_hasil_simulasi' => self::MODE_RINGKAS,
        'ubah_kata_sandi' => self::MODE_RINGKAS,

        // Hanya akses & peristiwa sistem (tampil di mode detail saja)
        'akses_dokumen_keberatan' => self::MODE_DETAIL,
        'penolakan_akses' => self::MODE_DETAIL,
        'peringatan_mendekati_tenggat_sla' => self::MODE_DETAIL,
        'peringatan_terlewat_tenggat_sla' => self::MODE_DETAIL,
        'email_notifikasi_gagal' => self::MODE_DETAIL,
    ];

    /**
     * Pemetaan warna badge per kategori (7 warna palet referensi; teks putih #FFFFFF untuk latar gelap, teks hitam #191414 untuk latar terang, border opasitas 40%):
     * - Keberatan: Klein Blue (#4100F5), teks putih #FFFFFF
     * - Pertanyaan: Aquamarine (#9BF0E1), teks hitam #191414
     * - Konten & Parameter: Citric (#CDF564), teks hitam #191414
     * - Hasil: Tangerine (#FF4632), teks putih #FFFFFF
     * - Akun: Black (#191414), teks putih #FFFFFF
     * - Keamanan: Fushia (#F023A5), teks putih #FFFFFF
     * - Sistem: Gradient linear-gradient(180deg, #9BF0E1 0%, #FDEA2E 20%, #FF4632 40%, #191414 60%, #4100F5 80%, #8DA2D9 100%)
     *   (termasuk Factory Yellow #FDEA2E & Storm #8DA2D9), teks putih #FFFFFF + text-shadow gelap tipis, border #191414/30
     *
     * @var array<string, string>
     */
    public const PETA_WARNA_KATEGORI = [
        self::KATEGORI_KEBERATAN => 'bg-[#4100F5] text-[#FFFFFF] border border-[#4100F5]/40',
        self::KATEGORI_PERTANYAAN => 'bg-[#9BF0E1] text-[#191414] border border-[#9BF0E1]/40',
        self::KATEGORI_HASIL => 'bg-[#FF4632] text-[#FFFFFF] border border-[#FF4632]/40',
        self::KATEGORI_KONTEN_PARAMETER => 'bg-[#CDF564] text-[#191414] border border-[#CDF564]/40',
        self::KATEGORI_AKUN => 'bg-[#191414] text-[#FFFFFF] border border-[#191414]/40',
        self::KATEGORI_KEAMANAN => 'bg-[#F023A5] text-[#FFFFFF] border border-[#F023A5]/40',
        self::KATEGORI_SISTEM => 'bg-[linear-gradient(180deg,#9BF0E1_0%,#FDEA2E_20%,#FF4632_40%,#191414_60%,#4100F5_80%,#8DA2D9_100%)] text-[#FFFFFF] [text-shadow:0_1px_2px_rgba(0,0,0,0.45)] border border-[#191414]/30',
    ];

    /**
     * Peta label Bahasa Indonesia untuk seluruh aksi audit di kode.
     *
     * @var array<string, string>
     */
    public const PETA_LABEL_AKSI = [
        'ajukan_keberatan' => 'Mengajukan keberatan',
        'akses_dokumen_keberatan' => 'Mengakses dokumen keberatan',
        'buat_pertanyaan' => 'Mengirim pertanyaan',
        'buat_versi_parameter_baru' => 'Membuat versi parameter penilaian baru',
        'buka_kembali_pertanyaan' => 'Membuka kembali pertanyaan',
        'email_notifikasi_gagal' => 'Email notifikasi gagal terkirim',
        'jawab_pertanyaan' => 'Menjawab pertanyaan',
        'lengkapi_perbaikan_keberatan' => 'Melengkapi perbaikan keberatan',
        'penolakan_akses' => 'Penolakan akses',
        'peringatan_mendekati_tenggat_sla' => 'Peringatan mendekati tenggat SLA',
        'peringatan_terlewat_tenggat_sla' => 'Peringatan melewati tenggat SLA',
        'tambah_faktor_penilaian' => 'Menambah faktor penilaian',
        'tambah_golongan_ukt' => 'Menambah golongan UKT',
        'tutup_pertanyaan' => 'Menutup pertanyaan',
        'ubah_faktor_penilaian' => 'Mengubah faktor penilaian',
        'ubah_golongan_ukt' => 'Mengubah golongan UKT',
        'ubah_kata_sandi' => 'Mengubah kata sandi',
        'ubah_konten_publik' => 'Mengubah konten publik',
        'ubah_perkiraan_waktu' => 'Mengubah perkiraan waktu tunggu',
        'ubah_status_keberatan' => 'Mengubah status keberatan',
        // Aksi tambahan hasil audit kode app/
        'ubah_visual_panduan' => 'Mengubah visual panduan tahapan',
        'entri_hasil_resmi' => 'Menyimpan hasil penetapan resmi',
        'buat_hasil_simulasi' => 'Membuat hasil simulasi penilaian',
    ];

    /**
     * Peta kategori untuk tiap aksi audit.
     *
     * @var array<string, list<string>>
     */
    public const PETA_KATEGORI_AKSI = [
        self::KATEGORI_KEBERATAN => [
            'ajukan_keberatan',
            'ubah_status_keberatan',
            'lengkapi_perbaikan_keberatan',
            'akses_dokumen_keberatan',
        ],
        self::KATEGORI_PERTANYAAN => [
            'buat_pertanyaan',
            'jawab_pertanyaan',
            'tutup_pertanyaan',
            'buka_kembali_pertanyaan',
        ],
        self::KATEGORI_HASIL => [
            'entri_hasil_resmi',
            'buat_hasil_simulasi',
        ],
        self::KATEGORI_KONTEN_PARAMETER => [
            'ubah_konten_publik',
            'ubah_visual_panduan',
            'tambah_faktor_penilaian',
            'ubah_faktor_penilaian',
            'tambah_golongan_ukt',
            'ubah_golongan_ukt',
            'buat_versi_parameter_baru',
            'ubah_perkiraan_waktu',
        ],
        self::KATEGORI_AKUN => [
            'ubah_kata_sandi',
        ],
        self::KATEGORI_KEAMANAN => [
            'penolakan_akses',
        ],
        self::KATEGORI_SISTEM => [
            'peringatan_mendekati_tenggat_sla',
            'peringatan_terlewat_tenggat_sla',
            'email_notifikasi_gagal',
        ],
    ];

    /**
     * Peta nama Bahasa Indonesia untuk tipe subjek audit.
     *
     * @var array<string, string>
     */
    public const PETA_LABEL_SUBJEK = [
        'PengajuanKeberatan' => 'Pengajuan Keberatan',
        'DokumenKeberatan' => 'Dokumen Keberatan',
        'Pertanyaan' => 'Pertanyaan',
        'KontenPublik' => 'Konten Publik',
        'FaktorPenilaian' => 'Faktor Penilaian',
        'GolonganUkt' => 'Golongan UKT',
        'PerkiraanWaktu' => 'Perkiraan Waktu',
        'VersiParameter' => 'Versi Parameter',
        'ParameterPenilaian' => 'Parameter Penilaian',
        'HasilUkt' => 'Hasil UKT',
        'RincianPenilaian' => 'Rincian Penilaian',
        'Mahasiswa' => 'Mahasiswa',
        'User' => 'Akun Pengguna',
        'route' => 'Rute Halaman',
    ];

    /**
     * Peta label Bahasa Indonesia untuk kunci JSON nilai_sebelum / nilai_sesudah.
     *
     * @var array<string, string>
     */
    public const PETA_LABEL_KUNCI = [
        'status' => 'Status',
        'status_baru' => 'Status Baru',
        'catatan_petugas' => 'Catatan Petugas',
        'catatan_perbaikan' => 'Catatan Perbaikan',
        'catatan' => 'Catatan',
        'alasan' => 'Alasan',
        'jawaban' => 'Jawaban',
        'judul' => 'Judul',
        'ringkasan' => 'Ringkasan',
        'isi' => 'Isi',
        'slug' => 'Slug Halaman',
        'visual_path' => 'Berkas Visual',
        'kode' => 'Kode',
        'nama' => 'Nama',
        'penjelasan_singkat' => 'Penjelasan Singkat',
        'pengaruh_umum' => 'Pengaruh Umum',
        'urutan' => 'Urutan',
        'aktif' => 'Status Aktif',
        'status_konfirmasi' => 'Status Konfirmasi',
        'nominal_min' => 'Nominal Minimum',
        'nominal_max' => 'Nominal Maksimum',
        'sumber' => 'Sumber',
        'berlaku_mulai' => 'Berlaku Mulai',
        'berlaku_sampai' => 'Berlaku Sampai',
        'tenggat_waktu' => 'Tenggat Waktu',
        'sisa_hari' => 'Sisa Hari Kerja',
        'hari_terlewat' => 'Hari Terlewat',
        'sla_hari_kerja' => 'SLA Hari Kerja',
        'perlu_tindak_lanjut_pimpinan' => 'Perlu Tindak Lanjut Pimpinan',
        'peran_pelaku' => 'Peran Pelaku',
        'path' => 'Jalur URL',
        'mahasiswa_id' => 'ID Mahasiswa',
        'pengajuan_id' => 'ID Pengajuan',
        'hasil_id' => 'ID Hasil UKT',
        'golongan_id' => 'ID Golongan',
        'versi_parameter_id' => 'ID Versi Parameter',
        'penerima_id' => 'ID Penerima',
        'nama_asli' => 'Nama Berkas',
        'jumlah_dokumen' => 'Jumlah Dokumen',
        'dokumen' => 'Dokumen',
        'dokumen_tambahan' => 'Dokumen Tambahan',
        'jenis' => 'Jenis',
        'teks_perkiraan' => 'Teks Perkiraan',
        'tanggal' => 'Tanggal',
        'judul_peristiwa' => 'Judul Peristiwa',
        'galat' => 'Pesan Galat',
        'skor_total' => 'Skor Total',
        'versi_baru' => 'Versi Baru',
        'versi_aktif_sebelumnya' => 'Versi Aktif Sebelumnya',
        'versi_id_sebelumnya' => 'ID Versi Sebelumnya',
        'aksi' => 'Aksi',
        'id' => 'ID',
    ];

    protected $table = 'audit_logs';

    protected $fillable = [
        'pelaku_id',
        'aksi',
        'subjek_tipe',
        'subjek_id',
        'nilai_sebelum',
        'nilai_sesudah',
        'dibuat_pada',
    ];

    protected function casts(): array
    {
        return [
            'nilai_sebelum' => 'array',
            'nilai_sesudah' => 'array',
            'dibuat_pada' => 'datetime',
        ];
    }

    public function pelaku(): BelongsTo
    {
        return $this->belongsTo(User::class, 'pelaku_id');
    }

    /**
     * Label Bahasa Indonesia untuk aksi pada baris audit ini (atau kode aksi yang diberikan).
     * Bila tidak terdaftar di peta, ubah underscore menjadi spasi dengan huruf awal kapital (tidak pernah kosong).
     */
    public function labelAksi(?string $kode = null): string
    {
        return self::labelUntukAksi($kode ?? (string) $this->aksi);
    }

    public static function labelUntukAksi(?string $kode): string
    {
        $kodeBersih = trim((string) $kode);

        if ($kodeBersih !== '' && isset(self::PETA_LABEL_AKSI[$kodeBersih])) {
            return self::PETA_LABEL_AKSI[$kodeBersih];
        }

        $fallback = Str::title(trim((string) preg_replace('/\s+/', ' ', str_replace(['_', '-'], ' ', $kodeBersih))));

        return $fallback !== '' ? $fallback : 'Aktivitas Sistem';
    }

    /**
     * Kategori Bahasa Indonesia untuk aksi pada baris audit ini (atau kode aksi yang diberikan).
     * Aksi yang tidak dikenal masuk kategori "Sistem".
     */
    public function kategori(?string $kode = null): string
    {
        return self::kategoriUntukAksi($kode ?? (string) $this->aksi);
    }

    public static function kategoriUntukAksi(?string $kode): string
    {
        $kodeBersih = trim((string) $kode);

        foreach (self::PETA_KATEGORI_AKSI as $namaKategori => $daftarKode) {
            if (in_array($kodeBersih, $daftarKode, true)) {
                return $namaKategori;
            }
        }

        return self::KATEGORI_SISTEM;
    }

    /**
     * Daftar kode aksi yang termasuk dalam suatu kategori.
     *
     * @return list<string>
     */
    public static function aksiUntukKategori(string $kategori): array
    {
        return self::PETA_KATEGORI_AKSI[$kategori] ?? [];
    }

    /**
     * Daftar seluruh kode aksi yang terpetakan ke kategori selain Sistem.
     *
     * @return list<string>
     */
    public static function aksiSelainSistem(): array
    {
        $hasil = [];
        foreach (self::PETA_KATEGORI_AKSI as $namaKategori => $daftarKode) {
            if ($namaKategori !== self::KATEGORI_SISTEM) {
                $hasil = array_merge($hasil, $daftarKode);
            }
        }

        return array_values(array_unique($hasil));
    }

    /**
     * Normalisasi nilai query parameter mode ('ringkas' atau 'detail', default 'ringkas').
     */
    public static function normalisasiMode(?string $mode): string
    {
        return trim((string) $mode) === self::MODE_DETAIL
            ? self::MODE_DETAIL
            : self::MODE_RINGKAS;
    }

    /**
     * Apakah aksi pada log ini ditampilkan di mode ringkas (perubahan data).
     * Aksi tak dikenal selalu masuk mode detail (false).
     */
    public function tampilDiModeRingkas(?string $kode = null): bool
    {
        return self::apakahTampilDiModeRingkas($kode ?? (string) $this->aksi);
    }

    public static function apakahTampilDiModeRingkas(?string $kode): bool
    {
        $kodeBersih = trim((string) $kode);

        return (self::PETA_MODE_AKSI[$kodeBersih] ?? self::MODE_DETAIL) === self::MODE_RINGKAS;
    }

    /**
     * Daftar kode aksi yang ditampilkan pada mode ringkas.
     *
     * @return list<string>
     */
    public static function daftarAksiModeRingkas(): array
    {
        $hasil = [];
        foreach (self::PETA_MODE_AKSI as $kodeAksi => $modeAksi) {
            if ($modeAksi === self::MODE_RINGKAS) {
                $hasil[] = $kodeAksi;
            }
        }

        return $hasil;
    }

    /**
     * Daftar kategori yang memiliki aksi pada mode yang dipilih.
     *
     * @return list<string>
     */
    public static function daftarKategoriUntukMode(string $mode): array
    {
        if (self::normalisasiMode($mode) === self::MODE_DETAIL) {
            return self::DAFTAR_KATEGORI;
        }

        $aksiRingkas = self::daftarAksiModeRingkas();
        $kategoriRingkas = [];

        foreach (self::PETA_KATEGORI_AKSI as $namaKategori => $daftarKode) {
            if (array_intersect($daftarKode, $aksiRingkas) !== []) {
                $kategoriRingkas[] = $namaKategori;
            }
        }

        return $kategoriRingkas;
    }

    /**
     * Kelas warna badge untuk kategori log ini (tanpa warna hijau/success maupun merah/error).
     */
    public function kelasBadgeKategori(?string $kategori = null): string
    {
        return self::kelasWarnaKategori($kategori ?? $this->kategori());
    }

    public static function kelasWarnaKategori(string $kategori): string
    {
        return self::PETA_WARNA_KATEGORI[$kategori] ?? self::PETA_WARNA_KATEGORI[self::KATEGORI_SISTEM];
    }

    /**
     * Label Bahasa Indonesia untuk tipe subjek pada baris audit ini.
     */
    public function labelSubjekTipe(?string $tipe = null): string
    {
        return self::labelUntukSubjekTipe($tipe ?? (string) $this->subjek_tipe);
    }

    public static function labelUntukSubjekTipe(?string $tipe): string
    {
        $tipeBersih = trim((string) $tipe);
        if ($tipeBersih === '') {
            return 'Sistem';
        }

        if (isset(self::PETA_LABEL_SUBJEK[$tipeBersih])) {
            return self::PETA_LABEL_SUBJEK[$tipeBersih];
        }

        $base = class_basename($tipeBersih);
        if (isset(self::PETA_LABEL_SUBJEK[$base])) {
            return self::PETA_LABEL_SUBJEK[$base];
        }

        $headline = trim(Str::headline($base));

        return $headline !== '' ? $headline : 'Sistem';
    }

    /**
     * Label untuk kunci JSON pada nilai_sebelum / nilai_sesudah.
     */
    public static function labelUntukKunci(string $kunci): string
    {
        $kunciBersih = trim($kunci);
        if (isset(self::PETA_LABEL_KUNCI[$kunciBersih])) {
            return self::PETA_LABEL_KUNCI[$kunciBersih];
        }

        $label = str_replace('_', ' ', $kunciBersih);

        return $label !== '' ? ucfirst($label) : 'Data';
    }

    /**
     * Format nilai tunggal atau array/objek menjadi satu baris ringkas yang mudah dibaca manusia.
     */
    public static function formatNilaiRingkas(mixed $nilai): string
    {
        if ($nilai === null || $nilai === '') {
            return self::LABEL_NILAI_KOSONG;
        }

        if (is_bool($nilai)) {
            return $nilai ? 'Ya' : 'Tidak';
        }

        if (is_array($nilai)) {
            if ($nilai === []) {
                return self::LABEL_NILAI_KOSONG;
            }

            $semuaSkalar = ! array_is_list($nilai)
                ? false
                : array_reduce($nilai, static fn (bool $carry, mixed $item) => $carry && (is_scalar($item) || $item === null), true);

            if ($semuaSkalar) {
                $teks = implode(', ', array_map(
                    static fn ($v) => ($v === null || $v === '')
                        ? self::LABEL_NILAI_KOSONG
                        : (is_bool($v) ? ($v ? 'Ya' : 'Tidak') : (string) $v),
                    $nilai
                ));
            } else {
                $bagian = [];
                foreach ($nilai as $k => $v) {
                    $vStr = is_array($v)
                        ? json_encode($v, JSON_UNESCAPED_UNICODE)
                        : (is_bool($v) ? ($v ? 'Ya' : 'Tidak') : (($v === null || $v === '') ? self::LABEL_NILAI_KOSONG : (string) $v));
                    $bagian[] = is_int($k) ? $vStr : ($k . ': ' . $vStr);
                }
                $teks = implode('; ', $bagian);
            }

            return Str::limit($teks, 140);
        }

        $teks = trim((string) $nilai);

        // Jika string berisi JSON panjang, ringkas satu baris
        if ((str_starts_with($teks, '{') || str_starts_with($teks, '[')) && strlen($teks) > 120) {
            return Str::limit((string) preg_replace('/\s+/', ' ', $teks), 120);
        }

        return Str::limit($teks, 140);
    }

    /**
     * Susun daftar perubahan per kunci: [['kunci' => ..., 'label' => ..., 'sebelum' => ..., 'sesudah' => ...], ...]
     *
     * @return list<array{kunci: string, label: string, sebelum: string, sesudah: string}>
     */
    public function daftarPerubahanTerbaca(): array
    {
        $sebelum = is_array($this->nilai_sebelum) ? $this->nilai_sebelum : [];
        $sesudah = is_array($this->nilai_sesudah) ? $this->nilai_sesudah : [];

        if ($sebelum === [] && $sesudah === []) {
            return [];
        }

        $semuaKunci = array_values(array_unique(array_merge(array_keys($sebelum), array_keys($sesudah))));
        $daftar = [];

        foreach ($semuaKunci as $kunci) {
            $kunciStr = (string) $kunci;
            $daftar[] = [
                'kunci' => $kunciStr,
                'label' => self::labelUntukKunci($kunciStr),
                'sebelum' => array_key_exists($kunci, $sebelum) ? self::formatNilaiRingkas($sebelum[$kunci]) : self::LABEL_NILAI_KOSONG,
                'sesudah' => array_key_exists($kunci, $sesudah) ? self::formatNilaiRingkas($sesudah[$kunci]) : self::LABEL_NILAI_KOSONG,
            ];
        }

        return $daftar;
    }
}

