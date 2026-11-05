<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DokumenKeberatan extends Model
{
    protected $table = 'dokumen_keberatan';

    protected $fillable = [
        'pengajuan_id',
        'path_penyimpanan',
        'nama_asli',
        'mime',
        'ukuran',
        'diunggah_oleh',
    ];

    protected function casts(): array
    {
        return [
            'ukuran' => 'integer',
        ];
    }

    public function pengajuan(): BelongsTo
    {
        return $this->belongsTo(PengajuanKeberatan::class, 'pengajuan_id');
    }

    public function pengunggah(): BelongsTo
    {
        return $this->belongsTo(User::class, 'diunggah_oleh');
    }
}

