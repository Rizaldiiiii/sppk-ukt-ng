<?php

namespace App\Http\Controllers;

use App\Models\FaktorPenilaian;
use App\Models\GolonganUkt;
use App\Models\KontenPublik;
use App\Models\PerkiraanWaktu;
use Illuminate\View\View;

class PublicController extends Controller
{
    /**
     * Beranda publik (/): ringkasan sistem dan akses cepat ke informasi terbuka tanpa login.
     * Tidak ada query ke tabel data pribadi mahasiswa di rute publik (Blueprint §7.1).
     */
    public function beranda(): View
    {
        $kontenRingkas = KontenPublik::whereIn('slug', ['alur', 'faktor', 'prosedur-keberatan'])
            ->get()
            ->keyBy('slug');

        $jumlahFaktorAktif = FaktorPenilaian::where('aktif', true)->count();
        $daftarGolongan = GolonganUkt::where('aktif', true)
            ->orderBy('nominal_min')
            ->get();

        return view('public.beranda', [
            'kontenRingkas' => $kontenRingkas,
            'jumlahFaktorAktif' => $jumlahFaktorAktif,
            'daftarGolongan' => $daftarGolongan,
        ]);
    }

    /**
     * Halaman alur penentuan UKT (/alur) — KF-02 umum.
     */
    public function alur(): View
    {
        $konten = KontenPublik::where('slug', 'alur')->first();
        $faktorAktif = FaktorPenilaian::where('aktif', true)
            ->orderBy('urutan')
            ->get();

        return view('public.alur', [
            'konten' => $konten,
            'faktorAktif' => $faktorAktif,
        ]);
    }

    /**
     * Halaman daftar faktor penentu UKT (/faktor) — KF-03 umum.
     */
    public function faktor(): View
    {
        $konten = KontenPublik::where('slug', 'faktor')->first();
        $daftarFaktor = FaktorPenilaian::where('aktif', true)
            ->orderBy('urutan')
            ->get();

        return view('public.faktor', [
            'konten' => $konten,
            'daftarFaktor' => $daftarFaktor,
        ]);
    }

    /**
     * Halaman rentang nominal tiap golongan UKT (/golongan) — KF-14.
     */
    public function golongan(): View
    {
        $daftarGolongan = GolonganUkt::where('aktif', true)
            ->orderBy('nominal_min')
            ->orderBy('id')
            ->get();

        $terakhirDiperbarui = $daftarGolongan->max('updated_at');

        return view('public.golongan', [
            'daftarGolongan' => $daftarGolongan,
            'terakhirDiperbarui' => $terakhirDiperbarui,
        ]);
    }

    /**
     * Halaman informasi alur pengajuan keberatan / peninjauan ulang (/prosedur-keberatan) — KF-06.
     */
    public function prosedurKeberatan(): View
    {
        $konten = KontenPublik::where('slug', 'prosedur-keberatan')->first();
        $perkiraanWaktu = PerkiraanWaktu::where('jenis', 'keberatan')
            ->where('aktif', true)
            ->latest('tanggal')
            ->first();

        return view('public.prosedur-keberatan', [
            'konten' => $konten,
            'perkiraanWaktu' => $perkiraanWaktu,
        ]);
    }

    /**
     * Halaman panduan visual tahapan (/panduan) — KF-10 (Tahap 3).
     * Menyediakan diagram SVG tahapan proses beserta versi teks aksesibel dan penanda kedaluwarsa bila alur berubah.
     */
    public function panduan(): View
    {
        $kontenPanduan = KontenPublik::where('slug', 'panduan')->first();
        $kontenAlur = KontenPublik::where('slug', 'alur')->first();
        $kontenProsedur = KontenPublik::where('slug', 'prosedur-keberatan')->first();

        $apakahKedaluwarsa = false;
        if ($kontenPanduan && $kontenPanduan->diperbarui_pada) {
            if ($kontenAlur?->diperbarui_pada && $kontenAlur->diperbarui_pada->gt($kontenPanduan->diperbarui_pada)) {
                $apakahKedaluwarsa = true;
            }
            if ($kontenProsedur?->diperbarui_pada && $kontenProsedur->diperbarui_pada->gt($kontenPanduan->diperbarui_pada)) {
                $apakahKedaluwarsa = true;
            }
        }

        if ($kontenAlur) {
            $isiAlur = json_decode($kontenAlur->isi, true);
            $tahapan = $isiAlur['tahapan'] ?? [];
            if (is_array($tahapan) && count($tahapan) !== 5) {
                $apakahKedaluwarsa = true;
            }
        }

        return view('public.panduan', [
            'kontenPanduan' => $kontenPanduan,
            'kontenAlur' => $kontenAlur,
            'kontenProsedur' => $kontenProsedur,
            'apakahKedaluwarsa' => $apakahKedaluwarsa,
        ]);
    }

    /**
     * Halaman jalur bertanya (/bantuan) — info umum + stub KF-08 (Tahap 2).
     */
    public function bantuan(): View
    {
        $kontenKontak = KontenPublik::where('slug', 'kontak')->first();
        $kontakPublikAktif = (bool) config('sppk.kontak_petugas_publik', true);

        return view('public.bantuan', [
            'kontenKontak' => $kontenKontak,
            'kontakPublikAktif' => $kontakPublikAktif,
        ]);
    }
}

