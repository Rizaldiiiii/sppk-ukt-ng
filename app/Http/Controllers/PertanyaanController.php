<?php

namespace App\Http\Controllers;

use App\Models\Pertanyaan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class PertanyaanController extends Controller
{
    /**
     * Halaman pertanyaan saya (/pertanyaan) — KF-08 ditunda ke Tahap 2 (stub).
     */
    public function index(Request $request): View
    {
        return view('mahasiswa.pertanyaan.stub', [
            'judulHalaman' => 'Pertanyaan kepada Petugas UKT',
            'kodeFitur' => 'KF-08',
            'tahapTarget' => 'Tahap 2 (Pendukung)',
            'ringkasanStub' => 'Fasilitas pengiriman pesan pertanyaan dari dalam sistem dan pelacakan jawaban petugas dijadwalkan pada Tahap 2. Untuk saat ini, silakan melihat informasi kanal layanan resmi di halaman Bantuan.',
        ]);
    }

    /**
     * Detail pertanyaan (/pertanyaan/{pertanyaan}) — memeriksa kepemilikan melalui PertanyaanPolicy.
     */
    public function show(Request $request, Pertanyaan $pertanyaan): View
    {
        Gate::authorize('view', $pertanyaan);

        return view('mahasiswa.pertanyaan.stub', [
            'judulHalaman' => 'Detail Pertanyaan #'.$pertanyaan->id,
            'kodeFitur' => 'KF-08',
            'tahapTarget' => 'Tahap 2 (Pendukung)',
            'ringkasanStub' => 'Detail percakapan pertanyaan dengan petugas UKT akan tersedia pada Tahap 2.',
        ]);
    }
}

