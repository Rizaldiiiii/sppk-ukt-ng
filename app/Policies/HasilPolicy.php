<?php

namespace App\Policies;

use App\Models\HasilUkt;
use App\Models\Mahasiswa;
use App\Models\User;
use App\Services\AuditService;

class HasilPolicy
{
    public function __construct(
        private readonly AuditService $auditService,
    ) {}

    public function view(User $user, HasilUkt $hasil): bool
    {
        if ($user->isPetugas()) {
            return true;
        }

        if ($user->isPimpinan() && config('sppk.pimpinan_lihat_rincian_perorangan', false)) {
            return true;
        }

        if ($user->isMahasiswa() && $user->mahasiswa && (int) $hasil->mahasiswa_id === (int) $user->mahasiswa->id) {
            return true;
        }

        $this->auditService->catatPenolakanAkses(
            pelaku: $user,
            subjekTipe: HasilUkt::class,
            subjekId: $hasil->id,
            alasan: 'Percobaan melihat hasil UKT milik mahasiswa lain atau di luar kewenangan peran'
        );

        return false;
    }

    public function viewMahasiswa(User $user, Mahasiswa $mahasiswa): bool
    {
        if ($user->isPetugas()) {
            return true;
        }

        if ($user->isPimpinan() && config('sppk.pimpinan_lihat_rincian_perorangan', false)) {
            return true;
        }

        if ($user->isMahasiswa() && (int) $mahasiswa->user_id === (int) $user->id) {
            return true;
        }

        $this->auditService->catatPenolakanAkses(
            pelaku: $user,
            subjekTipe: Mahasiswa::class,
            subjekId: $mahasiswa->id,
            alasan: 'Percobaan mengakses data hasil mahasiswa lain lewat URL langsung'
        );

        return false;
    }

    public function manage(User $user): bool
    {
        if ($user->isPetugas()) {
            return true;
        }

        $this->auditService->catatPenolakanAkses(
            pelaku: $user,
            subjekTipe: HasilUkt::class,
            subjekId: null,
            alasan: 'Percobaan mengelola hasil penetapan UKT oleh non-petugas'
        );

        return false;
    }
}

