<?php

namespace App\Http\Controllers\Petugas;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class RiwayatController extends Controller
{
    /**
     * Halaman penelusuran riwayat akses dan perubahan data untuk petugas (/petugas/riwayat) — KF-13.
     * Hanya-baca (append-only), dapat dicari dan difilter berdasarkan aksi serta tipe subjek.
     */
    public function index(Request $request): View
    {
        $aksiFilter = trim((string) $request->query('aksi', ''));
        $kategoriFilter = trim((string) $request->query('kategori', ''));
        $subjekFilter = trim((string) $request->query('subjek_tipe', ''));
        $kataKunci = trim((string) $request->query('q', ''));

        $daftarAksi = AuditLog::query()
            ->select('aksi')
            ->distinct()
            ->orderBy('aksi')
            ->pluck('aksi')
            ->toArray();

        $daftarSubjekTipe = AuditLog::query()
            ->select('subjek_tipe')
            ->distinct()
            ->orderBy('subjek_tipe')
            ->pluck('subjek_tipe')
            ->toArray();

        $daftarKategori = AuditLog::DAFTAR_KATEGORI;

        $query = AuditLog::with('pelaku')
            ->latest('dibuat_pada')
            ->latest('id');

        if ($aksiFilter !== '' && in_array($aksiFilter, $daftarAksi, true)) {
            $query->where('aksi', $aksiFilter);
        }

        if ($kategoriFilter !== '' && in_array($kategoriFilter, $daftarKategori, true)) {
            if ($kategoriFilter === AuditLog::KATEGORI_SISTEM) {
                $query->whereNotIn('aksi', AuditLog::aksiSelainSistem());
            } else {
                $query->whereIn('aksi', AuditLog::aksiUntukKategori($kategoriFilter));
            }
        }

        if ($subjekFilter !== '' && in_array($subjekFilter, $daftarSubjekTipe, true)) {
            $query->where('subjek_tipe', $subjekFilter);
        }

        if ($kataKunci !== '') {
            $query->where(function ($sub) use ($kataKunci) {
                $sub->where('aksi', 'like', '%' . $kataKunci . '%')
                    ->orWhere('subjek_tipe', 'like', '%' . $kataKunci . '%')
                    ->orWhere('subjek_id', 'like', '%' . $kataKunci . '%')
                    ->orWhere('nilai_sebelum', 'like', '%' . $kataKunci . '%')
                    ->orWhere('nilai_sesudah', 'like', '%' . $kataKunci . '%')
                    ->orWhereHas('pelaku', function ($uQuery) use ($kataKunci) {
                        $uQuery->where('name', 'like', '%' . $kataKunci . '%')
                            ->orWhere('email', 'like', '%' . $kataKunci . '%');
                    });
            });
        }

        $daftarLog = $query->paginate(15)->withQueryString();

        $ringkasanAksi = AuditLog::query()
            ->select('aksi', DB::raw('COUNT(*) as jumlah'))
            ->groupBy('aksi')
            ->orderByDesc('jumlah')
            ->pluck('jumlah', 'aksi')
            ->toArray();

        return view('petugas.riwayat.index', [
            'daftarLog' => $daftarLog,
            'daftarAksi' => $daftarAksi,
            'daftarKategori' => $daftarKategori,
            'daftarSubjekTipe' => $daftarSubjekTipe,
            'aksiFilter' => $aksiFilter,
            'kategoriFilter' => $kategoriFilter,
            'subjekFilter' => $subjekFilter,
            'kataKunci' => $kataKunci,
            'ringkasanAksi' => $ringkasanAksi,
        ]);
    }
}


