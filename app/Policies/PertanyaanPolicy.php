<?php

namespace App\Policies;

use App\Models\Pertanyaan;
use App\Models\User;
use App\Services\AuditService;

class PertanyaanPolicy
{
    public function __construct(
        private readonly AuditService $auditService,
    ) {}

    public function view(User $user, Pertanyaan $pertanyaan): bool
    {
        if ($user->isPetugas()) {
            return true;
        }

        if ($user->isMahasiswa() && $user->mahasiswa && (int) $pertanyaan->mahasiswa_id === (int) $user->mahasiswa->id) {
            return true;
        }

        $this->auditService->catatPenolakanAkses(
            pelaku: $user,
            subjekTipe: Pertanyaan::class,
            subjekId: $pertanyaan->id,
            alasan: 'Percobaan membuka pertanyaan milik mahasiswa lain atau oleh peran tidak berwenang'
        );

        return false;
    }

    public function answer(User $user, Pertanyaan $pertanyaan): bool
    {
        if ($user->isPetugas()) {
            return true;
        }

        $this->auditService->catatPenolakanAkses(
            pelaku: $user,
            subjekTipe: Pertanyaan::class,
            subjekId: $pertanyaan->id,
            alasan: 'Percobaan menjawab pertanyaan oleh non-petugas'
        );

        return false;
    }
}

