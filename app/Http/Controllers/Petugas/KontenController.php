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
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
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
        $isiTersusun = $this->susunIsiKonten($request, $konten);

        DB::transaction(function () use ($request, $konten, $isiTersusun) {
            $sebelum = [
                'judul' => $konten->judul,
                'ringkasan' => $konten->ringkasan,
                'isi' => $konten->isi,
            ];

            $updated = KontenPublik::whereKey($konten->id)->update([
                'judul' => $request->validated('judul'),
                'ringkasan' => $request->validated('ringkasan'),
                'isi' => $isiTersusun,
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

    public function uploadVisualPanduan(Request $request): RedirectResponse
    {
        Gate::authorize('manage', KontenPublik::class);

        $request->validate(
            [
                'visual' => ['required', 'file', 'mimes:png,svg', 'max:2048'],
            ],
            [
                'visual.required' => 'Berkas visual panduan wajib dipilih.',
                'visual.file' => 'Berkas visual panduan tidak valid.',
                'visual.mimes' => 'Format berkas visual panduan harus berupa PNG atau SVG.',
                'visual.max' => 'Ukuran berkas visual panduan maksimal 2 MB (2.048 KB).',
            ]
        );

        $konten = KontenPublik::where('slug', 'panduan')->firstOrFail();
        $visualLama = $konten->visual_path;

        $file = $request->file('visual');
        $ext = strtolower($file->getClientOriginalExtension() ?: ($file->extension() ?: 'png'));
        $namaFile = Str::uuid()->toString().'.'.$ext;
        $pathBaru = $file->storeAs('panduan-visual', $namaFile, 'public');

        DB::transaction(function () use ($request, $konten, $visualLama, $pathBaru) {
            KontenPublik::whereKey($konten->id)->update([
                'visual_path' => $pathBaru,
                'diperbarui_oleh' => $request->user()->id,
                'diperbarui_pada' => now(),
            ]);

            $this->auditService->catat(
                pelaku: $request->user(),
                aksi: 'ubah_visual_panduan',
                subjekTipe: KontenPublik::class,
                subjekId: $konten->id,
                nilaiSebelum: ['visual_path' => $visualLama],
                nilaiSesudah: ['visual_path' => $pathBaru]
            );
        });

        if ($visualLama && $visualLama !== $pathBaru && Storage::disk('public')->exists($visualLama)) {
            Storage::disk('public')->delete($visualLama);
        }

        Cache::forget('konten_publik_panduan');

        return back()->with('toast_success', 'Visual panduan tahapan berhasil diunggah dan diperbarui.');
    }

    public function hapusVisualPanduan(Request $request): RedirectResponse
    {
        Gate::authorize('manage', KontenPublik::class);

        $konten = KontenPublik::where('slug', 'panduan')->firstOrFail();
        $visualLama = $konten->visual_path;

        DB::transaction(function () use ($request, $konten, $visualLama) {
            KontenPublik::whereKey($konten->id)->update([
                'visual_path' => null,
                'diperbarui_oleh' => $request->user()->id,
                'diperbarui_pada' => now(),
            ]);

            $this->auditService->catat(
                pelaku: $request->user(),
                aksi: 'ubah_visual_panduan',
                subjekTipe: KontenPublik::class,
                subjekId: $konten->id,
                nilaiSebelum: ['visual_path' => $visualLama],
                nilaiSesudah: ['visual_path' => null]
            );
        });

        if ($visualLama && Storage::disk('public')->exists($visualLama)) {
            Storage::disk('public')->delete($visualLama);
        }

        Cache::forget('konten_publik_panduan');

        return back()->with('toast_success', 'Visual panduan berhasil dikembalikan ke diagram bawaan.');
    }

    private function susunIsiKonten(UpdateKontenPublikRequest $request, KontenPublik $konten): string
    {
        $validated = $request->validated();

        if ($konten->slug === 'alur' && isset($validated['tahapan']) && is_array($validated['tahapan'])) {
            $tahapan = [];
            foreach (array_values($validated['tahapan']) as $idx => $item) {
                $tahapan[] = [
                    'nomor' => $idx + 1,
                    'judul' => trim((string) ($item['judul'] ?? '')),
                    'deskripsi' => trim((string) ($item['deskripsi'] ?? '')),
                    'hubungan_data' => trim((string) ($item['hubungan_data'] ?? '')),
                ];
            }

            return (string) json_encode(['tahapan' => $tahapan], JSON_UNESCAPED_UNICODE);
        }

        if (
            $konten->slug === 'prosedur-keberatan'
            && isset($validated['tujuan_pengajuan'], $validated['siapa_boleh_mengajukan'], $validated['dokumen_diperlukan'], $validated['tahapan_peninjauan'])
        ) {
            return (string) json_encode([
                'siapa_boleh_mengajukan' => array_values(array_map(static fn ($v) => trim((string) $v), $validated['siapa_boleh_mengajukan'])),
                'dokumen_diperlukan' => array_values(array_map(static fn ($v) => trim((string) $v), $validated['dokumen_diperlukan'])),
                'tujuan_pengajuan' => trim((string) $validated['tujuan_pengajuan']),
                'tahapan_peninjauan' => array_values(array_map(static fn ($v) => trim((string) $v), $validated['tahapan_peninjauan'])),
            ], JSON_UNESCAPED_UNICODE);
        }

        if ($konten->slug === 'petunjuk-pengisian' && isset($validated['petunjuk']) && is_array($validated['petunjuk'])) {
            $defaults = KontenPublik::defaultPetunjukPengisian();
            $existing = json_decode((string) $konten->isi, true);
            $tersusun = [];

            foreach ($defaults as $key => $defaultKolom) {
                $sumberAwal = (is_array($existing) && isset($existing[$key]) && is_array($existing[$key]))
                    ? array_merge($defaultKolom, array_intersect_key($existing[$key], $defaultKolom))
                    : $defaultKolom;

                if (isset($validated['petunjuk'][$key]) && is_array($validated['petunjuk'][$key])) {
                    $inputKolom = $validated['petunjuk'][$key];
                    $tersusun[$key] = [
                        'judul' => trim((string) ($inputKolom['judul'] ?? $sumberAwal['judul'])),
                        'apa_diisi' => trim((string) ($inputKolom['apa_diisi'] ?? $sumberAwal['apa_diisi'])),
                        'format' => trim((string) ($inputKolom['format'] ?? $sumberAwal['format'])),
                        'contoh' => trim((string) ($inputKolom['contoh'] ?? $sumberAwal['contoh'])),
                        'akibat' => trim((string) ($inputKolom['akibat'] ?? $sumberAwal['akibat'])),
                    ];
                } else {
                    $tersusun[$key] = $sumberAwal;
                }
            }

            return (string) json_encode($tersusun, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
        }

        return (string) ($validated['isi'] ?? $konten->isi);
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
                nilaiSesudah: $faktor->only(['kode', 'nama', 'urutan', 'aktif'])
            );
        });

        return back()->with('toast_success', 'Faktor penilaian baru berhasil ditambahkan.');
    }

    public function updateFaktor(StoreFaktorPenilaianRequest $request, FaktorPenilaian $faktor): RedirectResponse
    {
        Gate::authorize('manage', KontenPublik::class);

        DB::transaction(function () use ($request, $faktor) {
            $sebelum = $faktor->only(['nama', 'penjelasan_singkat', 'pengaruh_umum', 'urutan', 'aktif']);
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
                nilaiSesudah: $faktor->only(['nama', 'penjelasan_singkat', 'pengaruh_umum', 'urutan', 'aktif'])
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

