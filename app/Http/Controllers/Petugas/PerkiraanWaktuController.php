<?php

namespace App\Http\Controllers\Petugas;

use App\Http\Controllers\Controller;
use App\Models\PerkiraanWaktu;
use App\Services\AuditService;
use App\Services\SlaService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class PerkiraanWaktuController extends Controller
{
    public function __construct(
        private readonly SlaService $slaService,
        private readonly AuditService $auditService,
    ) {}

    /**
     * Halaman kelola perkiraan waktu tunggu hasil & tenggat SLA keberatan (/petugas/perkiraan-waktu) — KF-11.
     */
    public function index(): View
    {
        $perkiraanPenetapan = PerkiraanWaktu::where('jenis', 'penetapan')
            ->where('aktif', true)
            ->latest('id')
            ->first();

        $perkiraanKeberatan = PerkiraanWaktu::where('jenis', 'keberatan')
            ->where('aktif', true)
            ->latest('id')
            ->first();

        $hariPenetapan = $this->slaService->ekstrakHariKerjaDariPerkiraan($perkiraanPenetapan);
        $hariKeberatan = $this->slaService->ekstrakHariKerjaDariPerkiraan($perkiraanKeberatan);

        $riwayatPerkiraan = PerkiraanWaktu::orderByDesc('id')->limit(10)->get();

        return view('petugas.perkiraan-waktu.index', [
            'perkiraanPenetapan' => $perkiraanPenetapan,
            'perkiraanKeberatan' => $perkiraanKeberatan,
            'pilihanPenetapanAktif' => $hariPenetapan !== null ? (string) $hariPenetapan : 'belum_ditetapkan',
            'pilihanKeberatanAktif' => $hariKeberatan !== null ? (string) $hariKeberatan : 'belum_ditetapkan',
            'daftarPilihanSla' => SlaService::PILIHAN_SLA,
            'riwayatPerkiraan' => $riwayatPerkiraan,
        ]);
    }

    /**
     * Perbarui ketetapan perkiraan waktu tunggu berdasarkan pilihan dropdown resmi (KF-11).
     * Hanya satu baris aktif per jenis; teks_perkiraan, sumber, dan tanggal dibentuk otomatis.
     */
    public function update(Request $request, string $jenis): RedirectResponse
    {
        if (! in_array($jenis, ['penetapan', 'keberatan'], true)) {
            abort(404, 'Jenis perkiraan waktu tidak dikenal.');
        }

        $validated = $request->validate(
            [
                'pilihan_sla' => ['required', 'string', Rule::in(array_keys(SlaService::PILIHAN_SLA))],
            ],
            [
                'pilihan_sla.required' => 'Pilihan perkiraan waktu tunggu wajib dipilih dari daftar.',
                'pilihan_sla.in' => 'Pilihan perkiraan waktu harus salah satu dari opsi resmi (Belum ditetapkan, 3, 5, 7, atau 14 hari kerja).',
            ]
        );

        $pilihan = $validated['pilihan_sla'];
        $user = $request->user();

        DB::transaction(function () use ($jenis, $pilihan, $user) {
            $sebelumAktif = PerkiraanWaktu::where('jenis', $jenis)
                ->where('aktif', true)
                ->latest('id')
                ->first();

            $nilaiSebelum = $sebelumAktif ? [
                'id' => $sebelumAktif->id,
                'jenis' => $sebelumAktif->jenis,
                'teks_perkiraan' => $sebelumAktif->teks_perkiraan,
                'sumber' => $sebelumAktif->sumber,
                'tanggal' => $sebelumAktif->tanggal?->toDateString(),
                'aktif' => true,
            ] : [
                'jenis' => $jenis,
                'status' => 'Belum ditetapkan',
            ];

            // Nonaktifkan seluruh baris aktif sebelumnya untuk jenis ini (hanya 1 baris aktif per jenis)
            PerkiraanWaktu::where('jenis', $jenis)
                ->where('aktif', true)
                ->update(['aktif' => false]);

            if ($pilihan === 'belum_ditetapkan') {
                $this->auditService->catat(
                    pelaku: $user,
                    aksi: 'ubah_perkiraan_waktu',
                    subjekTipe: PerkiraanWaktu::class,
                    subjekId: $jenis,
                    nilaiSebelum: $nilaiSebelum,
                    nilaiSesudah: [
                        'jenis' => $jenis,
                        'pilihan_sla' => 'Belum ditetapkan',
                        'aktif' => false,
                    ]
                );

                return;
            }

            $hariKerja = (int) $pilihan;
            $dataOtomatis = $this->slaService->bentukDataPerkiraan($jenis, $hariKerja, $user);

            $baru = PerkiraanWaktu::create([
                'jenis' => $jenis,
                'teks_perkiraan' => $dataOtomatis['teks_perkiraan'],
                'sumber' => $dataOtomatis['sumber'],
                'tanggal' => $dataOtomatis['tanggal'],
                'aktif' => true,
            ]);

            $this->auditService->catat(
                pelaku: $user,
                aksi: 'ubah_perkiraan_waktu',
                subjekTipe: PerkiraanWaktu::class,
                subjekId: $baru->id,
                nilaiSebelum: $nilaiSebelum,
                nilaiSesudah: [
                    'id' => $baru->id,
                    'jenis' => $baru->jenis,
                    'pilihan_sla' => $hariKerja . ' hari kerja',
                    'teks_perkiraan' => $baru->teks_perkiraan,
                    'sumber' => $baru->sumber,
                    'tanggal' => $baru->tanggal?->toDateString(),
                    'aktif' => true,
                ]
            );
        });

        $labelJenis = $jenis === 'penetapan' ? 'Penetapan Hasil UKT' : 'Peninjauan Keberatan UKT';
        $labelPilihan = SlaService::PILIHAN_SLA[$pilihan] ?? $pilihan;

        return back()->with(
            'toast_success',
            "Perkiraan waktu tunggu {$labelJenis} berhasil diperbarui menjadi \"{$labelPilihan}\". Pengajuan yang sudah berjalan tetap menggunakan tenggat awalnya."
        );
    }
}

