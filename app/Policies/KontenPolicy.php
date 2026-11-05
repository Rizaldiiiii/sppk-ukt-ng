<?php

namespace App\Policies;

use App\Models\KontenPublik;
use App\Models\User;
use App\Services\AuditService;

class KontenPolicy
{
    public function __construct(
        private readonly AuditService $auditService,
    ) {}

    public function manage(User $user): bool
    {
        if ($user->isPetugas()) {
            return true;
        }

        $this->auditService->catatPenolakanAkses(
            pelaku: $user,
            subjekTipe: KontenPublik::class,
            subjekId: null,
            alasan: 'Percobaan mengelola konten publik, faktor, atau golongan UKT oleh non-petugas'
        );

        return false;
    }
}

