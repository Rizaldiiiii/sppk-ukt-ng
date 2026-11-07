<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DokumenController;
use App\Http\Controllers\HasilController;
use App\Http\Controllers\KeberatanController;
use App\Http\Controllers\NotifikasiController;
use App\Http\Controllers\PertanyaanController;
use App\Http\Controllers\Petugas\BerandaPetugasController;
use App\Http\Controllers\Petugas\HasilPetugasController;
use App\Http\Controllers\Petugas\KeberatanPetugasController;
use App\Http\Controllers\Petugas\KontenController;
use App\Http\Controllers\Petugas\ParameterController;
use App\Http\Controllers\Petugas\PerkiraanWaktuController;
use App\Http\Controllers\Petugas\PertanyaanPetugasController;
use App\Http\Controllers\Petugas\RiwayatController;
use App\Http\Controllers\Pimpinan\RekapPimpinanController;
use App\Http\Controllers\PublicController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Rute Publik (Tanpa Login) — KF-02, KF-03, KF-06, KF-14 (+ Stub KF-10/08)
|--------------------------------------------------------------------------
*/
Route::get('/', [PublicController::class, 'beranda'])->name('publik.beranda');
Route::get('/alur', [PublicController::class, 'alur'])->name('publik.alur');
Route::get('/faktor', [PublicController::class, 'faktor'])->name('publik.faktor');
Route::get('/golongan', [PublicController::class, 'golongan'])->name('publik.golongan');
Route::get('/prosedur-keberatan', [PublicController::class, 'prosedurKeberatan'])->name('publik.prosedur-keberatan');
Route::get('/panduan', [PublicController::class, 'panduan'])->name('publik.panduan');
Route::get('/bantuan', [PublicController::class, 'bantuan'])->name('publik.bantuan');

/*
|--------------------------------------------------------------------------
| Autentikasi (KF-12)
|--------------------------------------------------------------------------
*/
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])
        ->middleware('throttle:login')
        ->name('login.submit');
});

Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    // Profil & Kata Sandi
    Route::get('/profil', [AuthController::class, 'profil'])->name('profil.index');
    Route::put('/profil/kata-sandi', [AuthController::class, 'updatePassword'])->name('profil.password');

    // Pemberitahuan di Sistem (KF-15)
    Route::get('/notifikasi', [NotifikasiController::class, 'index'])->name('notifikasi.index');
    Route::post('/notifikasi/baca-semua', [NotifikasiController::class, 'tandaiSemuaDibaca'])->name('notifikasi.baca-semua');
    Route::post('/notifikasi/{id}/baca', [NotifikasiController::class, 'tandaiDibaca'])->name('notifikasi.baca');

    // Dokumen Keberatan Privat lewat Controller Berotorisasi (Blueprint §9 & §15)
    Route::get('/dokumen-keberatan/{dokumen}', [DokumenController::class, 'unduh'])->name('dokumen.unduh');

    /*
    |--------------------------------------------------------------------------
    | Rute Mahasiswa — KF-01, KF-04, KF-05 (+ Stub KF-07, KF-08, KF-09)
    |--------------------------------------------------------------------------
    */
    Route::middleware('peran:mahasiswa')->group(function () {
        Route::get('/beranda', [HasilController::class, 'beranda'])->name('mahasiswa.beranda');
        Route::get('/hasil-saya/{hasil?}', [HasilController::class, 'index'])->name('mahasiswa.hasil');
        Route::post('/hasil-saya/{hasil?}', [HasilController::class, 'simulasiWhatIf'])->name('mahasiswa.hasil.what-if');
        Route::delete('/hasil-saya/{hasil?}/what-if', [HasilController::class, 'resetWhatIf'])->name('mahasiswa.hasil.what-if.reset');

        // Tahap 2 (KF-07, KF-08, KF-09) dengan penegakan Policy kepemilikan
        Route::get('/keberatan', [KeberatanController::class, 'index'])->name('mahasiswa.keberatan.index');
        Route::get('/keberatan/baru', [KeberatanController::class, 'create'])->name('mahasiswa.keberatan.create');
        Route::post('/keberatan', [KeberatanController::class, 'store'])
            ->middleware('throttle:20,1')
            ->name('mahasiswa.keberatan.store');
        Route::get('/keberatan/{pengajuan}', [KeberatanController::class, 'show'])->name('mahasiswa.keberatan.show');
        Route::post('/keberatan/{pengajuan}/perbaikan', [KeberatanController::class, 'perbaikan'])
            ->middleware('throttle:20,1')
            ->name('mahasiswa.keberatan.perbaikan');

        Route::get('/pertanyaan', [PertanyaanController::class, 'index'])->name('mahasiswa.pertanyaan.index');
        Route::post('/pertanyaan', [PertanyaanController::class, 'store'])
            ->middleware('throttle:20,1')
            ->name('mahasiswa.pertanyaan.store');
        Route::get('/pertanyaan/{pertanyaan}', [PertanyaanController::class, 'show'])->name('mahasiswa.pertanyaan.show');
    });

    /*
    |--------------------------------------------------------------------------
    | Rute Petugas UKT — Kelola Konten, Parameter, Hasil Resmi & Tahap 2
    |--------------------------------------------------------------------------
    */
    Route::prefix('petugas')->name('petugas.')->middleware('peran:petugas')->group(function () {
        Route::get('/beranda', [BerandaPetugasController::class, 'index'])->name('beranda');

        // Kelola Konten Publik, Faktor, dan Rentang Golongan (KF-02, KF-03, KF-06, KF-09, KF-14)
        Route::get('/konten', [KontenController::class, 'index'])->name('konten.index');
        Route::put('/konten/{slug}', [KontenController::class, 'updateKonten'])->name('konten.update');
        Route::post('/faktor', [KontenController::class, 'storeFaktor'])->name('faktor.store');
        Route::put('/faktor/{faktor}', [KontenController::class, 'updateFaktor'])->name('faktor.update');
        Route::post('/golongan', [KontenController::class, 'storeGolongan'])->name('golongan.store');
        Route::put('/golongan/{golongan}', [KontenController::class, 'updateGolongan'])->name('golongan.update');

        // Kelola Parameter Penilaian Berversi (KF-05, KN-06)
        Route::get('/parameter', [ParameterController::class, 'index'])->name('parameter.index');
        Route::post('/parameter', [ParameterController::class, 'store'])->name('parameter.store');

        // Entri / Kelola Hasil Resmi & Simulasi
        Route::get('/hasil', [HasilPetugasController::class, 'index'])->name('hasil.index');
        Route::post('/hasil/resmi', [HasilPetugasController::class, 'storeResmi'])->name('hasil.store-resmi');
        Route::post('/hasil/simulasi', [HasilPetugasController::class, 'storeSimulasi'])->name('hasil.store-simulasi');

        // Tahap 2 (KF-07, KF-08, KF-13)
        Route::get('/keberatan', [KeberatanPetugasController::class, 'index'])->name('keberatan.index');
        Route::get('/keberatan/{pengajuan}', [KeberatanPetugasController::class, 'show'])->name('keberatan.show');
        Route::patch('/keberatan/{pengajuan}/status', [KeberatanPetugasController::class, 'ubahStatus'])->name('keberatan.ubah-status');

        Route::get('/pertanyaan', [PertanyaanPetugasController::class, 'index'])->name('pertanyaan.index');
        Route::get('/pertanyaan/{pertanyaan}', [PertanyaanPetugasController::class, 'show'])->name('pertanyaan.show');
        Route::patch('/pertanyaan/{pertanyaan}/jawab', [PertanyaanPetugasController::class, 'jawab'])->name('pertanyaan.jawab');
        Route::patch('/pertanyaan/{pertanyaan}/tutup', [PertanyaanPetugasController::class, 'tutup'])->name('pertanyaan.tutup');
        Route::patch('/pertanyaan/{pertanyaan}/buka-kembali', [PertanyaanPetugasController::class, 'bukaKembali'])->name('pertanyaan.buka-kembali');

        Route::get('/riwayat', [RiwayatController::class, 'index'])->name('riwayat.index');

        // Tahap 3 (KF-11): Kelola Perkiraan Waktu Tunggu & SLA
        Route::get('/perkiraan-waktu', [PerkiraanWaktuController::class, 'index'])->name('perkiraan-waktu.index');
        Route::put('/perkiraan-waktu/{jenis}', [PerkiraanWaktuController::class, 'update'])->name('perkiraan-waktu.update');
    });

    /*
    |--------------------------------------------------------------------------
    | Rute Pimpinan / Auditor — Hanya Rekap Agregat (Bagian 3 PRD)
    |--------------------------------------------------------------------------
    */
    Route::prefix('pimpinan')->name('pimpinan.')->middleware('peran:pimpinan')->group(function () {
        Route::get('/rekap', [RekapPimpinanController::class, 'rekap'])->name('rekap');
        Route::get('/riwayat', [RekapPimpinanController::class, 'riwayat'])->name('riwayat');
    });
});
