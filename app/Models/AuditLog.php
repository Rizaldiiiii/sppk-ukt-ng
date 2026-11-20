<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class AuditLog extends Model
{
    public const KATEGORI_KEBERATAN = 'Keberatan';
    public const KATEGORI_PERTANYAAN = 'Pertanyaan';
    public const KATEGORI_KONTEN_PARAMETER = 'Konten & Parameter';
    public const KATEGORI_AKUN = 'Akun';
    public const KATEGORI_KEAMANAN = 'Keamanan';
    public const KATEGORI_SISTEM = 'Sistem';

    public const DAFTAR_KATEGORI = [
        self::KATEGORI_KEBERATAN,
        self::KATEGORI_PERTANYAAN,
        self::KATEGORI_KONTEN_PARAMETER,
        self::KATEGORI_AKUN,
        self::KATEGORI_KEAMANAN,
        self::KATEGORI_SISTEM,
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
        self::KATEGORI_KONTEN_PARAMETER => [
            'ubah_konten_publik',
            'ubah_visual_panduan',
            'tambah_faktor_penilaian',
            'ubah_faktor_penilaian',
            'tambah_golongan_ukt',
            'ubah_golongan_ukt',
            'buat_versi_parameter_baru',
            'ubah_perkiraan_waktu',
            'entri_hasil_resmi',
            'buat_hasil_simulasi',
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
            return '—';
        }

        if (is_bool($nilai)) {
            return $nilai ? 'Ya' : 'Tidak';
        }

        if (is_array($nilai)) {
            if ($nilai === []) {
                return '—';
            }

            $semuaSkalar = ! array_is_list($nilai)
                ? false
                : array_reduce($nilai, static fn (bool $carry, mixed $item) => $carry && (is_scalar($item) || $item === null), true);

            if ($semuaSkalar) {
                $teks = implode(', ', array_map(static fn ($v) => (string) $v, $nilai));
            } else {
                $bagian = [];
                foreach ($nilai as $k => $v) {
                    $vStr = is_array($v)
                        ? json_encode($v, JSON_UNESCAPED_UNICODE)
                        : (is_bool($v) ? ($v ? 'Ya' : 'Tidak') : (string) ($v ?? '—'));
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
                'sebelum' => array_key_exists($kunci, $sebelum) ? self::formatNilaiRingkas($sebelum[$kunci]) : '—',
                'sesudah' => array_key_exists($kunci, $sesudah) ? self::formatNilaiRingkas($sesudah[$kunci]) : '—',
            ];
        }

        return $daftar;
    }
}

