<?php

namespace App\Http\Controllers\Petugas;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreFaktorPenilaianRequest;
use App\Http\Requests\StoreGolonganUktRequest;
use App\Http\Requests\UpdateKontenPublikRequest;
use App\Models\FaktorPenilaian;
use App\Models\GolonganUkt;
use App\Models\KontenPublik;
use App\Services\AuditService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class KontenController extends Controller
{
    public function __construct(
        private readonly AuditService $auditService,
    ) {}

    public function index(): View
    {
        Gate::authorize('manage', KontenPublik::class);

        $daftarKonten = KontenPublik::orderBy('id')->get();
        $daftarFaktor = FaktorPenilaian::orderBy('urutan')->orderBy('id')->paginate(10, ['*'], 'faktor_page');
        $daftarGolongan = GolonganUkt::orderBy('nominal_min')->orderBy('id')->paginate(10, ['*'], 'golongan_page');

        return view('petugas.konten.index', [
            'daftarKonten' => $daftarKonten,
            'daftarFaktor' => $daftarFaktor,
            'daftarGolongan' => $daftarGolongan,
        ]);
    }

    public function updateKonten(UpdateKontenPublikRequest $request, string $slug): RedirectResponse
    {
        Gate::authorize('manage', KontenPublik::class);

        $konten = KontenPublik::where('slug', $slug)->firstOrFail();

        DB::transaction(function () use ($request, $konten) {
            $sebelum = [
                'judul' => $konten->judul,
                'ringkasan' => $konten->ringkasan,
                'isi' => $konten->isi,
            ];

            $updated = KontenPublik::whereKey($konten->id)->update([
                'judul' => $request->validated('judul'),
                'ringkasan' => $request->validated('ringkasan'),
                'isi' => $request->validated('isi'),
                'diperbarui_oleh' => $request->user()->id,
                'diperbarui_pada' => now(),
            ]);

            if ($updated === 0) {
                abort(404, 'Konten publik tidak ditemukan.');
            }

            $this->auditService->catat(
                pelaku: $request->user(),
                aksi: 'ubah_konten_publik',
                subjekTipe: KontenPublik::class,
                subjekId: $konten->id,
                nilaiSebelum: $sebelum,
                nilaiSesudah: [
                    'slug' => $konten->slug,
                    'judul' => $request->validated('judul'),
                    'ringkasan' => $request->validated('ringkasan'),
                ]
            );
        });

        Cache::forget('konten_publik_'.$slug);

        return back()->with('toast_success', 'Konten publik "'.$konten->judul.'" berhasil diperbarui.');
    }

    public function storeFaktor(StoreFaktorPenilaianRequest $request): RedirectResponse
    {
        Gate::authorize('manage', KontenPublik::class);

        DB::transaction(function () use ($request) {
            $data = $request->validated();
            $data['aktif'] = $request->boolean('aktif', true);

            $faktor = FaktorPenilaian::create($data);

            $this->auditService->catat(
                pelaku: $request->user(),
                aksi: 'tambah_faktor_penilaian',
                subjekTipe: FaktorPenilaian::class,
                subjekId: $faktor->id,
                nilaiSebelum: null,
                nilaiSesudah: $faktor->only(['kode', 'nama', 'status_konfirmasi', 'urutan', 'aktif'])
            );
        });

        return back()->with('toast_success', 'Faktor penilaian baru berhasil ditambahkan.');
    }

    public function updateFaktor(StoreFaktorPenilaianRequest $request, FaktorPenilaian $faktor): RedirectResponse
    {
        Gate::authorize('manage', KontenPublik::class);

        DB::transaction(function () use ($request, $faktor) {
            $sebelum = $faktor->only(['nama', 'penjelasan_singkat', 'pengaruh_umum', 'status_konfirmasi', 'urutan', 'aktif']);
            $data = $request->validated();
            $data['aktif'] = $request->boolean('aktif', true);

            $updated = FaktorPenilaian::whereKey($faktor->id)->update($data);

            if ($updated === 0) {
                abort(404, 'Faktor penilaian tidak ditemukan.');
            }

            $faktor->refresh();

            $this->auditService->catat(
                pelaku: $request->user(),
                aksi: 'ubah_faktor_penilaian',
                subjekTipe: FaktorPenilaian::class,
                subjekId: $faktor->id,
                nilaiSebelum: $sebelum,
                nilaiSesudah: $faktor->only(['nama', 'penjelasan_singkat', 'pengaruh_umum', 'status_konfirmasi', 'urutan', 'aktif'])
            );
        });

        return back()->with('toast_success', 'Faktor penilaian "'.$faktor->nama.'" berhasil diperbarui.');
    }

    public function storeGolongan(StoreGolonganUktRequest $request): RedirectResponse
    {
        Gate::authorize('manage', KontenPublik::class);

        DB::transaction(function () use ($request) {
            $data = $request->validated();
            $data['aktif'] = $request->boolean('aktif', true);

            $golongan = GolonganUkt::create($data);

            $this->auditService->catat(
                pelaku: $request->user(),
                aksi: 'tambah_golongan_ukt',
                subjekTipe: GolonganUkt::class,
                subjekId: $golongan->id,
                nilaiSebelum: null,
                nilaiSesudah: $golongan->only(['kode', 'nama', 'nominal_min', 'nominal_max', 'sumber', 'berlaku_mulai'])
            );
        });

        return back()->with('toast_success', 'Rentang golongan UKT berhasil ditambahkan.');
    }

    public function updateGolongan(StoreGolonganUktRequest $request, GolonganUkt $golongan): RedirectResponse
    {
        Gate::authorize('manage', KontenPublik::class);

        DB::transaction(function () use ($request, $golongan) {
            $sebelum = $golongan->only(['nama', 'nominal_min', 'nominal_max', 'sumber', 'berlaku_mulai', 'berlaku_sampai', 'aktif']);
            $data = $request->validated();
            $data['aktif'] = $request->boolean('aktif', true);

            $updated = GolonganUkt::whereKey($golongan->id)->update($data);

            if ($updated === 0) {
                abort(404, 'Golongan UKT tidak ditemukan.');
            }

            $golongan->refresh();

            $this->auditService->catat(
                pelaku: $request->user(),
                aksi: 'ubah_golongan_ukt',
                subjekTipe: GolonganUkt::class,
                subjekId: $golongan->id,
                nilaiSebelum: $sebelum,
                nilaiSesudah: $golongan->only(['nama', 'nominal_min', 'nominal_max', 'sumber', 'berlaku_mulai', 'berlaku_sampai', 'aktif'])
            );
        });

        return back()->with('toast_success', 'Rentang golongan "'.$golongan->nama.'" berhasil diperbarui.');
    }
}

