<?php

namespace App\Policies;

use App\Models\DokumenKeberatan;
use App\Models\User;
use App\Services\AuditService;

class DokumenPolicy
{
    public function __construct(
        private readonly AuditService $auditService,
    ) {}

    public function view(User $user, DokumenKeberatan $dokumen): bool
    {
        if ($user->isPetugas()) {
            return true;
        }

        $pengajuan = $dokumen->pengajuan;
        if ($user->isMahasiswa() && $user->mahasiswa && $pengajuan && (int) $pengajuan->mahasiswa_id === (int) $user->mahasiswa->id) {
            return true;
        }

        $this->auditService->catatPenolakanAkses(
            pelaku: $user,
            subjekTipe: DokumenKeberatan::class,
            subjekId: $dokumen->id,
            alasan: 'Percobaan mengakses dokumen keberatan tanpa otorisasi kepemilikan atau peran petugas'
        );

        return false;
    }
}

