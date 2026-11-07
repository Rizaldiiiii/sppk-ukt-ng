<?php

namespace App\Http\Controllers\Petugas;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\PengajuanKeberatan;
use App\Services\ObjectionService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;
use InvalidArgumentException;

class KeberatanPetugasController extends Controller
{
    public function __construct(
        private readonly ObjectionService $objectionService,
    ) {}

    /**
     * Daftar peninjauan pengajuan keberatan dengan filter status, pencarian, dan pagination (KF-07).
     */
    public function index(Request $request): View
    {
        $statusFilter = trim((string) $request->query('status', ''));
        $kataKunci = trim((string) $request->query('q', ''));

        $query = PengajuanKeberatan::with(['mahasiswa.user', 'hasil.golongan', 'dokumen', 'pemutus'])
            ->latest('diajukan_pada')
            ->latest('id');

        if ($statusFilter !== '' && in_array($statusFilter, ObjectionService::daftarStatus(), true)) {
            $query->where('status', $statusFilter);
        }

        if ($kataKunci !== '') {
            $query->where(function ($sub) use ($kataKunci) {
                $sub->where('alasan', 'like', '%' . $kataKunci . '%')
                    ->orWhere('catatan_petugas', 'like', '%' . $kataKunci . '%')
                    ->orWhereHas('mahasiswa', function ($mQuery) use ($kataKunci) {
                        $mQuery->where('nim', 'like', '%' . $kataKunci . '%')
                            ->orWhere('program_studi', 'like', '%' . $kataKunci . '%')
                            ->orWhereHas('user', function ($uQuery) use ($kataKunci) {
                                $uQuery->where('name', 'like', '%' . $kataKunci . '%');
                            });
                    });
            });
        }

        $daftarKeberatan = $query->paginate(10)->withQueryString();

        $ringkasanStatus = PengajuanKeberatan::query()
            ->select('status', DB::raw('COUNT(*) as jumlah'))
            ->groupBy('status')
            ->pluck('jumlah', 'status')
            ->toArray();

        return view('petugas.keberatan.index', [
            'daftarKeberatan' => $daftarKeberatan,
            'daftarStatus' => ObjectionService::daftarStatus(),
            'statusFilter' => $statusFilter,
            'kataKunci' => $kataKunci,
            'ringkasanStatus' => $ringkasanStatus,
        ]);
    }

    /**
     * Detail peninjauan keberatan oleh petugas beserta dokumen bukti dan formulir ubah status (KF-07).
     */
    public function show(PengajuanKeberatan $pengajuan): View
    {
        Gate::authorize('view', $pengajuan);

        $pengajuan->load(['mahasiswa.user', 'hasil.golongan', 'hasil.rincianPenilaian.faktor', 'dokumen.pengunggah', 'pemutus']);

        $riwayatStatus = AuditLog::with('pelaku')
            ->where('subjek_tipe', PengajuanKeberatan::class)
            ->where('subjek_id', (string) $pengajuan->id)
            ->orderBy('dibuat_pada')
            ->orderBy('id')
            ->get();

        return view('petugas.keberatan.show', [
            'pengajuan' => $pengajuan,
            'riwayatStatus' => $riwayatStatus,
            'daftarStatus' => ObjectionService::daftarStatus(),
            'apakahTerminal' => ObjectionService::apakahStatusTerminal($pengajuan->status),
        ]);
    }

    /**
     * Ubah status keberatan menggunakan ObjectionService::ubahStatus yang sudah ada (KF-07 & KF-15).
     * Status terminal (Disetujui / Ditolak) ditolak bila dicoba diubah kembali melalui aksi biasa.
     */
    public function ubahStatus(Request $request, PengajuanKeberatan $pengajuan): RedirectResponse
    {
        Gate::authorize('updateStatus', $pengajuan);

        $validated = $request->validate(
            [
                'status' => ['required', 'string', 'in:Diajukan,Dalam Peninjauan,Perlu Perbaikan,Disetujui,Ditolak'],
                'catatan_petugas' => ['nullable', 'string', 'max:2000'],
            ],
            [
                'status.required' => 'Kolom Status Peninjauan wajib dipilih.',
                'status.in' => 'Kolom Status Peninjauan harus bernilai Diajukan, Dalam Peninjauan, Perlu Perbaikan, Disetujui, atau Ditolak.',
                'catatan_petugas.max' => 'Kolom Catatan Petugas maksimal 2.000 karakter.',
            ]
        );

        try {
            $this->objectionService->ubahStatus(
                pengajuan: $pengajuan,
                petugas: $request->user(),
                statusBaru: $validated['status'],
                catatanPetugas: $validated['catatan_petugas'] ?? null
            );
        } catch (InvalidArgumentException $e) {
            return back()
                ->withInput()
                ->withErrors(['status' => $e->getMessage()])
                ->with('toast_error', $e->getMessage());
        }

        return back()->with('toast_success', 'Status pengajuan keberatan diperbarui menjadi "'.$validated['status'].'" dan pemberitahuan telah diantrekan.');
    }
}


