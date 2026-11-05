<?php

namespace App\Http\Controllers;

use App\Models\HasilUkt;
use App\Models\Mahasiswa;
use App\Models\PerkiraanWaktu;
use App\Models\User;
use App\Services\AuditService;
use App\Services\RuleEngineService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class HasilController extends Controller
{
    public function __construct(
        private readonly RuleEngineService $ruleEngineService,
        private readonly AuditService $auditService,
    ) {}

    /**
     * Beranda mahasiswa (/beranda).
     */
    public function beranda(Request $request): View
    {
        /** @var User $user */
        $user = $request->user()->loadMissing('mahasiswa');
        $mahasiswa = $user->mahasiswa;

        $hasilTerbaru = null;
        if ($mahasiswa) {
            $hasilTerbaru = HasilUkt::with(['golongan', 'versiParameter', 'rincianPenilaian.faktor'])
                ->where('mahasiswa_id', $mahasiswa->id)
                ->latest('id')
                ->first();
        }

        $jumlahNotifikasiBelumDibaca = $user->unreadNotifications()->count();
        $notifikasiTerbaru = $user->notifications()->latest()->limit(3)->get();

        return view('mahasiswa.beranda', [
            'user' => $user,
            'mahasiswa' => $mahasiswa,
            'hasilTerbaru' => $hasilTerbaru,
            'jumlahNotifikasiBelumDibaca' => $jumlahNotifikasiBelumDibaca,
            'notifikasiTerbaru' => $notifikasiTerbaru,
        ]);
    }

    /**
     * Halaman hasil dan rincian komponen penilaian (/hasil-saya dan /hasil-saya/{hasil}) — KF-01, KF-04, KF-05.
     */
    public function index(Request $request, ?HasilUkt $hasil = null): View
    {
        /** @var User $user */
        $user = $request->user()->loadMissing('mahasiswa');
        $mahasiswaSendiri = $user->mahasiswa;

        // Periksa bila ada percobaan manipulasi parameter query (nim atau mahasiswa_id) milik orang lain
        if ($request->filled('nim') && $mahasiswaSendiri && $request->string('nim')->toString() !== $mahasiswaSendiri->nim) {
            $targetMahasiswa = Mahasiswa::where('nim', $request->string('nim')->toString())->first();
            if ($targetMahasiswa) {
                Gate::authorize('viewMahasiswa', $targetMahasiswa);
            } else {
                $this->auditService->catatPenolakanAkses(
                    pelaku: $user,
                    subjekTipe: Mahasiswa::class,
                    subjekId: $request->string('nim')->toString(),
                    alasan: 'Percobaan mengakses hasil mahasiswa dengan NIM lain melalui parameter query'
                );
                abort(403, 'Akses ditolak. Anda hanya diizinkan melihat hasil milik Anda sendiri.');
            }
        }

        if ($request->filled('mahasiswa_id') && $mahasiswaSendiri && (int) $request->input('mahasiswa_id') !== (int) $mahasiswaSendiri->id) {
            $targetMahasiswa = Mahasiswa::find((int) $request->input('mahasiswa_id'));
            if ($targetMahasiswa) {
                Gate::authorize('viewMahasiswa', $targetMahasiswa);
            } else {
                $this->auditService->catatPenolakanAkses(
                    pelaku: $user,
                    subjekTipe: Mahasiswa::class,
                    subjekId: (int) $request->input('mahasiswa_id'),
                    alasan: 'Percobaan mengakses hasil mahasiswa dengan ID lain melalui parameter query'
                );
                abort(403, 'Akses ditolak. Anda hanya diizinkan melihat hasil milik Anda sendiri.');
            }
        }

        // Bila mengakses via rute /hasil-saya/{hasil}, periksa kepemilikan melalui HasilPolicy
        if ($hasil !== null) {
            Gate::authorize('view', $hasil);
            $hasil->loadMissing(['golongan', 'versiParameter', 'rincianPenilaian.faktor']);
            $hasilAktif = $hasil;
        } else {
            // Query dibatasi ketat ke mahasiswa_id milik pengguna yang sedang login
            $hasilAktif = $mahasiswaSendiri
                ? HasilUkt::with(['golongan', 'versiParameter', 'rincianPenilaian.faktor'])
                    ->where('mahasiswa_id', $mahasiswaSendiri->id)
                    ->latest('id')
                    ->first()
                : null;

            if ($hasilAktif) {
                Gate::authorize('view', $hasilAktif);
            }
        }

        $catatanFooterSimulasi = null;
        if ($hasilAktif && $hasilAktif->isSimulasi()) {
            $catatanFooterSimulasi = $this->ruleEngineService->catatanFooterSimulasi($hasilAktif->versiParameter);
        }

        $perkiraanWaktu = PerkiraanWaktu::where('jenis', 'penetapan')
            ->where('aktif', true)
            ->latest('tanggal')
            ->first();

        return view('mahasiswa.hasil', [
            'user' => $user,
            'mahasiswa' => $mahasiswaSendiri,
            'hasil' => $hasilAktif,
            'catatanFooterSimulasi' => $catatanFooterSimulasi,
            'perkiraanWaktu' => $perkiraanWaktu,
        ]);
    }
}

