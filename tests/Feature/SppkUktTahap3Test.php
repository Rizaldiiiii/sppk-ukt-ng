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
}

