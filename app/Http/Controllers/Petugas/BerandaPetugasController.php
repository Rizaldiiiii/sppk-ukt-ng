<?php

namespace App\Http\Controllers\Petugas;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\FaktorPenilaian;
use App\Models\GolonganUkt;
use App\Models\HasilUkt;
use App\Models\Mahasiswa;
use App\Services\RuleEngineService;
use Illuminate\View\View;

class BerandaPetugasController extends Controller
{
    public function __construct(
        private readonly RuleEngineService $ruleEngineService,
    ) {}

    public function index(): View
    {
        $versiAktif = $this->ruleEngineService->versiAktif();
        $totalMahasiswa = Mahasiswa::count();
        $totalHasilResmi = HasilUkt::where('sumber', 'resmi')->count();
        $totalHasilSimulasi = HasilUkt::where('sumber', 'simulasi')->count();
        $totalFaktorAktif = FaktorPenilaian::where('aktif', true)->count();
        $totalGolonganAktif = GolonganUkt::where('aktif', true)->count();
        $kegagalanEmail = AuditLog::where('aksi', 'email_notifikasi_gagal')
            ->latest('id')
            ->limit(5)
            ->get();

        return view('petugas.beranda', [
            'versiAktif' => $versiAktif,
            'totalMahasiswa' => $totalMahasiswa,
            'totalHasilResmi' => $totalHasilResmi,
            'totalHasilSimulasi' => $totalHasilSimulasi,
            'totalFaktorAktif' => $totalFaktorAktif,
            'totalGolonganAktif' => $totalGolonganAktif,
            'kegagalanEmail' => $kegagalanEmail,
        ]);
    }
}

