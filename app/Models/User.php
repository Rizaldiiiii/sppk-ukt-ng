<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    public const PERAN_MAHASISWA = 'mahasiswa';
    public const PERAN_PETUGAS = 'petugas';
    public const PERAN_PIMPINAN = 'pimpinan';

    protected $fillable = [
        'name',
        'nama',
        'email',
        'peran',
        'password',
        'email_verified_at',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    protected function nama(): Attribute
    {
        return Attribute::make(
            get: fn ($value, array $attributes) => $attributes['name'] ?? null,
            set: fn ($value) => ['name' => $value],
        );
    }

    public function mahasiswa(): HasOne
    {
        return $this->hasOne(Mahasiswa::class, 'user_id');
    }

    public function isMahasiswa(): bool
    {
        return $this->peran === self::PERAN_MAHASISWA;
    }

    public function isPetugas(): bool
    {
        return $this->peran === self::PERAN_PETUGAS;
    }

    public function isPimpinan(): bool
    {
        return $this->peran === self::PERAN_PIMPINAN;
    }

    public function labelPeran(): string
    {
        return match ($this->peran) {
            self::PERAN_PETUGAS => 'Petugas UKT',
            self::PERAN_PIMPINAN => 'Pimpinan / Auditor',
            default => 'Mahasiswa',
        };
    }
}
