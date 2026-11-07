<?php

namespace App\Http\Controllers\Pimpinan;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\GolonganUkt;
use App\Models\HasilUkt;
use App\Models\PengajuanKeberatan;
use App\Models\VersiParameter;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class RekapPimpinanController extends Controller
{
    /**
     * Kunci data pribadi / ekonomi perorangan yang wajib disamarkan pada tampilan audit Pimpinan
     * sesuai batas kewenangan Pimpinan (PRD Bagian 3.3 & Blueprint §7.7–7.8).
     */
    private const KUNCI_PRIBADI_MAHASISWA = [
        'mahasiswa_id',
        'nim',
        'nama',
        'name',
        'email',
        'nominal',
        'nama_asli',
        'dokumen',
        'dokumen_tambahan',
        'alasan',
        'catatan_perbaikan',
        'catatan_petugas',
        'isi',
        'jawaban',
        'penghasilan_gabungan',
        'jumlah_tanggungan',
        'daya_listrik',
        'status_tempat_tinggal',
        'kepemilikan_aset',
    ];

    /**
     * Halaman rekap agregat pimpinan (/pimpinan/rekap).
     * Hanya memilih hitungan agregat (COUNT / GROUP BY), tanpa daftar mahasiswa,
     * tanpa NIM, dan tanpa data ekonomi perorangan (PRD Bagian 3.3 & Blueprint §7.8).
     */
    public function rekap(): View
    {
        $rekapPerSumber = HasilUkt::query()
            ->select('sumber', DB::raw('COUNT(*) as jumlah'))
            ->groupBy('sumber')
            ->pluck('jumlah', 'sumber')
            ->toArray();

        $daftarGolongan = GolonganUkt::query()
            ->select(['id', 'kode', 'nama', 'nominal_min', 'nominal_max'])
            ->withCount('hasilUkt')
            ->orderBy('nominal_min')
            ->get();

        $rekapKeberatanPerStatus = PengajuanKeberatan::query()
            ->select('status', DB::raw('COUNT(*) as jumlah'))
            ->groupBy('status')
            ->pluck('jumlah', 'status')
            ->toArray();

        $versiAktif = VersiParameter::where('aktif', true)->latest('id')->first();
        $totalHasilTercatat = array_sum($rekapPerSumber);

        // Panel Pemantauan SLA Keberatan (KF-11) — hanya kolom non-pribadi (tanpa mahasiswa_id, NIM, nama, alasan, atau data ekonomi)
        $pengajuanAktifBerSla = PengajuanKeberatan::query()
            ->select(['id', 'status', 'diajukan_pada', 'sla_hari_kerja', 'tenggat_waktu', 'diputuskan_pada', 'updated_at'])
            ->whereNotIn('status', ['Disetujui', 'Ditolak'])
            ->whereNotNull('tenggat_waktu')
            ->orderBy('tenggat_waktu')
            ->orderBy('id')
            ->get();

        $jumlahMendekatiTenggat = 0;
        $jumlahMelewatiTenggat = 0;
        $daftarPantauanSla = [];

        foreach ($pengajuanAktifBerSla as $item) {
            $info = $item->infoTenggat();
            if ($info['kategori'] === 'terlewat') {
                $jumlahMelewatiTenggat++;
            } elseif ($info['kategori'] === 'mendekati') {
                $jumlahMendekatiTenggat++;
            }

            if (in_array($info['kategori'], ['terlewat', 'mendekati'], true)) {
                $daftarPantauanSla[] = [
                    'id' => $item->id,
                    'diajukan_pada' => $item->diajukan_pada,
                    'tenggat_waktu' => $item->tenggat_waktu,
                    'status' => $item->status,
                    'kategori' => $info['kategori'],
                    'hari_terlewat' => $info['hari_terlewat'],
                    'sisa_hari' => $info['sisa_hari'],
                    'badge_label' => $info['badge_label'],
                    'badge_status' => $info['badge_status'],
                    'perlu_tindak_lanjut_pimpinan' => $info['perlu_tindak_lanjut_pimpinan'],
                ];
            }
        }

        usort($daftarPantauanSla, function (array $a, array $b): int {
            if ($a['kategori'] !== $b['kategori']) {
                return $a['kategori'] === 'terlewat' ? -1 : 1;
            }
            if ($a['kategori'] === 'terlewat') {
                return ($b['hari_terlewat'] ?? 0) <=> ($a['hari_terlewat'] ?? 0);
            }

            return ($a['sisa_hari'] ?? 99) <=> ($b['sisa_hari'] ?? 99);
        });

        return view('pimpinan.rekap', [
            'rekapPerSumber' => $rekapPerSumber,
            'daftarGolongan' => $daftarGolongan,
            'rekapKeberatanPerStatus' => $rekapKeberatanPerStatus,
            'versiAktif' => $versiAktif,
            'totalHasilTercatat' => $totalHasilTercatat,
            'jumlahMendekatiTenggat' => $jumlahMendekatiTenggat,
            'jumlahMelewatiTenggat' => $jumlahMelewatiTenggat,
            'daftarPantauanSla' => $daftarPantauanSla,
        ]);
    }

    /**
     * Halaman riwayat audit pimpinan (/pimpinan/riwayat) — KF-13.
     * Menampilkan rekap aksi serta daftar riwayat perubahan/akses yang dapat dicari dan difilter,
     * dengan menyamarkan identitas dan nilai ekonomi perorangan mahasiswa (PRD Bagian 3.3 & Blueprint §7.7).
     */
    public function riwayat(Request $request): View
    {
        $aksiFilter = trim((string) $request->query('aksi', ''));
        $subjekFilter = trim((string) $request->query('subjek_tipe', ''));
        $kataKunci = trim((string) $request->query('q', ''));

        $rekapAksiAudit = AuditLog::query()
            ->select('aksi', DB::raw('COUNT(*) as jumlah'))
            ->groupBy('aksi')
            ->orderByDesc('jumlah')
            ->pluck('jumlah', 'aksi')
            ->toArray();

        $daftarAksi = array_keys($rekapAksiAudit);
        sort($daftarAksi);

        $daftarSubjekTipe = AuditLog::query()
            ->select('subjek_tipe')
            ->distinct()
            ->orderBy('subjek_tipe')
            ->pluck('subjek_tipe')
            ->toArray();

        $query = AuditLog::with('pelaku')
            ->latest('dibuat_pada')
            ->latest('id');

        if ($aksiFilter !== '' && in_array($aksiFilter, $daftarAksi, true)) {
            $query->where('aksi', $aksiFilter);
        }

        if ($subjekFilter !== '' && in_array($subjekFilter, $daftarSubjekTipe, true)) {
            $query->where('subjek_tipe', $subjekFilter);
        }

        if ($kataKunci !== '') {
            $query->where(function ($sub) use ($kataKunci) {
                $sub->where('aksi', 'like', '%' . $kataKunci . '%')
                    ->orWhere('subjek_tipe', 'like', '%' . $kataKunci . '%')
                    ->orWhere('subjek_id', 'like', '%' . $kataKunci . '%');
            });
        }

        $daftarLog = $query->paginate(15)->withQueryString();

        $daftarLog->getCollection()->transform(function (AuditLog $log) {
            $log->setAttribute('label_pelaku_aman', $this->formatPelakuUntukPimpinan($log));
            $log->setAttribute('nilai_sebelum_aman', $this->samarkanDataPribadi($log->nilai_sebelum));
            $log->setAttribute('nilai_sesudah_aman', $this->samarkanDataPribadi($log->nilai_sesudah));

            return $log;
        });

        return view('pimpinan.riwayat', [
            'rekapAksiAudit' => $rekapAksiAudit,
            'daftarLog' => $daftarLog,
            'daftarAksi' => $daftarAksi,
            'daftarSubjekTipe' => $daftarSubjekTipe,
            'aksiFilter' => $aksiFilter,
            'subjekFilter' => $subjekFilter,
            'kataKunci' => $kataKunci,
        ]);
    }

    private function formatPelakuUntukPimpinan(AuditLog $log): string
    {
        $pelaku = $log->pelaku;
        if (! $pelaku) {
            return 'Sistem / Pengunjung Publik';
        }

        if ($pelaku->isMahasiswa()) {
            return 'Akun Mahasiswa (Identitas Disamarkan)';
        }

        return $pelaku->nama . ' (' . $pelaku->labelPeran() . ')';
    }

    private function samarkanDataPribadi(?array $data): ?array
    {
        if ($data === null) {
            return null;
        }

        $hasil = [];
        foreach ($data as $key => $value) {
            if (in_array(strtolower((string) $key), self::KUNCI_PRIBADI_MAHASISWA, true)) {
                $hasil[$key] = '[Disamarkan untuk Privasi Mahasiswa]';
                continue;
            }

            if (is_array($value)) {
                $hasil[$key] = $this->samarkanDataPribadi($value);
                continue;
            }

            // Samarkan bila nilai string mengandung pola NIM 9 digit
            if (is_string($value) && preg_match('/\b\d{9}\b/', $value)) {
                $hasil[$key] = '[Disamarkan untuk Privasi Mahasiswa]';
                continue;
            }

            $hasil[$key] = $value;
        }

        return $hasil;
    }
}


