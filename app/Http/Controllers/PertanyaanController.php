<?php

namespace App\Http\Controllers;

use App\Models\KontenPublik;
use App\Models\Pertanyaan;
use App\Models\User;
use App\Services\AuditService;
use App\Services\NotificationService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Str;
use Illuminate\View\View;

class PertanyaanController extends Controller
{
    public function __construct(
        private readonly AuditService $auditService,
        private readonly NotificationService $notificationService,
    ) {}

    /**
     * Halaman pengiriman pertanyaan dan daftar pertanyaan milik mahasiswa (/pertanyaan) — KF-08 & KF-09.
     */
    public function index(Request $request): View
    {
        $mahasiswa = $request->user()->mahasiswa;

        $daftarPertanyaan = Pertanyaan::with('penjawab')
            ->where('mahasiswa_id', $mahasiswa->id)
            ->latest('created_at')
            ->latest('id')
            ->paginate(10);

        $kontenKontak = KontenPublik::where('slug', 'kontak')->first();
        $petunjuk = KontenPublik::petunjukPengisian();

        $formToken = (string) Str::uuid();
        $request->session()->put('pertanyaan_form_token', $formToken);

        return view('mahasiswa.pertanyaan.index', [
            'mahasiswa' => $mahasiswa,
            'daftarPertanyaan' => $daftarPertanyaan,
            'kontenKontak' => $kontenKontak,
            'petunjuk' => $petunjuk,
            'formToken' => $formToken,
        ]);
    }

    /**
     * Simpan pertanyaan baru dari mahasiswa (/pertanyaan) — KF-08 & KF-09.
     */
    public function store(Request $request): RedirectResponse
    {
        $mahasiswa = $request->user()->mahasiswa;

        $validated = $request->validate(
            [
                'isi' => ['required', 'string', 'min:10', 'max:2000'],
            ],
            [
                'isi.required' => 'Kolom Isi Pertanyaan wajib diisi. Tuliskan pertanyaan spesifik Anda mengenai komponen penilaian atau prosedur UKT.',
                'isi.min' => 'Kolom Isi Pertanyaan terlalu singkat (minimal 10 karakter). Jelaskan pertanyaan Anda dengan kalimat yang jelas agar petugas dapat memberikan jawaban yang tepat.',
                'isi.max' => 'Kolom Isi Pertanyaan melebihi batas maksimal 2.000 karakter. Ringkas pertanyaan Anda pada pokok permasalahan utama.',
            ]
        );

        // Pencegahan kirim ganda / klik ganda
        $submittedToken = $request->input('form_token');
        $sessionToken = $request->session()->pull('pertanyaan_form_token');

        $duplikatBaruSaja = Pertanyaan::where('mahasiswa_id', $mahasiswa->id)
            ->where('isi', trim($validated['isi']))
            ->where('created_at', '>=', now()->subSeconds(15))
            ->latest('id')
            ->first();

        if ($duplikatBaruSaja || ($submittedToken !== null && $sessionToken !== null && ! hash_equals((string) $sessionToken, (string) $submittedToken))) {
            $target = $duplikatBaruSaja ?? Pertanyaan::where('mahasiswa_id', $mahasiswa->id)->latest('id')->first();
            if ($target) {
                return redirect()
                    ->route('mahasiswa.pertanyaan.show', $target)
                    ->with('toast_success', 'Pertanyaan Anda telah tercatat dengan Nomor Tanda Terima #' . $target->id . ' (pengiriman ganda berhasil dicegah).');
            }
        }

        $pertanyaan = DB::transaction(function () use ($request, $mahasiswa, $validated) {
            $pertanyaan = Pertanyaan::create([
                'mahasiswa_id' => $mahasiswa->id,
                'isi' => trim($validated['isi']),
                'status' => 'baru',
                'jawaban' => null,
                'dijawab_oleh' => null,
                'dijawab_pada' => null,
            ]);

            $this->auditService->catat(
                pelaku: $request->user(),
                aksi: 'buat_pertanyaan',
                subjekTipe: Pertanyaan::class,
                subjekId: $pertanyaan->id,
                nilaiSebelum: null,
                nilaiSesudah: [
                    'mahasiswa_id' => $mahasiswa->id,
                    'status' => 'baru',
                ]
            );

            return $pertanyaan;
        });

        // Tanda terima di sistem untuk mahasiswa
        $this->notificationService->kirimPemberitahuanStatus(
            penerima: $request->user(),
            judulPeristiwa: 'Tanda Terima Pertanyaan #' . $pertanyaan->id,
            statusBaru: 'baru',
            tautanHalaman: url('/pertanyaan/' . $pertanyaan->id),
            pemicu: $request->user(),
            subjekTipe: Pertanyaan::class,
            subjekId: $pertanyaan->id,
            kirimEmail: false,
        );

        // Beri tahu daftar kerja petugas
        $daftarPetugas = User::where('peran', 'petugas')->get();
        foreach ($daftarPetugas as $petugas) {
            $this->notificationService->kirimPemberitahuanStatus(
                penerima: $petugas,
                judulPeristiwa: 'Pertanyaan Mahasiswa Baru #' . $pertanyaan->id . ' (' . $mahasiswa->nim . ')',
                statusBaru: 'baru',
                tautanHalaman: url('/petugas/pertanyaan/' . $pertanyaan->id),
                pemicu: $request->user(),
                subjekTipe: Pertanyaan::class,
                subjekId: $pertanyaan->id,
                kirimEmail: false,
            );
        }

        return redirect()
            ->route('mahasiswa.pertanyaan.show', $pertanyaan)
            ->with(
                'toast_success',
                'Pertanyaan berhasil dikirim dengan Nomor Tanda Terima #' . $pertanyaan->id . ' (Status: Baru). Anda akan menerima pemberitahuan saat petugas menjawab.'
            );
    }

    /**
     * Detail pertanyaan milik mahasiswa (/pertanyaan/{pertanyaan}) — memeriksa kepemilikan melalui PertanyaanPolicy.
     */
    public function show(Request $request, Pertanyaan $pertanyaan): View
    {
        Gate::authorize('view', $pertanyaan);

        $pertanyaan->load(['mahasiswa.user', 'penjawab']);

        return view('mahasiswa.pertanyaan.show', [
            'pertanyaan' => $pertanyaan,
        ]);
    }
}


