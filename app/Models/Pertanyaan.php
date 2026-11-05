<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Pertanyaan extends Model
{
    protected $table = 'pertanyaan';

    protected $fillable = [
        'mahasiswa_id',
        'isi',
        'status',
        'jawaban',
        'dijawab_oleh',
        'dijawab_pada',
    ];

    protected function casts(): array
    {
        return [
            'dijawab_pada' => 'datetime',
        ];
    }

    public function mahasiswa(): BelongsTo
    {
        return $this->belongsTo(Mahasiswa::class, 'mahasiswa_id');
    }

    public function penjawab(): BelongsTo
    {
        return $this->belongsTo(User::class, 'dijawab_oleh');
    }
}

