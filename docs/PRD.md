# PRD — Product Requirements Document

## Sistem Peninjauan dan Pengajuan Keberatan UKT Universitas Negeri Gorontalo

**Institusi:** Universitas Negeri Gorontalo (UNG)
**Singkatan:** SPPK-UKT UNG — penyebutan singkat sistem ini
**Produk:** Sistem Informasi Peninjauan dan Pengajuan Keberatan Uang Kuliah Tunggal (UKT) Universitas Negeri Gorontalo, berbasis web
**Pengguna utama:** Mahasiswa, petugas UKT, dan (sebagai pembaca rekap)
pimpinan / auditor — lihat Bagian 3 untuk batas kewenangannya
**Stack yang ditetapkan:** Laravel + Blade + Tailwind CSS + Alpine.js
**Desain yang ditetapkan:** Light glassmorphism, profesional, simple, tegas
(lihat `Glassmorphism.md` dan `Motion.md`)
**Status dokumen:** Draf v1.0 untuk pembangunan Tahap 1–3
**Bahasa dokumen:** Bahasa Indonesia

**Cara memakai dokumen ini:** Dokumen ini berdiri sendiri untuk urusan
*apa* yang dibangun dan *mengapa*. Angka-angka bukti dari survei sudah
ditulis lengkap di Bagian 2, sehingga pembaca tidak perlu membuka laporan
aslinya untuk memahami alasan tiap fungsi. File yang menyertainya adalah
`blueprint.md` (cara membangunnya), `Glassmorphism.md`, `Motion.md`,
`AGENTS.md`, `DESIGN.md`, dan `LOGO UNG (1).png`. Bila ada hal yang tidak diatur di sini atau
di file penyerta tersebut, ikuti spesifikasi sistem yang diberikan bersama
tugas — jangan mencari sumber produk lain di luar daftar itu.

> **Peringatan paling penting di dokumen ini, dibaca dulu sebelum membangun
> apa pun:** Sistem ini **bukan** penentu resmi golongan UKT. Penetapan
> resmi tetap menjadi kewenangan kampus. Formula, variabel, bobot, dan
> ambang resmi kampus **belum diketahui** pada saat laporan disusun, dan
> tidak diasumsikan di dokumen ini. Mesin penilaian (KF-05) adalah model
> penjelasan / simulasi yang aturannya dapat ditelusuri. Untuk versi
> prototipe seeder ini, status simulasi/seeder dijelaskan di footer
> halaman hasil, bukan diulang di tiap baris data (lihat KF-05) —
> keputusan pemilik produk, dengan konsekuensi bahwa tangkapan layar
> atau cetakan tanpa footer tidak membawa penjelasan itu. Bila data
> resmi masuk, penempatan penjelasan ini ditinjau ulang.
> Jangan pernah menampilkan simulasi seolah-olah itu keputusan resmi.

---

## 1. Ringkasan Produk

### 1.1 Masalah yang diselesaikan

Mahasiswa mengisi data UKT, lalu menerima hasil akhir tanpa melihat
bagaimana penilaian dilakukan di antaranya. Akibatnya:

- Mahasiswa tidak tahu indikator dan cara penilaian yang dipakai.
- Hasil dirasa tidak sebanding dengan kondisi ekonomi keluarga, tetapi
  mahasiswa tidak bisa memeriksa dasarnya.
- Jalur bertanya dan prosedur keberatan / peninjauan ulang tidak jelas.
- Informasi tersebar lewat senior, teman, dan media sosial, bukan dari
  satu sumber resmi yang mudah dipahami.

Bukti ukurannya ada di Bagian 2. Masalah utama bukan pada saat pengisian
data, melainkan pada tahap sesudah data diisi: dasar penilaian, penjelasan
hasil, dan kejelasan jalur bertanya serta keberatan.

### 1.2 Solusi dalam satu kalimat

Satu sistem web yang menjelaskan — dalam tabel dan kalimat singkat yang
mudah dibaca — faktor apa yang dipakai, bagaimana data diolah menjadi
hasil, mengapa nominal itu muncul, bagaimana alur keberatannya, dan apa
status keberatan mahasiswa, dengan informasi umum yang bisa dibuka tanpa
login dan informasi pribadi yang hanya bisa dilihat pemiliknya dan petugas
yang berwenang.

### 1.3 Tujuan produk

1. Mahasiswa memahami tahapan penentuan UKT, dari pengisian data sampai
   penetapan hasil.
2. Mahasiswa bisa memeriksa rincian di balik nominal UKT yang diterimanya,
   bukan hanya angka akhirnya.
3. Mahasiswa tahu ke mana harus bertanya dan bagaimana mengajukan
   keberatan, serta bisa memantau status keberatannya.
4. Petugas memiliki jalur yang terstruktur untuk menjawab pertanyaan,
   meninjau keberatan, dan mencatat perubahan — bukan lewat pesan
   tersebar di luar sistem.
5. Kampus memiliki jejak yang bisa ditelusuri: siapa mengakses apa, apa
   yang berubah, dan bagaimana sebuah hasil dihitung.

### 1.4 Bukan tujuan produk

- Bukan sistem penetapan resmi golongan UKT.
- Bukan sistem pembayaran UKT.
- Bukan sistem untuk mengubah data ekonomi mahasiswa yang sudah
  diverifikasi.
- Bukan pengganti keputusan petugas atau pimpinan. Sistem menjelaskan
  dan mencatat; keputusan tetap dibuat manusia yang berwenang.

---

## 2. Bukti Kebutuhan — dari Mana Setiap Fungsi Berasal

Sumber bukti utama adalah kuesioner terhadap **24 mahasiswa S1 dari
12 program studi** (angkatan 2025: 5, 2024: 9, 2023: 8, 2022: 2).
Perangkat utama saat mengisi data UKT: laptop/komputer 16 orang (66,7%),
ponsel 6 orang (25%), keduanya sama sering 2 orang (8,3%).

**Batasan bukti ini, ditulis jujur agar tidak dibesar-besarkan:**

- Sampelnya kecil (n = 24) dan hanya dari sudut pandang mahasiswa.
- Kewenangan petugas dan pimpinan **belum diverifikasi dengan pihak
  kampus**. Pembagian peran di Bagian 3 yang menyangkut petugas dan
  pimpinan adalah rancangan yang masih perlu dikonfirmasi kampus.
- Ambang prioritas Tahap 1/2/3 (60% dan 30%, lihat Bagian 8) ditetapkan
  setelah data diperoleh, sehingga bersifat pertimbangan dan dapat
  disesuaikan — bukan aturan dari responden.

### 2.1 Tingkat pemahaman saat ini: rendah

| Ukuran | Hasil |
|---|---|
| Skor kejelasan informasi proses UKT | 2,63 dari 5 |
| Skor pemahaman alasan di balik hasil UKT | 2,42 dari 5 |
| Tidak mengetahui tahapan proses penentuan UKT | 12 orang (50%) |
| Hasil hanya disampaikan sebagai hasil akhir tanpa penjelasan | 15 orang (62,5%) |
| Tidak tahu prosedur peninjauan UKT | 8 orang (33,3%) |
| Belum pernah mengajukan keberatan | 14 orang (58,3%) |
| Tidak tahu prosedur keberatan, hanya "pernah dengar" | 12 orang (50%) |
| Sudah pernah mengajukan keberatan | 2 orang (8,3%) |

### 2.2 Informasi yang paling dibutuhkan

| Kebutuhan informasi | Jumlah | Persentase |
|---|---|---|
| Penjelasan cara data diolah menjadi hasil | 17 | 70,8% |
| Rincian di balik nominal UKT yang diterima | 17 | 70,8% |
| Daftar lengkap data / faktor yang digunakan | 15 | 62,5% |
| Prosedur pengajuan keberatan | 9 | 37,5% |

Harapan terhadap proses: dijelaskan secara rinci 20 orang (83,3%),
ada kepastian data diproses dengan benar 12 orang (50%), ada cara mudah
bertanya atau mengoreksi bila keliru 12 orang (50%), waktu tunggu yang
jelas 7 orang (29,2%).

### 2.3 Informasi yang diminta dibuka tanpa login

| Informasi | Jumlah | Persentase |
|---|---|---|
| Alur proses (pengisian data sampai UKT ditetapkan) | 19 | 79,2% |
| Prosedur pengajuan keberatan | 18 | 75,0% |
| Rentang nominal tiap golongan UKT | 17 | 70,8% |
| Daftar faktor penentu | 14 | 58,3% |
| Kontak petugas | 10 | 41,7% (dipilih sebagai opsional publik) |

Hanya 4 responden (16,7%) yang memilih semua informasi hanya setelah
login. Artinya: halaman publik bukan tambahan, melainkan kebutuhan inti.

### 2.4 Kendala dan perilaku saat proses tidak jelas

| Kendala | Jumlah | Persentase |
|---|---|---|
| Tidak tahu harus bertanya ke mana tentang hasil UKT | 14 | 58,3% |
| Tidak memahami pengaruh data yang diisi terhadap hasil UKT | 12 | 50,0% |
| Prosedur keberatan hanya "pernah dengar" dan tidak jelas | 12 | 50,0% |
| Hasil UKT hanya disampaikan tanpa penjelasan | 15 | 62,5% |

Yang dilakukan saat bingung: bertanya ke petugas 12 orang (50%),
bertanya ke teman/senior 5 orang (20,8%), mencari di internet 4 orang
(16,7%), membiarkan saja 2 orang (8,3%), menerima hasil karena dirasa
wajar 1 orang (4,2%).

### 2.5 Bentuk penyajian dan pemberitahuan yang diminta

Bentuk penyajian paling mudah dipahami: rincian/tabel komponen penilaian
9 orang (37,5%), penjelasan melalui petugas langsung 8 orang (33,3%),
penjelasan tertulis singkat 4 orang (16,7%), video/panduan visual
3 orang (12,5%).

Saluran pemberitahuan status (boleh pilih lebih dari satu): email
22 orang (91,7%), notifikasi di sistem 18 orang (75,0%), WhatsApp
9 orang (37,5%), tidak perlu 0 orang (0%). Kesimpulan: email dan
notifikasi di sistem adalah saluran utama; WhatsApp hanya opsional.

### 2.6 Kenyamanan akses oleh pihak lain — sikap, bukan kewenangan

| Pihak | Nyaman | Netral | Tidak nyaman |
|---|---|---|---|
| Petugas UKT | 16 (66,7%) | 8 (33,3%) | 0 (0%) |
| Pimpinan fakultas / universitas | 9 (37,5%) | 14 (58,3%) | 1 (4,2%) |
| Orang tua / wali | 17 (70,8%) | 7 (29,2%) | 0 (0%) |

Catatan penting: jawaban netral **tidak** ditafsirkan sebagai persetujuan.
Kenyamanan ini adalah sikap mahasiswa, bukan dasar hukum kewenangan.
Karena itu, dokumen ini **tidak** memberikan akun orang tua, dan akses
pimpinan dibatasi pada rekap sampai kampus mengonfirmasi kewenangannya
(lihat Bagian 3.3).

### 2.7 Harapan fitur secara terbuka

Dari jawaban terbuka: transparansi penentuan UKT 11 orang (45,8%),
pengajuan keberatan / penurunan UKT 5 orang (20,8%), panduan dan
kemudahan pengisian data 3 orang (12,5%), kanal bantuan 1 orang (4,2%),
penyampaian informasi lebih jelas (umum) 1 orang (4,2%), tidak spesifik
3 orang (12,5%).

---

## 3. Pengguna, Peran, dan Hak Akses

### 3.1 Peran

| Peran | Siapa | Kebutuhan utamanya |
|---|---|---|
| Pengunjung publik | Siapa pun, tanpa login | Memahami alur, faktor, rentang golongan, dan prosedur keberatan sebelum memutuskan bertanya atau mengajukan |
| Mahasiswa | Mahasiswa yang login dengan akunnya sendiri | Melihat hasil dan rinciannya (milik sendiri), mengajukan keberatan, memantau status, bertanya ke petugas |
| Petugas UKT | Petugas yang ditunjuk kampus | Melihat dan mengelola rincian, menjawab pertanyaan, meninjau dan mengubah status keberatan, mengelola konten publik |
| Pimpinan / auditor | Pimpinan atau auditor, **bila kampus mengonfirmasi** | Melihat rekap dan jejak akses/perubahan; bukan mengelola data mahasiswa |

Tidak ada peran orang tua / wali di sistem ini. Kenyamanan orang tua
pada Bagian 2.6 tidak diubah menjadi hak akses.

### 3.2 Matriks hak akses

"Lihat" berarti membaca saja. "Kelola" berarti menambah, mengubah, dan
menghapus isi. "Milik sendiri" berarti sistem wajib membatasi hasil query
ke data milik pengguna yang login itu, bukan hanya menyembunyikan tombol
di tampilan.

| Informasi | Publik (tanpa login) | Mahasiswa | Petugas UKT | Pimpinan / Auditor |
|---|---|---|---|---|
| Data diri dan dokumen pengajuan | Tidak | Lihat (milik sendiri) | Lihat | Tidak ada akses |
| Rincian komponen penilaian dan dasar penetapan | Tidak | Lihat (milik sendiri) | Lihat dan kelola | Lihat (bila dikonfirmasi; default: rekap saja) |
| Hasil penetapan UKT | Tidak | Lihat (milik sendiri) | Lihat dan kelola | Lihat (rekap) |
| Alur proses, daftar faktor, rentang nominal, prosedur keberatan | Ya | Lihat | Kelola | Lihat |
| Kontak petugas | Opsional publik (lihat KF-08) | Lihat | Kelola | Lihat |
| Pengajuan keberatan dan statusnya | Tidak | Buat dan lihat (milik sendiri) | Lihat, tanggapi, ubah status | Lihat (rekap) |
| Riwayat akses dan perubahan data | Tidak | Tidak ada akses | Lihat | Lihat |

### 3.3 Keputusan akses yang masih terbuka — jangan diasumsikan selesai

1. Kewenangan petugas dan pimpinan atas data mahasiswa belum
   diverifikasi dengan kampus. Sampai ada konfirmasi tertulis, akses
   pimpinan atas rincian perorangan dinonaktifkan; yang aktif hanya
   rekap agregat tanpa data ekonomi perorangan.
2. Apakah kontak petugas dibuka untuk publik atau hanya setelah login:
   10 responden (41,7%) memilih publik, sehingga statusnya opsional.
   Keputusan ini harus ditetapkan pemilik produk sebelum Tahap 1 selesai,
   dan ditulis di sini sebagai konfigurasi, bukan ditebak saat coding.
3. Siapa yang memasukkan hasil penetapan resmi kampus ke sistem (petugas
   secara manual, atau impor dari sistem kampus yang sudah ada) belum
   ditetapkan. Sampai ditetapkan, alurnya adalah entri oleh petugas
   dengan jejak audit (KF-13). Integrasi otomatis ke sistem kampus
   **tidak** diasumsikan ada.

---

## 4. Prinsip Produk

Prinsip ini dipakai untuk menyelesaikan pertentangan desain dan prioritas
bila spesifikasi di bawah masih menyisakan pilihan.

1. **Transparansi dulu, bukan hasil dulu.** Angka akhir tanpa rincian
   adalah kegagalan produk ini, bukan versi sederhananya.
2. **Tabel dulu, narasi sebagai pendamping.** Rincian komponen
   ditampilkan sebagai tabel yang mudah dibaca; kalimat penjelasan
   menjelaskan tabel itu, bukan menggantikannya.
3. **Bahasa awam.** Setiap hasil dilengkapi alasan yang bisa dibaca
   tanpa pengetahuan teknis atau rumus. Istilah sistem ditulis dengan
   istilah yang dipakai mahasiswa dan kampus, konsisten di semua layar.
4. **Publik untuk yang umum, pribadi untuk yang pribadi.** Informasi
   umum (alur, faktor, rentang, prosedur) dibuka tanpa login. Data
   ekonomi, dokumen, rincian, dan keberatan hanya untuk pemiliknya dan
   petugas yang berwenang.
5. **Satu sumber jawaban.** Panduan, kontak petugas, dan prosedur
   keberatan harus mudah ditemukan dari satu tempat, supaya mahasiswa
   tidak lagi bergantung pada senior, teman, atau media sosial.
6. **Keputusan manusia, jejak mesin.** Sistem menghitung, menjelaskan,
   dan mencatat. Keputusan keberatan dibuat petugas; setiap perubahan
   status tercatat siapa, kapan, dan mengapa.
7. **Penjelasan status yang jujur.** Simulasi dijelaskan sebagai
   simulasi/seeder di footer halaman hasil untuk versi prototipe ini,
   bukan diulang di tiap baris data. Hasil resmi diberi label hasil
   resmi beserta sumbernya. Tidak ada label ganda yang membingungkan.
8. **Satu halaman tidak dipadatkan.** Bila konten terasa penuh, pakai
   scroll, tab, atau pagination — jangan mengecilkan font dan merapatkan
   isi sampai tidak terbaca (lihat `Glassmorphism.md`).

---

## 5. Daftar Fungsi — Spesifikasi, Alasan, dan Kriteria Penerimaan

Penomoran KF-01 sampai KF-15 dipertahankan dari laporan agar dapat
ditelusuri. Setiap fungsi di bawah memuat: apa yang dibangun, mengapa
fungsi itu dipilih (dengan bukti), siapa penggunanya, tahapnya, dan
kriteria penerimaan yang dapat diuji secara black-box. "Alasan" bukan
opini umum — ia merujuk ke angka di Bagian 2 atau ke ketergantungan
antarfungsi yang ditulis eksplisit.

### KF-01 — Rincian komponen penilaian dalam bentuk tabel

- **Spesifikasi:** Mahasiswa yang login melihat tabel rincian komponen
  penilaian yang membentuk hasil UKT-nya. Setiap baris memuat: nama
  komponen, masukan / nilai yang dipakai, penjelasan singkat komponen
  itu, dan langkah perhitungannya (lihat KF-05). Tabel ini adalah tampilan
  utama hasil, bukan lampiran tersembunyi.
- **Alasan dipilih:** Ini fungsi dengan dukungan wajib tertinggi.
  17 responden (70,8%) membutuhkan rincian di balik nominal, dan rincian
  komponen adalah aspek yang paling banyak dipilih sebagai prioritas
  wajib (P0: 15 responden). Bentuk penyajian yang paling mudah dipahami
  juga adalah tabel rincian (37,5%). Tanpa fungsi ini, produk ini tidak
  menyelesaikan masalah utamanya.
- **Pengguna:** Mahasiswa (milik sendiri), petugas (lihat dan kelola).
- **Tahap:** 1 (inti).
- **Kriteria penerimaan:**
  - Mahasiswa hanya melihat rinciannya sendiri; mencoba membuka rincian
    mahasiswa lain ditolak dan dicatat.
  - Tabel menampilkan semua komponen yang dipakai dalam perhitungan,
    tidak ada komponen yang dihitung tetapi disembunyikan.
  - Setiap baris bisa dibaca tanpa pengetahuan teknis; tidak ada baris
    yang hanya berisi kode atau angka tanpa penjelasan.
  - Bila belum ada hasil, tampilkan state kosong yang menjelaskan apa
    yang belum tersedia dan apa yang harus dilakukan, bukan tabel kosong.

### KF-02 — Penjelasan tahapan penentuan UKT dan hubungan data–hasil

- **Spesifikasi:** Sistem menjelaskan tahapan penentuan UKT dari
  pengisian data sampai penetapan hasil, serta hubungan antara data yang
  diisi dengan hasilnya. Penjelasan ini tersedia sebagai alur langkah
  demi langkah dan sebagai penjelasan di dalam tabel KF-01 (data ini
  menghasilkan komponen itu).
- **Alasan dipilih:** 17 responden (70,8%) membutuhkan penjelasan cara
  data diolah menjadi hasil; 19 responden (79,2%) memilih alur proses
  dibuka tanpa login; 50% responden tidak mengetahui tahapan prosesnya.
  Fungsi ini menjawab "kemana data saya diproses" sebelum mahasiswa
  sampai ke angka akhir.
- **Pengguna:** Publik (alur umum) dan mahasiswa (alur untuk datanya).
- **Tahap:** 1 (inti).
- **Kriteria penerimaan:**
  - Alur umum bisa dibuka tanpa login dan memuat semua tahapan dari
    pengisian sampai penetapan, tanpa langkah yang hilang.
  - Pada halaman hasil mahasiswa, setiap komponen bisa ditelusuri ke
    data masukan yang memengaruhinya.
  - Penjelasan memakai bahasa awam dan istilah yang sama dengan KF-01
    dan KF-03.

### KF-03 — Daftar faktor / variabel penentuan UKT beserta penjelasannya

- **Spesifikasi:** Sistem menampilkan daftar lengkap faktor atau variabel
  yang digunakan dalam penentuan UKT, masing-masing dengan penjelasan
  singkat: apa faktor itu, mengapa ia dipakai, dan bagaimana pengaruh
  umumnya terhadap hasil (tanpa mengklaim rumus resmi bila belum ada).
- **Alasan dipilih:** 15 responden (62,5%) membutuhkan daftar faktor;
  14 responden (58,3%) memilih daftar ini dibuka tanpa login. Faktor
  yang disebut responden selama ini hampir semuanya hanya penghasilan
  orang tua, padahal penentuan memakai lebih dari itu — daftar ini
  meluruskan pemahaman yang tidak lengkap itu.
- **Pengguna:** Publik dan mahasiswa; petugas mengelola daftarnya.
- **Tahap:** 1 (inti).
- **Kriteria penerimaan:**
  - Daftar publik memuat semua faktor yang dipakai model penjelasan,
    lengkap dengan penjelasan singkat tiap faktor.
  - Status konfirmasi sumber tiap faktor (`status_konfirmasi`) tetap
    disimpan dan dikelola oleh petugas di halaman kelola konten, namun
    tidak lagi ditampilkan di halaman publik Faktor (`/faktor`).
  - Perubahan daftar oleh petugas tercatat di riwayat (KF-13) dan
    langsung tercermin di halaman publik setelah disimpan.

### KF-04 — Alasan hasil UKT dalam kalimat singkat per komponen

- **Spesifikasi:** Setiap komponen penilaian pada KF-01 dilengkapi satu
  kalimat alasan yang menjelaskan mengapa komponen itu bernilai sekian
  untuk mahasiswa tersebut. Kalimat ini ditulis dari hasil perhitungan,
  bukan kalimat generik yang sama untuk semua mahasiswa.
- **Alasan dipilih:** Harapan paling dominan dari survei adalah proses
  dan hasil dijelaskan secara rinci (20 responden, 83,3%). Penjelasan
  alasan hasil juga menjadi prioritas penting (P1: 18 responden).
  Tabel tanpa alasan masih menyisakan pertanyaan "mengapa saya sekian".
- **Pengguna:** Mahasiswa (milik sendiri).
- **Tahap:** 1 (inti).
- **Kriteria penerimaan:**
  - Setiap baris KF-01 memiliki kalimat alasan yang berbeda-beda sesuai
    nilai komponennya; kalimat templat yang tidak berubah dilarang.
  - Kalimat alasan tidak memakai rumus atau singkatan tanpa penjelasan.
  - Bila komponen berasal dari simulasi, kalimat alasannya menyebut
    bahwa itu hasil simulasi (lihat KF-05).

### KF-05 — Mesin penilaian berbasis aturan (model penjelasan / simulasi)

- **Spesifikasi:** Sistem memiliki mesin penilaian berbasis aturan yang
  menghitung setiap komponen penilaian dan menampilkan langkah
  perhitungannya. Aturan disimpan sebagai parameter yang bisa diubah
  tanpa mengubah program (lihat KN-06 di Bagian 7). Masukan mesin adalah
  variabel yang ditetapkan kampus sesuai ketentuan resmi; daftar variabel
  menunggu sumber resmi. Keluaran mesin adalah nilai tiap komponen,
  langkah perhitungan, dan golongan hasil simulasi, ditampilkan sebagai
  tabel (KF-01).
- **Alasan dipilih:** Fungsi ini adalah penopang teknis KF-01, KF-02,
  dan KF-04. Tanpa mesin yang langkahnya bisa ditelusuri, rincian dan
  alasan hanya menjadi teks yang tidak bisa diverifikasi. Fungsi ini
  juga menjawab harapan kepastian bahwa data diproses dengan benar
  (12 responden, 50%) — kepastian itu datang dari keterlacakan, bukan
  dari klaim.
- **Pengguna:** Petugas mengelola parameternya; mahasiswa melihat
  keluaran dan langkahnya (milik sendiri).
- **Tahap:** 1 (inti, sebagai penopang penjelasan).
- **Kriteria penerimaan:**
  - Untuk masukan yang sama, mesin selalu menghasilkan keluaran yang
    sama (deterministik), dan setiap langkah perhitungan dapat
    ditelusuri ulang dari data masukan.
  - Mengubah parameter tidak memerlukan perubahan kode; perubahan
    parameter berversi (berlaku sejak tanggal tertentu) dan tidak
    mengubah hasil perhitungan lama yang sudah tercatat.
  - **Penjelasan status wajib (versi seeder/prototipe):** bila kampus
    belum mempublikasikan aturannya, hasil mesin ditampilkan sebagai
    simulasi. Penjelasannya ditempatkan di footer halaman hasil dan
    menyebut bagian mana saja yang merupakan seeder/simulasi dan bukan
    penetapan resmi — tidak diulang di tiap baris data, karena alasan
    tampilan yang ditetapkan pemilik produk. Konsekuensi yang diterima
    pemilik produk: tangkapan layar atau cetakan tabel tanpa footer
    tidak membawa penjelasan itu. Bila data resmi masuk, penempatan
    penjelasan ini ditinjau ulang.
  - Pengujian mesin memakai data uji buatan, bukan data mahasiswa asli
    (lihat Bagian 10).

### KF-06 — Informasi alur pengajuan keberatan / peninjauan ulang

- **Spesifikasi:** Sistem menyajikan informasi alur pengajuan keberatan
  atau peninjauan ulang UKT: siapa yang boleh mengajukan, dokumen apa
  yang diperlukan, ke mana pengajuan diteruskan, tahapan peninjauannya,
  dan berapa perkiraan waktunya (bila sudah ditetapkan; bila belum, tulis
  bahwa waktunya belum ditetapkan, jangan mengarang angka).
- **Alasan dipilih:** 18 responden (75%) memilih prosedur keberatan
  dibuka tanpa login. Prosedur yang hanya "pernah dengar" dialami
  12 responden (50%). Fungsi ini adalah prasyarat bagi keberatan itu
  sendiri: mahasiswa harus bisa memahami prosedurnya sebelum ia
  diminta mengisi formulirnya.
- **Pengguna:** Publik dan mahasiswa; petugas mengelola isinya.
- **Tahap:** 1 (inti).
- **Kriteria penerimaan:**
  - Halaman prosedur bisa dibuka tanpa login dan memuat semua tahapan,
    syarat, dan dokumen yang dibutuhkan.
  - Halaman ini tertaut langsung ke formulir pengajuan (KF-07) bila
    mahasiswa login, dan ke halaman login bila belum login — dengan
    penjelasan bahwa pengajuan memerlukan login.
  - Bila prosedur resmi berubah, petugas bisa memperbarui halaman ini
    tanpa mengubah kode.

### KF-07 — Pengajuan keberatan secara daring dan pemantauan statusnya

- **Spesifikasi:** Mahasiswa yang login bisa mengajukan keberatan secara
  daring (alasan keberatan, data pendukung yang relevan, dan dokumen
  bukti bila diminta), lalu melihat status pengajuannya dan riwayat
  perubahannya. Petugas bisa melihat pengajuan, menanggapi, meminta
  perbaikan, dan mengubah statusnya. Setiap perubahan status memicu
  pemberitahuan (KF-15) dan tercatat di riwayat (KF-13).
- **Alasan dipilih:** 12 responden (50%) mengharapkan cara mudah bertanya
  atau mengoreksi bila keliru; 5 responden (20,8%) secara terbuka
  mengharapkan fitur pengajuan keberatan / penurunan UKT. Fungsi ini
  adalah inti dari nama produk ini — tetapi penempatannya di Tahap 2
  mengikuti aturan prioritas laporan (lihat Bagian 8), karena dukungan
  surveinya berada di rentang 30–<60%, sementara informasi prosedurnya
  (KF-06) sudah harus tersedia di Tahap 1.
- **Pengguna:** Mahasiswa (buat dan lihat milik sendiri), petugas
  (lihat, tanggapi, ubah status).
- **Tahap:** 2 (pendukung).
- **Kriteria penerimaan:**
  - Mahasiswa hanya bisa membuat dan melihat keberatannya sendiri;
    pengajuan mahasiswa lain tidak bisa dibuka.
  - Formulir menolak pengajuan kosong (tanpa alasan) dengan pesan yang
    jelas, dan menyimpan draf masukan bila terjadi kesalahan validasi
    supaya tidak perlu mengetik ulang.
  - Status pengajuan selalu terlihat beserta tanggal perubahan terakhir
    dan, bila ada, catatan petugas yang boleh dibaca mahasiswa.
  - Pengajuan yang sama tidak bisa terkirim dua kali karena klik ganda.
  - Status yang dipakai di seluruh sistem konsisten (lihat status yang
    diusulkan di `blueprint.md`; status resmi final mengikuti ketentuan
    kampus bila sudah ditetapkan).

### KF-08 — Jalur bertanya dan fasilitas mengirim pertanyaan ke petugas

- **Spesifikasi:** Sistem menyediakan informasi jalur bertanya yang jelas
  (siapa petugas yang bisa dihubungi dan melalui kanal apa) dan fasilitas
  mengirim pertanyaan kepada petugas dari dalam sistem. Pertanyaan dan
  jawaban tercatat dan bisa dilihat kembali oleh mahasiswa yang
  mengajukan.
- **Alasan dipilih:** 14 responden (58,3%) tidak tahu harus bertanya ke
  mana tentang hasil UKT, dan 50% responden memilih bertanya ke petugas
  sebagai tindakan pertama saat bingung. Satu responden secara terbuka
  meminta "help center". Tanpa jalur ini, mahasiswa kembali bertanya
  lewat teman, senior, atau media sosial.
- **Pengguna:** Mahasiswa (kirim dan lihat milik sendiri), petugas
  (jawab); kontak petugas untuk publik bersifat opsional (Bagian 3.3).
- **Tahap:** 2 (pendukung).
- **Kriteria penerimaan:**
  - Halaman bantuan menampilkan jalur bertanya yang jelas tanpa perlu
    login untuk informasi umumnya; pengiriman pertanyaan memerlukan
    login supaya bisa dijawab dan dilacak.
  - Setiap pertanyaan yang dikirim mendapat tanda terima (nomor atau
    status) dan bisa dilihat kembali beserta jawabannya.
  - Pertanyaan mahasiswa lain tidak bisa dibuka oleh mahasiswa.

### KF-09 — Petunjuk pengisian data yang jelas

- **Spesifikasi:** Setiap formulir pengisian data dilengkapi petunjuk
  yang jelas: apa yang harus diisi, format yang diharapkan, contoh bila
  perlu, dan apa akibatnya bila data salah atau tidak lengkap. Petunjuk
  tampil di dekat field yang bersangkutan, bukan hanya di satu halaman
  panduan terpisah.
- **Alasan dipilih:** Kejelasan prosedur pengisian ditandai prioritas
  wajib oleh 10 responden (41,7%) dan penting oleh 16 responden. Laporan
  menyimpulkan panduan pengisian cukup berupa perbaikan penjelasan pada
  formulir — masalah utama bukan pada pengisian itu sendiri, sehingga
  fungsi ini ditempatkan di Tahap 2, bukan Tahap 1.
- **Pengguna:** Mahasiswa; petugas mengelola teks petunjuknya.
- **Tahap:** 2 (pendukung).
- **Kriteria penerimaan:**
  - Setiap field yang aturannya tidak intuitif memiliki petunjuk di
    dekatnya dan pesan kesalahan yang menyebut field dan perbaikan yang
    diminta.
  - Pesan kesalahan tidak menghapus masukan yang sudah benar.
  - Petunjuk bisa diperbarui petugas tanpa mengubah kode.

### KF-10 — Panduan visual tahapan proses

- **Spesifikasi:** Panduan visual tahapan proses — misalnya diagram alur
  bergambar atau video / panduan visual singkat — yang menjelaskan
  tahapan penentuan UKT dan tahapan keberatan secara visual, sebagai
  pelengkap penjelasan tertulis (KF-02 dan KF-06), bukan penggantinya.
- **Alasan dipilih:** 3 responden (12,5%) memilih video atau panduan
  visual sebagai bentuk penyajian yang paling mudah dipahami. Dukungan
  yang kecil ini menempatkannya di Tahap 3. Fungsi ini dipilih karena
  melengkapi, bukan karena menjadi andalan: penjelasan tertulis dan
  tabel tetap menjadi jalur utama.
- **Pengguna:** Publik dan mahasiswa.
- **Tahap:** 3 (lanjutan).
- **Kriteria penerimaan:**
  - Panduan visual memuat tahapan yang sama dengan KF-02 dan KF-06;
    bila alur berubah, panduan visual diperbarui atau diberi label
    kedaluwarsa, tidak dibiarkan menampilkan alur lama.
  - Panduan bisa dibuka di laptop dan ponsel tanpa plugin khusus.
  - Ada alternatif teks untuk setiap panduan visual (aksesibilitas).

### KF-11 — Perkiraan waktu tunggu hasil

- **Spesifikasi:** Sistem menampilkan perkiraan waktu tunggu hasil
  (penetapan atau keberatan) bila perkiraan itu sudah ditetapkan oleh
  kampus / petugas, beserta tanggal pembaruan perkiraan tersebut. Bila
  belum ditetapkan, sistem menulis bahwa waktu tunggu belum ditetapkan
  dan menampilkan status prosesnya, bukan mengarang angka.
- **Alasan dipilih:** 7 responden (29,2%) mengharapkan waktu tunggu yang
  jelas. Dukungan di bawah 30% menempatkannya di Tahap 3. Fungsi ini
  dipilih sebagai penenang kecemasan yang terukur — bukan sebagai janji
  waktu yang tidak bisa ditepati.
- **Pengguna:** Mahasiswa (untuk pengajuannya) dan publik (untuk
  perkiraan umum, bila ada).
- **Tahap:** 3 (lanjutan).
- **Kriteria penerimaan:**
  - Perkiraan yang ditampilkan selalu mencantumkan sumber dan tanggalnya.
  - Bila perkiraan terlewati, status tetap diperbarui dan mahasiswa
    diberi tahu, bukan dibiarkan tanpa kabar.
  - Tidak ada perkiraan yang ditampilkan tanpa dasar penetapan.

### KF-12 — Autentikasi dan pembatasan hak akses berdasarkan peran

- **Spesifikasi:** Sistem menerapkan autentikasi (login) dan pembatasan
  hak akses berdasarkan peran sesuai matriks Bagian 3, serta memisahkan
  informasi publik dan informasi pribadi secara tegas di sisi server,
  bukan hanya di tampilan. Setiap halaman dan aksi memeriksa peran dan
  kepemilikan data sebelum menampilkan atau mengubah apa pun.
- **Alasan dipilih:** Fungsi ini tidak berasal dari satu butir kuesioner,
  melainkan dari kebutuhan perlindungan data ekonomi dan dokumen
  mahasiswa, serta dari matriks hak akses laporan. Tanpa fungsi ini,
  semua fungsi pribadi (KF-01, KF-07, KF-08) tidak aman untuk dibuka.
  Karena itu ia ditempatkan di Tahap 1 meski tanpa persentase survei —
  penempatannya mengikuti ketergantungan, sesuai aturan laporan.
- **Pengguna:** Semua peran.
- **Tahap:** 1 (inti, prasyarat).
- **Kriteria penerimaan:**
  - Pengunjung tanpa login tidak bisa membuka data pribadi lewat URL
    langsung, bukan hanya lewat navigasi yang disembunyikan.
  - Mahasiswa tidak bisa membuka, mengubah, atau menghapus data milik
    mahasiswa lain lewat manipulasi parameter.
  - Petugas dan pimpinan hanya bisa melakukan aksi sesuai matriks
    Bagian 3; aksi di luar perannya ditolak dan dicatat.
  - Sesi dan kata sandi mengikuti praktik keamanan yang ditetapkan di
    `AGENTS.md` (hash modern, tidak pernah menyimpan kata sandi mentah,
    tidak menampilkannya di log).

### KF-13 — Riwayat akses dan perubahan data serta hasil penilaian

- **Spesifikasi:** Sistem mencatat riwayat akses dan perubahan data
  serta hasil penilaian: siapa, kapan, apa yang diakses/diubah, dan nilai
  sebelum–sesudah untuk perubahan penting (termasuk perubahan parameter
  KF-05, perubahan status keberatan, dan perubahan rincian oleh petugas).
  Riwayat bisa dilihat petugas dan pimpinan / auditor sesuai matriks;
  mahasiswa tidak memiliki akses ke riwayat ini.
- **Alasan dipilih:** 12 responden (50%) mengharapkan kepastian bahwa
  data diproses dengan benar. Kepastian itu tidak bisa diberikan oleh
  klaim, hanya oleh jejak yang bisa diperiksa. Fungsi ini juga penopang
  akuntabilitas petugas.
- **Pengguna:** Petugas (lihat), pimpinan / auditor (lihat).
- **Tahap:** 2 (pendukung).
- **Kriteria penerimaan:**
  - Setiap perubahan status keberatan dan parameter penilaian tercatat
    dengan pelaku, waktu, dan alasan / catatan bila ada.
  - Riwayat tidak bisa diubah atau dihapus oleh pengguna biasa maupun
    petugas yang sama yang melakukan perubahan; penghapusan riwayat
    (bila ada kebijakan retensi) hanya lewat mekanisme yang tercatat.
  - Hasil perhitungan lama tetap bisa ditelusuri ke versi parameter
    yang memakainya, meski parameter sudah berubah.

### KF-14 — Rentang nominal tiap golongan UKT di halaman publik

- **Spesifikasi:** Sistem menampilkan rentang nominal tiap golongan UKT
  pada halaman yang bisa dibuka tanpa login. Rentang ini dikelola petugas
  dan mencantumkan sumber serta tanggal pembaruannya.
- **Alasan dipilih:** 17 responden (70,8%) memilih rentang nominal dibuka
  tanpa login. Ini informasi umum yang tidak mengandung data pribadi,
  sehingga tidak ada alasan untuk menguncinya di balik login.
- **Pengguna:** Publik; petugas mengelola.
- **Tahap:** 1 (inti).
- **Kriteria penerimaan:**
  - Halaman rentang bisa dibuka tanpa login dan memuat semua golongan
    yang berlaku, tanpa golongan yang hilang.
  - Setiap perubahan rentang mencantumkan tanggal pembaruan dan
    tercatat di riwayat.
  - Bila rentang resmi berubah, halaman lama tidak dibiarkan tampil
    sebagai rentang yang berlaku.

### KF-15 — Pemberitahuan status melalui email dan notifikasi di sistem

- **Spesifikasi:** Sistem mengirim pemberitahuan status UKT atau
  keberatan melalui email dan notifikasi di sistem setiap kali status
  penting berubah (misalnya keberatan diterima, diminta perbaikan,
  diputuskan). Pemberitahuan memuat status baru, tanggal, dan tautan ke
  halaman status di sistem — bukan rincian data ekonomi di dalam email.
- **Alasan dipilih:** Email dipilih 22 responden (91,7%) dan notifikasi
  di sistem 18 responden (75,0%) — dukungan tertinggi di seluruh survei.
  Tidak ada responden yang menyatakan pemberitahuan tidak diperlukan.
  WhatsApp dipilih 9 responden (37,5%) sehingga sifatnya opsional dan
  tidak dibangun sebagai saluran utama di versi ini.
- **Pengguna:** Mahasiswa (penerima), petugas (pemicu lewat perubahan
  status).
- **Tahap:** 1 (inti).
- **Kriteria penerimaan:**
  - Setiap perubahan status yang tercantum di KF-07 menghasilkan
    pemberitahuan email dan notifikasi di sistem, kecuali pengguna
    menonaktifkan salah satunya di pengaturan (bila pengaturan itu
    disediakan).
  - Pemberitahuan yang gagal terkirim dicoba ulang lewat antrian dan
    tercatat kegagalannya; kegagalan pengiriman tidak boleh
    menggagalkan perubahan status itu sendiri, tetapi harus terlihat
    oleh petugas.
  - Email tidak memuat data ekonomi atau dokumen mahasiswa; detail
    lengkap hanya bisa dibuka setelah login di sistem.
  - WhatsApp tidak dijanjikan di versi ini; bila nanti ditambahkan,
    ia adalah saluran tambahan, bukan pengganti email / notifikasi.

---

## 6. Fungsi yang Sengaja Tidak Dibangun

Menulis yang tidak dibangun sama pentingnya dengan yang dibangun, supaya
ruang lingkup tidak melebar diam-diam saat implementasi.

| Tidak dibangun di versi ini | Alasan |
|---|---|
| Penetapan resmi golongan UKT oleh sistem | Tetap kewenangan kampus; formula resmi belum diketahui (lihat peringatan di awal) |
| Pembayaran UKT | Tidak disebut oleh responden; di luar fokus transparansi laporan |
| Perubahan data ekonomi mahasiswa yang sudah diverifikasi | Di luar lingkup yang ditetapkan laporan; perubahan semacam itu memerlukan kewenangan dan prosedur tersendiri |
| Akun orang tua / wali | Kenyamanan orang tua adalah sikap, bukan hak akses; tidak ada peran ini di matriks laporan |
| Notifikasi WhatsApp sebagai saluran utama | Hanya 37,5% responden memilihnya; email dan notifikasi sistem sudah mencakup 91,7% dan 75% |
| Integrasi otomatis ke sistem kampus yang sudah ada | Belum ditetapkan sumber datanya; diasumsikan tidak ada sampai diputuskan (lihat Bagian 3.3) |
| Rumus resmi yang dikarang | Dilarang. Bila sumber resmi belum ada, tampilkan simulasi berlabel, bukan rumus buatan yang disajikan sebagai resmi |

Bila salah satu baris di atas nanti diputuskan untuk dibangun, keputusan
itu ditulis sebagai perubahan PRD, bukan ditambahkan diam-diam di kode.

---

## 7. Kebutuhan Nonfungsional

Penomoran KN-01 sampai KN-07 dipertahankan dari laporan. Kuesioner hanya
menanyakan sebagian aspek mutu; aspek lain adalah rancangan atau ketentuan
tugas, dan ditulis demikian di bawah — bukan disajikan sebagai kebutuhan
yang dinyatakan responden.

| ID | Kategori | Kebutuhan | Dasar | Cara memeriksa |
|---|---|---|---|---|
| KN-01 | Keamanan dan privasi | Data ekonomi dan dokumen mahasiswa hanya bisa diakses sesuai peran (Bagian 3) dan disimpan dengan perlindungan yang memadai | Rancangan; sikap netral pimpinan 58,3% menjadi alasan kehati-hatian, bukan persetujuan | Uji akses per peran dan kepemilikan (KF-12); dokumen tidak bisa dibuka lewat URL langsung tanpa otorisasi |
| KN-02 | Kegunaan | Rincian komponen disajikan dalam tabel yang mudah dibaca | 9 responden (37,5%) memilih tabel komponen | Uji baca di tiga lebar layar (≈390, 768, 1280); tabel berubah menjadi kartu di mobile |
| KN-03 | Keterjelasan | Setiap hasil dilengkapi alasan penetapan yang bisa dibaca tanpa pengetahuan teknis | 20 responden (83,3%) mengharapkan penjelasan rinci | Uji baca kalimat alasan KF-04; tidak ada rumus tanpa penjelasan |
| KN-04 | Keterlacakan dan konsistensi | Untuk masukan yang sama, mesin menghasilkan keluaran yang sama, dan setiap langkah perhitungan bisa ditelusuri | 12 responden (50%) mengharapkan kepastian pemrosesan | Uji ulang perhitungan dengan masukan identik; telusuri ke versi parameter |
| KN-05 | Aksesibilitas perangkat | Sistem berbasis web bisa dibuka lewat peramban di laptop/komputer dan ponsel, dengan tampilan yang nyaman di laptop sebagai perangkat utama | Laptop 66,7%, ponsel 25% | Uji di laptop dan ponsel; desktop-first, tetapi mobile tetap bisa dipakai penuh untuk tugas mahasiswa |
| KN-06 | Kemudahan perubahan | Aturan penilaian disimpan sebagai parameter yang bisa diubah tanpa mengubah program | Rancangan untuk mendukung KF-05 | Ubah parameter lewat antarmuka petugas, tanpa deploy kode, dan hasil lama tidak berubah |
| KN-07 | Kemudahan pengujian | Setiap kebutuhan fungsional memiliki kriteria penerimaan yang bisa diuji dengan black-box testing | Ketentuan tugas | Setiap KF di Bagian 5 memiliki kriteria penerimaan; pengujian memakai data buatan, bukan data mahasiswa asli |

Kebutuhan nonfungsional ini berlaku untuk seluruh tahap, bukan hanya
tahap tempat fungsi tertentu dibangun.

Tambahan yang ditetapkan dokumen ini (bukan dari laporan, ditulis
eksplisit supaya tidak disangka hasil survei):

- **Aksesibilitas konten:** kontras teks minimal 4,5:1 (WCAG AA),
  fokus keyboard terlihat, target sentuh minimal 44×44px, dan dukungan
  `prefers-reduced-motion` — sesuai `Glassmorphism.md` dan `Motion.md`.
- **Kinerja yang wajar:** halaman daftar memakai pagination, bukan
  memuat semua baris sekaligus; query daftar dibatasi dan tidak
  menimbulkan pola N+1 yang jelas.
- **Bahasa dan format lokal:** Bahasa Indonesia; mata uang
  `Rp 1.250.000`; tanggal `12 Okt 2026`.

---

## 8. Prioritas dan Roadmap

### 8.1 Aturan penentuan tahap (dari laporan)

Dukungan data diukur dari persentase tertinggi yang diperoleh suatu
kebutuhan pada tabel kebutuhan informasi, harapan, kendala, butir
informasi tanpa login, dan pemberitahuan, atau dari persentase pemilih
P0 bila hanya tersedia pada tabel prioritas.

- **Tahap 1** bila persentase tertinggi 60% atau lebih.
- **Tahap 2** bila 30% sampai kurang dari 60%.
- **Tahap 3** bila kurang dari 30%.
- Kebutuhan tanpa dasar data ditempatkan menurut ketergantungannya pada
  kebutuhan lain.

Ambang 60% dan 30% ditetapkan setelah data diperoleh, sehingga bersifat
pertimbangan dan dapat disesuaikan.

### 8.2 Tahapan

| Tahap | Fungsi | Alasan penempatan singkat |
|---|---|---|
| **Tahap 1 (inti)** | KF-01, KF-02, KF-03, KF-04, KF-05, KF-06, KF-12, KF-14, KF-15 | Dukungan kebutuhan utama 62,5–83,3%; rentang nominal 70,8% untuk akses publik dan email 91,7% untuk pemberitahuan. KF-05 mendukung penjelasan hasil, KF-12 diperlukan untuk melindungi data |
| **Tahap 2 (pendukung)** | KF-07, KF-08, KF-09, KF-13 | Dukungan 41,7–58,3%. Mencakup pengajuan keberatan, bantuan petugas, petunjuk pengisian, dan riwayat pemrosesan |
| **Tahap 3 (lanjutan)** | KF-10, KF-11 | Panduan visual 12,5% dan kejelasan waktu tunggu 29,2% |

Catatan yang harus dibaca bersama tabel ini: pengajuan keberatan
(KF-07) berada di Tahap 2 menurut aturan laporan, sementara informasi
prosedur keberatan (KF-06) sudah ada di Tahap 1. Bila pemilik produk
memutuskan mempercepat KF-07 ke Tahap 1 karena nama produk ini
menekankan keberatan, keputusan itu dicatat sebagai perubahan prioritas
di dokumen ini — bukan dilakukan diam-diam dengan mengabaikan tabel.

### 8.3 Urutan pembangunan yang disarankan di dalam tiap tahap

1. Fondasi: autentikasi dan hak akses (KF-12), lalu halaman publik
   (KF-02 alur umum, KF-03, KF-06, KF-14).
2. Inti penjelasan: mesin parameter (KF-05), tabel rincian (KF-01),
   kalimat alasan (KF-04), dan pemberitahuan (KF-15).
3. Interaksi: pengajuan keberatan (KF-07), pertanyaan (KF-08), petunjuk
   formulir (KF-09), riwayat (KF-13).
4. Pelengkap: panduan visual (KF-10) dan perkiraan waktu tunggu (KF-11).

---

## 9. Risiko dan Keputusan yang Masih Terbuka

Daftar ini bukan kekurangan dokumen — ia adalah daftar yang harus
ditutup sebelum atau selama pembangunan, dan tidak boleh ditutup dengan
menebak.

| # | Keputusan / risiko | Dampak bila tidak ditutup | Siapa yang menutupnya |
|---|---|---|---|
| 1 | Formula, variabel, bobot, dan ambang resmi kampus belum diketahui | KF-05 hanya bisa berjalan sebagai simulasi berlabel; KF-01 menandai statusnya (di footer halaman hasil untuk prototipe seeder) dan status konfirmasi sumber KF-03 dikelola di sisi petugas (tidak lagi ditampilkan di halaman publik Faktor) | Kampus (sumber resmi) |
| 2 | Kewenangan petugas dan pimpinan atas data mahasiswa belum diverifikasi | Akses pimpinan atas rincian perorangan tetap nonaktif; hanya rekap yang aktif | Kampus (konfirmasi tertulis) |
| 3 | Sumber hasil penetapan resmi (entri manual petugas vs impor sistem kampus) belum ditetapkan | Alur input hasil dan format datanya belum bisa dikunci | Pemilik produk bersama kampus |
| 4 | Kontak petugas dibuka publik atau hanya setelah login | Mempengaruhi halaman bantuan dan halaman publik Tahap 1 | Pemilik produk |
| 5 | Penyedia email dan domain pengirim untuk KF-15 belum ditetapkan | Pemberitahuan email tidak bisa diuji end-to-end sampai ini ada | Pemilik produk / tim teknis |
| 6 | Status resmi keberatan (nama dan transisi yang berlaku di kampus) belum ditetapkan | Status di sistem memakai usulan di `blueprint.md` sampai ada ketentuan resmi | Kampus / pemilik produk |
| 7 | Sampel survei kecil (n = 24) dan hanya mahasiswa | Prioritas bisa bergeser bila diuji ke kelompok yang lebih besar; jangan menggeneralisasi tanpa pengujian lanjutan | Tim, lewat pengujian pengguna Tahap 1 |
| 8 | Dokumen bukti pendukung keberatan: jenis, ukuran, dan format yang diterima belum ditetapkan | Validasi upload di KF-07 belum bisa dikunci | Kampus / pemilik produk |

---

## 10. Pengujian dan Kriteria Penerimaan Global

- Setiap fungsi diuji secara black-box terhadap kriteria penerimaannya
  di Bagian 5, bukan hanya dengan "halaman berhasil dibuka".
- Pengujian mesin penilaian (KF-05) memakai **data uji buatan**, bukan
  data mahasiswa asli, dengan kasus yang mencakup: masukan lengkap,
  masukan di batas ambang, masukan tidak lengkap, dan perubahan parameter
  (hasil lama tidak boleh berubah).
- Pengujian hak akses memakai matriks Bagian 3: setiap peran diuji
  membuka data milik sendiri, milik orang lain, dan halaman di luar
  perannya — lewat URL langsung, bukan hanya lewat navigasi.
- Pengujian pemberitahuan memakai kotak surat uji dan antrian: status
  berubah, email dan notifikasi terkirim, kegagalan tercatat, dan
  perubahan status tidak gagal hanya karena email gagal.
- Pengujian tampilan dilakukan di tiga lebar (≈390, 768, 1280) dengan
  data realistis, termasuk state kosong, state error, dan state loading
  — bukan hanya state ideal.
- Laporan hasil pengujian menyebut apa yang dijalankan, apa yang lolos,
  apa yang gagal, dan apa yang belum diuji. Klaim "sudah diuji" tanpa
  daftar itu tidak diterima.

---

## 11. Matriks Keterlacakan

Setiap fungsi dapat ditelusuri ke bukti dan tahapnya tanpa membuka
laporan aslinya.

| ID | Bukti utama (dari Bagian 2) | Tahap |
|---|---|---|
| KF-01 | Rincian 70,8%; P0 tertinggi (15) | 1 |
| KF-02 | Penjelasan pengolahan 70,8%; alur publik 79,2% | 1 |
| KF-03 | Daftar faktor 62,5%; publik 58,3% | 1 |
| KF-04 | Penjelasan rinci 83,3%; P1 alasan (18) | 1 |
| KF-05 | Penopang KF-01/02/04; kepastian proses 50% | 1 |
| KF-06 | Prosedur publik 75% | 1 |
| KF-07 | Kemudahan koreksi 50%; harapan keberatan 20,8% | 2 |
| KF-08 | Tidak tahu bertanya ke mana 58,3% | 2 |
| KF-09 | Kejelasan pengisian P0 10, P1 16 | 2 |
| KF-10 | Panduan visual 12,5% | 3 |
| KF-11 | Waktu tunggu jelas 29,2% | 3 |
| KF-12 | Ketergantungan: prasyarat semua fungsi pribadi | 1 |
| KF-13 | Kepastian proses 50% | 2 |
| KF-14 | Rentang publik 70,8% | 1 |
| KF-15 | Email 91,7%; notifikasi sistem 75% | 1 |

---

## 12. Glosarium Singkat

| Istilah | Arti di dokumen ini |
|---|---|
| UKT | Uang Kuliah Tunggal, biaya yang dibayarkan mahasiswa tiap semester di perguruan tinggi negeri |
| SPPK-UKT UNG | Singkatan dari Sistem Peninjauan dan Pengajuan Keberatan UKT Universitas Negeri Gorontalo; dipakai untuk penyebutan singkat sistem ini |
| Golongan UKT | Kelompok besaran UKT tempat mahasiswa ditetapkan berdasarkan kemampuan ekonomi |
| Komponen penilaian | Satu unsur perhitungan yang membentuk hasil (misalnya satu variabel yang dinilai), ditampilkan sebagai satu baris di KF-01 |
| Penetapan resmi | Keputusan golongan UKT oleh kampus yang berwenang; bukan keluaran simulasi sistem ini |
| Simulasi | Hasil mesin KF-05 bila aturan resmi belum dipublikasikan; untuk versi seeder/prototipe, statusnya dijelaskan di footer halaman hasil, bukan diulang di tiap baris |
| Keberatan / peninjauan ulang | Pengajuan mahasiswa yang merasa hasil UKT tidak sesuai kondisi, untuk ditinjau kembali oleh petugas |
| Petugas UKT | Petugas yang ditunjuk kampus untuk mengelola rincian, pertanyaan, dan peninjauan keberatan |
| Pimpinan / auditor | Pembaca rekap dan jejak; akses rincian perorangan hanya bila kampus mengonfirmasi |
