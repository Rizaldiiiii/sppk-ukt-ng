<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('mahasiswa', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->unique()->constrained('users')->cascadeOnDelete();
            $table->string('nim', 30)->unique();
            $table->string('program_studi');
            $table->unsignedSmallInteger('angkatan');
            $table->json('data_masukan')->nullable();
            $table->timestamps();
        });

        Schema::create('konten_publik', function (Blueprint $table) {
            $table->id();
            $table->string('slug', 60)->unique();
            $table->string('judul');
            $table->text('ringkasan')->nullable();
            $table->longText('isi');
            $table->foreignId('diperbarui_oleh')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('diperbarui_pada')->nullable();
            $table->timestamps();
        });

        Schema::create('golongan_ukt', function (Blueprint $table) {
            $table->id();
            $table->string('kode', 20)->unique();
            $table->string('nama', 100);
            $table->unsignedBigInteger('nominal_min');
            $table->unsignedBigInteger('nominal_max');
            $table->string('sumber');
            $table->date('berlaku_mulai');
            $table->date('berlaku_sampai')->nullable();
            $table->boolean('aktif')->default(true);
            $table->timestamps();
        });

        Schema::create('faktor_penilaian', function (Blueprint $table) {
            $table->id();
            $table->string('kode', 60)->unique();
            $table->string('nama');
            $table->text('penjelasan_singkat');
            $table->text('pengaruh_umum')->nullable();
            $table->string('satuan', 40)->nullable();
            $table->string('status_konfirmasi', 40)->default('menunggu_konfirmasi');
            $table->unsignedSmallInteger('urutan')->default(1);
            $table->boolean('aktif')->default(true);
            $table->timestamps();
        });

        Schema::create('versi_parameter', function (Blueprint $table) {
            $table->id();
            $table->string('versi', 40)->unique();
            $table->date('berlaku_mulai');
            $table->foreignId('dibuat_oleh')->nullable()->constrained('users')->nullOnDelete();
            $table->text('catatan')->nullable();
            $table->boolean('aktif')->default(true)->index();
            $table->timestamps();
        });

        Schema::create('parameter_penilaian', function (Blueprint $table) {
            $table->id();
            $table->foreignId('versi_parameter_id')->constrained('versi_parameter')->cascadeOnDelete();
            $table->foreignId('faktor_id')->constrained('faktor_penilaian')->cascadeOnDelete();
            $table->json('aturan');
            $table->timestamps();

            $table->unique(['versi_parameter_id', 'faktor_id']);
        });

        Schema::create('hasil_ukt', function (Blueprint $table) {
            $table->id();
            $table->foreignId('mahasiswa_id')->constrained('mahasiswa')->cascadeOnDelete();
            $table->foreignId('golongan_id')->nullable()->constrained('golongan_ukt')->nullOnDelete();
            $table->string('sumber', 20)->default('simulasi'); // resmi | simulasi
            $table->foreignId('versi_parameter_id')->nullable()->constrained('versi_parameter')->nullOnDelete();
            $table->unsignedBigInteger('nominal')->nullable();
            $table->decimal('skor_total', 8, 2)->nullable();
            $table->date('tanggal_penetapan')->nullable();
            $table->string('sumber_penetapan')->nullable();
            $table->foreignId('ditetapkan_oleh')->nullable()->constrained('users')->nullOnDelete();
            $table->text('catatan')->nullable();
            $table->timestamps();

            $table->index('mahasiswa_id');
            $table->index(['mahasiswa_id', 'sumber']);
        });

        Schema::create('rincian_penilaian', function (Blueprint $table) {
            $table->id();
            $table->foreignId('hasil_id')->constrained('hasil_ukt')->cascadeOnDelete();
            $table->foreignId('faktor_id')->constrained('faktor_penilaian')->cascadeOnDelete();
            $table->string('nilai_masukan');
            $table->decimal('nilai_komponen', 8, 2);
            $table->text('langkah_perhitungan');
            $table->text('kalimat_alasan');
            $table->timestamps();

            $table->index('hasil_id');
        });

        Schema::create('pengajuan_keberatan', function (Blueprint $table) {
            $table->id();
            $table->foreignId('mahasiswa_id')->constrained('mahasiswa')->cascadeOnDelete();
            $table->foreignId('hasil_id')->nullable()->constrained('hasil_ukt')->nullOnDelete();
            $table->text('alasan');
            $table->string('status', 40)->default('Diajukan');
            $table->text('catatan_petugas')->nullable();
            $table->timestamp('diajukan_pada')->nullable();
            $table->timestamp('diputuskan_pada')->nullable();
            $table->foreignId('diputuskan_oleh')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index(['mahasiswa_id', 'status']);
            $table->index('status');
        });

        Schema::create('dokumen_keberatan', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pengajuan_id')->constrained('pengajuan_keberatan')->cascadeOnDelete();
            $table->string('path_penyimpanan');
            $table->string('nama_asli');
            $table->string('mime', 100);
            $table->unsignedBigInteger('ukuran');
            $table->foreignId('diunggah_oleh')->constrained('users')->cascadeOnDelete();
            $table->timestamps();
        });

        Schema::create('pertanyaan', function (Blueprint $table) {
            $table->id();
            $table->foreignId('mahasiswa_id')->constrained('mahasiswa')->cascadeOnDelete();
            $table->text('isi');
            $table->string('status', 30)->default('baru'); // baru | dijawab | ditutup
            $table->text('jawaban')->nullable();
            $table->foreignId('dijawab_oleh')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('dijawab_pada')->nullable();
            $table->timestamps();

            $table->index(['mahasiswa_id', 'status']);
        });

        Schema::create('notifications', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('type');
            $table->morphs('notifiable');
            $table->text('data');
            $table->timestamp('read_at')->nullable();
            $table->timestamps();

            $table->index(['notifiable_id', 'read_at']);
        });

        Schema::create('audit_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pelaku_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('aksi', 100);
            $table->string('subjek_tipe', 100);
            $table->string('subjek_id', 64)->nullable();
            $table->json('nilai_sebelum')->nullable();
            $table->json('nilai_sesudah')->nullable();
            $table->timestamp('dibuat_pada')->useCurrent();
            $table->timestamps();

            $table->index(['subjek_tipe', 'subjek_id']);
        });

        Schema::create('perkiraan_waktu', function (Blueprint $table) {
            $table->id();
            $table->string('jenis', 40); // penetapan | keberatan
            $table->string('teks_perkiraan');
            $table->string('sumber');
            $table->date('tanggal');
            $table->boolean('aktif')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('perkiraan_waktu');
        Schema::dropIfExists('audit_logs');
        Schema::dropIfExists('notifications');
        Schema::dropIfExists('pertanyaan');
        Schema::dropIfExists('dokumen_keberatan');
        Schema::dropIfExists('pengajuan_keberatan');
        Schema::dropIfExists('rincian_penilaian');
        Schema::dropIfExists('hasil_ukt');
        Schema::dropIfExists('parameter_penilaian');
        Schema::dropIfExists('versi_parameter');
        Schema::dropIfExists('faktor_penilaian');
        Schema::dropIfExists('golongan_ukt');
        Schema::dropIfExists('konten_publik');
        Schema::dropIfExists('mahasiswa');
    }
};

