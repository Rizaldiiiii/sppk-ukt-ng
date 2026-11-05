<?php

namespace App\Http\Controllers\Petugas;

use App\Http\Controllers\Controller;
use Illuminate\View\View;

class RiwayatController extends Controller
{
    public function index(): View
    {
        return view('petugas.keberatan.stub', [
            'judulHalaman' => 'Riwayat Akses dan Perubahan Data',
            'kodeFitur' => 'KF-13',
            'tahapTarget' => 'Tahap 2 (Pendukung)',
            'ringkasanStub' => 'Antarmuka penelusuran riwayat akses dan perubahan data (KF-13) dijadwalkan pada Tahap 2. Pada Tahap 1 ini, pencatatan audit_logs di tingkat database sudah aktif untuk setiap perubahan konten, parameter, status, dan penolakan akses.',
        ]);
    }
}

