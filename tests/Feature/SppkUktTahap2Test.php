<?php

namespace Tests\Feature;

use App\Mail\StatusUktBerubahMail;
use App\Models\AuditLog;
use App\Models\DokumenKeberatan;
use App\Models\PengajuanKeberatan;
use App\Models\Pertanyaan;
use App\Models\User;
use App\Services\ObjectionService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class SppkUktTahap2Test extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    public function test_kf07_mahasiswa_mengajukan_keberatan_validasi_kirim_ganda_dan_isolasi_kepemilikan(): void
    {
        Mail::fake();
        Storage::fake('local');

        $budi = User::where('email', 'budi.pratama@student.ung.ac.id')->firstOrFail();
        $ayu = User::where('email', 'ayu.lestari@student.ung.ac.id')->firstOrFail();
        $hasilBudi = $budi->mahasiswa->hasilTerbaru;

        // 1. Halaman formulir keberatan menampilkan petunjuk per kolom KF-09
        $this->actingAs($budi)
            ->get('/keberatan/baru')
            ->assertStatus(200)
            ->assertSee('Petunjuk Pengisian (KF-09)')
            ->assertSee('Apa yang harus diisi:')
            ->assertSee('Format yang diharapkan:')
            ->assertSee('Contoh pengisian:')
            ->assertSee('Akibat bila salah / kurang lengkap:');

        // 2. Kiriman tanpa alasan ditolak, pesan validasi menyebut kolom & cara memperbaikinya (KF-09)
        $this->actingAs($budi)
            ->from('/keberatan/baru')
            ->post('/keberatan', [
                'hasil_id' => $hasilBudi->id,
                'alasan' => '',
            ])
            ->assertRedirect('/keberatan/baru')
            ->assertSessionHasErrors('alasan');

        $pesanErrorAlasan = session('errors')->first('alasan');
        $this->assertStringContainsString('Kolom Alasan Keberatan wajib diisi', $pesanErrorAlasan);

        // 3. Budi mengirim pengajuan keberatan valid dengan berkas PDF bukti pendukung
        $berkasPdf = UploadedFile::fake()->create('sk-penghasilan-kelurahan.pdf', 320, 'application/pdf');
        $alasanValid = 'Pada komponen Penghasilan Gabungan tertulis Rp 4.500.000, namun ayah baru saja pensiun sehingga pendapatan keluarga turun menjadi Rp 1.900.000 per bulan.';

        $responseStore = $this->actingAs($budi)->post('/keberatan', [
            'hasil_id' => $hasilBudi->id,
            'alasan' => $alasanValid,
            'dokumen' => [$berkasPdf],
        ]);

        $pengajuanBudi = PengajuanKeberatan::where('mahasiswa_id', $budi->mahasiswa->id)->firstOrFail();
        $responseStore->assertRedirect(route('mahasiswa.keberatan.show', $pengajuanBudi));
        $this->assertSame(ObjectionService::STATUS_DIAJUKAN, $pengajuanBudi->status);

        // Pastikan berkas disimpan dengan nama acak di disk privat local dan nama asli di DB
        $dokumenTersimpan = DokumenKeberatan::where('pengajuan_id', $pengajuanBudi->id)->firstOrFail();
        $this->assertSame('sk-penghasilan-kelurahan.pdf', $dokumenTersimpan->nama_asli);
        $this->assertNotSame('dokumen-keberatan/sk-penghasilan-kelurahan.pdf', $dokumenTersimpan->path_penyimpanan);
        Storage::disk('local')->assertExists($dokumenTersimpan->path_penyimpanan);

        // 4. Pencegahan kirim ganda / klik ganda: kiriman identik beruntun hanya menghasilkan 1 baris
        $this->actingAs($budi)->post('/keberatan', [
            'hasil_id' => $hasilBudi->id,
            'alasan' => $alasanValid,
        ])->assertRedirect(route('mahasiswa.keberatan.show', $pengajuanBudi));

        $this->assertSame(1, PengajuanKeberatan::where('mahasiswa_id', $budi->mahasiswa->id)->count());

        // 5. Ayu membuat keberatan, Budi mencoba membuka keberatan milik Ayu -> 403 & tercatat di audit_logs
        $pengajuanAyu = PengajuanKeberatan::create([
            'mahasiswa_id' => $ayu->mahasiswa->id,
            'hasil_id' => $ayu->mahasiswa->hasilTerbaru?->id,
            'alasan' => 'Keberatan milik Ayu Lestari mengenai jumlah tanggungan keluarga.',
            'status' => ObjectionService::STATUS_DIAJUKAN,
            'diajukan_pada' => now(),
        ]);

        $this->actingAs($budi)
            ->get('/keberatan/' . $pengajuanAyu->id)
            ->assertStatus(403);

        $this->assertDatabaseHas('audit_logs', [
            'pelaku_id' => $budi->id,
            'aksi' => 'penolakan_akses',
            'subjek_tipe' => PengajuanKeberatan::class,
            'subjek_id' => (string) $pengajuanAyu->id,
        ]);
    }

    public function test_kf07_petugas_filter_ubah_status_perlu_perbaikan_dan_status_terminal(): void
    {
        Mail::fake();
        Storage::fake('local');

        $petugas = User::where('email', 'petugas@ung.ac.id')->firstOrFail();
        $budi = User::where('email', 'budi.pratama@student.ung.ac.id')->firstOrFail();

        $pengajuan = PengajuanKeberatan::create([
            'mahasiswa_id' => $budi->mahasiswa->id,
            'hasil_id' => $budi->mahasiswa->hasilTerbaru?->id,
            'alasan' => 'Penurunan penghasilan keluarga sejak awal semester genap 2026.',
            'status' => ObjectionService::STATUS_DIAJUKAN,
            'diajukan_pada' => now(),
        ]);

        // 1. Petugas melihat daftar keberatan & memfilter berdasarkan status dan pencarian NIM
        $this->actingAs($petugas)
            ->get('/petugas/keberatan?status=Diajukan&q=531412001')
            ->assertStatus(200)
            ->assertSee('Budi Pratama')
            ->assertSee('531412001');

        // 2. Petugas mengubah status menjadi Perlu Perbaikan disertai catatan
        $this->actingAs($petugas)
            ->patch('/petugas/keberatan/' . $pengajuan->id . '/status', [
                'status' => ObjectionService::STATUS_PERLU_PERBAIKAN,
                'catatan_petugas' => 'Mohon lampirkan Kartu Keluarga terbaru yang telah dilegalisasi.',
            ])
            ->assertRedirect();

        $pengajuan->refresh();
        $this->assertSame(ObjectionService::STATUS_PERLU_PERBAIKAN, $pengajuan->status);

        // Budi dapat membaca catatan petugas dan melihat formulir kelengkapan perbaikan
        $this->actingAs($budi)
            ->get('/keberatan/' . $pengajuan->id)
            ->assertStatus(200)
            ->assertSee('Mohon lampirkan Kartu Keluarga terbaru yang telah dilegalisasi.')
            ->assertSee('Lengkapi Penjelasan atau Unggah Dokumen Bukti Tambahan');

        // 3. Budi melengkapi perbaikan beserta unggahan berkas tambahan -> status kembali Dalam Peninjauan
        $berkasKk = UploadedFile::fake()->create('kk-terbaru-legalisasi.pdf', 400, 'application/pdf');
        $this->actingAs($budi)
            ->post('/keberatan/' . $pengajuan->id . '/perbaikan', [
                'catatan_perbaikan' => 'Berikut kami lampirkan berkas Kartu Keluarga terbaru yang sudah dilegalisasi kelurahan.',
                'dokumen' => [$berkasKk],
            ])
            ->assertRedirect(route('mahasiswa.keberatan.show', $pengajuan));

        $pengajuan->refresh();
        $this->assertSame(ObjectionService::STATUS_DALAM_PENINJAUAN, $pengajuan->status);
        $this->assertSame(1, $pengajuan->dokumen()->count());

        // 4. Petugas menetapkan keputusan akhir Disetujui (status terminal)
        $this->actingAs($petugas)
            ->patch('/petugas/keberatan/' . $pengajuan->id . '/status', [
                'status' => ObjectionService::STATUS_DISETUJUI,
                'catatan_petugas' => 'Bukti lengkap dan terverifikasi. Pengajuan peninjauan ulang disetujui.',
            ])
            ->assertRedirect();

        $pengajuan->refresh();
        $this->assertSame(ObjectionService::STATUS_DISETUJUI, $pengajuan->status);
        $this->assertNotNull($pengajuan->diputuskan_pada);
        $this->assertSame($petugas->id, $pengajuan->diputuskan_oleh);

        // 5. Status Disetujui / Ditolak bersifat terminal: percobaan mengubah kembali melalui aksi biasa ditolak
        $this->actingAs($petugas)
            ->patch('/petugas/keberatan/' . $pengajuan->id . '/status', [
                'status' => ObjectionService::STATUS_DITOLAK,
                'catatan_petugas' => 'Percobaan ubah ulang setelah terminal.',
            ])
            ->assertSessionHasErrors('status');

        $pengajuan->refresh();
        $this->assertSame(ObjectionService::STATUS_DISETUJUI, $pengajuan->status);
    }

    public function test_kf08_pertanyaan_mahasiswa_dan_pengelolaan_jawaban_petugas(): void
    {
        Mail::fake();

        $budi = User::where('email', 'budi.pratama@student.ung.ac.id')->firstOrFail();
        $ayu = User::where('email', 'ayu.lestari@student.ung.ac.id')->firstOrFail();
        $petugas = User::where('email', 'petugas@ung.ac.id')->firstOrFail();

        // 1. Pengunjung belum login mencoba POST /pertanyaan -> diarahkan ke /login
        $this->post('/pertanyaan', ['isi' => 'Pertanyaan tanpa login'])
            ->assertRedirect(route('login'));

        // 2. Budi mengirim pertanyaan baru -> mendapat tanda terima dengan status 'baru'
        $isiPertanyaan = 'Apakah Surat Keterangan Penghasilan dari Kepala Desa dapat dipakai bila orang tua bekerja sebagai petani?';
        $this->actingAs($budi)
            ->post('/pertanyaan', ['isi' => $isiPertanyaan])
            ->assertRedirect();

        $pertanyaanBudi = Pertanyaan::where('mahasiswa_id', $budi->mahasiswa->id)->firstOrFail();
        $this->assertSame('baru', $pertanyaanBudi->status);

        // Klik ganda / kirim ganda hanya menyimpan 1 baris pertanyaan
        $this->actingAs($budi)
            ->post('/pertanyaan', ['isi' => $isiPertanyaan])
            ->assertRedirect(route('mahasiswa.pertanyaan.show', $pertanyaanBudi));
        $this->assertSame(1, Pertanyaan::where('mahasiswa_id', $budi->mahasiswa->id)->count());

        // 3. Budi mencoba membuka pertanyaan milik Ayu -> 403 & tercatat di audit_logs
        $pertanyaanAyu = Pertanyaan::create([
            'mahasiswa_id' => $ayu->mahasiswa->id,
            'isi' => 'Pertanyaan milik Ayu mengenai komponen daya listrik rumah tangga.',
            'status' => 'baru',
        ]);

        $this->actingAs($budi)
            ->get('/pertanyaan/' . $pertanyaanAyu->id)
            ->assertStatus(403);

        $this->assertDatabaseHas('audit_logs', [
            'pelaku_id' => $budi->id,
            'aksi' => 'penolakan_akses',
            'subjek_tipe' => Pertanyaan::class,
            'subjek_id' => (string) $pertanyaanAyu->id,
        ]);

        // 4. Petugas menjawab pertanyaan Budi -> status menjadi 'dijawab', notifikasi terkirim, tercatat di audit_logs
        $jawabanResmi = 'Ya, Surat Keterangan Penghasilan dari Kepala Desa/Lurah yang berstempel resmi dapat digunakan sebagai bukti pendukung.';
        $this->actingAs($petugas)
            ->patch('/petugas/pertanyaan/' . $pertanyaanBudi->id . '/jawab', [
                'jawaban' => $jawabanResmi,
            ])
            ->assertRedirect();

        $pertanyaanBudi->refresh();
        $this->assertSame('dijawab', $pertanyaanBudi->status);
        $this->assertSame($jawabanResmi, $pertanyaanBudi->jawaban);
        $this->assertSame($petugas->id, $pertanyaanBudi->dijawab_oleh);
        $this->assertNotNull($pertanyaanBudi->dijawab_pada);

        $this->assertDatabaseHas('audit_logs', [
            'pelaku_id' => $petugas->id,
            'aksi' => 'jawab_pertanyaan',
            'subjek_tipe' => Pertanyaan::class,
            'subjek_id' => (string) $pertanyaanBudi->id,
        ]);

        // Budi dapat membaca jawaban petugas pada halaman detail pertanyaannya
        $this->actingAs($budi)
            ->get('/pertanyaan/' . $pertanyaanBudi->id)
            ->assertStatus(200)
            ->assertSee($jawabanResmi);

        // 5. Petugas menutup pertanyaan -> pertanyaan yang ditutup tidak bisa dijawab lagi sebelum dibuka kembali
        $this->actingAs($petugas)
            ->patch('/petugas/pertanyaan/' . $pertanyaanBudi->id . '/tutup')
            ->assertRedirect();

        $pertanyaanBudi->refresh();
        $this->assertSame('ditutup', $pertanyaanBudi->status);

        $this->actingAs($petugas)
            ->patch('/petugas/pertanyaan/' . $pertanyaanBudi->id . '/jawab', [
                'jawaban' => 'Percobaan menjawab saat pertanyaan ditutup.',
            ])
            ->assertSessionHasErrors('jawaban');

        // Setelah dibuka kembali oleh petugas, pertanyaan dapat dijawab lagi
        $this->actingAs($petugas)
            ->patch('/petugas/pertanyaan/' . $pertanyaanBudi->id . '/buka-kembali')
            ->assertRedirect();

        $pertanyaanBudi->refresh();
        $this->assertSame('dijawab', $pertanyaanBudi->status);
    }

    public function test_kf09_petugas_dapat_mengelola_teks_petunjuk_pengisian_tanpa_mengubah_kode(): void
    {
        $petugas = User::where('email', 'petugas@ung.ac.id')->firstOrFail();
        $budi = User::where('email', 'budi.pratama@student.ung.ac.id')->firstOrFail();

        $petunjukBaru = \App\Models\KontenPublik::defaultPetunjukPengisian();
        $petunjukBaru['keberatan_alasan']['contoh'] = 'Contoh baru dari petugas: Lampirkan SK Pensiun Nomor 123/2026.';

        $this->actingAs($petugas)
            ->put('/petugas/konten/petunjuk-pengisian', [
                'judul' => 'Petunjuk Pengisian Formulir Keberatan dan Pertanyaan (KF-09)',
                'ringkasan' => 'Panduan per kolom yang telah diperbarui oleh petugas.',
                'petunjuk' => $petunjukBaru,
            ])
            ->assertRedirect();

        $this->actingAs($budi)
            ->get('/keberatan/baru')
            ->assertStatus(200)
            ->assertSee('Contoh baru dari petugas: Lampirkan SK Pensiun Nomor 123/2026.');
    }

    public function test_editor_terstruktur_konten_publik_alur_prosedur_dan_petunjuk(): void
    {
        $petugas = User::where('email', 'petugas@ung.ac.id')->firstOrFail();

        // Pastikan tampilan awal halaman publik /alur dan /prosedur-keberatan tercatat
        $kontenAlurAwal = \App\Models\KontenPublik::where('slug', 'alur')->firstOrFail();
        $kontenProsedurAwal = \App\Models\KontenPublik::where('slug', 'prosedur-keberatan')->firstOrFail();
        $htmlAlurAwal = $this->get('/alur')->assertStatus(200)->getContent();
        $htmlProsedurAwal = $this->get('/prosedur-keberatan')->assertStatus(200)->getContent();

        // 1. Simpan ulang /alur dan /prosedur-keberatan dengan data terstruktur yang tidak diubah -> halaman publik tampil persis sama
        $decodedAlurAwal = json_decode($kontenAlurAwal->isi, true);
        $this->actingAs($petugas)
            ->put('/petugas/konten/alur', [
                'judul' => $kontenAlurAwal->judul,
                'ringkasan' => $kontenAlurAwal->ringkasan,
                'tahapan' => $decodedAlurAwal['tahapan'],
            ])
            ->assertRedirect();

        $decodedProsedurAwal = json_decode($kontenProsedurAwal->isi, true);
        $this->actingAs($petugas)
            ->put('/petugas/konten/prosedur-keberatan', [
                'judul' => $kontenProsedurAwal->judul,
                'ringkasan' => $kontenProsedurAwal->ringkasan,
                'tujuan_pengajuan' => $decodedProsedurAwal['tujuan_pengajuan'],
                'siapa_boleh_mengajukan' => $decodedProsedurAwal['siapa_boleh_mengajukan'],
                'dokumen_diperlukan' => $decodedProsedurAwal['dokumen_diperlukan'],
                'tahapan_peninjauan' => $decodedProsedurAwal['tahapan_peninjauan'],
            ])
            ->assertRedirect();

        $this->assertSame($kontenAlurAwal->isi, $kontenAlurAwal->fresh()->isi);
        $this->assertSame($kontenProsedurAwal->isi, $kontenProsedurAwal->fresh()->isi);

        $this->get('/alur')
            ->assertStatus(200)
            ->assertSee('Pengisian Data Sosial-Ekonomi & Unggah Bukti')
            ->assertSee('Peninjauan Ulang / Pengajuan Keberatan (Bila Hasil Dirasa Tidak Sesuai)');

        $this->get('/prosedur-keberatan')
            ->assertStatus(200)
            ->assertSee($decodedProsedurAwal['tujuan_pengajuan']);

        // 2. Edit tahapan alur lewat payload terstruktur + tambah tahapan baru & hapus tahapan (nomor diurutkan ulang otomatis)
        $tahapanEdit = $decodedAlurAwal['tahapan'];
        unset($tahapanEdit[1]); // Hapus tahapan ke-2
        $tahapanEdit[] = [
            'judul' => 'Tahap Tambahan: Evaluasi Berkala Semester',
            'deskripsi' => 'Mahasiswa dapat memantau pembaruan status evaluasi setiap awal semester.',
            'hubungan_data' => 'Data pembaruan kondisi keluarga ditautkan ke riwayat hasil.',
        ];

        $this->actingAs($petugas)
            ->put('/petugas/konten/alur', [
                'judul' => $kontenAlurAwal->judul,
                'ringkasan' => $kontenAlurAwal->ringkasan,
                'tahapan' => array_values($tahapanEdit),
            ])
            ->assertRedirect();

        $alurBaru = json_decode($kontenAlurAwal->fresh()->isi, true);
        $this->assertCount(5, $alurBaru['tahapan']);
        $this->assertSame([1, 2, 3, 4, 5], array_column($alurBaru['tahapan'], 'nomor'));
        $this->assertSame('Tahap Tambahan: Evaluasi Berkala Semester', $alurBaru['tahapan'][4]['judul']);

        $this->get('/alur')
            ->assertStatus(200)
            ->assertSee('Tahap Tambahan: Evaluasi Berkala Semester')
            ->assertDontSee('Pemeriksaan Kelengkapan & Verifikasi Dokumen oleh Petugas');

        // 3. Tolak hapus tahapan terakhir (tahapan kosong / < 1 tahapan)
        $this->actingAs($petugas)
            ->from('/petugas/konten')
            ->put('/petugas/konten/alur', [
                'judul' => $kontenAlurAwal->judul,
                'ringkasan' => $kontenAlurAwal->ringkasan,
                'tahapan' => [],
            ])
            ->assertRedirect('/petugas/konten')
            ->assertSessionHasErrors('tahapan');

        // 4. Tolak simpan bila judul tahapan kosong
        $this->actingAs($petugas)
            ->from('/petugas/konten')
            ->put('/petugas/konten/alur', [
                'judul' => $kontenAlurAwal->judul,
                'ringkasan' => $kontenAlurAwal->ringkasan,
                'tahapan' => [
                    [
                        'judul' => '',
                        'deskripsi' => 'Deskripsi tanpa judul tahapan.',
                        'hubungan_data' => 'Hubungan data.',
                    ],
                ],
            ])
            ->assertRedirect('/petugas/konten')
            ->assertSessionHasErrors('tahapan.0.judul');

        // 5. Edit satu kolom petunjuk-pengisian lewat payload terstruktur -> kolom lain tetap utuh
        $kontenPetunjuk = \App\Models\KontenPublik::where('slug', 'petunjuk-pengisian')->firstOrFail();
        $this->actingAs($petugas)
            ->put('/petugas/konten/petunjuk-pengisian', [
                'judul' => $kontenPetunjuk->judul,
                'ringkasan' => $kontenPetunjuk->ringkasan,
                'petunjuk' => [
                    'pertanyaan_isi' => [
                        'judul' => 'Pertanyaan kepada Petugas UKT (Diperbarui)',
                        'apa_diisi' => 'Isikan pertanyaan ringkas dan jelas.',
                        'format' => 'Minimal 10 karakter.',
                        'contoh' => 'Contoh pertanyaan terstruktur baru.',
                        'akibat' => 'Pertanyaan kosong ditolak.',
                    ],
                ],
            ])
            ->assertRedirect();

        $petunjukTersimpan = \App\Models\KontenPublik::petunjukPengisian();
        $this->assertSame('Pertanyaan kepada Petugas UKT (Diperbarui)', $petunjukTersimpan['pertanyaan_isi']['judul']);
        $this->assertSame('Contoh pertanyaan terstruktur baru.', $petunjukTersimpan['pertanyaan_isi']['contoh']);
        // Kolom lain (mis. keberatan_hasil) tetap utuh
        $this->assertSame('Hasil UKT yang Diajukan Keberatan', $petunjukTersimpan['keberatan_hasil']['judul']);

        // 6. Halaman kelola konten petugas tidak lagi menampilkan status konfirmasi sumber faktor
        $this->actingAs($petugas)
            ->get('/petugas/konten')
            ->assertStatus(200)
            ->assertDontSee('Terkonfirmasi resmi')
            ->assertDontSee('Menunggu konfirmasi resmi')
            ->assertDontSee('Status Konfirmasi');

        // 7. Tambah dan edit faktor penilaian tanpa field status_konfirmasi tetap berhasil
        $this->actingAs($petugas)
            ->post('/petugas/faktor', [
                'kode' => 'tagihan_air_rumah',
                'nama' => 'Tagihan Air Rumah Tangga',
                'penjelasan_singkat' => 'Rata-rata biaya pemakaian air bersih bulanan keluarga.',
                'pengaruh_umum' => 'Indikator tambahan konsumsi utilitas rumah tangga.',
                'urutan' => 6,
            ])
            ->assertRedirect();

        $faktorBaru = \App\Models\FaktorPenilaian::where('kode', 'tagihan_air_rumah')->firstOrFail();
        $this->assertSame('Tagihan Air Rumah Tangga', $faktorBaru->nama);

        $this->actingAs($petugas)
            ->put('/petugas/faktor/' . $faktorBaru->id, [
                'nama' => 'Tagihan Air Bersih Rumah Tangga',
                'penjelasan_singkat' => 'Rata-rata biaya pemakaian air bersih PDAM bulanan keluarga.',
                'pengaruh_umum' => 'Indikator tambahan konsumsi utilitas rumah tangga per bulan.',
                'urutan' => 6,
            ])
            ->assertRedirect();

        $this->assertSame('Tagihan Air Bersih Rumah Tangga', $faktorBaru->fresh()->nama);
    }

    public function test_kf13_riwayat_audit_petugas_dan_pimpinan_serta_penolakan_akses_mahasiswa(): void
    {
        $budi = User::where('email', 'budi.pratama@student.ung.ac.id')->firstOrFail();
        $petugas = User::where('email', 'petugas@ung.ac.id')->firstOrFail();
        $pimpinan = User::where('email', 'pimpinan@ung.ac.id')->firstOrFail();

        // Buat aktivitas pertanyaan & keberatan oleh Budi agar tercatat di audit_logs
        $this->actingAs($budi)->post('/pertanyaan', [
            'isi' => 'Pertanyaan Budi untuk pengujian jejak audit KF-13.',
        ]);

        // 1. Mahasiswa tidak memiliki akses ke halaman riwayat petugas maupun pimpinan (403)
        $this->actingAs($budi)->get('/petugas/riwayat')->assertStatus(403);
        $this->actingAs($budi)->get('/pimpinan/riwayat')->assertStatus(403);

        // 2. Petugas dapat menelusuri dan memfilter riwayat audit lengkap (/petugas/riwayat)
        $this->actingAs($petugas)
            ->get('/petugas/riwayat?aksi=buat_pertanyaan')
            ->assertStatus(200)
            ->assertSee('buat_pertanyaan')
            ->assertSee('Budi Pratama');

        // 3. Pimpinan dapat menelusuri riwayat audit (/pimpinan/riwayat) TANPA melihat nama/NIM/data ekonomi pribadi mahasiswa
        $responsePimpinan = $this->actingAs($pimpinan)
            ->get('/pimpinan/riwayat');

        $responsePimpinan->assertStatus(200)
            ->assertSee('buat_pertanyaan')
            ->assertSee('Akun Mahasiswa (Identitas Disamarkan)')
            ->assertDontSee('Budi Pratama')
            ->assertDontSee('Ayu Lestari')
            ->assertDontSee('531412001')
            ->assertDontSee('531412002');
    }
}

