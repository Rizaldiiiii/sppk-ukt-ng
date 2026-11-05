<?php

namespace App\Http\Controllers;

use App\Models\PengajuanKeberatan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class KeberatanController extends Controller
{
    /**
     * Daftar keberatan saya (/keberatan) — KF-07 ditunda ke Tahap 2 (stub).
     */
    public function index(Request $request): View
    {
        Gate::authorize('create', PengajuanKeberatan::class);

        return view('mahasiswa.keberatan.stub', [
            'judulHalaman' => 'Pengajuan Keberatan UKT',
            'kodeFitur' => 'KF-07',
            'tahapTarget' => 'Tahap 2 (Pendukung)',
            'ringkasanStub' => 'Fasilitas pengajuan keberatan secara daring dan pemantauan status pengajuan dijadwalkan pada pembangunan Tahap 2. Pada Tahap 1 ini, Anda sudah dapat mempelajari syarat, dokumen pendukung, dan alur peninjauan ulang secara lengkap.',
        ]);
    }

    /**
     * Formulir keberatan baru (/keberatan/baru) — KF-07 & KF-09 ditunda ke Tahap 2 (stub).
     */
    public function create(Request $request): View
    {
        Gate::authorize('create', PengajuanKeberatan::class);

        return view('mahasiswa.keberatan.stub', [
            'judulHalaman' => 'Formulir Pengajuan Keberatan',
            'kodeFitur' => 'KF-07 & KF-09',
            'tahapTarget' => 'Tahap 2 (Pendukung)',
            'ringkasanStub' => 'Formulir pengiriman pengajuan keberatan daring beserta petunjuk pengisian per kolom (KF-09) belum tersedia di Tahap 1 dan akan dibuka pada Tahap 2.',
        ]);
    }

    /**
     * Detail keberatan (/keberatan/{pengajuan}) — memeriksa kepemilikan melalui KeberatanPolicy.
     */
    public function show(Request $request, PengajuanKeberatan $pengajuan): View
    {
        Gate::authorize('view', $pengajuan);

        return view('mahasiswa.keberatan.stub', [
            'judulHalaman' => 'Detail Pengajuan Keberatan #'.$pengajuan->id,
            'kodeFitur' => 'KF-07',
            'tahapTarget' => 'Tahap 2 (Pendukung)',
            'ringkasanStub' => 'Halaman pemantauan detail dan tindak lanjut perbaikan keberatan daring akan diaktifkan penuh pada Tahap 2.',
            'pengajuan' => $pengajuan,
        ]);
    }
}

