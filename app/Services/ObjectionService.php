<?php

namespace App\Services;

use App\Models\PengajuanKeberatan;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

class ObjectionService
{
    public const STATUS_DIAJUKAN = 'Diajukan';
    public const STATUS_DALAM_PENINJAUAN = 'Dalam Peninjauan';
    public const STATUS_PERLU_PERBAIKAN = 'Perlu Perbaikan';
    public const STATUS_DISETUJUI = 'Disetujui';
    public const STATUS_DITOLAK = 'Ditolak';

    public function __construct(
        private readonly AuditService $auditService,
        private readonly NotificationService $notificationService,
    ) {}

    /**
     * Ubah status pengajuan keberatan dengan pencatatan audit dan pemicu notifikasi KF-15.
     * Kegagalan email tidak membatalkan perubahan status (Blueprint §7.3 & §7.6).
     */
    public function ubahStatus(
        PengajuanKeberatan $pengajuan,
        User $petugas,
        string $statusBaru,
        ?string $catatanPetugas = null
    ): PengajuanKeberatan {
        $statusDiizinkan = [
            self::STATUS_DIAJUKAN,
            self::STATUS_DALAM_PENINJAUAN,
            self::STATUS_PERLU_PERBAIKAN,
            self::STATUS_DISETUJUI,
            self::STATUS_DITOLAK,
        ];

        if (! in_array($statusBaru, $statusDiizinkan, true)) {
            throw new InvalidArgumentException("Status keberatan tidak valid: {$statusBaru}");
        }

        return DB::transaction(function () use ($pengajuan, $petugas, $statusBaru, $catatanPetugas) {
            $statusLama = $pengajuan->status;
            $catatanLama = $pengajuan->catatan_petugas;

            $dataUpdate = [
                'status' => $statusBaru,
                'catatan_petugas' => $catatanPetugas,
            ];

            if (in_array($statusBaru, [self::STATUS_DISETUJUI, self::STATUS_DITOLAK], true)) {
                $dataUpdate['diputuskan_pada'] = now();
                $dataUpdate['diputuskan_oleh'] = $petugas->id;
            }

            $updated = PengajuanKeberatan::whereKey($pengajuan->id)->update($dataUpdate);

            if ($updated === 0) {
                throw new InvalidArgumentException('Pengajuan keberatan tidak ditemukan untuk diperbarui.');
            }

            $pengajuan->refresh();

            $this->auditService->catat(
                pelaku: $petugas,
                aksi: 'ubah_status_keberatan',
                subjekTipe: PengajuanKeberatan::class,
                subjekId: $pengajuan->id,
                nilaiSebelum: [
                    'status' => $statusLama,
                    'catatan_petugas' => $catatanLama,
                ],
                nilaiSesudah: [
                    'status' => $statusBaru,
                    'catatan_petugas' => $catatanPetugas,
                ]
            );

            $mahasiswaUser = $pengajuan->mahasiswa?->user;
            if ($mahasiswaUser) {
                $this->notificationService->kirimPemberitahuanStatus(
                    penerima: $mahasiswaUser,
                    judulPeristiwa: 'Perubahan Status Pengajuan Keberatan UKT',
                    statusBaru: $statusBaru,
                    tautanHalaman: url('/keberatan/'.$pengajuan->id),
                    pemicu: $petugas,
                    subjekTipe: PengajuanKeberatan::class,
                    subjekId: $pengajuan->id,
                );
            }

            return $pengajuan;
        });
    }
}

