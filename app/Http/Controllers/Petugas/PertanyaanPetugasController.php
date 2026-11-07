<?php

namespace App\Http\Controllers\Petugas;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\Pertanyaan;
use App\Services\AuditService;
use App\Services\NotificationService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class PertanyaanPetugasController extends Controller
{
    public const DAFTAR_STATUS = ['baru', 'dijawab', 'ditutup'];

    public function __construct(
        private readonly AuditService $auditService,
        private readonly NotificationService $notificationService,
    ) {}

    /**
     * Daftar pertanyaan mahasiswa untuk petugas (/petugas/pertanyaan) — KF-08.
     */
    public function index(Request $request): View
    {
        $statusFilter = trim((string) $request->query('status', ''));
        $kataKunci = trim((string) $request->query('q', ''));

        $query = Pertanyaan::with(['mahasiswa.user', 'penjawab'])
            ->latest('created_at')
            ->latest('id');

        if ($statusFilter !== '' && in_array($statusFilter, self::DAFTAR_STATUS, true)) {
            $query->where('status', $statusFilter);
        }

        if ($kataKunci !== '') {
            $query->where(function ($sub) use ($kataKunci) {
                $sub->where('isi', 'like', '%' . $kataKunci . '%')
                    ->orWhere('jawaban', 'like', '%' . $kataKunci . '%')
                    ->orWhereHas('mahasiswa', function ($mQuery) use ($kataKunci) {
                        $mQuery->where('nim', 'like', '%' . $kataKunci . '%')
                            ->orWhere('program_studi', 'like', '%' . $kataKunci . '%')
                            ->orWhereHas('user', function ($uQuery) use ($kataKunci) {
                                $uQuery->where('name', 'like', '%' . $kataKunci . '%');
                            });
                    });
            });
        }

        $daftarPertanyaan = $query->paginate(10)->withQueryString();

        $ringkasanStatus = Pertanyaan::query()
            ->select('status', DB::raw('COUNT(*) as jumlah'))
            ->groupBy('status')
            ->pluck('jumlah', 'status')
            ->toArray();

        return view('petugas.pertanyaan.index', [
            'daftarPertanyaan' => $daftarPertanyaan,
            'daftarStatus' => self::DAFTAR_STATUS,
            'statusFilter' => $statusFilter,
            'kataKunci' => $kataKunci,
            'ringkasanStatus' => $ringkasanStatus,
        ]);
    }

    /**
     * Detail pertanyaan mahasiswa & formulir jawaban petugas (/petugas/pertanyaan/{pertanyaan}) — KF-08.
     */
    public function show(Pertanyaan $pertanyaan): View
    {
        Gate::authorize('view', $pertanyaan);

        $pertanyaan->load(['mahasiswa.user', 'penjawab']);

        $riwayatAksi = AuditLog::with('pelaku')
            ->where('subjek_tipe', Pertanyaan::class)
            ->where('subjek_id', (string) $pertanyaan->id)
            ->orderBy('dibuat_pada')
            ->orderBy('id')
            ->get();

        return view('petugas.pertanyaan.show', [
            'pertanyaan' => $pertanyaan,
            'riwayatAksi' => $riwayatAksi,
        ]);
    }

    /**
     * Berikan atau perbarui jawaban atas pertanyaan mahasiswa — KF-08 & KF-15.
     * Pertanyaan yang sudah "ditutup" tidak bisa dijawab lagi kecuali dibuka kembali terlebih dahulu oleh petugas.
     */
    public function jawab(Request $request, Pertanyaan $pertanyaan): RedirectResponse
    {
        Gate::authorize('answer', $pertanyaan);

        if ($pertanyaan->status === 'ditutup') {
            $pesan = 'Pertanyaan #' . $pertanyaan->id . ' sudah ditutup dan tidak dapat dijawab lagi kecuali dibuka kembali terlebih dahulu oleh petugas.';

            return back()
                ->withInput()
                ->withErrors(['jawaban' => $pesan])
                ->with('toast_error', $pesan);
        }

        $validated = $request->validate(
            [
                'jawaban' => ['required', 'string', 'min:5', 'max:3000'],
            ],
            [
                'jawaban.required' => 'Kolom Jawaban Petugas wajib diisi sebelum dikirim kepada mahasiswa.',
                'jawaban.min' => 'Kolom Jawaban Petugas terlalu singkat (minimal 5 karakter).',
                'jawaban.max' => 'Kolom Jawaban Petugas maksimal 3.000 karakter.',
            ]
        );

        DB::transaction(function () use ($request, $pertanyaan, $validated) {
            $statusLama = $pertanyaan->status;
            $jawabanLama = $pertanyaan->jawaban;

            $updated = Pertanyaan::whereKey($pertanyaan->id)->update([
                'status' => 'dijawab',
                'jawaban' => trim($validated['jawaban']),
                'dijawab_oleh' => $request->user()->id,
                'dijawab_pada' => now(),
            ]);

            if ($updated === 0) {
                abort(404, 'Pertanyaan tidak ditemukan.');
            }

            $pertanyaan->refresh();

            $this->auditService->catat(
                pelaku: $request->user(),
                aksi: 'jawab_pertanyaan',
                subjekTipe: Pertanyaan::class,
                subjekId: $pertanyaan->id,
                nilaiSebelum: [
                    'status' => $statusLama,
                    'jawaban' => $jawabanLama,
                ],
                nilaiSesudah: [
                    'status' => 'dijawab',
                    'jawaban' => trim($validated['jawaban']),
                ]
            );
        });

        $mahasiswaUser = $pertanyaan->mahasiswa?->user;
        if ($mahasiswaUser) {
            $this->notificationService->kirimPemberitahuanStatus(
                penerima: $mahasiswaUser,
                judulPeristiwa: 'Pertanyaan #' . $pertanyaan->id . ' Telah Dijawab Petugas',
                statusBaru: 'dijawab',
                tautanHalaman: url('/pertanyaan/' . $pertanyaan->id),
                pemicu: $request->user(),
                subjekTipe: Pertanyaan::class,
                subjekId: $pertanyaan->id,
                kirimEmail: true,
            );
        }

        return back()->with('toast_success', 'Jawaban untuk pertanyaan #' . $pertanyaan->id . ' berhasil disimpan dan mahasiswa telah diberi tahu.');
    }

    /**
     * Tutup pertanyaan yang sudah selesai ditangani (/petugas/pertanyaan/{pertanyaan}/tutup) — KF-08.
     */
    public function tutup(Request $request, Pertanyaan $pertanyaan): RedirectResponse
    {
        Gate::authorize('answer', $pertanyaan);

        if ($pertanyaan->status === 'ditutup') {
            return back()->with('toast_error', 'Pertanyaan #' . $pertanyaan->id . ' sudah berstatus ditutup.');
        }

        DB::transaction(function () use ($request, $pertanyaan) {
            $statusLama = $pertanyaan->status;

            $updated = Pertanyaan::whereKey($pertanyaan->id)->update([
                'status' => 'ditutup',
            ]);

            if ($updated === 0) {
                abort(404, 'Pertanyaan tidak ditemukan.');
            }

            $pertanyaan->refresh();

            $this->auditService->catat(
                pelaku: $request->user(),
                aksi: 'tutup_pertanyaan',
                subjekTipe: Pertanyaan::class,
                subjekId: $pertanyaan->id,
                nilaiSebelum: ['status' => $statusLama],
                nilaiSesudah: ['status' => 'ditutup']
            );
        });

        $mahasiswaUser = $pertanyaan->mahasiswa?->user;
        if ($mahasiswaUser) {
            $this->notificationService->kirimPemberitahuanStatus(
                penerima: $mahasiswaUser,
                judulPeristiwa: 'Pertanyaan #' . $pertanyaan->id . ' Ditutup',
                statusBaru: 'ditutup',
                tautanHalaman: url('/pertanyaan/' . $pertanyaan->id),
                pemicu: $request->user(),
                subjekTipe: Pertanyaan::class,
                subjekId: $pertanyaan->id,
                kirimEmail: false,
            );
        }

        return back()->with('toast_success', 'Pertanyaan #' . $pertanyaan->id . ' telah ditutup.');
    }

    /**
     * Buka kembali pertanyaan yang sebelumnya ditutup (/petugas/pertanyaan/{pertanyaan}/buka-kembali) — KF-08.
     */
    public function bukaKembali(Request $request, Pertanyaan $pertanyaan): RedirectResponse
    {
        Gate::authorize('answer', $pertanyaan);

        if ($pertanyaan->status !== 'ditutup') {
            return back()->with('toast_error', 'Pertanyaan #' . $pertanyaan->id . ' tidak sedang berstatus ditutup.');
        }

        $statusBaru = $pertanyaan->jawaban ? 'dijawab' : 'baru';

        DB::transaction(function () use ($request, $pertanyaan, $statusBaru) {
            $updated = Pertanyaan::whereKey($pertanyaan->id)->update([
                'status' => $statusBaru,
            ]);

            if ($updated === 0) {
                abort(404, 'Pertanyaan tidak ditemukan.');
            }

            $pertanyaan->refresh();

            $this->auditService->catat(
                pelaku: $request->user(),
                aksi: 'buka_kembali_pertanyaan',
                subjekTipe: Pertanyaan::class,
                subjekId: $pertanyaan->id,
                nilaiSebelum: ['status' => 'ditutup'],
                nilaiSesudah: ['status' => $statusBaru]
            );
        });

        return back()->with('toast_success', 'Pertanyaan #' . $pertanyaan->id . ' berhasil dibuka kembali (status: ' . $statusBaru . ').');
    }
}


