<?php

namespace App\Http\Controllers\Petugas;

use App\Http\Controllers\Controller;
use App\Models\PengajuanKeberatan;
use App\Services\ObjectionService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class KeberatanPetugasController extends Controller
{
    public function __construct(
        private readonly ObjectionService $objectionService,
    ) {}

    public function index(): View
    {
        return view('petugas.keberatan.stub', [
            'judulHalaman' => 'Daftar Peninjauan Keberatan UKT',
            'kodeFitur' => 'KF-07',
            'tahapTarget' => 'Tahap 2 (Pendukung)',
            'ringkasanStub' => 'Antarmuka antrian peninjauan pengajuan keberatan daring oleh petugas dijadwalkan pada Tahap 2.',
        ]);
    }

    public function show(PengajuanKeberatan $pengajuan): View
    {
        Gate::authorize('view', $pengajuan);

        return view('petugas.keberatan.stub', [
            'judulHalaman' => 'Detail Peninjauan Keberatan #'.$pengajuan->id,
            'kodeFitur' => 'KF-07',
            'tahapTarget' => 'Tahap 2 (Pendukung)',
            'ringkasanStub' => 'Halaman detail peninjauan pengajuan keberatan dan pemeriksaan berkas dijadwalkan pada Tahap 2.',
            'pengajuan' => $pengajuan,
        ]);
    }

    public function ubahStatus(Request $request, PengajuanKeberatan $pengajuan): RedirectResponse
    {
        Gate::authorize('updateStatus', $pengajuan);

        $validated = $request->validate([
            'status' => ['required', 'string', 'in:Diajukan,Dalam Peninjauan,Perlu Perbaikan,Disetujui,Ditolak'],
            'catatan_petugas' => ['nullable', 'string', 'max:2000'],
        ]);

        $this->objectionService->ubahStatus(
            pengajuan: $pengajuan,
            petugas: $request->user(),
            statusBaru: $validated['status'],
            catatanPetugas: $validated['catatan_petugas'] ?? null
        );

        return back()->with('toast_success', 'Status pengajuan keberatan diperbarui menjadi "'.$validated['status'].'" dan pemberitahuan telah diantrekan.');
    }
}

