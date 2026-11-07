<?php

namespace Database\Seeders;

use App\Models\FaktorPenilaian;
use App\Models\GolonganUkt;
use App\Models\KontenPublik;
use App\Models\Mahasiswa;
use App\Models\ParameterPenilaian;
use App\Models\User;
use App\Models\VersiParameter;
use App\Services\RuleEngineService;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed data contoh SPPK-UKT UNG Tahap 1 sesuai docs/akun-contoh-tahap1.md, PRD.md, dan blueprint.md.
     */
    public function run(): void
    {
        // 1. Akun Contoh (docs/akun-contoh-tahap1.md)
        $petugas = User::updateOrCreate(
            ['email' => 'petugas@ung.ac.id'],
            [
                'name' => 'Dewi Petugas',
                'peran' => User::PERAN_PETUGAS,
                'password' => Hash::make('Petugas123!'),
                'email_verified_at' => now(),
            ]
        );

        $pimpinan = User::updateOrCreate(
            ['email' => 'pimpinan@ung.ac.id'],
            [
                'name' => 'Pimpinan UNG',
                'peran' => User::PERAN_PIMPINAN,
                'password' => Hash::make('Pimpinan123!'),
                'email_verified_at' => now(),
            ]
        );

        $userBudi = User::updateOrCreate(
            ['email' => 'budi.pratama@student.ung.ac.id'],
            [
                'name' => 'Budi Pratama',
                'peran' => User::PERAN_MAHASISWA,
                'password' => Hash::make('Mahasiswa123!'),
                'email_verified_at' => now(),
            ]
        );

        $userAyu = User::updateOrCreate(
            ['email' => 'ayu.lestari@student.ung.ac.id'],
            [
                'name' => 'Ayu Lestari',
                'peran' => User::PERAN_MAHASISWA,
                'password' => Hash::make('Mahasiswa123!'),
                'email_verified_at' => now(),
            ]
        );

        $userCitra = User::updateOrCreate(
            ['email' => 'citra.kirana@student.ung.ac.id'],
            [
                'name' => 'Citra Kirana',
                'peran' => User::PERAN_MAHASISWA,
                'password' => Hash::make('Mahasiswa123!'),
                'email_verified_at' => now(),
            ]
        );

        // Profil Mahasiswa
        $masukanBudi = [
            'penghasilan_gabungan' => 4500000,
            'jumlah_tanggungan' => 2,
            'daya_listrik' => 1300,
            'status_tempat_tinggal' => 'Milik sendiri sederhana',
            'kepemilikan_aset' => '1 kendaraan roda dua & 1 kendaraan roda empat sederhana',
        ];

        $mahasiswaBudi = Mahasiswa::updateOrCreate(
            ['user_id' => $userBudi->id],
            [
                'nim' => '531412001',
                'program_studi' => 'Informatika',
                'angkatan' => 2024,
                'data_masukan' => $masukanBudi,
            ]
        );

        $masukanAyu = [
            'penghasilan_gabungan' => 2400000,
            'jumlah_tanggungan' => 4,
            'daya_listrik' => 900,
            'status_tempat_tinggal' => 'Menumpang / Kontrak',
            'kepemilikan_aset' => '1 kendaraan roda dua',
        ];

        $mahasiswaAyu = Mahasiswa::updateOrCreate(
            ['user_id' => $userAyu->id],
            [
                'nim' => '531412002',
                'program_studi' => 'Sistem Informasi',
                'angkatan' => 2024,
                'data_masukan' => $masukanAyu,
            ]
        );

        Mahasiswa::updateOrCreate(
            ['user_id' => $userCitra->id],
            [
                'nim' => '531412003',
                'program_studi' => 'Pendidikan Teknologi Informasi',
                'angkatan' => 2025,
                'data_masukan' => null,
            ]
        );

        // 2. Rentang Golongan UKT (KF-14)
        $daftarGolonganData = [
            [
                'kode' => 'GOL-I',
                'nama' => 'Golongan I',
                'nominal_min' => 500000,
                'nominal_max' => 500000,
                'sumber' => 'Ketentuan Standar Batas Bawah UKT PTN (Data Contoh Seeder Tahap 1)',
                'berlaku_mulai' => '2026-01-01',
            ],
            [
                'kode' => 'GOL-II',
                'nama' => 'Golongan II',
                'nominal_min' => 1000000,
                'nominal_max' => 1000000,
                'sumber' => 'Ketentuan Standar Batas Bawah UKT PTN (Data Contoh Seeder Tahap 1)',
                'berlaku_mulai' => '2026-01-01',
            ],
            [
                'kode' => 'GOL-III',
                'nama' => 'Golongan III',
                'nominal_min' => 1500000,
                'nominal_max' => 2000000,
                'sumber' => 'Referensi Rentang UKT Rumpun Saintek & Sosial Humaniora UNG (Data Contoh Seeder)',
                'berlaku_mulai' => '2026-01-01',
            ],
            [
                'kode' => 'GOL-IV',
                'nama' => 'Golongan IV',
                'nominal_min' => 2250000,
                'nominal_max' => 2750000,
                'sumber' => 'Referensi Rentang UKT Rumpun Saintek & Sosial Humaniora UNG (Data Contoh Seeder)',
                'berlaku_mulai' => '2026-01-01',
            ],
            [
                'kode' => 'GOL-V',
                'nama' => 'Golongan V',
                'nominal_min' => 3000000,
                'nominal_max' => 3500000,
                'sumber' => 'Referensi Rentang UKT Rumpun Saintek & Sosial Humaniora UNG (Data Contoh Seeder)',
                'berlaku_mulai' => '2026-01-01',
            ],
            [
                'kode' => 'GOL-VI',
                'nama' => 'Golongan VI',
                'nominal_min' => 3750000,
                'nominal_max' => 4500000,
                'sumber' => 'Referensi Rentang UKT Rumpun Saintek & Sosial Humaniora UNG (Data Contoh Seeder)',
                'berlaku_mulai' => '2026-01-01',
            ],
            [
                'kode' => 'GOL-VII',
                'nama' => 'Golongan VII',
                'nominal_min' => 4750000,
                'nominal_max' => 5500000,
                'sumber' => 'Referensi Rentang UKT Rumpun Saintek & Sosial Humaniora UNG (Data Contoh Seeder)',
                'berlaku_mulai' => '2026-01-01',
            ],
            [
                'kode' => 'GOL-VIII',
                'nama' => 'Golongan VIII',
                'nominal_min' => 6000000,
                'nominal_max' => 7500000,
                'sumber' => 'Referensi Rentang UKT Rumpun Saintek & Sosial Humaniora UNG (Data Contoh Seeder)',
                'berlaku_mulai' => '2026-01-01',
            ],
        ];

        $mapGolongan = [];
        foreach ($daftarGolonganData as $g) {
            $mapGolongan[$g['kode']] = GolonganUkt::updateOrCreate(
                ['kode' => $g['kode']],
                array_merge($g, ['aktif' => true])
            );
        }

        // 3. Faktor Penilaian (KF-03)
        $daftarFaktorData = [
            [
                'kode' => 'penghasilan_gabungan',
                'nama' => 'Penghasilan Gabungan Orang Tua / Wali',
                'penjelasan_singkat' => 'Jumlah seluruh pendapatan kotor bulanan ayah dan ibu (atau wali yang menanggung biaya kuliah mahasiswa) dari pekerjaan utama maupun tambahan.',
                'pengaruh_umum' => 'Semakin tinggi penghasilan gabungan bulanan keluarga, semakin besar kemampuan ekonomi yang terukur sehingga mengarah pada kelompok golongan UKT yang lebih tinggi.',
                'satuan' => 'rupiah',
                'status_konfirmasi' => 'terkonfirmasi',
                'urutan' => 1,
                'aktif' => true,
            ],
            [
                'kode' => 'jumlah_tanggungan',
                'nama' => 'Jumlah Tanggungan Keluarga',
                'penjelasan_singkat' => 'Banyaknya anggota keluarga dalam Kartu Keluarga yang masih dibiayai penuh oleh orang tua/wali, terutama anak yang sedang menempuh pendidikan.',
                'pengaruh_umum' => 'Semakin banyak jumlah tanggungan aktif dalam keluarga, beban pengeluaran rumah tangga semakin terbagi sehingga menjadi faktor pengurang beban komponen UKT.',
                'satuan' => 'orang',
                'status_konfirmasi' => 'terkonfirmasi',
                'urutan' => 2,
                'aktif' => true,
            ],
            [
                'kode' => 'daya_listrik',
                'nama' => 'Daya Listrik Rumah Tangga',
                'penjelasan_singkat' => 'Kapasitas daya listrik terpasang (dalam satuan Volt-Ampere / VA) pada tempat tinggal utama keluarga berdasarkan bukti tagihan atau token listrik.',
                'pengaruh_umum' => 'Kapasitas daya listrik dipakai sebagai indikator tidak langsung atas skala konsumsi utilitas rumah tangga sehari-hari.',
                'satuan' => 'va',
                'status_konfirmasi' => 'menunggu_konfirmasi',
                'urutan' => 3,
                'aktif' => true,
            ],
            [
                'kode' => 'status_tempat_tinggal',
                'nama' => 'Status Kepemilikan Tempat Tinggal',
                'penjelasan_singkat' => 'Kondisi kepemilikan hunian keluarga saat ini, seperti menumpang pada kerabat, menyewa/kontrak, rumah milik sendiri sederhana, atau rumah milik sendiri permanen.',
                'pengaruh_umum' => 'Keluarga yang masih menyewa atau menumpang memiliki beban biaya hunian rutin yang berbeda dibandingkan keluarga dengan aset tempat tinggal mapan.',
                'satuan' => 'kategori',
                'status_konfirmasi' => 'menunggu_konfirmasi',
                'urutan' => 4,
                'aktif' => true,
            ],
            [
                'kode' => 'kepemilikan_aset',
                'nama' => 'Kepemilikan Kendaraan dan Aset Produktif',
                'penjelasan_singkat' => 'Kepemilikan kendaraan bermotor (roda dua maupun roda empat) serta aset tambahan yang tercatat dalam dokumen pendukung keluarga.',
                'pengaruh_umum' => 'Menjadi indikator pelengkap untuk memastikan kesesuaian antara laporan penghasilan bulanan dengan profil kepemilikan aset keluarga.',
                'satuan' => 'kategori',
                'status_konfirmasi' => 'menunggu_konfirmasi',
                'urutan' => 5,
                'aktif' => true,
            ],
        ];

        $mapFaktor = [];
        foreach ($daftarFaktorData as $f) {
            $mapFaktor[$f['kode']] = FaktorPenilaian::updateOrCreate(
                ['kode' => $f['kode']],
                $f
            );
        }

        // 4. Versi Parameter Penilaian v1.0 (KF-05)
        $versiParameter = VersiParameter::updateOrCreate(
            ['versi' => 'v1.0-simulasi'],
            [
                'berlaku_mulai' => '2026-01-15',
                'dibuat_oleh' => $petugas->id,
                'catatan' => 'Parameter awal model penjelasan/simulasi Tahap 1 SPPK-UKT UNG (bukan formula resmi kampus; untuk keterlacakan langkah komponen).',
                'aktif' => true,
            ]
        );

        $aturanPerKode = [
            'penghasilan_gabungan' => [
                'tipe' => 'rentang_angka',
                'bobot' => 35,
                'penjelasan_aturan' => 'Mengukur tingkat kemampuan pendapatan bulanan keluarga dengan porsi pertimbangan 35%.',
                'rentang' => [
                    ['min' => 0, 'max' => 1500000, 'skor' => 1, 'label' => 'Penghasilan sangat rendah (≤ Rp 1.500.000)', 'dampak' => 'menunjukkan kapasitas pendapatan dasar sehingga meringankan komponen biaya'],
                    ['min' => 1500001, 'max' => 3000000, 'skor' => 2, 'label' => 'Penghasilan menengah bawah (Rp 1.500.001 – Rp 3.000.000)', 'dampak' => 'menunjukkan kemampuan ekonomi rumah tangga terbatas-menengah'],
                    ['min' => 3000001, 'max' => 5000000, 'skor' => 3, 'label' => 'Penghasilan menengah (Rp 3.000.001 – Rp 5.000.000)', 'dampak' => 'menunjukkan kemampuan ekonomi menengah yang stabil'],
                    ['min' => 5000001, 'max' => 8000000, 'skor' => 4, 'label' => 'Penghasilan menengah atas (Rp 5.000.001 – Rp 8.000.000)', 'dampak' => 'menunjukkan kapasitas pembiayaan pendidikan yang cukup kuat'],
                    ['min' => 8000001, 'max' => 999999999, 'skor' => 5, 'label' => 'Penghasilan tinggi (> Rp 8.000.000)', 'dampak' => 'menunjukkan kapasitas pembiayaan penuh pada kelompok atas'],
                ],
            ],
            'jumlah_tanggungan' => [
                'tipe' => 'rentang_angka',
                'bobot' => 20,
                'penjelasan_aturan' => 'Memperhitungkan jumlah tanggungan keluarga sebagai faktor pembagi beban rumah tangga dengan porsi 20%.',
                'rentang' => [
                    ['min' => 5, 'max' => 50, 'skor' => 1, 'label' => 'Tanggungan sangat banyak (≥ 5 orang)', 'dampak' => 'membagi beban pengeluaran keluarga secara signifikan sehingga menurunkan skor komponen'],
                    ['min' => 4, 'max' => 4, 'skor' => 2, 'label' => 'Tanggungan banyak (4 orang)', 'dampak' => 'menunjukkan beban biaya hidup dan pendidikan keluarga yang cukup besar'],
                    ['min' => 3, 'max' => 3, 'skor' => 3, 'label' => 'Tanggungan sedang (3 orang)', 'dampak' => 'menunjukkan proporsi tanggungan rumah tangga tingkat menengah'],
                    ['min' => 2, 'max' => 2, 'skor' => 4, 'label' => 'Tanggungan sedikit (2 orang)', 'dampak' => 'menunjukkan beban tanggungan keluarga yang relatif ringan'],
                    ['min' => 0, 'max' => 1, 'skor' => 5, 'label' => 'Tanggungan minimal (≤ 1 orang)', 'dampak' => 'menunjukkan ruang alokasi pendapatan keluarga yang lebih longgar'],
                ],
            ],
            'daya_listrik' => [
                'tipe' => 'rentang_angka',
                'bobot' => 15,
                'penjelasan_aturan' => 'Mengukur indikator kapasitas utilitas listrik tempat tinggal dengan porsi 15%.',
                'rentang' => [
                    ['min' => 0, 'max' => 450, 'skor' => 1, 'label' => 'Daya listrik subsidi (≤ 450 VA)', 'dampak' => 'mencerminkan penggunaan energi rumah tangga bersubsidi'],
                    ['min' => 451, 'max' => 900, 'skor' => 2, 'label' => 'Daya listrik rumah tangga dasar (900 VA)', 'dampak' => 'mencerminkan konsumsi utilitas rumah tangga skala dasar-menengah'],
                    ['min' => 901, 'max' => 1300, 'skor' => 3, 'label' => 'Daya listrik rumah tangga menengah (1.300 VA)', 'dampak' => 'mencerminkan kapasitas utilitas hunian standar perkotaan'],
                    ['min' => 1301, 'max' => 2200, 'skor' => 4, 'label' => 'Daya listrik menengah atas (2.200 VA)', 'dampak' => 'mencerminkan konsumsi utilitas rumah tangga di atas rata-rata'],
                    ['min' => 2201, 'max' => 999999, 'skor' => 5, 'label' => 'Daya listrik besar (> 2.200 VA)', 'dampak' => 'mencerminkan kapasitas fasilitas hunian skala besar'],
                ],
            ],
            'status_tempat_tinggal' => [
                'tipe' => 'kategori',
                'bobot' => 15,
                'penjelasan_aturan' => 'Menilai kondisi kepemilikan tempat tinggal keluarga dengan porsi 15%.',
                'rentang' => [
                    ['nilai' => 'Menumpang / Kontrak', 'skor' => 2, 'label' => 'Hunian menumpang atau kontrak sewa', 'dampak' => 'memperhitungkan adanya pengeluaran rutin sewa atau ketiadaan aset rumah tetap'],
                    ['nilai' => 'Milik sendiri sederhana', 'skor' => 3, 'label' => 'Rumah milik sendiri kategori sederhana', 'dampak' => 'menunjukkan kepemilikan tempat tinggal tetap dengan nilai aset standar'],
                    ['nilai' => 'Milik sendiri permanen', 'skor' => 4, 'label' => 'Rumah milik sendiri permanen/mapan', 'dampak' => 'menunjukkan kemapanan aset tempat tinggal keluarga'],
                ],
            ],
            'kepemilikan_aset' => [
                'tipe' => 'kategori',
                'bobot' => 15,
                'penjelasan_aturan' => 'Menilai profil kepemilikan kendaraan dan aset pendukung keluarga dengan porsi 15%.',
                'rentang' => [
                    ['nilai' => 'Tidak memiliki kendaraan bermotor', 'skor' => 1, 'label' => 'Tanpa kendaraan bermotor', 'dampak' => 'menunjukkan keterbatasan kepemilikan sarana transportasi keluarga'],
                    ['nilai' => '1 kendaraan roda dua', 'skor' => 2, 'label' => 'Satu kendaraan roda dua untuk mobilitas harian', 'dampak' => 'menunjukkan kepemilikan aset transportasi dasar rumah tangga'],
                    ['nilai' => '1 kendaraan roda dua & 1 kendaraan roda empat sederhana', 'skor' => 3, 'label' => 'Kepemilikan kendaraan roda dua dan roda empat keluarga', 'dampak' => 'menunjukkan ketersediaan aset bergerak tingkat menengah'],
                    ['nilai' => 'Lebih dari 1 kendaraan roda empat / aset produktif', 'skor' => 5, 'label' => 'Kepemilikan multi-kendaraan atau aset produktif', 'dampak' => 'menunjukkan profil aset keluarga yang mapan'],
                ],
            ],
        ];

        foreach ($aturanPerKode as $kodeFaktor => $aturan) {
            $faktor = $mapFaktor[$kodeFaktor];
            ParameterPenilaian::updateOrCreate(
                [
                    'versi_parameter_id' => $versiParameter->id,
                    'faktor_id' => $faktor->id,
                ],
                [
                    'aturan' => $aturan,
                ]
            );
        }

        // 5. Konten Publik (KF-02, KF-03, KF-06, Kontak)
        $daftarKontenPublik = [
            [
                'slug' => 'alur',
                'judul' => 'Alur Penentuan Golongan UKT dan Hubungan Data dengan Hasil',
                'ringkasan' => 'Penjelasan langkah demi langkah bagaimana data sosial-ekonomi yang Anda isi diproses menjadi komponen penilaian hingga ditetapkan oleh kampus.',
                'isi' => json_encode([
                    'tahapan' => [
                        [
                            'nomor' => 1,
                            'judul' => 'Pengisian Data Sosial-Ekonomi & Unggah Bukti',
                            'deskripsi' => 'Calon mahasiswa atau mahasiswa mengisi data kondisi ekonomi keluarga (penghasilan orang tua/wali, jumlah tanggungan, daya listrik, kondisi hunian, dan kepemilikan aset) beserta dokumen buktinya melalui saluran pendataan kampus.',
                            'hubungan_data' => 'Data mentah pada tahap ini menjadi masukan langsung untuk setiap baris faktor penilaian pada tabel rincian UKT.',
                        ],
                        [
                            'nomor' => 2,
                            'judul' => 'Pemeriksaan Kelengkapan & Verifikasi Dokumen oleh Petugas',
                            'deskripsi' => 'Petugas UKT memeriksa kesesuaian angka yang diisikan dengan bukti dokumen resmi (slip gaji/surat keterangan penghasilan, Kartu Keluarga, tagihan listrik, dan foto tempat tinggal).',
                            'hubungan_data' => 'Memastikan tidak ada komponen yang dihitung dari data yang keliru atau belum terverifikasi.',
                        ],
                        [
                            'nomor' => 3,
                            'judul' => 'Pengolahan Komponen Penilaian Berbasis Parameter',
                            'deskripsi' => 'Setiap variabel masukan dipetakan ke dalam kategori kontribusi masing-masing faktor untuk menghasilkan rincian nilai per komponen dan alasan tertulisnya.',
                            'hubungan_data' => 'Setiap komponen dihitung secara terpisah dan transparan sehingga mahasiswa dapat melihat faktor mana yang meringankan atau meningkatkan hasil penilaian.',
                        ],
                        [
                            'nomor' => 4,
                            'judul' => 'Penetapan Resmi Golongan UKT oleh Kampus',
                            'deskripsi' => 'Pihak kampus yang berwenang menetapkan golongan dan besaran nominal UKT per semester melalui keputusan resmi.',
                            'hubungan_data' => 'Hasil penetapan resmi ditampilkan kepada mahasiswa lengkap dengan tabel rincian komponen dan kalimat alasan di balik angka tersebut.',
                        ],
                        [
                            'nomor' => 5,
                            'judul' => 'Peninjauan Ulang / Pengajuan Keberatan (Bila Hasil Dirasa Tidak Sesuai)',
                            'deskripsi' => 'Apabila mahasiswa menilai golongan UKT belum mencerminkan kondisi ekonomi sebenarnya atau terjadi perubahan kondisi keluarga, mahasiswa dapat mengikuti prosedur pengajuan keberatan.',
                            'hubungan_data' => 'Keputusan peninjauan ulang dibuat oleh petugas berwenang berdasarkan bukti pembaruan dan tercatat dalam riwayat sistem.',
                        ],
                    ],
                ], JSON_UNESCAPED_UNICODE),
                'diperbarui_oleh' => $petugas->id,
                'diperbarui_pada' => now(),
            ],
            [
                'slug' => 'faktor',
                'judul' => 'Daftar Faktor dan Variabel Penentuan UKT',
                'ringkasan' => 'Penentuan UKT tidak hanya melihat penghasilan orang tua semata, melainkan menggabungkan beberapa indikator beban tanggungan dan kondisi rumah tangga secara menyeluruh.',
                'isi' => 'Halaman ini menampilkan seluruh faktor penilaian yang digunakan dalam model penjelasan SPPK-UKT UNG. Faktor yang belum memiliki konfirmasi tertulis dari regulasi terbuka kampus ditandai secara jujur dengan status "Menunggu konfirmasi resmi" agar mahasiswa mendapatkan informasi yang transparan.',
                'diperbarui_oleh' => $petugas->id,
                'diperbarui_pada' => now(),
            ],
            [
                'slug' => 'prosedur-keberatan',
                'judul' => 'Prosedur Pengajuan Keberatan dan Peninjauan Ulang UKT',
                'ringkasan' => 'Panduan resmi bagi mahasiswa Universitas Negeri Gorontalo yang ingin mengajukan peninjauan ulang atau keberatan atas penetapan golongan UKT.',
                'isi' => json_encode([
                    'siapa_boleh_mengajukan' => [
                        'Mahasiswa aktif S1 Universitas Negeri Gorontalo yang hasil penetapan UKT-nya dirasa tidak sebanding dengan kemampuan ekonomi keluarga.',
                        'Mahasiswa yang keluarganya mengalami penurunan kemampuan ekonomi signifikan (misalnya kepala keluarga pensiun, kehilangan pekerjaan, sakit menahun, atau terdampak bencana).',
                        'Mahasiswa yang menemukan ketidaksesuaian pencatatan data komponen penilaian pada rincian hasil UKT.',
                    ],
                    'dokumen_diperlukan' => [
                        'Surat permohonan peninjauan ulang UKT yang ditandatangani mahasiswa dan orang tua/wali.',
                        'Salinan Kartu Keluarga (KK) terbaru untuk membuktikan jumlah tanggungan keluarga.',
                        'Slip gaji terbaru atau Surat Keterangan Penghasilan dari kelurahan/desa bagi pekerja sektor informal.',
                        'Bukti pembayaran tagihan listrik atau pembelian token listrik 2 bulan terakhir.',
                        'Foto kondisi tempat tinggal (tampak depan dan ruang utama) serta bukti pendukung perubahan kondisi ekonomi bila ada.',
                    ],
                    'tujuan_pengajuan' => 'Pengajuan diteruskan kepada Tim Verifikasi dan Petugas UKT melalui Biro Akademik, Kemahasiswaan, dan Perencanaan (BAKP) / Bagian Keuangan Universitas Negeri Gorontalo.',
                    'tahapan_peninjauan' => [
                        'Mahasiswa memeriksa terlebih dahulu tabel rincian komponen dan alasan hasil UKT pada menu "Hasil & Rincian Saya".',
                        'Mahasiswa menyiapkan alasan keberatan pada komponen yang dinilai tidak sesuai beserta dokumen bukti pendukungnya.',
                        'Petugas UKT memeriksa kelengkapan berkas (status: Diajukan → Dalam Peninjauan). Bila ada dokumen yang kurang jelas, petugas akan memberi status "Perlu Perbaikan" beserta catatannya.',
                        'Tim peninjau melakukan evaluasi ulang dan menetapkan keputusan akhir ("Disetujui" atau "Ditolak") disertai catatan alasan yang dapat dibaca mahasiswa.',
                    ],
                ], JSON_UNESCAPED_UNICODE),
                'diperbarui_oleh' => $petugas->id,
                'diperbarui_pada' => now(),
            ],
            [
                'slug' => 'kontak',
                'judul' => 'Kanal Bantuan dan Informasi Layanan Petugas UKT',
                'ringkasan' => 'Informasi resmi loket pelayanan dan kanal konsultasi terkait penjelasan hasil maupun prosedur peninjauan UKT Universitas Negeri Gorontalo.',
                'isi' => 'Layanan Informasi UKT — Biro Akademik, Kemahasiswaan, dan Perencanaan (BAKP) & Bagian Keuangan Rektorat Universitas Negeri Gorontalo, Jl. Jend. Sudirman No. 6, Kota Gorontalo. Jam layanan tatap muka: Senin–Jumat pukul 08.00–16.00 WITA.',
                'diperbarui_oleh' => $petugas->id,
                'diperbarui_pada' => now(),
            ],
            [
                'slug' => 'petunjuk-pengisian',
                'judul' => 'Petunjuk Pengisian Formulir Keberatan dan Pertanyaan (KF-09)',
                'ringkasan' => 'Panduan per kolom yang tampil langsung di samping setiap isian formulir mahasiswa (apa yang harus diisi, format yang diharapkan, contoh pengisian, dan akibat bila keliru).',
                'isi' => json_encode(KontenPublik::defaultPetunjukPengisian(), JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT),
                'diperbarui_oleh' => $petugas->id,
                'diperbarui_pada' => now(),
            ],
        ];

        foreach ($daftarKontenPublik as $k) {
            KontenPublik::updateOrCreate(
                ['slug' => $k['slug']],
                $k
            );
        }

        // 6. Hasil UKT Contoh (Budi: Resmi Golongan IV Rp 2.500.000; Ayu: Simulasi; Citra: Kosong)
        /** @var RuleEngineService $ruleEngine */
        $ruleEngine = app(RuleEngineService::class);

        // Hapus hasil lama seeder bila dijalankan ulang agar tidak duplikat
        $mahasiswaBudi->hasilUkt()->delete();
        $mahasiswaAyu->hasilUkt()->delete();

        // Budi Pratama — Hasil Resmi Golongan IV, Rp 2.500.000 / semester + tabel rincian lengkap
        $ruleEngine->simpanHasil(
            mahasiswa: $mahasiswaBudi,
            masukan: $masukanBudi,
            sumber: 'resmi',
            versiParameter: $versiParameter,
            golonganResmi: $mapGolongan['GOL-IV'],
            nominalResmi: 2500000,
            tanggalPenetapan: '2026-02-10',
            sumberPenetapan: 'SK Penetapan UKT Rektor UNG Semester Genap 2025/2026 (Entri Petugas)',
            ditetapkanOleh: $petugas,
            catatan: 'Penetapan resmi Golongan IV dengan rincian komponen terverifikasi.',
            kirimNotifikasi: true
        );

        // Ayu Lestari — Hasil Simulasi (footer penjelasan seeder/simulasi wajib tampil di /hasil-saya)
        $ruleEngine->simpanHasil(
            mahasiswa: $mahasiswaAyu,
            masukan: $masukanAyu,
            sumber: 'simulasi',
            versiParameter: $versiParameter,
            ditetapkanOleh: $petugas,
            catatan: 'Perhitungan simulasi berdasarkan parameter penjelasan v1.0-simulasi.',
            kirimNotifikasi: true
        );
    }
}
