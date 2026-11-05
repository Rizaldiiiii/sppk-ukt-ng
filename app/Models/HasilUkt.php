<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class HasilUkt extends Model
{
    protected $table = 'hasil_ukt';

    protected $fillable = [
        'mahasiswa_id',
        'golongan_id',
        'sumber',
        'versi_parameter_id',
        'nominal',
        'skor_total',
        'tanggal_penetapan',
        'sumber_penetapan',
        'ditetapkan_oleh',
        'catatan',
    ];

    protected function casts(): array
    {
        return [
            'nominal' => 'integer',
            'skor_total' => 'float',
            'tanggal_penetapan' => 'date',
        ];
    }

    public function mahasiswa(): BelongsTo
    {
        return $this->belongsTo(Mahasiswa::class, 'mahasiswa_id');
    }

    public function golongan(): BelongsTo
    {
        return $this->belongsTo(GolonganUkt::class, 'golongan_id');
    }

    public function versiParameter(): BelongsTo
    {
        return $this->belongsTo(VersiParameter::class, 'versi_parameter_id');
    }

    public function penetap(): BelongsTo
    {
        return $this->belongsTo(User::class, 'ditetapkan_oleh');
    }

    public function rincianPenilaian(): HasMany
    {
        return $this->hasMany(RincianPenilaian::class, 'hasil_id');
    }

    public function isResmi(): bool
    {
        return $this->sumber === 'resmi';
    }

    public function isSimulasi(): bool
    {
        return $this->sumber !== 'resmi';
    }

    public function formatNominal(): string
    {
        if ($this->nominal === null) {
            return 'Belum ditetapkan';
        }

        return 'Rp '.number_format($this->nominal, 0, ',', '.');
    }
}

