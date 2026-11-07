<?php

namespace App\Services;

use App\Models\PengajuanKeberatan;
use App\Models\PerkiraanWaktu;
use App\Models\User;
use Carbon\Carbon;
use Carbon\CarbonInterface;

class SlaService
{
    /**
     * Pilihan resmi SLA hari kerja pada halaman /petugas/perkiraan-waktu (KF-11).
     * Tidak menggunakan input teks bebas agar sistem dapat menghitung tenggat otomatis.
     *
     * @var array<string, string>
     */
    public const PILIHAN_SLA = [
        'belum_ditetapkan' => 'Belum ditetapkan',
        '3' => '3 hari kerja',
        '5' => '5 hari kerja',
        '7' => '7 hari kerja',
        '14' => '14 hari kerja',
    ];

    /**
     * Daftar hari libur nasional Indonesia (tanggal merah di luar Sabtu–Minggu) untuk perhitungan hari kerja.
     *
     * @var list<string>
     */
    public const LIBUR_NASIONAL = [
        '2026-01-01', // Tahun Baru Masehi
        '2026-01-16', // Isra Mikraj
        '2026-02-17', // Tahun Baru Imlek
        '2026-03-19', // Hari Suci Nyepi
        '2026-03-20', // Idulfitri 1447 H
        '2026-03-23', // Cuti Bersama Idulfitri
        '2026-04-03', // Wafat Yesus Kristus
        '2026-05-01', // Hari Buruh Internasional
        '2026-05-14', // Kenaikan Yesus Kristus
        '2026-05-27', // Iduladha 1447 H
        '2026-06-01', // Hari Lahir Pancasila
        '2026-06-16', // Tahun Baru Islam 1448 H
        '2026-08-17', // Hari Kemerdekaan RI
        '2026-08-25', // Maulid Nabi Muhammad SAW
        '2026-12-25', // Hari Raya Natal
    ];

    /**
     * Ambil jumlah hari kerja SLA aktif untuk jenis tertentu ('penetapan' atau 'keberatan').
     * Mengembalikan null bila belum ditetapkan.
     */
    public function ambilHariKerjaAktif(string $jenis = 'keberatan'): ?int
    {
        $perkiraan = PerkiraanWaktu::where('jenis', $jenis)
            ->where('aktif', true)
            ->latest('id')
            ->first();

        return $this->ekstrakHariKerjaDariPerkiraan($perkiraan);
    }

    /**
     * Ekstrak angka hari kerja (3, 5, 7, 14) dari model PerkiraanWaktu.
     */
    public function ekstrakHariKerjaDariPerkiraan(?PerkiraanWaktu $perkiraan): ?int
    {
        if (! $perkiraan || ! $perkiraan->aktif) {
            return null;
        }

        if (preg_match('/\b(3|5|7|14)\s*hari\s*kerja\b/i', (string) $perkiraan->teks_perkiraan, $matches)) {
            return (int) $matches[1];
        }

        return null;
    }

    /**
     * Bentuk teks_perkiraan, sumber, dan tanggal otomatis dari pilihan dropdown petugas (KF-11).
     *
     * @return array{teks_perkiraan: string, sumber: string, tanggal: string}
     */
    public function bentukDataPerkiraan(string $jenis, int $hariKerja, User $petugas): array
    {
        $teks = $jenis === 'penetapan'
            ? "Perkiraan waktu tunggu penetapan hasil UKT adalah {$hariKerja} hari kerja sejak berkas dinyatakan lengkap."
            : "Perkiraan waktu penyelesaian peninjauan keberatan UKT adalah {$hariKerja} hari kerja sejak pengajuan diterima.";

        return [
            'teks_perkiraan' => $teks,
            'sumber' => 'Ketentuan Layanan Petugas UKT UNG (Ditetapkan oleh ' . $petugas->nama . ')',
            'tanggal' => now()->toDateString(),
        ];
    }

    /**
     * Periksa apakah suatu tanggal merupakan hari kerja (Senin–Jumat dan bukan libur nasional).
     */
    public function apakahHariKerja(CarbonInterface $tanggal): bool
    {
        if ($tanggal->isWeekend()) {
            return false;
        }

        return ! in_array($tanggal->toDateString(), self::LIBUR_NASIONAL, true);
    }

    /**
     * Hitung tanggal tenggat waktu dengan menambahkan N hari kerja (Senin–Jumat, lewati libur nasional).
     */
    public function hitungTenggatWaktu(CarbonInterface $diajukanPada, int $hariKerja): Carbon
    {
        $cursor = Carbon::instance($diajukanPada->toDateTime())->startOfDay();
        $tersisa = max(1, $hariKerja);

        while ($tersisa > 0) {
            $cursor->addDay();
            if ($this->apakahHariKerja($cursor)) {
                $tersisa--;
            }
        }

        return $cursor;
    }

    /**
     * Hitung jumlah hari kerja di antara dua tanggal (dari $mulai eksklusif sampai $akhir inklusif).
     */
    public function hitungHariKerjaAntara(CarbonInterface $mulai, CarbonInterface $akhir): int
    {
        $awal = Carbon::instance($mulai->toDateTime())->startOfDay();
        $ujung = Carbon::instance($akhir->toDateTime())->startOfDay();

        if ($ujung->lte($awal)) {
            return 0;
        }

        $jumlah = 0;
        $cursor = $awal->copy();

        while ($cursor->lt($ujung)) {
            $cursor->addDay();
            if ($this->apakahHariKerja($cursor)) {
                $jumlah++;
            }
        }

        return $jumlah;
    }

    /**
     * Evaluasi status tenggat waktu satu PengajuanKeberatan (KF-11).
     *
     * @return array{
     *     kategori: string,
     *     sisa_hari: int|null,
     *     hari_terlewat: int|null,
     *     persen_terpakai: float|null,
     *     teks_perkiraan: string,
     *     badge_label: string|null,
     *     badge_status: string|null,
     *     perlu_tindak_lanjut_pimpinan: bool
     * }
     */
    public function evaluasiTenggat(PengajuanKeberatan $pengajuan, ?CarbonInterface $acuan = null): array
    {
        $hariIni = $acuan
            ? Carbon::instance($acuan->toDateTime())->startOfDay()
            : now()->startOfDay();

        $apakahTerminal = ObjectionService::apakahStatusTerminal($pengajuan->status);

        if (! $pengajuan->tenggat_waktu || ! $pengajuan->sla_hari_kerja) {
            return [
                'kategori' => $apakahTerminal ? 'selesai' : 'belum_ditetapkan',
                'sisa_hari' => null,
                'hari_terlewat' => null,
                'persen_terpakai' => null,
                'teks_perkiraan' => 'Waktu tunggu belum ditetapkan',
                'badge_label' => null,
                'badge_status' => null,
                'perlu_tindak_lanjut_pimpinan' => false,
            ];
        }

        $tenggat = Carbon::instance($pengajuan->tenggat_waktu->toDateTime())->startOfDay();
        $tanggalTenggatFormatted = $tenggat->locale('id')->translatedFormat('d M Y');
        $slaHariKerja = max(1, (int) $pengajuan->sla_hari_kerja);

        if ($apakahTerminal) {
            $tanggalPutus = ($pengajuan->diputuskan_pada ?? $pengajuan->updated_at)?->locale('id')->translatedFormat('d M Y') ?? $tanggalTenggatFormatted;

            return [
                'kategori' => 'selesai',
                'sisa_hari' => 0,
                'hari_terlewat' => 0,
                'persen_terpakai' => 100.0,
                'teks_perkiraan' => "Perkiraan selesai: {$tanggalTenggatFormatted} (Diputuskan pada {$tanggalPutus})",
                'badge_label' => null,
                'badge_status' => null,
                'perlu_tindak_lanjut_pimpinan' => false,
            ];
        }

        // Bila sudah melewati tanggal tenggat
        if ($hariIni->gt($tenggat)) {
            $hariKerjaTerlewat = $this->hitungHariKerjaAntara($tenggat, $hariIni);
            $hariTerlewat = max(1, $hariKerjaTerlewat);

            return [
                'kategori' => 'terlewat',
                'sisa_hari' => 0,
                'hari_terlewat' => $hariTerlewat,
                'persen_terpakai' => 100.0,
                'teks_perkiraan' => "Perkiraan selesai: {$tanggalTenggatFormatted}, terlewat {$hariTerlewat} hari",
                'badge_label' => "Terlewat {$hariTerlewat} hari",
                'badge_status' => 'error',
                'perlu_tindak_lanjut_pimpinan' => true,
            ];
        }

        // Masih dalam batas tenggat (hariIni <= tenggat)
        $sisaHariKerja = $this->hitungHariKerjaAntara($hariIni, $tenggat);
        $hariTerpakai = max(0, $slaHariKerja - $sisaHariKerja);
        $persenTerpakai = round(($hariTerpakai / $slaHariKerja) * 100, 1);

        // Mendekati batas waktu bila >= 80% waktu terpakai ATAU sisa <= 1 hari kerja
        $apakahMendekati = $persenTerpakai >= 80.0 || $sisaHariKerja <= 1;

        return [
            'kategori' => $apakahMendekati ? 'mendekati' : 'normal',
            'sisa_hari' => $sisaHariKerja,
            'hari_terlewat' => 0,
            'persen_terpakai' => $persenTerpakai,
            'teks_perkiraan' => "Perkiraan selesai: {$tanggalTenggatFormatted}, sisa {$sisaHariKerja} hari",
            'badge_label' => $apakahMendekati ? 'Mendekati batas waktu' : null,
            'badge_status' => $apakahMendekati ? 'warning' : null,
            'perlu_tindak_lanjut_pimpinan' => false,
        ];
    }
}

