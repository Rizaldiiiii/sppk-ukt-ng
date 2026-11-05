<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ParameterPenilaian extends Model
{
    protected $table = 'parameter_penilaian';

    protected $fillable = [
        'versi_parameter_id',
        'faktor_id',
        'aturan',
    ];

    protected function casts(): array
    {
        return [
            'aturan' => 'array',
        ];
    }

    public function versiParameter(): BelongsTo
    {
        return $this->belongsTo(VersiParameter::class, 'versi_parameter_id');
    }

    public function faktor(): BelongsTo
    {
        return $this->belongsTo(FaktorPenilaian::class, 'faktor_id');
    }
}

