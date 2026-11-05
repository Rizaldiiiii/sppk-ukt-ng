<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PerkiraanWaktu extends Model
{
    protected $table = 'perkiraan_waktu';

    protected $fillable = [
        'jenis',
        'teks_perkiraan',
        'sumber',
        'tanggal',
        'aktif',
    ];

    protected function casts(): array
    {
        return [
            'tanggal' => 'date',
            'aktif' => 'boolean',
        ];
    }
}

