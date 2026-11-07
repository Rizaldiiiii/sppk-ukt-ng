<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Cache;

class KontenPublik extends Model
{
    protected $table = 'konten_publik';

    protected $fillable = [
        'slug',
        'judul',
        'ringkasan',
        'isi',
        'diperbarui_oleh',
        'diperbarui_pada',
    ];

    protected function casts(): array
    {
        return [
            'diperbarui_pada' => 'datetime',
        ];
    }

    public function pembaru(): BelongsTo
    {
        return $this->belongsTo(User::class, 'diperbarui_oleh');
    }

    /**
     * Petunjuk pengisian per kolom bawaan untuk formulir keberatan & pertanyaan (KF-09).
     *
     * @return array<string, array{judul: string, apa_diisi: string, format: string, contoh: string, akibat: string}>
     */
    public static function defaultPetunjukPengisian(): array
    {
        return [
            'keberatan_hasil' => [
                'judul' => 'Hasil UKT yang Diajukan Keberatan',
                'apa_diisi' => 'Pilih penetapan atau perhitungan hasil UKT milik Anda yang ingin diajukan untuk peninjauan ulang.',
                'format' => 'Pilihan dari daftar hasil UKT tercatat pada akun Anda (boleh dikosongkan bila hasil belum terbit).',
                'contoh' => 'Golongan IV — Rp 2.500.000 / Semester (Penetapan Resmi 10 Feb 2026).',
                'akibat' => 'Bila memilih hasil yang keliru, petugas perlu mengonfirmasi ulang periode penetapan yang Anda maksud.',
            ],
            'keberatan_alasan' => [
                'judul' => 'Alasan Keberatan & Rincian Kondisi Ekonomi (Wajib)',
                'apa_diisi' => 'Sebutkan komponen penilaian yang dinilai belum sesuai beserta kondisi ekonomi keluarga yang sebenarnya secara faktual.',
                'format' => 'Teks uraian Bahasa Indonesia minimal 20 karakter dan maksimal 3.000 karakter.',
                'contoh' => 'Pada komponen Penghasilan Gabungan tertulis Rp 4.500.000, namun sejak Januari 2026 ayah mengalami pemutusan hubungan kerja sehingga pendapatan keluarga saat ini Rp 1.800.000/bulan dengan 3 tanggungan aktif.',
                'akibat' => 'Pengajuan dengan alasan kosong akan ditolak oleh sistem; alasan yang terlalu singkat tanpa menyebut komponen membuat status menjadi Perlu Perbaikan.',
            ],
            'keberatan_dokumen' => [
                'judul' => 'Dokumen Bukti Pendukung (Batas Konservatif Sementara)',
                'apa_diisi' => 'Unggah berkas bukti resmi yang mendukung alasan keberatan (misalnya Surat Keterangan Penghasilan/PHK, Kartu Keluarga terbaru, tagihan listrik, atau foto hunian).',
                'format' => 'Berkas PDF, JPG, atau PNG; ukuran maksimal 2 MB (2.048 KB) per berkas; maksimal 3 berkas sekali unggah (batas konservatif sementara menunggu konfirmasi resmi kampus).',
                'contoh' => 'SK_Penghasilan_Kelurahan_2026.pdf (450 KB), Kartu_Keluarga_Terbaru.pdf (620 KB).',
                'akibat' => 'Berkas dengan format selain PDF/JPG/PNG atau melebihi 2 MB akan ditolak otomatis saat pengiriman; tanpa bukti yang jelas petugas dapat meminta Perlu Perbaikan.',
            ],
            'keberatan_perbaikan' => [
                'judul' => 'Keterangan Kelengkapan Perbaikan (Wajib saat Status Perlu Perbaikan)',
                'apa_diisi' => 'Tuliskan penjelasan tambahan yang menjawab catatan petugas beserta rincian dokumen baru yang Anda lampirkan.',
                'format' => 'Teks uraian minimal 15 karakter dan maksimal 2.000 karakter, disertai unggahan berkas tambahan bila diminta.',
                'contoh' => 'Menindaklanjuti catatan petugas, berikut kami lampirkan hasil pindai Kartu Keluarga terbaru yang telah dilegalisasi kelurahan serta bukti token listrik 2 bulan terakhir.',
                'akibat' => 'Bila penjelasan perbaikan tidak menjawab poin catatan petugas, pengajuan belum dapat disetujui dalam evaluasi akhir.',
            ],
            'pertanyaan_isi' => [
                'judul' => 'Isi Pertanyaan kepada Petugas UKT (Wajib)',
                'apa_diisi' => 'Tuliskan pertanyaan spesifik Anda mengenai komponen penilaian UKT, prosedur peninjauan ulang, atau persyaratan dokumen.',
                'format' => 'Teks kalimat jelas minimal 10 karakter dan maksimal 2.000 karakter.',
                'contoh' => 'Apakah pekerja sektor informal (petani/pedagang harian) yang tidak memiliki slip gaji dapat menggunakan Surat Keterangan Penghasilan dari Kepala Desa sebagai bukti pengajuan keberatan?',
                'akibat' => 'Pertanyaan yang kosong tidak dapat dikirim; pertanyaan yang tidak jelas membuat petugas memerlukan klarifikasi tambahan.',
            ],
        ];
    }

    /**
     * Ambil petunjuk pengisian per kolom dari tabel konten_publik (slug: petunjuk-pengisian)
     * yang dapat diperbarui petugas melalui menu Kelola Konten Publik tanpa mengubah kode (KF-09).
     *
     * @return array<string, array{judul: string, apa_diisi: string, format: string, contoh: string, akibat: string, catatan_tambahan?: string}>
     */
    public static function petunjukPengisian(): array
    {
        $defaults = self::defaultPetunjukPengisian();

        $konten = Cache::remember('konten_publik_petunjuk-pengisian', 300, function () {
            return self::where('slug', 'petunjuk-pengisian')->first();
        });

        if (! $konten || ! is_string($konten->isi) || trim($konten->isi) === '') {
            return $defaults;
        }

        $decoded = json_decode($konten->isi, true);

        if (is_array($decoded)) {
            foreach ($defaults as $key => $defaultItem) {
                if (isset($decoded[$key]) && is_array($decoded[$key])) {
                    $defaults[$key] = array_merge($defaultItem, array_intersect_key($decoded[$key], $defaultItem));
                }
            }

            return $defaults;
        }

        // Bila petugas menyunting isi sebagai teks biasa (bukan JSON), tetap tampilkan sebagai catatan umum
        foreach ($defaults as $key => $defaultItem) {
            $defaults[$key]['catatan_tambahan'] = trim($konten->isi);
        }

        return $defaults;
    }
}


