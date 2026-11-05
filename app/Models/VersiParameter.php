<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class VersiParameter extends Model
{
    protected $table = 'versi_parameter';

    protected $fillable = [
        'versi',
        'berlaku_mulai',
        'dibuat_oleh',
        'catatan',
        'aktif',
    ];

    protected function casts(): array
    {
        return [
            'berlaku_mulai' => 'date',
            'aktif' => 'boolean',
        ];
    }

    public function pembuat(): BelongsTo
    {
        return $this->belongsTo(User::class, 'dibuat_oleh');
    }

    public function parameterPenilaian(): HasMany
    {
        return $this->hasMany(ParameterPenilaian::class, 'versi_parameter_id');
    }

    public function hasilUkt(): HasMany
    {
        return $this->hasMany(HasilUkt::class, 'versi_parameter_id');
    }
}

