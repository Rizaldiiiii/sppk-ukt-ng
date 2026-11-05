<?php

namespace App\Http\Controllers\Petugas;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreVersiParameterRequest;
use App\Models\VersiParameter;
use App\Services\RuleEngineService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class ParameterController extends Controller
{
    public function __construct(
        private readonly RuleEngineService $ruleEngineService,
    ) {}

    public function index(): View
    {
        Gate::authorize('manage', VersiParameter::class);

        $versiAktif = $this->ruleEngineService->versiAktif();
        $riwayatVersi = VersiParameter::with(['pembuat', 'parameterPenilaian.faktor'])
            ->withCount('hasilUkt')
            ->orderByDesc('id')
            ->paginate(10);

        return view('petugas.parameter.index', [
            'versiAktif' => $versiAktif,
            'riwayatVersi' => $riwayatVersi,
        ]);
    }

    /**
     * Buat versi parameter baru tanpa mengubah versi lama maupun hasil perhitungan lama (KF-05, KN-06).
     */
    public function store(StoreVersiParameterRequest $request): RedirectResponse
    {
        Gate::authorize('manage', VersiParameter::class);

        $versiAktif = $this->ruleEngineService->versiAktif();
        $bobotInput = $request->validated('bobot');

        $aturanPerFaktorId = [];
        if ($versiAktif) {
            foreach ($versiAktif->parameterPenilaian as $paramLama) {
                $aturanBaru = $paramLama->aturan;
                if (isset($bobotInput[$paramLama->faktor_id])) {
                    $aturanBaru['bobot'] = (float) $bobotInput[$paramLama->faktor_id];
                }
                $aturanPerFaktorId[$paramLama->faktor_id] = $aturanBaru;
            }
        }

        $versiBaru = $this->ruleEngineService->buatVersiParameterBaru(
            petugas: $request->user(),
            kodeVersi: $request->validated('versi'),
            berlakuMulai: $request->validated('berlaku_mulai'),
            catatan: $request->validated('catatan'),
            aturanPerFaktorId: $aturanPerFaktorId
        );

        return redirect()
            ->route('petugas.parameter.index')
            ->with('toast_success', 'Versi parameter baru ('.$versiBaru->versi.') berhasil diterbitkan. Hasil perhitungan lama tetap terjaga pada versinya masing-masing.');
    }
}

