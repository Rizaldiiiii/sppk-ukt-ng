<?php

namespace App\Models;

use App\Services\SlaService;
use Carbon\CarbonInterface;
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
        'sla_hari_kerja',
        'tenggat_waktu',
        'diputuskan_pada',
        'diputuskan_oleh',
        'notifikasi_mendekati_dikirim_pada',
        'notifikasi_terlewat_dikirim_pada',
    ];

    protected function casts(): array
    {
        return [
            'diajukan_pada' => 'datetime',
            'sla_hari_kerja' => 'integer',
            'tenggat_waktu' => 'date',
            'diputuskan_pada' => 'datetime',
            'notifikasi_mendekati_dikirim_pada' => 'datetime',
            'notifikasi_terlewat_dikirim_pada' => 'datetime',
        ];
    }

    /**
     * Evaluasi informasi tenggat waktu SLA pengajuan ini (KF-11).
     */
    public function infoTenggat(?CarbonInterface $acuan = null): array
    {
        return app(SlaService::class)->evaluasiTenggat($this, $acuan);
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

