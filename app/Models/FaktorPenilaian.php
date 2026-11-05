<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class FaktorPenilaian extends Model
{
    protected $table = 'faktor_penilaian';

    protected $fillable = [
        'kode',
        'nama',
        'penjelasan_singkat',
        'pengaruh_umum',
        'satuan',
        'status_konfirmasi',
        'urutan',
        'aktif',
    ];

    protected function casts(): array
    {
        return [
            'urutan' => 'integer',
            'aktif' => 'boolean',
        ];
    }

    public function parameterPenilaian(): HasMany
    {
        return $this->hasMany(ParameterPenilaian::class, 'faktor_id');
    }

    public function isTerkonfirmasi(): bool
    {
        return $this->status_konfirmasi === 'terkonfirmasi';
    }

    public function labelStatusKonfirmasi(): string
    {
        return $this->isTerkonfirmasi()
            ? 'Terkonfirmasi resmi'
            : 'Menunggu konfirmasi resmi';
    }
}

