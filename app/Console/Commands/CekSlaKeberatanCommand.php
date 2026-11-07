<?php

namespace App\Console\Commands;

use App\Models\PengajuanKeberatan;
use App\Models\User;
use App\Services\AuditService;
use App\Services\NotificationService;
use App\Services\ObjectionService;
use Illuminate\Console\Command;

class CekSlaKeberatanCommand extends Command
{
    /**
     * Nama dan signature perintah Artisan terjadwal untuk pemeriksaan harian SLA keberatan (KF-11).
     *
     * @var string
     */
    protected $signature = 'sppk:cek-sla-keberatan';

    /**
     * Deskripsi perintah Artisan.
     *
     * @var string
     */
    protected $description = 'Periksa harian tenggat waktu SLA pengajuan keberatan UKT dan kirim pemberitahuan kepada petugas, pimpinan, serta mahasiswa (KF-11).';

    public function handle(
        NotificationService $notificationService,
        AuditService $auditService,
    ): int {
        $daftarPengajuan = PengajuanKeberatan::with(['mahasiswa.user'])
            ->whereNotIn('status', [ObjectionService::STATUS_DISETUJUI, ObjectionService::STATUS_DITOLAK])
            ->whereNotNull('tenggat_waktu')
            ->get();

        $daftarPetugas = User::where('peran', User::PERAN_PETUGAS)->get();
        $daftarPimpinan = User::where('peran', User::PERAN_PIMPINAN)->get();

        $jumlahMendekati = 0;
        $jumlahTerlewat = 0;

        foreach ($daftarPengajuan as $pengajuan) {
            $info = $pengajuan->infoTenggat();

            if ($info['kategori'] === 'mendekati' && $pengajuan->notifikasi_mendekati_dikirim_pada === null) {
                $sisaHari = $info['sisa_hari'] ?? 0;

                foreach ($daftarPetugas as $petugas) {
                    $notificationService->kirimPemberitahuanStatus(
                        penerima: $petugas,
                        judulPeristiwa: "Pengajuan Keberatan #{$pengajuan->id} Mendekati Batas Waktu (Sisa {$sisaHari} Hari Kerja)",
                        statusBaru: 'Mendekati batas waktu (' . $pengajuan->status . ')',
                        tautanHalaman: url('/petugas/keberatan/' . $pengajuan->id),
                        pemicu: null,
                        subjekTipe: PengajuanKeberatan::class,
                        subjekId: $pengajuan->id,
                        kirimEmail: false,
                    );
                }

                // Pimpinan menerima notifikasi hanya dengan ID pengajuan tanpa nama/NIM/data ekonomi pribadi
                foreach ($daftarPimpinan as $pimpinan) {
                    $notificationService->kirimPemberitahuanStatus(
                        penerima: $pimpinan,
                        judulPeristiwa: "Pantauan SLA: Pengajuan Keberatan #{$pengajuan->id} Mendekati Batas Waktu",
                        statusBaru: 'Mendekati batas waktu (' . $pengajuan->status . ')',
                        tautanHalaman: url('/pimpinan/rekap'),
                        pemicu: null,
                        subjekTipe: PengajuanKeberatan::class,
                        subjekId: $pengajuan->id,
                        kirimEmail: false,
                    );
                }

                PengajuanKeberatan::whereKey($pengajuan->id)->update([
                    'notifikasi_mendekati_dikirim_pada' => now(),
                ]);

                $auditService->catat(
                    pelaku: null,
                    aksi: 'peringatan_mendekati_tenggat_sla',
                    subjekTipe: PengajuanKeberatan::class,
                    subjekId: $pengajuan->id,
                    nilaiSebelum: null,
                    nilaiSesudah: [
                        'status' => $pengajuan->status,
                        'tenggat_waktu' => $pengajuan->tenggat_waktu?->toDateString(),
                        'sisa_hari' => $sisaHari,
                    ]
                );

                $jumlahMendekati++;
            } elseif ($info['kategori'] === 'terlewat' && $pengajuan->notifikasi_terlewat_dikirim_pada === null) {
                $hariTerlewat = $info['hari_terlewat'] ?? 1;
                $statusLama = $pengajuan->status;
                $statusBaru = $statusLama === ObjectionService::STATUS_DIAJUKAN
                    ? ObjectionService::STATUS_DALAM_PENINJAUAN
                    : $statusLama;

                $catatanSla = 'Perkiraan waktu tunggu peninjauan telah terlewat (' . $hariTerlewat . ' hari kerja). Pengajuan Anda tetap aktif dan sedang diprioritaskan untuk diselesaikan oleh petugas.';
                $catatanDiperbarui = $pengajuan->catatan_petugas
                    ? $pengajuan->catatan_petugas
                    : $catatanSla;

                PengajuanKeberatan::whereKey($pengajuan->id)->update([
                    'status' => $statusBaru,
                    'catatan_petugas' => $catatanDiperbarui,
                    'notifikasi_terlewat_dikirim_pada' => now(),
                ]);

                // Beri tahu mahasiswa agar tidak menunggu tanpa kabar (Kriteria Penerimaan KF-11 butir 2)
                $mahasiswaUser = $pengajuan->mahasiswa?->user;
                if ($mahasiswaUser) {
                    $notificationService->kirimPemberitahuanStatus(
                        penerima: $mahasiswaUser,
                        judulPeristiwa: "Pemberitahuan Perkiraan Waktu Peninjauan Keberatan #{$pengajuan->id}",
                        statusBaru: "{$statusBaru} — Perkiraan waktu terlewat {$hariTerlewat} hari, sedang diprioritaskan petugas",
                        tautanHalaman: url('/keberatan/' . $pengajuan->id),
                        pemicu: null,
                        subjekTipe: PengajuanKeberatan::class,
                        subjekId: $pengajuan->id,
                        kirimEmail: true,
                    );
                }

                // Beri tahu seluruh petugas
                foreach ($daftarPetugas as $petugas) {
                    $notificationService->kirimPemberitahuanStatus(
                        penerima: $petugas,
                        judulPeristiwa: "PERINGATAN SLA: Pengajuan Keberatan #{$pengajuan->id} Terlewat {$hariTerlewat} Hari",
                        statusBaru: "Terlewat {$hariTerlewat} hari ({$statusBaru})",
                        tautanHalaman: url('/petugas/keberatan/' . $pengajuan->id),
                        pemicu: null,
                        subjekTipe: PengajuanKeberatan::class,
                        subjekId: $pengajuan->id,
                        kirimEmail: false,
                    );
                }

                // Beri tahu pimpinan tanpa data pribadi mahasiswa
                foreach ($daftarPimpinan as $pimpinan) {
                    $notificationService->kirimPemberitahuanStatus(
                        penerima: $pimpinan,
                        judulPeristiwa: "Tindak Lanjut Pimpinan: Pengajuan Keberatan #{$pengajuan->id} Terlewat {$hariTerlewat} Hari",
                        statusBaru: 'Perlu tindak lanjut pimpinan',
                        tautanHalaman: url('/pimpinan/rekap'),
                        pemicu: null,
                        subjekTipe: PengajuanKeberatan::class,
                        subjekId: $pengajuan->id,
                        kirimEmail: false,
                    );
                }

                $auditService->catat(
                    pelaku: null,
                    aksi: 'peringatan_terlewat_tenggat_sla',
                    subjekTipe: PengajuanKeberatan::class,
                    subjekId: $pengajuan->id,
                    nilaiSebelum: [
                        'status' => $statusLama,
                    ],
                    nilaiSesudah: [
                        'status' => $statusBaru,
                        'tenggat_waktu' => $pengajuan->tenggat_waktu?->toDateString(),
                        'hari_terlewat' => $hariTerlewat,
                        'perlu_tindak_lanjut_pimpinan' => true,
                    ]
                );

                $jumlahTerlewat++;
            }
        }

        $this->info("Pemeriksaan SLA selesai: {$jumlahMendekati} mendekati batas waktu, {$jumlahTerlewat} melewati batas waktu.");

        return self::SUCCESS;
    }
}

