<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AuditLog extends Model
{
    protected $table = 'audit_logs';

    protected $fillable = [
        'pelaku_id',
        'aksi',
        'subjek_tipe',
        'subjek_id',
        'nilai_sebelum',
        'nilai_sesudah',
        'dibuat_pada',
    ];

    protected function casts(): array
    {
        return [
            'nilai_sebelum' => 'array',
            'nilai_sesudah' => 'array',
            'dibuat_pada' => 'datetime',
        ];
    }

    public function pelaku(): BelongsTo
    {
        return $this->belongsTo(User::class, 'pelaku_id');
    }
}

