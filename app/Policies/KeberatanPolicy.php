<?php

namespace App\Policies;

use App\Models\PengajuanKeberatan;
use App\Models\User;
use App\Services\AuditService;

class KeberatanPolicy
{
    public function __construct(
        private readonly AuditService $auditService,
    ) {}

    public function view(User $user, PengajuanKeberatan $pengajuan): bool
    {
        if ($user->isPetugas()) {
            return true;
        }

        if ($user->isMahasiswa() && $user->mahasiswa && (int) $pengajuan->mahasiswa_id === (int) $user->mahasiswa->id) {
            return true;
        }

        $this->auditService->catatPenolakanAkses(
            pelaku: $user,
            subjekTipe: PengajuanKeberatan::class,
            subjekId: $pengajuan->id,
            alasan: 'Percobaan membuka pengajuan keberatan milik mahasiswa lain atau oleh peran tidak berwenang'
        );

        return false;
    }

    public function create(User $user): bool
    {
        if ($user->isMahasiswa()) {
            return true;
        }

        $this->auditService->catatPenolakanAkses(
            pelaku: $user,
            subjekTipe: PengajuanKeberatan::class,
            subjekId: null,
            alasan: 'Percobaan membuat pengajuan keberatan oleh non-mahasiswa'
        );

        return false;
    }

    public function updateStatus(User $user, PengajuanKeberatan $pengajuan): bool
    {
        if ($user->isPetugas()) {
            return true;
        }

        $this->auditService->catatPenolakanAkses(
            pelaku: $user,
            subjekTipe: PengajuanKeberatan::class,
            subjekId: $pengajuan->id,
            alasan: 'Percobaan mengubah status keberatan oleh non-petugas'
        );

        return false;
    }
}

