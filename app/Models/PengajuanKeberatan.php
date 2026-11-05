<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PengajuanKeberatan extends Model
{
    protected $table = 'pengajuan_keberatan';

    protected $fillable = [
        'mahasiswa_id',
        'hasil_id',
        'alasan',
        'status',
        'catatan_petugas',
        'diajukan_pada',
        'diputuskan_pada',
        'diputuskan_oleh',
    ];

    protected function casts(): array
    {
        return [
            'diajukan_pada' => 'datetime',
            'diputuskan_pada' => 'datetime',
        ];
    }

    public function mahasiswa(): BelongsTo
    {
        return $this->belongsTo(Mahasiswa::class, 'mahasiswa_id');
    }

    public function hasil(): BelongsTo
    {
        return $this->belongsTo(HasilUkt::class, 'hasil_id');
    }

    public function pemutus(): BelongsTo
    {
        return $this->belongsTo(User::class, 'diputuskan_oleh');
    }

    public function dokumen(): HasMany
    {
        return $this->hasMany(DokumenKeberatan::class, 'pengajuan_id');
    }
}

