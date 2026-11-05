<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RincianPenilaian extends Model
{
    protected $table = 'rincian_penilaian';

    protected $fillable = [
        'hasil_id',
        'faktor_id',
        'nilai_masukan',
        'nilai_komponen',
        'langkah_perhitungan',
        'kalimat_alasan',
    ];

    protected function casts(): array
    {
        return [
            'nilai_komponen' => 'float',
        ];
    }

    public function hasil(): BelongsTo
    {
        return $this->belongsTo(HasilUkt::class, 'hasil_id');
    }

    public function faktor(): BelongsTo
    {
        return $this->belongsTo(FaktorPenilaian::class, 'faktor_id');
    }
}

