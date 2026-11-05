<?php

namespace App\Services;

use App\Models\AuditLog;
use App\Models\User;

class AuditService
{
    private const SENSITIVE_KEYS = [
        'password',
        'password_confirmation',
        'kata_sandi',
        'kata_sandi_hash',
        'remember_token',
        'token',
        '_token',
    ];

    public function catat(
        ?User $pelaku,
        string $aksi,
        string $subjekTipe,
        string|int|null $subjekId = null,
        ?array $nilaiSebelum = null,
        ?array $nilaiSesudah = null
    ): AuditLog {
        return AuditLog::create([
            'pelaku_id' => $pelaku?->id,
            'aksi' => $aksi,
            'subjek_tipe' => $subjekTipe,
            'subjek_id' => $subjekId !== null ? (string) $subjekId : null,
            'nilai_sebelum' => $this->bersihkanSensitif($nilaiSebelum),
            'nilai_sesudah' => $this->bersihkanSensitif($nilaiSesudah),
            'dibuat_pada' => now(),
        ]);
    }

    public function catatPenolakanAkses(
        ?User $pelaku,
        string $subjekTipe,
        string|int|null $subjekId = null,
        string $alasan = 'Akses lintas pemilik atau lintas peran ditolak'
    ): AuditLog {
        return $this->catat(
            pelaku: $pelaku,
            aksi: 'penolakan_akses',
            subjekTipe: $subjekTipe,
            subjekId: $subjekId,
            nilaiSebelum: null,
            nilaiSesudah: [
                'peran_pelaku' => $pelaku?->peran ?? 'publik',
                'alasan' => $alasan,
                'path' => request()?->path(),
            ]
        );
    }

    private function bersihkanSensitif(?array $data): ?array
    {
        if ($data === null) {
            return null;
        }

        $hasil = [];
        foreach ($data as $key => $value) {
            if (in_array(strtolower((string) $key), self::SENSITIVE_KEYS, true)) {
                continue;
            }

            $hasil[$key] = is_array($value) ? $this->bersihkanSensitif($value) : $value;
        }

        return $hasil;
    }
}

