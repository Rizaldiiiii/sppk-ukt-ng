<?php

namespace App\Services;

use App\Mail\StatusUktBerubahMail;
use App\Models\User;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Throwable;

class NotificationService
{
    public function __construct(
        private readonly AuditService $auditService,
    ) {}

    /**
     * Kirim pemberitahuan di sistem (database notification) dan antrean email (KF-15).
     *
     * Email hanya memuat status baru, tanggal, dan tautan ke halaman status di sistem —
     * tidak pernah memuat data ekonomi, dokumen, kata sandi, atau token.
     * Kegagalan pengiriman email dicatat di audit_logs tanpa menggagalkan transaksi utama.
     */
    public function kirimPemberitahuanStatus(
        User $penerima,
        string $judulPeristiwa,
        string $statusBaru,
        string $tautanHalaman,
        ?User $pemicu = null,
        string $subjekTipe = 'status_ukt',
        string|int|null $subjekId = null,
        bool $kirimEmail = true
    ): string {
        $tanggalFormatted = now()->locale('id')->translatedFormat('d M Y H:i');
        $notificationId = (string) Str::uuid();

        // 1. Simpan notifikasi di sistem (tabel notifications)
        $penerima->notifications()->create([
            'id' => $notificationId,
            'type' => StatusUktBerubahMail::class,
            'data' => [
                'judul' => $judulPeristiwa,
                'status_baru' => $statusBaru,
                'tanggal' => $tanggalFormatted,
                'tautan' => $tautanHalaman,
                'pesan' => "{$judulPeristiwa}: Status saat ini adalah \"{$statusBaru}\" pada {$tanggalFormatted}. Silakan buka halaman sistem untuk melihat rinciannya.",
            ],
            'read_at' => null,
        ]);

        // 2. Antrekan email bila diaktifkan
        if ($kirimEmail && config('sppk.email_notifikasi_aktif', true)) {
            try {
                Mail::to($penerima->email)->queue(
                    new StatusUktBerubahMail(
                        namaPenerima: $penerima->nama,
                        judulPeristiwa: $judulPeristiwa,
                        statusBaru: $statusBaru,
                        tanggalPerubahan: $tanggalFormatted,
                        tautanHalaman: $tautanHalaman,
                    )
                );
            } catch (Throwable $e) {
                // Kegagalan pengiriman email tidak boleh menggagalkan perubahan status itu sendiri,
                // tetapi wajib dicatat agar terlihat oleh petugas (PRD KF-15 & Blueprint §7.6).
                $this->auditService->catat(
                    pelaku: $pemicu,
                    aksi: 'email_notifikasi_gagal',
                    subjekTipe: $subjekTipe,
                    subjekId: $subjekId ?? $penerima->id,
                    nilaiSebelum: null,
                    nilaiSesudah: [
                        'penerima_id' => $penerima->id,
                        'judul_peristiwa' => $judulPeristiwa,
                        'status_baru' => $statusBaru,
                        'galat' => $e->getMessage(),
                    ]
                );
            }
        }

        return $notificationId;
    }
}

