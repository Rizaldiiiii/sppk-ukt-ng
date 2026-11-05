# Glassmorphism UI Design System

Color Palette, Typography & Visual Guidelines

**Untuk:** Sistem Peninjauan dan Pengajuan Keberatan UKT (SPPK-UKT UNG)
**Institusi:** Universitas Negeri Gorontalo (UNG)
**Pengguna:** Mahasiswa dan admin/petugas
**Tema:** Light saja (tidak ada dark mode)
**Stack:** Laravel + Blade + Tailwind CSS + Alpine.js
**Dokumen pendamping:** `Motion.md` (aturan animasi; ringkasannya ada di Bagian 9 dokumen ini)

**Arah visual:** Light Glassmorphism yang profesional, simple, dan tegas.
**Menggantikan:** `Neumorphism.md` — dokumen itu tidak lagi dipakai untuk proyek ini. Jangan membaca `Neumorphism.md`, jangan memakai shadow ganda neumorphism (`#D9D3CD` / `#FFFFFF`), dan jangan mencampur efek neumorphism dengan efek kaca dalam satu halaman. Bila ada konflik antara dokumen ini dan `Neumorphism.md`, dokumen ini yang menang.

**Cara memakai dokumen ini:** Dokumen ini berdiri sendiri untuk urusan desain visual. File yang menyertainya adalah `AGENTS.md`, `DESIGN.md`, `Motion.md`, dan `LOGO UNG (1).png`.
Bila ada hal yang tidak diatur di sini atau di file penyerta tersebut, ikuti spesifikasi sistem yang diberikan bersama tugas — jangan mencari sumber desain lain di luar daftar itu. Contoh status, menu, dan data di dokumen ini adalah contoh; sesuaikan dengan spesifikasi sistem tersebut sebelum dipakai produksi.

**Arti warna primer di dokumen ini:** primer `#f8f8f7` adalah warna dasar halaman (background). Tombol utama memakai warna sekunder `#550b14`, karena teks putih di atas `#f8f8f7` tidak terbaca. Permukaan kaca memakai putih transparan di atas background itu, bukan warna primer polos.

**Aturan migrasi dari neumorphism (wajib):** Ganti semua class `neu-*` menjadi padanan `glass-*` pada Bagian 11.3. Setelah migrasi selesai, pencarian `neu-` dan `shadow-neu-` di seluruh `resources/` harus menghasilkan 0. Jangan membiarkan kedua sistem hidup berdampingan.

## 1. Design Philosophy

Desain menggunakan pendekatan Light Glassmorphism, yaitu permukaan seperti kaca buram: tembus pandang secukupnya, blur di belakangnya, border terang yang tipis, dan bayangan lembut yang menyebar — tetapi dengan karakter yang tegas dan rapi, bukan dekoratif berlebihan.

Karakteristik utama:

- Permukaan kaca: putih transparan + `backdrop-blur`, bukan permukaan polos.
- Background berlapis: gradien sangat tipis dari palet yang sama, bukan gambar foto dan bukan warna gelap.
- Sudut seimbang: kartu kaca 16px, tombol dan input 10px, tidak tajam dan tidak terlalu membulat.
- Satu warna tindakan yang dominan (maroon gelap), satu warna highlight pendukung, dan sisanya netral hangat.
- Tipografi yang tegas untuk judul dan mudah dibaca untuk isi.
- Ruang yang lega: jarak antarkelompok lebih besar daripada jarak di dalam kelompok.
- Minimal decorative elements untuk menjaga fokus pada konten. Efek kaca adalah latar, bukan isi.

Prinsip utama: **tenang dilihat, tegas dibaca, jelas ditekan.** Kaca tidak boleh mengalahkan isi.

### 1.1 Aturan anti-ramai

Satu halaman tidak boleh dipadatkan sampai semua informasi muat tanpa scroll dengan cara mengecilkan font, merapatkan padding, atau menumpuk kartu.

Bila konten terasa penuh, lakukan ini sesuai urutan:

1. Tentukan satu tugas utama halaman itu, dan letakkan di posisi paling dominan.
2. Pindahkan konten sekunder ke tab, accordion, halaman detail, atau di bawah lipatan (scroll vertikal itu wajar).
3. Gunakan pagination atau filter pada tabel, bukan menampilkan semua baris sekaligus.
4. Sembunyikan kolom sekunder di layar sempit, bukan mengecilkan semuanya.
5. Terakhir, kurangi dekorasi — bukan kurangi keterbacaan. Bila kaca membuat halaman terasa ramai, kurangi transparansinya dulu, jangan kurangi ukuran teks.

Scroll vertikal bukan kegagalan desain. Halaman yang sesak dan sulit dipindai adalah kegagalan desain.

### 1.2 Aturan kaca yang tidak boleh dilanggar

1. Teks tidak pernah diletakkan langsung di atas gradien atau blob warna tanpa panel kaca di belakangnya.
2. Teks kecil (12–13px) hanya boleh ada di atas kaca dengan opasitas putih minimal 0.85, atau di atas permukaan solid.
3. Tabel, footer penjelasan simulasi/seeder, dan pesan error memakai panel hampir solid (Bagian 5.6 dan 5.12). Jangan dibuat transparan.
4. Tombol utama tidak dibuat transparan. Ia tetap solid maroon (Bagian 5.2).
5. Satu halaman memakai maksimal dua tingkat blur yang berbeda untuk kartu biasa, ditambah satu tingkat untuk modal/dropdown. Jangan membuat tiap kartu punya blur yang berbeda-beda.
6. Blob dan gradien latar tidak boleh bergerak, berkedip, atau berubah warna saat scroll.

---

## 2. Color Palette

Palet warna tetap sama dengan sistem sebelumnya, supaya identitas tidak berubah. Yang berubah hanyalah cara permukaan memakai warna itu: dari permukaan polos + shadow ganda, menjadi permukaan kaca transparan + blur + border terang.

### 2.1 Warna Utama

| Token | HEX | Penggunaan |
|---|---|---|
| Primer | `#f8f8f7` | Background utama halaman |
| Sekunder | `#550b14` | Tombol utama, CTA, navigasi aktif, angka kunci, link |
| Sekunder Hover | `#470912` | Hover tombol utama dan link |
| Sekunder Active | `#3A0710` | Active/pressed tombol utama |
| Sekunder Light | `#F5E8E9` | Background selected, navigasi aktif, highlight ringan |
| Sekunder Soft | `#FBEFF0` | Background aksen yang sangat ringan |
| Highlight | `#7e6961` | Aksen sekunder, ikon pendukung, seri grafik kedua |
| Highlight Dark | `#6B574F` | Teks highlight bila perlu penekanan lebih kuat |
| Highlight Light | `#EDE6E3` | Background aksen highlight, salah satu ujung gradien latar |

Catatan: warna sekunder adalah maroon yang sangat gelap. Ia dipakai secukupnya — tombol utama, status aktif, dan penanda penting — bukan sebagai background halaman dan bukan sebagai background kartu kaca. Background halaman tetap primer yang hampir putih, supaya halaman terasa lega dan profesional, bukan berat.

### 2.2 Background & Surface Colors

Background halaman bukan satu warna datar. Ia terdiri dari tiga lapis, dari bawah ke atas:

1. **Base:** `#f8f8f7` (primer), menutupi seluruh halaman.
2. **Gradien tipis:** linear 135deg dari `#f8f8f7` ke `#F3EDEA` ke `#F5E8E9`. Perbedaannya harus tipis — bila gradien terlihat mencolok, turunkan intensitasnya.
3. **Blob dekoratif:** dua sampai tiga bentuk bulat besar yang sangat buram di belakang konten, memakai `#F5E8E9` dan `#EDE6E3` dengan opasitas 0.45–0.60 dan `filter: blur(60px)`. Blob tidak boleh berada di belakang teks tanpa panel kaca, tidak boleh menutupi logo, dan posisinya tetap (`position: fixed`), tidak mengikuti scroll secara terpisah.

| Token | Nilai | Penggunaan |
|---|---|---|
| Background | `#f8f8f7` | Base halaman |
| Background Gradient | `linear-gradient(135deg, #f8f8f7 0%, #F3EDEA 55%, #F5E8E9 100%)` | Lapisan di atas base, tipis saja |
| Glass | `rgba(255, 255, 255, 0.60)` | Kartu kaca biasa |
| Glass Strong | `rgba(255, 255, 255, 0.75)` | Sidebar, header, kartu yang memuat teks padat |
| Glass Raised | `rgba(255, 255, 255, 0.88)` | Modal, dropdown, popover, toast |
| Glass Solid | `#FFFFFF` | Area tabel bila perlu kontras penuh dengan halaman |
| Glass Inset | `rgba(255, 255, 255, 0.55)` | Input dan search bar, di atas panel kaca induknya |
| Glass Hover | `rgba(255, 255, 255, 0.72)` | Hover pada baris dan permukaan kaca |

Catatan: opasitas di atas adalah nilai awal. Bila teks di atasnya sulit dibaca pada lebar 390px atau di layar yang terang, naikkan opasitasnya — jangan mengecilkan teks atau menipiskan warna teks.

### 2.3 Neutral Colors

| Token | HEX | Penggunaan |
|---|---|---|
| Text Primary | `#1C1917` | Judul dan teks utama |
| Text Secondary | `#57534E` | Deskripsi dan informasi pendukung |
| Text Muted | `#78716C` | Placeholder dan metadata (bukan untuk teks penting yang kecil) |
| Text Disabled | `#A8A29E` | Teks nonaktif saja; jangan dipakai untuk informasi bermakna |
| Border Glass | `rgba(255, 255, 255, 0.65)` | Border utama permukaan kaca |
| Border Subtle | `#E2DFDC` | Pemisah dekoratif di atas permukaan solid |
| Divider | `#DDD9D6` | Garis pemisah antarbagian |
| Border Input | `#78716C` | Batas input bila harus terlihat jelas (dipakai bersama border kaca) |

### 2.4 Status Colors

Warna status tidak dibuat transparan. Badge dan latar status memakai warna solid yang sama seperti sebelumnya, supaya status tetap terbaca di atas kaca.

| Token | Teks | Background | Penggunaan |
|---|---|---|---|
| Success | `#166534` | `#DCFCE7` | Berhasil, disetujui |
| Warning | `#92400E` | `#FEF3C7` | Perlu perhatian, menunggu |
| Error | `#991B1B` | `#FEE2E2` | Gagal, ditolak, error |
| Info | `#1E40AF` | `#DBEAFE` | Informasi, simulasi |

### 2.5 Gradient Palette

Gradien hanya dipakai tipis, di dua tempat saja:

- Tombol utama: `linear-gradient(135deg, #550b14, #6E1420)`.
- Garis aksen 3px di atas kartu utama atau di bawah judul halaman: `linear-gradient(90deg, #550b14, #7e6961)`.
- Gradien latar halaman: lihat Bagian 2.2.

Jangan memakai gradien pelangi, gradien gelap, atau gradien pada teks judul. Jangan memakai gradien sebagai background kartu kaca — kartu kaca memakai putih transparan + blur.

### 2.6 Pemeriksaan Kontras (WCAG AA)

Glassmorphism memiliki kelemahan alami yang sama dengan neumorphism: permukaan dan background cenderung berdekatan. Dokumen ini mewajibkan (bukan sekadar menyarankan) pemeriksaan berikut:

- Teks normal `#1C1917` di atas kaca putih 0.60 yang berada di atas gradien tipis harus memenuhi kontras minimal 4,5:1. Bila tidak memenuhi, naikkan opasitas kaca menjadi 0.75 atau 0.85.
- Teks sekunder `#57534E` hanya untuk teks 14px ke atas di atas kaca. Untuk teks 12–13px, pakai `#1C1917` atau `#57534E` di atas panel hampir solid.
- Teks muted `#78716C` tidak boleh dipakai untuk informasi penting di atas kaca transparan.
- Link dan tombol utama `#550b14` dengan teks putih memenuhi AA; jangan membuat tombol utama transparan.
- Status badge memakai pasangan teks + background pada Bagian 2.4, bukan warna saja.

Target kontras:

- Teks normal: minimal 4,5:1.
- Teks besar: minimal 3:1.
- Komponen UI dan indikator grafis penting: minimal 3:1 terhadap warna di sekitarnya.

---

## 3. Glass Shadow & Blur System

Shadow pada glassmorphism bukan shadow ganda gelap-terang. Ia adalah satu bayangan lembut yang menyebar ke bawah, ditambah garis terang tipis di tepi atas kaca (`inset 0 1px 0 rgba(255,255,255,0.7)`).

### 3.1 Blur Scale

| Token | Nilai | Penggunaan |
|---|---|---|
| Blur SM | `blur(8px)` | Input kecil, chip |
| Blur MD | `blur(16px)` | Kartu kaca biasa, sidebar, header |
| Blur LG | `blur(24px)` | Modal, dropdown, toast |
| Blur Latar | `blur(60px)` | Blob dekoratif di belakang konten saja |

Selalu pasangkan `backdrop-filter` dengan `-webkit-backdrop-filter` yang sama, dan tambahkan `background` transparan pada elemen yang sama. `backdrop-filter` tanpa background transparan tidak terlihat.

### 3.2 Standard Shadows

| Token | CSS Value | Penggunaan |
|---|---|---|
| Shadow XS | `0 2px 8px rgba(28, 25, 23, 0.06), inset 0 1px 0 rgba(255, 255, 255, 0.7)` | Elemen kecil |
| Shadow SM | `0 4px 16px rgba(28, 25, 23, 0.08), inset 0 1px 0 rgba(255, 255, 255, 0.7)` | Tombol sekunder kaca |
| Shadow MD | `0 8px 32px rgba(28, 25, 23, 0.08), inset 0 1px 0 rgba(255, 255, 255, 0.7)` | Kartu kaca |
| Shadow LG | `0 16px 48px rgba(28, 25, 23, 0.12), inset 0 1px 0 rgba(255, 255, 255, 0.7)` | Panel besar, modal |

### 3.3 Inset / Input

Input kaca tidak memakai inset shadow gelap seperti neumorphism. Ia memakai background transparan + border kaca + shadow sangat ringan:

```css
box-shadow:
  0 2px 8px rgba(28, 25, 23, 0.05),
  inset 0 1px 0 rgba(255, 255, 255, 0.7);
```

Penggunaan:

- Input field.
- Search bar.
- Area form.
- Selected controls.

### 3.4 Button States

Tombol utama tetap solid, jadi state-nya sama seperti sebelumnya kecuali shadow-nya diganti ke sistem kaca:

- Default: gradien tipis `#550b14 → #6E1420`, shadow SM.
- Hover: background `#470912`, naik 1px (`translateY(-1px)`), shadow MD.
- Active / Pressed: background `#3A0710`, skala 0.97, shadow XS.

Tombol sekunder kaca:

- Default: putih 0.60 + blur MD + border kaca + shadow SM.
- Hover: putih 0.72 + naik 1px, shadow MD.
- Active: skala 0.97, shadow XS.

Catatan: shadow dan blur tidak boleh menjadi satu-satunya penanda interaksi. Selalu ada perubahan lain yang terlihat: warna, ikon, label, atau outline fokus (lihat Bagian 10).

---

## 4. Typography System

Tidak berubah dari sistem sebelumnya. Font, skala, dan warna teks dipertahankan supaya pergantian tema tidak mengubah keterbacaan.

### 4.1 Recommended Font Families

Menggunakan kombinasi dua keluarga font:

**Primary Font: Poppins**

Digunakan untuk:

- Heading.
- Page title.
- Card title.
- Navigation label.
- Angka statistik yang ditonjolkan.
- Label tombol.

Karakteristik:

- Geometris, tegas, dan mudah dipindai.
- Cocok untuk kesan profesional dan simple.
- Perlu diimbangi dengan body yang netral agar tidak terasa berat.

Google Fonts: https://fonts.google.com/specimen/Poppins

**Secondary Font: Inter**

Digunakan untuk:

- Body text.
- Form labels.
- Input fields.
- Table content.
- Deskripsi dan metadata.

Karakteristik:

- Sangat mudah dibaca.
- Proporsi huruf dirancang untuk antarmuka digital.
- Cocok untuk informasi yang cukup padat seperti tabel peninjauan.
- Konsisten pada berbagai ukuran layar.

Google Fonts: https://fonts.google.com/specimen/Inter

### 4.2 Typography Scale

Satu catatan teknis: Poppins tidak memiliki weight 750, jadi H2 memakai 700.

| Elemen | Font | Size | Weight | Line Height |
|---|---|---|---|---|
| Display | Poppins | 40px | 800 | 1.15 |
| H1 | Poppins | 32px | 800 | 1.2 |
| H2 | Poppins | 26px | 700 | 1.25 |
| H3 | Poppins | 21px | 700 | 1.3 |
| H4 | Poppins | 18px | 700 | 1.35 |
| Body Large | Inter | 16px | 400 | 1.6 |
| Body | Inter | 14px | 400 | 1.6 |
| Body Small | Inter | 13px | 400 | 1.5 |
| Caption | Inter | 12px | 400 | 1.4 |
| Button | Poppins | 14px | 700 | 1.2 |
| Statistic | Poppins | 28px | 800 | 1.2 |

Catatan: teks dasar adalah 14px (Body), dengan 16px untuk Body Large pada paragraf penjelas yang penting. Jangan mengecilkan body di bawah 14px untuk memuat lebih banyak konten dalam satu layar — itu melanggar Bagian 1.1.

### 4.3 Typography Color

| Elemen | Color |
|---|---|
| Main Heading | `#1C1917` |
| Body Text | `#1C1917` |
| Secondary Text | `#57534E` |
| Placeholder | `#78716C` |
| Link | `#550b14` |
| Disabled | `#A8A29E` |

---

## 5. Component Design Guidelines

Aturan umum: **jangan memaksakan komponen.** Bagian 5.1–5.10 adalah komponen inti yang hampir pasti dipakai di sistem UKT. Bagian 5.11 adalah komponen kondisional: hanya didokumentasikan dan dibangun bila ada fitur yang benar-benar memakainya. Komponen pajangan tanpa data dan tanpa aksi tidak dibuat.

Setiap komponen inti harus memiliki state yang dirancang: default, hover, focus-visible, active/pressed, disabled, loading (bila ada aksi), error (bila ada input), dan empty (bila menampilkan data).

### 5.1 Cards

- Border radius: 16px untuk kartu kaca utama, 12px untuk kartu kecil di dalamnya.
- Background: Glass (`rgba(255, 255, 255, 0.60)`) + `backdrop-filter: blur(16px)`.
- Border: 1px solid `rgba(255, 255, 255, 0.65)`.
- Shadow: MD (Bagian 3.2).
- Padding: 24–32px (lega).
- Satu kartu utama per layar boleh lebih besar daripada kartu pendukung.
- Jangan menumpuk lebih dari 4 kartu setara dalam satu baris di desktop tanpa alasan; bila daftarnya panjang, pakai tabel atau pagination.
- Kartu yang memuat teks padat atau angka penting memakai Glass Strong (0.75), bukan Glass biasa.

### 5.2 Buttons

- Border radius: 10px.
- Height: 44–48px.
- Primary button menggunakan warna sekunder `#550b14` dengan gradien tipis (Bagian 2.5) dan teks putih. Ia tidak transparan dan tidak memakai blur.
- Secondary button menggunakan kaca: putih 0.60 + blur MD + border kaca, teks Text Primary.
- Ghost/text button hanya untuk aksi tersier; warnanya sekunder.
- Destructive button memakai Error (`#991B1B`), dan selalu didampingi konfirmasi untuk aksi yang tidak bisa dibatalkan.
- Active state memakai skala 0.97 (Bagian 3.4), bukan inset shadow gelap.
- Tombol loading mempertahankan lebarnya, menampilkan spinner, dan menonaktifkan klik ganda.

### 5.3 Input Fields

- Border radius: 10px.
- Background: Glass Inset (`rgba(255, 255, 255, 0.55)`) + `backdrop-filter: blur(12px)`.
- Border: 1px solid `rgba(255, 255, 255, 0.70)` ditambah outline tipis `#E2DFDC` bila berada di atas background yang sangat terang.
- Height: 44–48px.
- Focus state menggunakan outline sekunder `#550b14` 2px dengan offset 2px yang terlihat jelas, bukan hanya perubahan blur atau shadow. Saat focus, background input naik menjadi 0.75.
- Label selalu terlihat di atas field, bukan hanya placeholder.
- Pesan error menempel pada field-nya, memakai warna Error dan ikon, di atas latar solid Error BG, bukan di atas kaca transparan.

### 5.4 Modal

- Border radius: 16px.
- Background: Glass Raised (`rgba(255, 255, 255, 0.88)`) + `backdrop-filter: blur(24px)`.
- Shadow: LG.
- Overlay menggunakan warna gelap hangat dengan transparansi secukupnya: `rgba(28, 25, 23, 0.45)`.
- Pastikan fokus keyboard tidak hilang dan bisa kembali ke pemicu modal saat modal ditutup. Lihat animasi modal di `Motion.md` Bagian 7.
- Modal dipakai untuk konfirmasi dan form pendek saja. Form pengajuan yang panjang memakai halaman sendiri, bukan modal yang harus di-scroll berlapis-lapis.

### 5.5 Navigation

**Sidebar kiri** sebagai navigasi utama di desktop.

- Sidebar memakai Glass Strong (`rgba(255, 255, 255, 0.75)`) + `backdrop-filter: blur(16px)` + border kanan kaca.
- Navigasi aktif menggunakan Sekunder Light (`#F5E8E9`) dengan opasitas 0.90, bukan kaca transparan tipis.
- Icon navigasi aktif menggunakan Sekunder (`#550b14`); nonaktif memakai Text Secondary.
- Label menggunakan Text Primary, font Poppins untuk label navigasi.
- Logo dan nama institusi berada di atas sidebar (lihat Bagian 5.10).
- Badge role pengguna (Mahasiswa / Admin) tampil di area profil sidebar, karena kedua role melihat menu dan aksi yang berbeda.
- Contoh menu (sesuaikan dengan fitur nyata, bukan daftar wajib): mahasiswa — Beranda, Pengajuan Saya, Buat Pengajuan, Bantuan; admin/petugas — Beranda, Daftar Pengajuan, Peninjauan, Rekap. Menu yang tidak dimiliki suatu role disembunyikan, bukan hanya dinonaktifkan tanpa penjelasan, kecuali aturan produk menentukan lain.
- Header publik (guest) memakai Glass Strong + blur 12px dan tetap menempel di atas (`sticky`), supaya isi yang di-scroll di belakangnya tetap terbaca lewat blur, bukan menembus teks header.

### 5.6 Tables

Untuk tabel data, jangan menerapkan efek kaca berlebihan. Tabel adalah tempat keterbacaan paling penting.

Gunakan:

- Background area tabel: putih 0.92 atau `#FFFFFF` bila perlu kontras penuh dengan halaman. Jangan memakai Glass 0.60 untuk tabel.
- Divider halus (`#DDD9D6`).
- Header yang memiliki kontras jelas (teks Text Primary, font Poppins 600), background `#F3F2F0` dengan opasitas 0.90.
- Tinggi baris 56–64px (lega).
- Hover state yang ringan (`#F1EFED`).
- Status badge dengan warna status solid (Bagian 2.4).
- Kolom angka (misalnya nominal UKT) rata kanan dengan angka tabular.
- Format locale Indonesia: `Rp 1.250.000`, tanggal `12 Okt 2026`.
- Di mobile, tabel diubah menjadi kartu kaca (satu baris = satu kartu), bukan tabel transparan yang mengecil.

### 5.7 Dropdown

- Radius 12px, background Glass Raised (0.88) + blur 24px, shadow LG.
- Item aktif/hover memakai Sekunder Light.
- Panah indikator berputar saat terbuka (lihat `Motion.md` Bagian 6).
- Dropdown tidak boleh menutupi field pemicunya tanpa alasan.

### 5.8 Toast / Notifikasi

- Radius 12px, background Glass Raised (0.88) + blur 24px, shadow LG.
- Ikon dan label status wajib ada; warna saja tidak cukup.
- Posisi konsisten di seluruh aplikasi; durasi tampil cukup untuk dibaca, dan bisa ditutup manual.
- Toast sukses/gagal setelah pengajuan dikirim atau peninjauan disimpan adalah contoh pemakaian yang sah; toast dekoratif tidak dibuat.

### 5.9 Tabs

- Tab aktif memakai teks sekunder gelap dengan garis bawah sekunder 2px atau background Sekunder Light; tab nonaktif memakai Text Secondary.
- Tab dipakai untuk memecah halaman yang ramai (misalnya detail pengajuan: Ringkasan, Dokumen, Riwayat) daripada menumpuk semuanya dalam satu scroll panjang.
- Panel isi tab memakai kartu kaca biasa, bukan tab transparan tanpa latar.

### 5.10 Brand: Logo dan Nama Institusi

Wajib ada, dan perlakuannya khusus:

- **Logo:** `LOGO UNG (1).png`. Jangan mewarnai ulang, jangan memotong, jangan menarik (stretch) logo, dan jangan memakai warna-warna di dalam logo (ungu, biru, oranye, krem) sebagai palet UI. Palet UI tetap Bagian 2 dokumen ini.
- **Penempatan:** atas sidebar (desktop) dan header halaman login / halaman awal. Di sidebar, logo 40–48px berdampingan dengan teks. Di halaman login, logo 56–64px di atas form.
- **Nama:** tulis lengkap `Universitas Negeri Gorontalo` di samping logo pada tampilan desktop dan halaman login. Singkatan `UNG` hanya boleh dipakai bila ruang benar-benar tidak memungkinkan (misalnya sidebar yang diciutkan menjadi ikon), dan logo tetap harus memiliki teks alternatif `Universitas Negeri Gorontalo` untuk aksesibilitas.
- **Latar logo:** selalu di atas permukaan terang dan hampir solid (`#f8f8f7`, putih 0.85 ke atas, atau putih). Jangan menaruh logo di atas tombol maroon, gradien, blob warna, atau kaca transparan tipis. Bila sidebar memakai kaca, beri panel solid tipis di belakang logo.
- **Penempatan di proyek Laravel:** salin `LOGO UNG (1).png` menjadi `public/images/logo-ung.png` saat implementasi, lalu rujuk file itu dari layout Blade.

### 5.11 Komponen Kondisional (hanya bila fiturnya ada)

| Komponen | Dibuat bila | Contoh di sistem UKT |
|---|---|---|
| Stepper | Alur multi-langkah yang berurutan | Pengajuan keberatan bertahap (data, alasan, dokumen, review) |
| Breadcrumb | Halaman detail yang dalam | Beranda / Daftar Pengajuan / Detail Pengajuan |
| Pagination | Daftar melebihi satu layar | Tabel peninjauan admin |
| Grafik | Ada kebutuhan rekap/visualisasi | Rekap pengajuan per status untuk admin (bila diminta) |
| Kalender | Ada tenggat atau jadwal yang dipilih pengguna | Batas waktu pengajuan, jadwal peninjauan (bila diminta) |
| Upload file | Ada dokumen pendukung | Upload bukti pendukung pengajuan |

Bila fitur di kolom tengah tidak ada, komponennya tidak dibuat dan tidak perlu dicari-cari alasannya.

### 5.12 Panel Penjelasan Simulasi / Seeder (wajib, khusus SPPK-UKT UNG)

Panel ini bukan komponen umum. Ia adalah footer penjelasan status data di halaman hasil (`/hasil-saya`), dan aturannya mengikat:

- Panel memakai Glass Raised yang hampir solid: `rgba(255, 255, 255, 0.92)` + blur 16px + border Sekunder Light.
- Jangan memakai Glass biasa (0.60) untuk panel ini. Teks penjelasan harus terbaca penuh pada lebar 390px, 768px, dan 1280px.
- Isi panel tidak diubah oleh pergantian tema: hanya daftar bagian mana saja yang merupakan seeder/simulasi, sesuai spesifikasi sistem. Jangan menambah pernyataan "bukan penetapan resmi" di tiap baris tabel.
- Jangan memindahkan panel ini ke modal, toast, atau tooltip.

---

## 6. Border Radius System

Kepribadian sudut: **seimbang, sedikit lebih membulat pada kartu kaca** supaya tepi blur terlihat lembut, tetapi tombol dan input tetap tegas.

| Token | Value | Penggunaan |
|---|---|---|
| Radius XS | 6px | Elemen sangat kecil (checkbox, dot) |
| Radius SM | 8px | Badge, chip, tag kecil |
| Radius MD | 10px | Input, button, dropdown |
| Radius LG | 16px | Cards kaca, modal, panel besar |
| Radius Full | 9999px | Pills dan avatar |

Aturan sudut bersarang: radius dalam = radius luar − padding. Jangan memakai radius 20px ke atas pada kartu. Satu-satunya pengecualian adalah blob dekoratif di latar, yang memang bulat penuh.

---

## 7. Spacing System

Menggunakan kelipatan 4px sebagai basis. Tidak berubah dari sistem sebelumnya.

| Token | Value |
|---|---|
| 1 | 4px |
| 2 | 8px |
| 3 | 12px |
| 4 | 16px |
| 5 | 20px |
| 6 | 24px |
| 8 | 32px |
| 10 | 40px |
| 12 | 48px |
| 16 | 64px |

Rekomendasi (kepadatan lega):

- Card padding: 24–32px.
- Section spacing: 40–64px.
- Form field spacing: 20–24px.
- Component internal spacing: 12–16px.
- Page padding desktop: 32px; tablet: 24px; mobile: 16px.
- Gap di dalam kelompok < gap antarkelompok < gap antarbagian.

---

## 8. Iconography

Rekomendasi: Lucide Icons.

Website: https://lucide.dev/

Karakteristik:

- Outline icons.
- Stroke konsisten.
- Minimalis.
- Mudah diintegrasikan dengan Blade.

Panduan:

- Default size: 20px.
- Small icon: 16px.
- Large icon: 24px.
- Stroke width: 1.8–2px.
- Gunakan warna Sekunder atau Text Secondary.
- Ikon dipakai pada navigasi, aksi utama, status, dan empty state. Tidak perlu ikon di setiap baris tabel bila tidak menambah kejelasan.

Hindari mencampurkan berbagai gaya ikon dalam satu halaman.

---

## 9. Motion & Interaction

Ringkasan saja; spesifikasi lengkap ada di `Motion.md`. `Motion.md` tetap berlaku penuh untuk glassmorphism, kecuali penyesuaian blur di bawah.

| Interaction | Duration | Behavior |
|---|---|---|
| Hover | 150ms | Shadow + blur transition |
| Button Press | 100ms | Skala 0.97 |
| Modal | 200ms | Fade + scale |
| Dropdown | 150ms | Fade + translate |
| Card Hover | 200ms | Naik 4px + shadow MD + blur naik satu tingkat |

Penyesuaian khusus kaca:

- Transisi `backdrop-filter` memakai durasi yang sama dengan transisi shadow (150–200ms) dan easing yang sama. Jangan menganimasikan blur dari 0 ke 24px saat scroll reveal — blur awal kartu sudah 16px sejak awal.
- Hover kartu kaca: `transform: translateY(-4px)` + shadow MD. Jangan mengubah opasitas background saat hover lebih dari 0.12.
- Jangan menganimasikan blob latar.

Rekomendasi easing:

```css
transition-timing-function:
  cubic-bezier(0.2, 0.8, 0.2, 1);
```

Motion harus membantu pengguna memahami perubahan state, bukan sekadar dekorasi. Hormati preferensi `prefers-reduced-motion` (lihat `Motion.md` Bagian 15). Pada mode itu, transisi blur dimatikan, tetapi blur statis kartu tetap ada.

---

## 10. Accessibility Guidelines

Glassmorphism memiliki kelemahan alami dalam hal kontras karena background, permukaan kaca, dan blur cenderung berdekatan. Untuk mengatasinya, dokumen ini mewajibkan (bukan sekadar menyarankan):

1. Jangan menggunakan blur atau shadow sebagai satu-satunya penanda interaksi.
2. Pastikan teks memenuhi standar kontras WCAG AA (Bagian 2.6). Bila ragu, naikkan opasitas kaca, jangan turunkan ukuran teks.
3. Gunakan outline fokus keyboard yang jelas: sekunder `#550b14` 2px dengan offset 2px.
4. Bedakan disabled, hover, active, dan selected state dengan lebih dari satu isyarat visual.
5. Jangan hanya mengandalkan warna untuk menunjukkan status; selalu ada ikon dan label.
6. Pastikan input memiliki label yang terlihat.
7. Hindari teks terlalu kecil pada background berwarna atau di atas kaca tipis.
8. Target sentuh minimal 44×44px, terutama pada tampilan mobile.
9. Gunakan elemen semantik (`nav`, `main`, `table`, `button`, `label`) dan nama aksesibel untuk kontrol ikon saja.
10. Jangan menaruh teks penting di atas blob warna, gradien kuat, atau foto.

Target kontras:

- Teks normal: minimal 4,5:1.
- Teks besar: minimal 3:1.
- Komponen UI dan indikator grafis penting: minimal 3:1 terhadap warna di sekitarnya.

---

## 11. CSS Design Tokens & Tailwind Config

Proyek ini memakai Tailwind v4, jadi token didefinisikan di `@theme` di `resources/css/app.css`, bukan di `tailwind.config.js`. Jangan membuat `tailwind.config.js` baru dan jangan mempertahankan dua sumber token yang berbeda.

### 11.1 CSS Variables

```css
:root {
  /* Primer, sekunder, highlight */
  --color-primer: #f8f8f7;
  --color-sekunder: #550b14;
  --color-sekunder-hover: #470912;
  --color-sekunder-active: #3A0710;
  --color-sekunder-light: #F5E8E9;
  --color-sekunder-soft: #FBEFF0;
  --color-highlight: #7e6961;
  --color-highlight-dark: #6B574F;
  --color-highlight-light: #EDE6E3;

  /* Background */
  --color-background: #f8f8f7;
  --color-background-gradient: linear-gradient(135deg, #f8f8f7 0%, #F3EDEA 55%, #F5E8E9 100%);

  /* Glass surfaces */
  --color-glass: rgba(255, 255, 255, 0.60);
  --color-glass-strong: rgba(255, 255, 255, 0.75);
  --color-glass-raised: rgba(255, 255, 255, 0.88);
  --color-glass-solid: #FFFFFF;
  --color-glass-inset: rgba(255, 255, 255, 0.55);
  --color-glass-hover: rgba(255, 255, 255, 0.72);
  --color-glass-footer: rgba(255, 255, 255, 0.92);

  /* Text */
  --color-text-primary: #1C1917;
  --color-text-secondary: #57534E;
  --color-text-muted: #78716C;
  --color-text-disabled: #A8A29E;

  /* Border */
  --color-border-glass: rgba(255, 255, 255, 0.65);
  --color-border-subtle: #E2DFDC;
  --color-divider: #DDD9D6;
  --color-border-input: #78716C;

  /* Status: teks + background (solid, jangan transparan) */
  --color-success: #166534;
  --color-success-bg: #DCFCE7;
  --color-warning: #92400E;
  --color-warning-bg: #FEF3C7;
  --color-error: #991B1B;
  --color-error-bg: #FEE2E2;
  --color-info: #1E40AF;
  --color-info-bg: #DBEAFE;

  /* Blur */
  --glass-blur-sm: blur(8px);
  --glass-blur-md: blur(16px);
  --glass-blur-lg: blur(24px);

  /* Shadows (kaca: satu bayangan lembut + garis terang atas) */
  --shadow-glass-xs: 0 2px 8px rgba(28, 25, 23, 0.06), inset 0 1px 0 rgba(255, 255, 255, 0.7);
  --shadow-glass-sm: 0 4px 16px rgba(28, 25, 23, 0.08), inset 0 1px 0 rgba(255, 255, 255, 0.7);
  --shadow-glass-md: 0 8px 32px rgba(28, 25, 23, 0.08), inset 0 1px 0 rgba(255, 255, 255, 0.7);
  --shadow-glass-lg: 0 16px 48px rgba(28, 25, 23, 0.12), inset 0 1px 0 rgba(255, 255, 255, 0.7);

  /* Radius */
  --radius-xs: 6px;
  --radius-sm: 8px;
  --radius-md: 10px;
  --radius-lg: 16px;
  --radius-full: 9999px;

  /* Typography */
  --font-heading: "Poppins", sans-serif;
  --font-body: "Inter", sans-serif;
}
```

### 11.2 Tailwind v4 `@theme` (di `resources/css/app.css`)

Pindahkan nilai yang sama ke `@theme`. Jangan menulis nilai hex mentah di dalam markup Blade bila sudah ada tokennya.

```css
@theme {
  --font-sans: "Inter", ui-sans-serif, system-ui, sans-serif;
  --font-heading: "Poppins", ui-sans-serif, system-ui, sans-serif;
  --font-body: "Inter", ui-sans-serif, system-ui, sans-serif;

  --color-primer: #f8f8f7;
  --color-sekunder: #550b14;
  --color-sekunder-hover: #470912;
  --color-sekunder-active: #3A0710;
  --color-sekunder-light: #F5E8E9;
  --color-sekunder-soft: #FBEFF0;

  --color-highlight: #7e6961;
  --color-highlight-dark: #6B574F;
  --color-highlight-light: #EDE6E3;

  --color-glass: rgba(255, 255, 255, 0.60);
  --color-glass-strong: rgba(255, 255, 255, 0.75);
  --color-glass-raised: rgba(255, 255, 255, 0.88);

  --color-ink: #1C1917;
  --color-ink-secondary: #57534E;
  --color-ink-muted: #78716C;

  --color-success: #166534;
  --color-success-bg: #DCFCE7;
  --color-warning: #92400E;
  --color-warning-bg: #FEF3C7;
  --color-error: #991B1B;
  --color-error-bg: #FEE2E2;
  --color-info: #1E40AF;
  --color-info-bg: #DBEAFE;

  --radius-xs: 6px;
  --radius-sm: 8px;
  --radius-md: 10px;
  --radius-lg: 16px;

  --shadow-glass-xs: 0 2px 8px rgba(28, 25, 23, 0.06), inset 0 1px 0 rgba(255, 255, 255, 0.7);
  --shadow-glass-sm: 0 4px 16px rgba(28, 25, 23, 0.08), inset 0 1px 0 rgba(255, 255, 255, 0.7);
  --shadow-glass-md: 0 8px 32px rgba(28, 25, 23, 0.08), inset 0 1px 0 rgba(255, 255, 255, 0.7);
  --shadow-glass-lg: 0 16px 48px rgba(28, 25, 23, 0.12), inset 0 1px 0 rgba(255, 255, 255, 0.7);
}
```

Aturan pemakaian token: komponen merujuk ke token, bukan nilai hex mentah di dalam markup. Bila perlu nilai baru, tambahkan token dulu. Nilai hardcode `#550b14` dan `#f8f8f7` yang tersebar di Blade harus diganti ke token selama migrasi.

### 11.3 Pemetaan Class Lama ke Baru (wajib dipakai saat migrasi)

| Class lama (neumorphism) | Class baru (glassmorphism) | Catatan |
|---|---|---|
| `.neu-card` | `.glass-card` | Putih 0.60 + blur 16px + border kaca + shadow MD, radius 16px |
| `.neu-card-raised` | `.glass-card-raised` | Putih 0.75 + blur 16px + shadow MD, radius 16px |
| `.neu-inset` | `.glass-inset` | Putih 0.55 + blur 12px, untuk area cekung/form |
| `.neu-input` | `.glass-input` | Putih 0.55 + blur 12px + border kaca, focus outline `#550b14` 2px |
| `.neu-btn-primary` | `.glass-btn-primary` | Tetap solid gradien `#550b14 → #6E1420`, teks putih, tanpa blur |
| `.neu-btn-secondary` | `.glass-btn-secondary` | Putih 0.60 + blur 16px + border kaca |
| `.shadow-neu-xs` / `sm` / `md` / `lg` | `.shadow-glass-xs` / `sm` / `md` / `lg` | Lihat Bagian 3.2 |
| `.shadow-neu-inset` | Hapus, pakai shadow input pada Bagian 3.3 | Jangan bawa inset gelap ke tema kaca |
| `.accent-bar` | `.accent-bar` (tetap) | Gradien 90deg `#550b14 → #7e6961`, tinggi 3px |
| `.interactive-card` | `.interactive-card` (tetap, definisi diubah) | Hover: naik 4px + shadow MD, durasi 200ms |

Class `.page-content`, `.scroll-progress`, `.loading-spinner`, `.skeleton`, dan aturan `prefers-reduced-motion` di `app.css` tetap dipakai, definisinya disesuaikan ke Bagian 9 bila perlu.

---

## 12. Responsive: Desktop-First

Desain dirancang dari desktop dulu, lalu disesuaikan ke bawah. Desktop-first bukan berarti mobile diabaikan.

| Lebar | Perilaku |
|---|---|
| Desktop (1280px ke atas) | Sidebar kiri penuh + grid konten fluid; desain utama dibuat di sini |
| Tablet (768–1279px) | Sidebar menjadi rail ikon atau drawer kaca; grid 2 kolom menjadi 1–2 kolom |
| Mobile (di bawah 768px) | Sidebar menjadi drawer kaca; tabel menjadi kartu/key-value; filter masuk ke panel bawah |

Aturan yang tetap berlaku di semua lebar:

- Tabel di mobile diubah menjadi kartu (judul, status, 1–2 field kunci, menu aksi), bukan tabel yang mengecil sampai tidak terbaca.
- Target sentuh minimal 44×44px di tablet dan mobile.
- Konten tidak boleh menempel ke tepi container; padding halaman mobile minimal 16px.
- Blur pada mobile dijaga tetap 16px untuk kartu, tetapi blob latar dikurangi menjadi dua dan opasitasnya diturunkan, supaya performa tetap ringan.
- Periksa hasil di tiga lebar (≈390, 768, 1280) sebelum sebuah layar dinyatakan selesai. Pada tiap lebar, periksa juga keterbacaan teks di atas kaca.

---

## 13. Recommended Design Direction

Kombinasi final yang direkomendasikan:

| Category | Selection |
|---|---|
| Main Style | Light Glassmorphism |
| Karakter | Profesional, simple, tegas |
| Tema | Light saja |
| Primer | `#f8f8f7` (hampir putih, background dasar) |
| Sekunder | `#550b14` (maroon gelap, tindakan utama) |
| Highlight | `#7e6961` (aksen sekunder) |
| Gradien | Tipis saja: tombol utama, garis aksen 3px, dan latar halaman |
| Background | Gradien tipis primer + blob buram `#F5E8E9` / `#EDE6E3` |
| Heading Font | Poppins |
| Body Font | Inter |
| Icon Style | Lucide Outline |
| Shadow Style | Satu bayangan lembut + garis terang atas |
| Blur | 16px kartu, 24px modal/dropdown |
| Border Radius | 10px tombol/input, 16px kartu/modal |
| Density | Lega |
| Navigasi | Sidebar kiri kaca (desktop-first) |
| Motion | Subtle & Smooth (lihat `Motion.md`) |
| Brand | Logo UNG + `Universitas Negeri Gorontalo` |

Final Design Principle:

> Antarmuka yang tenang, tegas, dan mudah dibaca: permukaan kaca yang jernih, satu warna tindakan yang jelas, dan ruang yang lega — tanpa satu halaman pun yang dipadatkan sampai sesak, dan tanpa teks yang tenggelam di balik kaca.

Desain harus tetap mengutamakan keterbacaan, aksesibilitas, konsistensi, dan kemudahan penggunaan. Efek visual glassmorphism merupakan pendukung pengalaman pengguna, bukan pengganti hierarki visual dan usability.
