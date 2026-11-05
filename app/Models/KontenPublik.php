<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class KontenPublik extends Model
{
    protected $table = 'konten_publik';

    protected $fillable = [
        'slug',
        'judul',
        'ringkasan',
        'isi',
        'diperbarui_oleh',
        'diperbarui_pada',
    ];

    protected function casts(): array
    {
        return [
            'diperbarui_pada' => 'datetime',
        ];
    }

    public function pembaru(): BelongsTo
    {
        return $this->belongsTo(User::class, 'diperbarui_oleh');
    }
}

