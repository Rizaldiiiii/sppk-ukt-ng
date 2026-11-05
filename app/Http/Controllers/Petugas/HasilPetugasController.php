<?php

namespace App\Http\Controllers\Petugas;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreHasilResmiRequest;
use App\Models\FaktorPenilaian;
use App\Models\GolonganUkt;
use App\Models\HasilUkt;
use App\Models\Mahasiswa;
use App\Services\RuleEngineService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class HasilPetugasController extends Controller
{
    public function __construct(
        private readonly RuleEngineService $ruleEngineService,
    ) {}

    public function index(): View
    {
        Gate::authorize('manage', HasilUkt::class);

        $daftarHasil = HasilUkt::with(['mahasiswa.user', 'golongan', 'versiParameter', 'penetap'])
            ->latest('id')
            ->paginate(10);

        $daftarMahasiswa = Mahasiswa::with('user')->orderBy('nim')->get();
        $daftarGolongan = GolonganUkt::where('aktif', true)->orderBy('nominal_min')->get();
        $faktorAktif = FaktorPenilaian::where('aktif', true)->orderBy('urutan')->get();
        $versiAktif = $this->ruleEngineService->versiAktif();

        return view('petugas.hasil.index', [
            'daftarHasil' => $daftarHasil,
            'daftarMahasiswa' => $daftarMahasiswa,
            'daftarGolongan' => $daftarGolongan,
            'faktorAktif' => $faktorAktif,
            'versiAktif' => $versiAktif,
        ]);
    }

    /**
     * Entri hasil penetapan resmi kampus beserta rinciannya (Blueprint §4.2, §5.3, KF-15).
     */
    public function storeResmi(StoreHasilResmiRequest $request): RedirectResponse
    {
        Gate::authorize('manage', HasilUkt::class);

        $mahasiswa = Mahasiswa::with('user')->findOrFail($request->validated('mahasiswa_id'));
        $golongan = GolonganUkt::findOrFail($request->validated('golongan_id'));

        $this->ruleEngineService->simpanHasil(
            mahasiswa: $mahasiswa,
            masukan: $request->validated('masukan'),
            sumber: 'resmi',
            versiParameter: null,
            golonganResmi: $golongan,
            nominalResmi: (int) $request->validated('nominal'),
            tanggalPenetapan: $request->validated('tanggal_penetapan'),
            sumberPenetapan: $request->validated('sumber_penetapan'),
            ditetapkanOleh: $request->user(),
            catatan: $request->validated('catatan'),
            kirimNotifikasi: true
        );

        return redirect()
            ->route('petugas.hasil.index')
            ->with('toast_success', 'Hasil penetapan resmi untuk '.$mahasiswa->user->nama.' ('.$mahasiswa->nim.') berhasil disimpan dan pemberitahuan telah diantrekan.');
    }

    /**
     * Hitung simulasi baru bagi mahasiswa dengan versi parameter aktif saat ini.
     */
    public function storeSimulasi(Request $request): RedirectResponse
    {
        Gate::authorize('manage', HasilUkt::class);

        $validated = $request->validate([
            'mahasiswa_id' => ['required', 'integer', 'exists:mahasiswa,id'],
            'masukan' => ['required', 'array'],
            'catatan' => ['nullable', 'string', 'max:1000'],
        ]);

        $mahasiswa = Mahasiswa::with('user')->findOrFail($validated['mahasiswa_id']);

        $this->ruleEngineService->simpanHasil(
            mahasiswa: $mahasiswa,
            masukan: $validated['masukan'],
            sumber: 'simulasi',
            versiParameter: null,
            ditetapkanOleh: $request->user(),
            catatan: $validated['catatan'] ?? 'Perhitungan simulasi menggunakan versi parameter aktif',
            kirimNotifikasi: true
        );

        return redirect()
            ->route('petugas.hasil.index')
            ->with('toast_success', 'Hasil simulasi baru untuk '.$mahasiswa->user->nama.' berhasil dihitung dengan versi parameter aktif.');
    }
}

