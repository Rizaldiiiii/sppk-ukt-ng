<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Tambahkan kolom SLA terkuci dan tenggat waktu pada tabel pengajuan_keberatan (KF-11 Tahap 3).
     */
    public function up(): void
    {
        Schema::table('pengajuan_keberatan', function (Blueprint $table) {
            $table->unsignedSmallInteger('sla_hari_kerja')->nullable()->after('diajukan_pada');
            $table->date('tenggat_waktu')->nullable()->after('sla_hari_kerja');
            $table->timestamp('notifikasi_mendekati_dikirim_pada')->nullable()->after('diputuskan_oleh');
            $table->timestamp('notifikasi_terlewat_dikirim_pada')->nullable()->after('notifikasi_mendekati_dikirim_pada');

            $table->index(['status', 'tenggat_waktu']);
        });
    }

    public function down(): void
    {
        Schema::table('pengajuan_keberatan', function (Blueprint $table) {
            $table->dropIndex(['status', 'tenggat_waktu']);
            $table->dropColumn([
                'sla_hari_kerja',
                'tenggat_waktu',
                'notifikasi_mendekati_dikirim_pada',
                'notifikasi_terlewat_dikirim_pada',
            ]);
        });
    }
};

