<?php

namespace App\Http\Controllers\Pimpinan;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\GolonganUkt;
use App\Models\HasilUkt;
use App\Models\PengajuanKeberatan;
use App\Models\VersiParameter;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class RekapPimpinanController extends Controller
{
    /**
     * Halaman rekap agregat pimpinan (/pimpinan/rekap).
     * Hanya memilih hitungan agregat (COUNT / GROUP BY), tanpa daftar mahasiswa,
     * tanpa NIM, dan tanpa data ekonomi perorangan (PRD Bagian 3.3 & Blueprint §7.8).
     */
    public function rekap(): View
    {
        $rekapPerSumber = HasilUkt::query()
            ->select('sumber', DB::raw('COUNT(*) as jumlah'))
            ->groupBy('sumber')
            ->pluck('jumlah', 'sumber')
            ->toArray();

        $daftarGolongan = GolonganUkt::query()
            ->select(['id', 'kode', 'nama', 'nominal_min', 'nominal_max'])
            ->withCount('hasilUkt')
            ->orderBy('nominal_min')
            ->get();

        $rekapKeberatanPerStatus = PengajuanKeberatan::query()
            ->select('status', DB::raw('COUNT(*) as jumlah'))
            ->groupBy('status')
            ->pluck('jumlah', 'status')
            ->toArray();

        $versiAktif = VersiParameter::where('aktif', true)->latest('id')->first();
        $totalHasilTercatat = array_sum($rekapPerSumber);

        return view('pimpinan.rekap', [
            'rekapPerSumber' => $rekapPerSumber,
            'daftarGolongan' => $daftarGolongan,
            'rekapKeberatanPerStatus' => $rekapKeberatanPerStatus,
            'versiAktif' => $versiAktif,
            'totalHasilTercatat' => $totalHasilTercatat,
        ]);
    }

    /**
     * Halaman riwayat rekap pimpinan (/pimpinan/riwayat) — KF-13 ditunda ke Tahap 2 (stub).
     */
    public function riwayat(): View
    {
        $rekapAksiAudit = AuditLog::query()
            ->select('aksi', DB::raw('COUNT(*) as jumlah'))
            ->groupBy('aksi')
            ->pluck('jumlah', 'aksi')
            ->toArray();

        return view('pimpinan.riwayat-stub', [
            'rekapAksiAudit' => $rekapAksiAudit,
        ]);
    }
}

