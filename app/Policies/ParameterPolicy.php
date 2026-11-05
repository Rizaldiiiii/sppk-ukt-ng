<?php

namespace App\Policies;

use App\Models\User;
use App\Models\VersiParameter;
use App\Services\AuditService;

class ParameterPolicy
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
            subjekTipe: VersiParameter::class,
            subjekId: null,
            alasan: 'Percobaan mengelola parameter mesin penilaian oleh non-petugas'
        );

        return false;
    }
}

