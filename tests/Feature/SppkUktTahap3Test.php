<?php

namespace Tests\Feature;

use App\Mail\StatusUktBerubahMail;
use App\Models\KontenPublik;
use App\Models\PengajuanKeberatan;
use App\Models\PerkiraanWaktu;
use App\Models\User;
use App\Services\ObjectionService;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class SppkUktTahap3Test extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    public function test_kf10_halaman_panduan_visual_publik_lengkap_dengan_svg_versi_teks_dan_deteksi_kedaluwarsa(): void
    {
        $response = $this->get('/panduan');

        $response->assertStatus(200)
            ->assertSee('KF-10')
            ->assertSee('Alur Penentuan UKT, dari Data sampai Keputusan')
            ->assertSee('Mahasiswa — toga')
            ->assertSee('Petugas — lanyard + kartu ID')
            ->assertSee('Pimpinan — jas gelap + dasi emas')
            ->assertSee('Sistem — roda gigi, otomatis')
            ->assertSee('Isi Data &amp; Unggah Bukti', false)
            ->assertSee('Verifikasi Dokumen oleh Petugas')
            ->assertSee('Dihitung per Komponen')
            ->assertSee('Total 3,20 / 5,00')
            ->assertSee('Ditetapkan oleh Kampus')
            ->assertSee('Keberatan, bila Hasil Tidak Sesuai')
            ->assertSee('Versi teks (alternatif dari diagram di atas)')
            ->assertDontSee('Label Kedaluwarsa')
            ->assertDontSee('Budi Pratama')
            ->assertDontSee('531412001');

        // Bila konten alur diperbarui lebih baru daripada panduan visual, label kedaluwarsa wajib tampil (Kriteria KF-10 butir 1)
        KontenPublik::where('slug', 'alur')->update([
            'diperbarui_pada' => now()->addMinutes(10),
        ]);

        $this->get('/panduan')
            ->assertStatus(200)
            ->assertSee('Label Kedaluwarsa');
    }

    public function test_kf11_kelola_perkiraan_waktu_hanya_menerima_dropdown_resmi_dan_satu_baris_aktif_per_jenis(): void
    {
        $petugas = User::where('email', 'petugas@ung.ac.id')->firstOrFail();

        // 1. Halaman kelola perkiraan waktu bisa dibuka petugas
        $this->actingAs($petugas)
            ->get('/petugas/perkiraan-waktu')
            ->assertStatus(200)
            ->assertSee('Kelola Perkiraan Waktu Tunggu')
            ->assertSee('Belum ditetapkan')
            ->assertSee('5 hari kerja');

        // 2. Input teks bebas di luar opsi dropdown ditolak validasi
        $this->actingAs($petugas)
            ->from('/petugas/perkiraan-waktu')
            ->put('/petugas/perkiraan-waktu/keberatan', [
                'pilihan_sla' => 'sekitar 10 hari bebas',
            ])
            ->assertRedirect('/petugas/perkiraan-waktu')
            ->assertSessionHasErrors('pilihan_sla');

        // 3. Petugas memilih "5 hari kerja" untuk keberatan -> otomatis membentuk teks, sumber, tanggal
        $this->actingAs($petugas)
            ->put('/petugas/perkiraan-waktu/keberatan', [
                'pilihan_sla' => '5',
            ])
            ->assertRedirect();

        $aktifPertama = PerkiraanWaktu::where('jenis', 'keberatan')->where('aktif', true)->firstOrFail();
        $this->assertStringContainsString('5 hari kerja', $aktifPertama->teks_perkiraan);
        $this->assertStringContainsString('Dewi Petugas', $aktifPertama->sumber);
        $this->assertSame(1, PerkiraanWaktu::where('jenis', 'keberatan')->where('aktif', true)->count());

        // 4. Petugas mengubah ke "7 hari kerja" -> baris lama otomatis nonaktif, hanya 1 baris aktif
        $this->actingAs($petugas)
            ->put('/petugas/perkiraan-waktu/keberatan', [
                'pilihan_sla' => '7',
            ])
            ->assertRedirect();

        $this->assertSame(1, PerkiraanWaktu::where('jenis', 'keberatan')->where('aktif', true)->count());
        $this->assertFalse($aktifPertama->fresh()->aktif);
        $aktifKedua = PerkiraanWaktu::where('jenis', 'keberatan')->where('aktif', true)->firstOrFail();
        $this->assertStringContainsString('7 hari kerja', $aktifKedua->teks_perkiraan);

        // 5. Tampil di halaman publik /prosedur-keberatan lengkap dengan sumber & tanggal
        $this->get('/prosedur-keberatan')
            ->assertStatus(200)
            ->assertSee($aktifKedua->teks_perkiraan)
            ->assertSee($aktifKedua->sumber);
    }

    public function test_kf11_penguncian_sla_perhitungan_hari_kerja_badge_urutan_petugas_scheduler_dan_panel_pimpinan(): void
    {
        Mail::fake();

        $petugas = User::where('email', 'petugas@ung.ac.id')->firstOrFail();
        $pimpinan = User::where('email', 'pimpinan@ung.ac.id')->firstOrFail();
        $budi = User::where('email', 'budi.pratama@student.ung.ac.id')->firstOrFail();
        $ayu = User::where('email', 'ayu.lestari@student.ung.ac.id')->firstOrFail();

        // A. Saat waktu tunggu belum ditetapkan, pengajuan keberatan tidak mengarang angka
        Carbon::setTestNow(Carbon::parse('2026-04-06 09:00:00')); // Senin
        $this->actingAs($ayu)->post('/keberatan', [
            'hasil_id' => $ayu->mahasiswa->hasilTerbaru?->id,
            'alasan' => 'Keberatan diajukan saat waktu tunggu belum ditetapkan oleh kampus.',
        ]);

        $pengajuanTanpaSla = PengajuanKeberatan::where('mahasiswa_id', $ayu->mahasiswa->id)->firstOrFail();
        $this->assertNull($pengajuanTanpaSla->sla_hari_kerja);
        $this->assertNull($pengajuanTanpaSla->tenggat_waktu);

        $this->actingAs($ayu)
            ->get('/keberatan/' . $pengajuanTanpaSla->id)
            ->assertStatus(200)
            ->assertSee('Waktu tunggu belum ditetapkan')
            ->assertSee('Status: Diajukan');

        // B. Petugas menetapkan SLA keberatan = 5 hari kerja
        $this->actingAs($petugas)->put('/petugas/perkiraan-waktu/keberatan', [
            'pilihan_sla' => '5',
        ]);

        // Budi mengajukan keberatan pada Senin 6 April 2026 -> +5 hari kerja (Sel 7, Rab 8, Kam 9, Jum 10, Sen 13 April 2026)
        $this->actingAs($budi)->post('/keberatan', [
            'hasil_id' => $budi->mahasiswa->hasilTerbaru?->id,
            'alasan' => 'Penghasilan orang tua berkurang signifikan karena pensiun per Maret 2026.',
        ]);

        $pengajuanBudi = PengajuanKeberatan::where('mahasiswa_id', $budi->mahasiswa->id)->firstOrFail();
        $this->assertSame(5, $pengajuanBudi->sla_hari_kerja);
        $this->assertSame('2026-04-13', $pengajuanBudi->tenggat_waktu->toDateString());

        // C. Petugas mengubah SLA ke 14 hari kerja -> tenggat Budi yang sudah berjalan TIDAK berubah!
        $this->actingAs($petugas)->put('/petugas/perkiraan-waktu/keberatan', [
            'pilihan_sla' => '14',
        ]);
        $pengajuanBudi->refresh();
        $this->assertSame(5, $pengajuanBudi->sla_hari_kerja);
        $this->assertSame('2026-04-13', $pengajuanBudi->tenggat_waktu->toDateString());

        // D. Pada Jumat 10 April 2026 (sisa 1 hari kerja / 80% terpakai) -> muncul badge "Mendekati batas waktu"
        Carbon::setTestNow(Carbon::parse('2026-04-10 10:00:00'));
        $this->actingAs($budi)
            ->get('/keberatan/' . $pengajuanBudi->id)
            ->assertStatus(200)
            ->assertSee('Perkiraan selesai:')
            ->assertSee('sisa 1 hari')
            ->assertSee('Mendekati batas waktu');

        // Jalankan scheduler harian -> petugas & pimpinan menerima notifikasi mendekati batas waktu
        Artisan::call('sppk:cek-sla-keberatan');
        $this->assertNotNull($pengajuanBudi->fresh()->notifikasi_mendekati_dikirim_pada);
        $this->assertGreaterThan(0, $petugas->notifications()->count());
        $this->assertGreaterThan(0, $pimpinan->notifications()->count());

        // E. Pada Rabu 15 April 2026 (lewat 2 hari kerja dari tenggat 13 April 2026) -> badge "Terlewat 2 hari"
        Carbon::setTestNow(Carbon::parse('2026-04-15 10:00:00'));
        $this->actingAs($budi)
            ->get('/keberatan/' . $pengajuanBudi->id)
            ->assertStatus(200)
            ->assertSee('Terlewat 2 hari');

        // Di daftar petugas, pengajuan Budi yang terlewat tampil paling atas & bisa difilter status_tenggat=terlewat
        $responsePetugasList = $this->actingAs($petugas)->get('/petugas/keberatan');
        $responsePetugasList->assertStatus(200)
            ->assertSeeInOrder(['#' . $pengajuanBudi->id, '#' . $pengajuanTanpaSla->id]);

        $this->actingAs($petugas)
            ->get('/petugas/keberatan?status_tenggat=terlewat')
            ->assertStatus(200)
            ->assertSee('#' . $pengajuanBudi->id)
            ->assertDontSee('#' . $pengajuanTanpaSla->id);

        // Jalankan scheduler harian saat terlewat -> mahasiswa diberi tahu & status diperbarui ke Dalam Peninjauan
        Artisan::call('sppk:cek-sla-keberatan');
        $pengajuanBudi->refresh();
        $this->assertSame(ObjectionService::STATUS_DALAM_PENINJAUAN, $pengajuanBudi->status);
        $this->assertNotNull($pengajuanBudi->notifikasi_terlewat_dikirim_pada);
        Mail::assertQueued(StatusUktBerubahMail::class);

        // F. Panel SLA di /pimpinan/rekap menampilkan tugas terlewat dengan tanda "Perlu tindak lanjut pimpinan" tanpa data pribadi
        $responsePimpinan = $this->actingAs($pimpinan)->get('/pimpinan/rekap');
        $responsePimpinan->assertStatus(200)
            ->assertSee('Panel Pemantauan Tenggat Waktu Peninjauan Keberatan')
            ->assertSee('#' . $pengajuanBudi->id)
            ->assertSee('Terlewat 2 hari')
            ->assertSee('Perlu tindak lanjut pimpinan')
            ->assertDontSee('Budi Pratama')
            ->assertDontSee('531412001')
            ->assertDontSee('pensiun per Maret 2026');

        Carbon::setTestNow();
    }

    public function test_simulasi_what_if_pada_halaman_hasil_deterministik_isolasi_satu_faktor_validasi_dan_tidak_mengubah_database(): void
    {
        $budi = User::where('email', 'budi.pratama@student.ung.ac.id')->firstOrFail();
        $ayu = User::where('email', 'ayu.lestari@student.ung.ac.id')->firstOrFail();

        $hasilBudiAwal = $budi->mahasiswa->hasilTerbaru;
        $jumlahHasilAwal = \App\Models\HasilUkt::count();
        $jumlahRincianAwal = \App\Models\RincianPenilaian::count();

        // 1. Halaman /hasil-saya memuat bagian "Coba Kalau Dataku Berbeda", label wajib, 5 faktor dengan bobot, dan tautan KF-08 & KF-07
        $responseAwal = $this->actingAs($budi)->get('/hasil-saya');
        $responseAwal->assertStatus(200)
            ->assertSee('Coba Kalau Dataku Berbeda')
            ->assertSee('Simulasi what-if — bukan penetapan resmi, mengubah di sini tidak mengubah datamu')
            ->assertSee('Penghasilan Gabungan Orang Tua / Wali (35%)')
            ->assertSee('Jumlah Tanggungan Keluarga (20%)')
            ->assertSee('Daya Listrik Rumah Tangga (15%)')
            ->assertSee('Status Kepemilikan Tempat Tinggal (15%)')
            ->assertSee('Kepemilikan Kendaraan dan Aset Produktif (15%)')
            ->assertSee(route('mahasiswa.pertanyaan.index'))
            ->assertSee(route('mahasiswa.keberatan.create'));

        $whatIfAwal = $responseAwal->viewData('whatIf');
        $this->assertNotNull($whatIfAwal);
        $this->assertSame(0, $whatIfAwal['jumlah_faktor_diubah']);
        $this->assertSame(3.05, $whatIfAwal['skor_total_asli']);
        $this->assertSame(3.05, $whatIfAwal['skor_total_what_if']);
        $this->assertSame(0.0, $whatIfAwal['selisih_total']);

        // 2. Input sama menghasilkan output sama (determinisme)
        $masukanSamaBudi = [
            'penghasilan_gabungan' => 'Penghasilan menengah (Rp 3.000.001 – Rp 5.000.000)',
            'jumlah_tanggungan' => 'Tanggungan sedikit (2 orang)',
            'daya_listrik' => 'Daya listrik rumah tangga menengah (1.300 VA)',
            'status_tempat_tinggal' => 'Rumah milik sendiri kategori sederhana',
            'kepemilikan_aset' => 'Satu kendaraan roda dua untuk mobilitas harian',
        ];

        $this->actingAs($budi)->post('/hasil-saya', $masukanSamaBudi)->assertRedirect();
        $whatIfRun1 = $this->actingAs($budi)->get('/hasil-saya')->viewData('whatIf');

        $this->actingAs($budi)->post('/hasil-saya', $masukanSamaBudi)->assertRedirect();
        $whatIfRun2 = $this->actingAs($budi)->get('/hasil-saya')->viewData('whatIf');

        $this->assertSame($whatIfRun1['skor_total_what_if'], $whatIfRun2['skor_total_what_if']);
        $this->assertSame($whatIfRun1['perbandingan_komponen'], $whatIfRun2['perbandingan_komponen']);

        // 3. Ubah 1 faktor (penghasilan_gabungan ke kategori terendah) -> HANYA komponen itu yang berubah!
        $masukanUbahSatu = $masukanSamaBudi;
        $masukanUbahSatu['penghasilan_gabungan'] = 'Penghasilan sangat rendah (≤ Rp 1.500.000)';

        $this->actingAs($budi)->post('/hasil-saya', $masukanUbahSatu)->assertRedirect();
        $responseSetelahUbah = $this->actingAs($budi)->get('/hasil-saya');
        $responseSetelahUbah->assertStatus(200)
            ->assertSee('Selisih Total: -0,70 poin')
            ->assertSee('Selisih Komponen: -0,70 poin');

        $whatIfSetelahUbah = $responseSetelahUbah->viewData('whatIf');
        $this->assertSame(1, $whatIfSetelahUbah['jumlah_faktor_diubah']);
        $this->assertSame(3.05, $whatIfSetelahUbah['skor_total_asli']);
        $this->assertSame(2.35, $whatIfSetelahUbah['skor_total_what_if']);
        $this->assertSame(-0.70, $whatIfSetelahUbah['selisih_total']);

        $komponenBaselineByKode = collect($whatIfRun1['perbandingan_komponen'])->keyBy('kode_faktor');
        foreach ($whatIfSetelahUbah['perbandingan_komponen'] as $komp) {
            $kode = $komp['kode_faktor'];
            $baseline = $komponenBaselineByKode[$kode];

            if ($kode === 'penghasilan_gabungan') {
                $this->assertTrue($komp['apakah_diubah']);
                $this->assertSame(1.05, $komp['poin_asli']);
                $this->assertSame(0.35, $komp['poin_what_if']);
                $this->assertSame(-0.70, $komp['selisih_poin']);
                $this->assertNotSame($baseline['langkah_what_if'], $komp['langkah_what_if']);
                $this->assertNotSame($baseline['alasan_what_if'], $komp['alasan_what_if']);
            } else {
                // 4 komponen lainnya wajib tidak berubah sama sekali
                $this->assertFalse($komp['apakah_diubah'], "Komponen {$kode} seharusnya tidak berubah.");
                $this->assertSame(0.0, $komp['selisih_poin'], "Selisih poin {$kode} seharusnya 0.");
                $this->assertSame($baseline['poin_what_if'], $komp['poin_what_if']);
                $this->assertSame($baseline['masukan_what_if'], $komp['masukan_what_if']);
                $this->assertSame($baseline['langkah_what_if'], $komp['langkah_what_if']);
                $this->assertSame($baseline['alasan_what_if'], $komp['alasan_what_if']);
            }
        }

        // 4. Hasil asli dan data mahasiswa di database TIDAK berubah sama sekali
        $this->assertSame($jumlahHasilAwal, \App\Models\HasilUkt::count());
        $this->assertSame($jumlahRincianAwal, \App\Models\RincianPenilaian::count());
        $hasilBudiSegar = $hasilBudiAwal->fresh(['rincianPenilaian']);
        $this->assertSame(3.05, (float) $hasilBudiSegar->skor_total);
        $this->assertSame(2500000, (int) $hasilBudiSegar->nominal);
        $this->assertSame(4500000, $budi->mahasiswa->fresh()->data_masukan['penghasilan_gabungan']);

        // 5. Validasi di server: masukan tidak lengkap atau di luar kategori ditolak dengan pesan jelas
        $masukanTidakLengkap = $masukanSamaBudi;
        $masukanTidakLengkap['jumlah_tanggungan'] = '';

        $this->actingAs($budi)
            ->from('/hasil-saya')
            ->post('/hasil-saya', $masukanTidakLengkap)
            ->assertRedirect('/hasil-saya#simulasi-what-if')
            ->assertSessionHasErrors('jumlah_tanggungan');

        $pesanErrorKurang = session('errors')->first('jumlah_tanggungan');
        $this->assertStringContainsString('Jumlah Tanggungan Keluarga', $pesanErrorKurang);

        $masukanDiLuarKategori = $masukanSamaBudi;
        $masukanDiLuarKategori['daya_listrik'] = '9999 VA angka bebas tidak valid';

        $this->actingAs($budi)
            ->from('/hasil-saya')
            ->post('/hasil-saya', $masukanDiLuarKategori)
            ->assertRedirect('/hasil-saya#simulasi-what-if')
            ->assertSessionHasErrors('daya_listrik');

        $pesanErrorKategori = session('errors')->first('daya_listrik');
        $this->assertStringContainsString('di luar kategori aturan penilaian', $pesanErrorKategori);

        // 6. Akun Ayu (hasil simulasi) tetap menampilkan #footer-status-simulasi dan bagian What-If
        $this->actingAs($ayu)
            ->get('/hasil-saya')
            ->assertStatus(200)
            ->assertSee('Coba Kalau Dataku Berbeda')
            ->assertSee('id="footer-status-simulasi"', false);
    }
}

