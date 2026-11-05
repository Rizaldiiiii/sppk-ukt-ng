<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class GolonganUkt extends Model
{
    protected $table = 'golongan_ukt';

    protected $fillable = [
        'kode',
        'nama',
        'nominal_min',
        'nominal_max',
        'sumber',
        'berlaku_mulai',
        'berlaku_sampai',
        'aktif',
    ];

    protected function casts(): array
    {
        return [
            'nominal_min' => 'integer',
            'nominal_max' => 'integer',
            'berlaku_mulai' => 'date',
            'berlaku_sampai' => 'date',
            'aktif' => 'boolean',
        ];
    }

    public function hasilUkt(): HasMany
    {
        return $this->hasMany(HasilUkt::class, 'golongan_id');
    }

    public function formatRentang(): string
    {
        if ($this->nominal_min === $this->nominal_max) {
            return 'Rp '.number_format($this->nominal_min, 0, ',', '.');
        }

        return 'Rp '.number_format($this->nominal_min, 0, ',', '.').' – Rp '.number_format($this->nominal_max, 0, ',', '.');
    }
}

