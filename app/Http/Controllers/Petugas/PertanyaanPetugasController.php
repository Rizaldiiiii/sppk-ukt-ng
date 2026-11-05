<?php

namespace App\Http\Controllers\Petugas;

use App\Http\Controllers\Controller;
use Illuminate\View\View;

class PertanyaanPetugasController extends Controller
{
    public function index(): View
    {
        return view('petugas.keberatan.stub', [
            'judulHalaman' => 'Daftar Pertanyaan Mahasiswa',
            'kodeFitur' => 'KF-08',
            'tahapTarget' => 'Tahap 2 (Pendukung)',
            'ringkasanStub' => 'Pengelolaan dan pemberian jawaban atas pertanyaan mahasiswa dari dalam sistem dijadwalkan pada Tahap 2.',
        ]);
    }
}

