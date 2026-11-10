<?php

namespace Tests\Feature;

use App\Mail\StatusUktBerubahMail;
use App\Models\AuditLog;
use App\Models\DokumenKeberatan;
use App\Models\HasilUkt;
use App\Models\Mahasiswa;
use App\Models\PengajuanKeberatan;
use App\Models\User;
use App\Services\ObjectionService;
use App\Services\RuleEngineService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use RuntimeException;
use Tests\TestCase;

class SppkUktTahap1Test extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    public function test_semua_rute_publik_bisa_dibuka_tanpa_login_dan_tidak_membocorkan_data_pribadi(): void
    {
        $rutePublik = [
            '/',
            '/alur',
            '/faktor',
            '/golongan',
            '/prosedur-keberatan',
            '/panduan',
            '/bantuan',
        ];

        foreach ($rutePublik as $url) {
            $response = $this->get($url);
            $response->assertStatus(200);

            // Pastikan tidak ada nama atau NIM mahasiswa pribadi di respons publik
            $response->assertDontSee('Budi Pratama');
            $response->assertDontSee('Ayu Lestari');
            $response->assertDontSee('531412001');
            $response->assertDontSee('531412002');
        }

        // KF-03: Daftar faktor menampilkan 5 faktor tanpa kolom/badge status konfirmasi sumber di halaman publik
        $this->get('/faktor')
            ->assertSee('Penghasilan Gabungan Orang Tua / Wali')
            ->assertDontSee('Status Sumber')
            ->assertDontSee('Terkonfirmasi resmi');

        // KF-14: Halaman golongan menampilkan semua golongan beserta sumbernya
        $this->get('/golongan')
            ->assertSee('Golongan I')
            ->assertSee('Golongan IV')
            ->assertSee('Golongan VIII')
            ->assertSee('Rp 2.250.000');

        // KF-06: Halaman prosedur keberatan menautkan ke login saat belum login
        $this->get('/prosedur-keberatan')
            ->assertSee('Masuk untuk Mengajukan Keberatan')
            ->assertSee(route('login'));
    }

    public function test_budi_melihat_hasil_resmi_dan_ditolak_403_saat_membuka_data_ayu_lewat_url_langsung(): void
    {
        $budi = User::where('email', 'budi.pratama@student.ung.ac.id')->firstOrFail();
        $ayu = User::where('email', 'ayu.lestari@student.ung.ac.id')->firstOrFail();

        $hasilBudi = HasilUkt::where('mahasiswa_id', $budi->mahasiswa->id)->firstOrFail();
        $hasilAyu = HasilUkt::where('mahasiswa_id', $ayu->mahasiswa->id)->firstOrFail();

        // Budi membuka hasil miliknya sendiri (/hasil-saya)
        $response = $this->actingAs($budi)->get('/hasil-saya');
        $response->assertStatus(200);
        $response->assertSee('Hasil penetapan resmi kampus');
        $response->assertSee('Golongan IV');
        $response->assertSee('Rp 2.500.000');
        $response->assertDontSee('id="footer-status-simulasi"', false);

        // Semua 5 komponen tampil dengan kalimat alasan yang berbeda-beda (KF-01 & KF-04)
        $alasanUnik = $hasilBudi->rincianPenilaian->pluck('kalimat_alasan')->unique();
        $this->assertCount(5, $alasanUnik);
        foreach ($alasanUnik as $kalimat) {
            $response->assertSee($kalimat);
        }

        // Budi mencoba membuka hasil milik Ayu lewat URL langsung /hasil-saya/{id_ayu} -> harus 403 & tercatat di audit_logs
        $this->actingAs($budi)
            ->get('/hasil-saya/'.$hasilAyu->id)
            ->assertStatus(403);

        $this->assertDatabaseHas('audit_logs', [
            'pelaku_id' => $budi->id,
            'aksi' => 'penolakan_akses',
            'subjek_tipe' => HasilUkt::class,
            'subjek_id' => (string) $hasilAyu->id,
        ]);

        // Budi mencoba membuka hasil Ayu lewat manipulasi query ?nim=531412002 -> harus 403 & tercatat
        $this->actingAs($budi)
            ->get('/hasil-saya?nim=531412002')
            ->assertStatus(403);
    }

    public function test_ayu_melihat_footer_simulasi_seeder_dan_citra_melihat_empty_state(): void
    {
        $ayu = User::where('email', 'ayu.lestari@student.ung.ac.id')->firstOrFail();
        $citra = User::where('email', 'citra.kirana@student.ung.ac.id')->firstOrFail();

        // Ayu (simulasi): wajib tampil footer penjelasan seeder/simulasi, bukan penetapan resmi
        $responseAyu = $this->actingAs($ayu)->get('/hasil-saya');
        $responseAyu->assertStatus(200);
        $responseAyu->assertSee('id="footer-status-simulasi"', false);
        $responseAyu->assertSee('Data pada halaman ini adalah seeder/simulasi');
        $responseAyu->assertSee('bukan penetapan resmi kampus');
        $responseAyu->assertDontSee('Hasil penetapan resmi kampus');

        // Citra (tanpa hasil): wajib tampil empty state, bukan tabel kosong
        $responseCitra = $this->actingAs($citra)->get('/hasil-saya');
        $responseCitra->assertStatus(200);
        $responseCitra->assertSee('Hasil Penilaian UKT Anda Belum Tersedia');
        $responseCitra->assertDontSee('Tabel Rincian Komponen Penilaian');
    }

    public function test_pembatasan_peran_dan_rekap_agregat_pimpinan_tanpa_data_perorangan(): void
    {
        $budi = User::where('email', 'budi.pratama@student.ung.ac.id')->firstOrFail();
        $pimpinan = User::where('email', 'pimpinan@ung.ac.id')->firstOrFail();
        $hasilBudi = HasilUkt::where('mahasiswa_id', $budi->mahasiswa->id)->firstOrFail();

        // Mahasiswa mencoba mengakses halaman petugas dan pimpinan -> 403 & tercatat
        $this->actingAs($budi)->get('/petugas/beranda')->assertStatus(403);
        $this->actingAs($budi)->get('/pimpinan/rekap')->assertStatus(403);

        // Pimpinan mengakses rekap agregat -> 200 OK, tanpa nama/NIM/data ekonomi mahasiswa
        $rekapResponse = $this->actingAs($pimpinan)->get('/pimpinan/rekap');
        $rekapResponse->assertStatus(200);
        $rekapResponse->assertSee('Rekapitulasi Agregat UKT');
        $rekapResponse->assertDontSee('Budi Pratama');
        $rekapResponse->assertDontSee('Ayu Lestari');
        $rekapResponse->assertDontSee('531412001');
        $rekapResponse->assertDontSee('531412002');

        // Pimpinan mencoba mengakses halaman petugas atau rincian hasil mahasiswa -> 403 & tercatat
        $this->actingAs($pimpinan)->get('/petugas/parameter')->assertStatus(403);
        $this->actingAs($pimpinan)->get('/hasil-saya/'.$hasilBudi->id)->assertStatus(403);
    }

    public function test_rule_engine_deterministik_validasi_masukan_dan_versi_parameter_tidak_mengubah_hasil_lama(): void
    {
        /** @var RuleEngineService $ruleEngine */
        $ruleEngine = app(RuleEngineService::class);
        $petugas = User::where('email', 'petugas@ung.ac.id')->firstOrFail();
        $ayu = User::where('email', 'ayu.lestari@student.ung.ac.id')->firstOrFail();
        $citra = User::where('email', 'citra.kirana@student.ung.ac.id')->firstOrFail();

        $hasilLamaAyu = HasilUkt::with('rincianPenilaian')->where('mahasiswa_id', $ayu->mahasiswa->id)->firstOrFail();
        $skorLamaAyu = $hasilLamaAyu->skor_total;
        $versiLamaId = $hasilLamaAyu->versi_parameter_id;
        $rincianLamaSnapshot = $hasilLamaAyu->rincianPenilaian->pluck('nilai_komponen', 'faktor_id')->toArray();

        // 1. Deterministik: masukan sama + versi sama -> keluaran identik
        $masukanUji = [
            'penghasilan_gabungan' => 2400000,
            'jumlah_tanggungan' => 4,
            'daya_listrik' => 900,
            'status_tempat_tinggal' => 'Menumpang / Kontrak',
            'kepemilikan_aset' => '1 kendaraan roda dua',
        ];
        $eval1 = $ruleEngine->hitung($masukanUji);
        $eval2 = $ruleEngine->hitung($masukanUji);
        $this->assertSame($eval1['skor_total'], $eval2['skor_total']);
        $this->assertSame($eval1['rincian'], $eval2['rincian']);

        // 2. Masukan tidak lengkap -> tidak mengarang nilai, mengembalikan status tidak_bisa_dihitung
        $evalKurang = $ruleEngine->hitung(['penghasilan_gabungan' => 2400000]);
        $this->assertSame('tidak_bisa_dihitung', $evalKurang['status']);
        $this->assertNotEmpty($evalKurang['faktor_kurang']);

        // 3. Petugas menerbitkan versi parameter baru lewat HTTP POST /petugas/parameter
        $versiAktif = $ruleEngine->versiAktif();
        $bobotBaru = [];
        foreach ($versiAktif->parameterPenilaian as $index => $param) {
            // Ubah distribusi bobot secara signifikan pada versi baru
            $bobotBaru[$param->faktor_id] = $index === 0 ? 60 : 10;
        }

        $this->actingAs($petugas)
            ->post('/petugas/parameter', [
                'versi' => 'v2.0-simulasi',
                'berlaku_mulai' => '2026-06-01',
                'catatan' => 'Perubahan bobot pengujian keterlacakan versi',
                'bobot' => $bobotBaru,
            ])
            ->assertRedirect(route('petugas.parameter.index'));

        // Pastikan hasil lama Ayu TIDAK berubah sama sekali (versi_parameter_id, skor_total, dan rincian tetap utuh)
        $hasilLamaAyu->refresh()->load('rincianPenilaian');
        $this->assertSame($versiLamaId, $hasilLamaAyu->versi_parameter_id);
        $this->assertSame($skorLamaAyu, $hasilLamaAyu->skor_total);
        $this->assertSame($rincianLamaSnapshot, $hasilLamaAyu->rincianPenilaian->pluck('nilai_komponen', 'faktor_id')->toArray());

        // Pastikan hasil baru untuk Citra memakai versi parameter baru (v2.0-simulasi)
        $hasilBaruCitra = $ruleEngine->simpanHasil(
            mahasiswa: $citra->mahasiswa,
            masukan: $masukanUji,
            sumber: 'simulasi',
            ditetapkanOleh: $petugas
        );

        $this->assertNotSame($versiLamaId, $hasilBaruCitra->versi_parameter_id);
        $this->assertSame('v2.0-simulasi', $hasilBaruCitra->versiParameter->versi);
    }

    public function test_perubahan_status_menghasilkan_notifikasi_dan_antrean_email_tanpa_data_ekonomi_serta_tahan_kegagalan_email(): void
    {
        Mail::fake();

        $petugas = User::where('email', 'petugas@ung.ac.id')->firstOrFail();
        $budi = User::where('email', 'budi.pratama@student.ung.ac.id')->firstOrFail();

        $pengajuan = PengajuanKeberatan::create([
            'mahasiswa_id' => $budi->mahasiswa->id,
            'hasil_id' => $budi->mahasiswa->hasilTerbaru?->id,
            'alasan' => 'Penurunan penghasilan orang tua karena pensiun.',
            'status' => ObjectionService::STATUS_DIAJUKAN,
            'diajukan_pada' => now(),
        ]);

        // 1. Petugas mengubah status -> notifikasi sistem + antrean email terkirim
        $this->actingAs($petugas)
            ->patch('/petugas/keberatan/'.$pengajuan->id.'/status', [
                'status' => ObjectionService::STATUS_DALAM_PENINJAUAN,
                'catatan_petugas' => 'Berkas sedang diperiksa tim verifikasi.',
            ])
            ->assertRedirect();

        $this->assertDatabaseHas('pengajuan_keberatan', [
            'id' => $pengajuan->id,
            'status' => ObjectionService::STATUS_DALAM_PENINJAUAN,
        ]);

        Mail::assertQueued(StatusUktBerubahMail::class, function (StatusUktBerubahMail $mail) use ($budi) {
            $rendered = $mail->render();
            // Pastikan tidak memuat data ekonomi, NIM, atau kata sandi di isi email
            $this->assertStringNotContainsString('4.500.000', $rendered);
            $this->assertStringNotContainsString('Mahasiswa123!', $rendered);
            $this->assertStringContainsString(ObjectionService::STATUS_DALAM_PENINJAUAN, $rendered);

            return $mail->hasTo($budi->email);
        });

        // 2. Simulasikan kegagalan antrean email -> status tetap berubah & kegagalan tercatat di audit_logs
        Mail::shouldReceive('to')->andThrow(new RuntimeException('Simulasi gangguan koneksi SMTP'));

        /** @var ObjectionService $objectionService */
        $objectionService = app(ObjectionService::class);
        $objectionService->ubahStatus(
            pengajuan: $pengajuan,
            petugas: $petugas,
            statusBaru: ObjectionService::STATUS_PERLU_PERBAIKAN,
            catatanPetugas: 'Mohon unggah ulang surat keterangan penghasilan terbaru.'
        );

        // Status tetap berubah menjadi Perlu Perbaikan meskipun email gagal
        $this->assertDatabaseHas('pengajuan_keberatan', [
            'id' => $pengajuan->id,
            'status' => ObjectionService::STATUS_PERLU_PERBAIKAN,
        ]);

        // Kegagalan pengiriman email tercatat di audit_logs agar terlihat oleh petugas
        $this->assertDatabaseHas('audit_logs', [
            'pelaku_id' => $petugas->id,
            'aksi' => 'email_notifikasi_gagal',
            'subjek_tipe' => PengajuanKeberatan::class,
            'subjek_id' => (string) $pengajuan->id,
        ]);
    }

    public function test_dokumen_keberatan_hanya_bisa_dibuka_lewat_controller_berotorisasi(): void
    {
        Storage::fake('local');

        $budi = User::where('email', 'budi.pratama@student.ung.ac.id')->firstOrFail();
        $ayu = User::where('email', 'ayu.lestari@student.ung.ac.id')->firstOrFail();
        $petugas = User::where('email', 'petugas@ung.ac.id')->firstOrFail();
        $pimpinan = User::where('email', 'pimpinan@ung.ac.id')->firstOrFail();

        $pathPrivat = 'dokumen-keberatan/bukti-slip-budi-acak987.pdf';
        Storage::disk('local')->put($pathPrivat, 'ISI-DOKUMEN-PRIVAT-BUDI');

        $pengajuanBudi = PengajuanKeberatan::create([
            'mahasiswa_id' => $budi->mahasiswa->id,
            'alasan' => 'Pengajuan keberatan Budi',
            'status' => 'Diajukan',
            'diajukan_pada' => now(),
        ]);

        $dokumen = DokumenKeberatan::create([
            'pengajuan_id' => $pengajuanBudi->id,
            'path_penyimpanan' => $pathPrivat,
            'nama_asli' => 'slip-gaji-orangtua.pdf',
            'mime' => 'application/pdf',
            'ukuran' => 1024,
            'diunggah_oleh' => $budi->id,
        ]);

        // Pengunjung tanpa login -> dialihkan ke login
        $this->get('/dokumen-keberatan/'.$dokumen->id)->assertRedirect(route('login'));

        // Mahasiswa pemilik (Budi) -> diizinkan (200 OK)
        $this->actingAs($budi)->get('/dokumen-keberatan/'.$dokumen->id)->assertStatus(200);

        // Petugas UKT -> diizinkan (200 OK)
        $this->actingAs($petugas)->get('/dokumen-keberatan/'.$dokumen->id)->assertStatus(200);

        // Mahasiswa lain (Ayu) -> ditolak 403 & tercatat di audit_logs
        $this->actingAs($ayu)->get('/dokumen-keberatan/'.$dokumen->id)->assertStatus(403);
        $this->assertDatabaseHas('audit_logs', [
            'pelaku_id' => $ayu->id,
            'aksi' => 'penolakan_akses',
            'subjek_tipe' => DokumenKeberatan::class,
            'subjek_id' => (string) $dokumen->id,
        ]);

        // Pimpinan -> ditolak 403 & tercatat di audit_logs
        $this->actingAs($pimpinan)->get('/dokumen-keberatan/'.$dokumen->id)->assertStatus(403);
    }
}

