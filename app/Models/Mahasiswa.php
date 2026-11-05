<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Mahasiswa extends Model
{
    protected $table = 'mahasiswa';

    protected $fillable = [
        'user_id',
        'nim',
        'program_studi',
        'angkatan',
        'data_masukan',
    ];

    protected function casts(): array
    {
        return [
            'angkatan' => 'integer',
            'data_masukan' => 'array',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function hasilUkt(): HasMany
    {
        return $this->hasMany(HasilUkt::class, 'mahasiswa_id');
    }

    public function hasilTerbaru(): HasOne
    {
        return $this->hasOne(HasilUkt::class, 'mahasiswa_id')->latestOfMany('id');
    }

    public function pengajuanKeberatan(): HasMany
    {
        return $this->hasMany(PengajuanKeberatan::class, 'mahasiswa_id');
    }

    public function pertanyaan(): HasMany
    {
        return $this->hasMany(Pertanyaan::class, 'mahasiswa_id');
    }
}

