# Neumorphism UI Design System

Color Palette, Typography & Visual Guidelines

**Untuk:** Sistem Peninjauan dan Pengajuan Keberatan UKT (SPPK-UKT UNG)
**Institusi:** Universitas Negeri Gorontalo (UNG)
**Pengguna:** Mahasiswa dan admin/petugas
**Tema:** Light saja (tidak ada dark mode)
**Stack:** Laravel + Blade + Tailwind CSS + Alpine.js
**Dokumen pendamping:** `Motion.md` (aturan animasi; ringkasannya ada di Bagian 9 dokumen ini)

**Arah visual:** Light Neumorphism yang profesional, simple, dan tegas.
**Referensi visual:** `ss contoh neumorphism.jpeg` — kanvas terang, kartu lembut
dengan bayangan halus, isi tidak dipadatkan, dan satu elemen dominan per area.

**Cara memakai dokumen ini:** Dokumen ini berdiri sendiri untuk urusan
desain visual. File yang menyertainya adalah `AGENTS.md`, `DESIGN.md`,
`Motion.md`, `LOGO UNG (1).png`, dan `ss contoh neumorphism.jpeg`.
Bila ada hal yang tidak diatur di sini atau di file penyerta tersebut,
ikuti spesifikasi sistem yang diberikan bersama tugas — jangan mencari
sumber desain lain di luar daftar itu. Contoh status, menu, dan data di
dokumen ini adalah contoh; sesuaikan dengan spesifikasi sistem tersebut
sebelum dipakai produksi.

**Arti warna primer di dokumen ini:** primer `#f8f8f7` adalah warna dasar
permukaan (background dan kartu). Tombol utama memakai warna sekunder
`#550b14`, karena teks putih di atas `#f8f8f7` tidak terbaca.

## 1. Design Philosophy

Desain menggunakan pendekatan Light Neumorphism (Soft UI), yaitu gaya
antarmuka yang memberikan ilusi kedalaman melalui permainan cahaya dan
bayangan, tetapi dengan karakter yang tegas dan rapi.

Karakteristik utama:

- Soft shadows ganda untuk memberikan kesan elevated dan inset.
- Sudut seimbang (8–12px), tidak tajam dan tidak terlalu membulat.
- Permukaan terang yang hampir putih, dengan pemisahan elemen yang tetap jelas.
- Satu warna tindakan yang dominan (maroon gelap), satu warna highlight
  pendukung, dan sisanya netral hangat.
- Tipografi yang tegas untuk judul dan mudah dibaca untuk isi.
- Ruang yang lega: jarak antarkelompok lebih besar daripada jarak di dalam
  kelompok.
- Minimal decorative elements untuk menjaga fokus pada konten.

Prinsip utama: **tenang dilihat, tegas dibaca, jelas ditekan.**

### 1.1 Aturan anti-ramai

Satu halaman tidak boleh dipadatkan sampai semua informasi muat tanpa scroll
dengan cara mengecilkan font, merapatkan padding, atau menumpuk kartu.

Bila konten terasa penuh, lakukan ini sesuai urutan:

1. Tentukan satu tugas utama halaman itu, dan letakkan di posisi paling
   dominan.
2. Pindahkan konten sekunder ke tab, accordion, halaman detail, atau
   di bawah lipatan (scroll vertikal itu wajar).
3. Gunakan pagination atau filter pada tabel, bukan menampilkan semua baris
   sekaligus.
4. Sembunyikan kolom sekunder di layar sempit, bukan mengecilkan semuanya.
5. Terakhir, kurangi dekorasi — bukan kurangi keterbacaan.

Scroll vertikal bukan kegagalan desain. Halaman yang sesak dan sulit dipindai
adalah kegagalan desain.

---

## 2. Color Palette

Palet dirancang dari tiga warna yang diminta, ditambah netral hangat dan
warna status yang kontrasnya sudah diperiksa terhadap WCAG AA.

### 2.1 Warna Utama

| Token | HEX | Penggunaan |
|---|---|---|
| Primer | `#f8f8f7` | Background utama dan permukaan dasar neumorphism |
| Sekunder | `#550b14` | Tombol utama, CTA, navigasi aktif, angka kunci, link |
| Sekunder Hover | `#470912` | Hover tombol utama dan link |
| Sekunder Active | `#3A0710` | Active/pressed tombol utama |
| Sekunder Light | `#F5E8E9` | Background selected, navigasi aktif, highlight ringan |
| Sekunder Soft | `#FBEFF0` | Background aksen yang sangat ringan |
| Highlight | `#7e6961` | Aksen sekunder, ikon pendukung, seri grafik kedua |
| Highlight Dark | `#6B574F` | Teks highlight bila perlu penekanan lebih kuat |
| Highlight Light | `#EDE6E3` | Background aksen highlight |

Catatan: warna sekunder adalah maroon yang sangat gelap. Ia dipakai
secukupnya — tombol utama, status aktif, dan penanda penting — bukan sebagai
background halaman. Background halaman tetap primer yang hampir putih, supaya
halaman terasa lega dan profesional, bukan berat.

### 2.2 Background & Surface Colors

| Token | HEX | Penggunaan |
|---|---|---|
| Background | `#f8f8f7` | Background utama |
| Background Soft | `#F3F2F0` | Area sekunder yang sedikit lebih gelap |
| Surface | `#f8f8f7` | Card dan panel neumorphism (sama dengan background) |
| Surface Raised | `#FFFFFF` | Modal, dropdown, popover — permukaan yang melayang |
| Surface Inset | `#EFEDEA` | Area cekung: input, search bar, progress track |
| Surface Hover | `#F1EFED` | Hover pada baris tabel dan permukaan netral |

Catatan: background dan surface sengaja memiliki warna yang sama atau
berdekatan. Perbedaan kedalaman terutama dibentuk melalui
shadow ganda, bukan border yang kontras. Pengecualian: tabel, form, dan
permukaan melayang boleh memakai permukaan putih yang bersih agar data mudah
dibaca (lihat Bagian 5.6).

### 2.3 Neutral Colors

| Token | HEX | Penggunaan |
|---|---|---|
| Text Primary | `#1C1917` | Judul dan teks utama |
| Text Secondary | `#57534E` | Deskripsi dan informasi pendukung |
| Text Muted | `#78716C` | Placeholder dan metadata (bukan untuk teks penting yang kecil) |
| Text Disabled | `#A8A29E` | Teks nonaktif saja; jangan dipakai untuk informasi bermakna |
| Border Subtle | `#E2DFDC` | Pemisah dekoratif dan batas kartu yang lembut |
| Divider | `#DDD9D6` | Garis pemisah antarbagian |
| Border Input | `#78716C` | Batas input dan kontrol yang batasnya adalah satu-satunya penanda |

### 2.4 Status Colors

Dipilih dengan syarat kontras berikut. Setiap status
selalu dipasangkan dengan ikon dan label teks, tidak pernah hanya warna.

| Token | HEX (teks) | Background | Penggunaan |
|---|---|---|---|
| Success | `#166534` | `#DCFCE7` | Disetujui, berhasil, tren positif |
| Warning | `#92400E` | `#FEF3C7` | Perlu perbaikan, dokumen kurang, perhatian |
| Error | `#991B1B` | `#FEE2E2` | Ditolak, gagal, aksi destruktif |
| Info | `#1E40AF` | `#DBEAFE` | Diajukan, informasi, sedang menunggu |
| In Review | `#7e6961` | `#EDE6E3` | Dalam peninjauan (memakai warna highlight) |

Contoh pemetaan status UKT (sesuaikan dengan spesifikasi sistem yang diberikan bersama tugas; contoh di sini bukan aturan final):

| Status pengajuan | Tampilan |
|---|---|
| Diajukan | Info |
| Dalam Peninjauan | In Review (highlight) |
| Perlu Perbaikan | Warning |
| Disetujui | Success |
| Ditolak | Error |

Tidak ada palet kategori tambahan. Bila perlu membedakan seri grafik,
pakai urutan ini dan jangan menambah warna pelangi: sekunder `#550b14`,
highlight `#7e6961`, netral gelap `#57534E`, lalu warna status yang relevan
saja, maksimal 5 seri.

### 2.5 Gradient Palette

Gradien dipakai **tipis saja**. Artinya: gradien hanya
sebagai aksen kecil, bukan background halaman, bukan background kartu besar,
dan bukan gradien ungu.

| Nama | Gradient | Penggunaan yang diizinkan |
|---|---|---|
| Primary Gradient (tipis) | `#550b14 → #6E1420` | Tombol utama saja |
| Accent Bar | `#550b14 → #7e6961` | Garis aksen 3px di atas kartu terpilih atau header halaman |
| Highlight Soft | `#EDE6E3 → #F5E8E9` | Background aksen yang sangat ringan, bila benar-benar perlu |

Contoh CSS:

```css
/* Tombol utama: gradien satu rona, selisihnya kecil */
background: linear-gradient(
  135deg,
  #550b14,
  #6E1420
);

/* Garis aksen: tipis, bukan blok warna */
.accent-bar {
  height: 3px;
  background: linear-gradient(90deg, #550b14, #7e6961);
}
```

Larangan: tidak ada gradien lavender/ungu, tidak ada gradien biru–ungu,
tidak ada gradien pada seluruh hero, dan tidak ada dua gradien berbeda dalam
satu layar.

### 2.6 Pemeriksaan Kontras (WCAG AA)

Target: teks normal minimal 4,5:1, teks besar minimal 3:1, batas komponen
penting minimal 3:1 terhadap warna di sekitarnya. Pasangan berikut sudah
dihitung terhadap background `#f8f8f7` atau putih:

| Pasangan | Rasio | Keterangan |
|---|---|---|
| Teks putih di atas sekunder `#550b14` | 14,54:1 | Lolos AA |
| Teks utama `#1C1917` di atas `#f8f8f7` | 16,46:1 | Lolos AA |
| Teks sekunder `#57534E` di atas `#f8f8f7` | 7,18:1 | Lolos AA |
| Highlight `#7e6961` di atas `#f8f8f7` | 4,84:1 | Lolos AA |
| Teks muted `#78716C` di atas `#f8f8f7` | 4,51:1 | Lolos AA, mepet — jangan dipakai untuk teks penting berukuran kecil |
| Success `#166534` di atas `#DCFCE7` | 6,49:1 | Lolos AA |
| Warning `#92400E` di atas `#FEF3C7` | 6,37:1 | Lolos AA |
| Error `#991B1B` di atas `#FEE2E2` | 6,80:1 | Lolos AA |
| Info `#1E40AF` di atas `#DBEAFE` | 7,15:1 | Lolos AA |
| Sekunder `#550b14` di atas `#F5E8E9` | 12,19:1 | Lolos AA |

Bila sebuah kombinasi baru ditambahkan, hitung rasionya dulu. Bila di bawah
4,5:1 untuk teks normal, gelapkan warna teksnya — jangan mengandalkan
"masih kelihatan".

---

## 3. Neumorphism Shadow System

Shadow merupakan salah satu elemen terpenting dalam desain ini.
Warnanya netral hangat agar cocok dengan `#f8f8f7`.

Warna bayangan:

- Bayangan gelap: `#D9D3CD`
- Bayangan terang: `#FFFFFF`

### 3.1 Standard Shadows

| Token | CSS Value | Penggunaan |
|---|---|---|
| Shadow XS | `3px 3px 6px #D9D3CD, -3px -3px 6px #FFFFFF` | Elemen kecil |
| Shadow SM | `5px 5px 10px #D9D3CD, -5px -5px 10px #FFFFFF` | Tombol |
| Shadow MD | `8px 8px 16px #D9D3CD, -8px -8px 16px #FFFFFF` | Card |
| Shadow LG | `12px 12px 24px #D9D3CD, -12px -12px 24px #FFFFFF` | Panel besar, modal |

### 3.2 Inset Shadows

Digunakan untuk memberikan kesan permukaan cekung.

```css
box-shadow:
  inset 4px 4px 8px #D9D3CD,
  inset -4px -4px 8px #FFFFFF;
```

Penggunaan:

- Input field.
- Search bar.
- Area form.
- Selected controls.
- Progress track.

### 3.3 Button States

Default:

```css
box-shadow:
  5px 5px 10px #D9D3CD,
  -5px -5px 10px #FFFFFF;
```

Hover:

```css
box-shadow:
  3px 3px 7px #D9D3CD,
  -3px -3px 7px #FFFFFF;
```

Active / Pressed:

```css
box-shadow:
  inset 3px 3px 6px #D9D3CD,
  inset -3px -3px 6px #FFFFFF;
```

Catatan: shadow tidak boleh menjadi satu-satunya penanda interaksi.
Selalu ada perubahan lain yang terlihat: warna, ikon, label, atau outline
fokus (lihat Bagian 10).

---

## 4. Typography System

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

Catatan: teks dasar adalah 14px (Body), dengan 16px untuk Body Large pada
paragraf penjelas yang penting. Jangan mengecilkan body di bawah 14px untuk
memuat lebih banyak konten dalam satu layar — itu melanggar Bagian 1.1.

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

Aturan umum: **jangan memaksakan komponen.** Bagian 5.1–5.10
adalah komponen inti yang hampir pasti dipakai di sistem UKT. Bagian 5.11
adalah komponen kondisional: hanya didokumentasikan dan dibangun bila ada
fitur yang benar-benar memakainya. Komponen pajangan tanpa data dan tanpa
aksi tidak dibuat.

Setiap komponen inti harus memiliki state yang dirancang: default, hover,
focus-visible, active/pressed, disabled, loading (bila ada aksi), error
(bila ada input), dan empty (bila menampilkan data).

### 5.1 Cards

- Border radius: 12px.
- Background: Surface (`#f8f8f7`).
- Shadow: MD.
- Padding: 24–32px (lega).
- Hindari border yang terlalu kontras; pakai Border Subtle bila perlu.
- Satu kartu utama per layar boleh lebih besar daripada kartu pendukung.
- Jangan menumpuk lebih dari 4 kartu setara dalam satu baris di desktop
  tanpa alasan; bila daftarnya panjang, pakai tabel atau pagination.

### 5.2 Buttons

- Border radius: 10px.
- Height: 44–48px.
- Primary button menggunakan warna sekunder `#550b14` dengan gradien tipis
  (Bagian 2.5) dan teks putih.
- Secondary button menggunakan Surface dengan Shadow SM dan teks
  Text Primary.
- Ghost/text button hanya untuk aksi tersier; warnanya sekunder.
- Destructive button memakai Error (`#991B1B`), dan selalu didampingi
  konfirmasi untuk aksi yang tidak bisa dibatalkan.
- Active state menggunakan inset shadow (Bagian 3.3).
- Tombol loading mempertahankan lebarnya, menampilkan spinner, dan
  menonaktifkan klik ganda.

### 5.3 Input Fields

- Border radius: 10px.
- Background: Surface Inset (`#EFEDEA`).
- Gunakan inset shadow.
- Height: 44–48px.
- Border: Border Input (`#78716C`) bila batas harus terlihat jelas.
- Focus state menggunakan outline sekunder `#550b14` 2px dengan offset 2px
  yang terlihat jelas, bukan hanya perubahan shadow.
- Label selalu terlihat di atas field, bukan hanya placeholder.
- Pesan error menempel pada field-nya, memakai warna Error dan ikon.

### 5.4 Modal

- Border radius: 12px.
- Background: Surface Raised (`#FFFFFF`).
- Shadow: LG.
- Overlay menggunakan warna gelap hangat dengan transparansi secukupnya:
  `rgba(28, 25, 23, 0.45)`.
- Pastikan fokus keyboard tidak hilang dan bisa kembali ke pemicu modal
  saat modal ditutup. Lihat animasi modal di `Motion.md` Bagian 7.
- Modal dipakai untuk konfirmasi dan form pendek saja. Form pengajuan yang
  panjang memakai halaman sendiri, bukan modal yang harus di-scroll
  berlapis-lapis.

### 5.5 Navigation

**Sidebar kiri** sebagai
navigasi utama di desktop.

- Sidebar memakai Surface (`#f8f8f7`) dengan shadow lembut.
- Navigasi aktif menggunakan Sekunder Light (`#F5E8E9`).
- Icon navigasi aktif menggunakan Sekunder (`#550b14`); nonaktif memakai
  Text Secondary.
- Label menggunakan Text Primary, font Poppins untuk label navigasi.
- Logo dan nama institusi berada di atas sidebar (lihat Bagian 5.10).
- Badge role pengguna (Mahasiswa / Admin) tampil di area profil sidebar,
  karena kedua role melihat menu dan aksi yang berbeda.
- Contoh menu (sesuaikan dengan fitur nyata, bukan daftar wajib):
  mahasiswa — Beranda, Pengajuan Saya, Buat Pengajuan, Bantuan;
  admin/petugas — Beranda, Daftar Pengajuan, Peninjauan, Rekap.
  Menu yang tidak dimiliki suatu role disembunyikan, bukan hanya
  dinonaktifkan tanpa penjelasan, kecuali aturan produk menentukan lain.

### 5.6 Tables

Untuk tabel data, jangan menerapkan efek neumorphism berlebihan.

Gunakan:

- Background yang bersih (`#FFFFFF` untuk area tabel bila perlu kontras
  dengan halaman).
- Divider halus (`#DDD9D6`).
- Header yang memiliki kontras jelas (teks Text Primary, font Poppins 600).
- Tinggi baris 56–64px (lega).
- Hover state yang ringan (`#F1EFED`).
- Status badge dengan warna status (Bagian 2.4).
- Kolom angka (misalnya nominal UKT) rata kanan dengan angka tabular.
- Format locale Indonesia: `Rp 1.250.000`, tanggal `12 Okt 2026`.

### 5.7 Dropdown

- Radius 10px, background Surface Raised (`#FFFFFF`), shadow LG.
- Item aktif/hover memakai Sekunder Light.
- Panah indikator berputar saat terbuka (lihat `Motion.md` Bagian 6).
- Dropdown tidak boleh menutupi field pemicunya tanpa alasan.

### 5.8 Toast / Notifikasi

- Radius 10px, background Surface Raised, shadow LG.
- Ikon dan label status wajib ada; warna saja tidak cukup.
- Posisi konsisten di seluruh aplikasi; durasi tampil cukup untuk dibaca,
  dan bisa ditutup manual.
- Toast sukses/gagal setelah pengajuan dikirim atau peninjauan disimpan
  adalah contoh pemakaian yang sah; toast dekoratif tidak dibuat.

### 5.9 Tabs

- Tab aktif memakai teks sekunder gelap dengan garis bawah sekunder 2px
  atau background Sekunder Light; tab nonaktif memakai Text Secondary.
- Tab dipakai untuk memecah halaman yang ramai (misalnya detail pengajuan:
  Ringkasan, Dokumen, Riwayat) daripada menumpuk semuanya dalam satu scroll
  panjang.

### 5.10 Brand: Logo dan Nama Institusi

Wajib ada, dan perlakuannya khusus:

- **Logo:** `LOGO UNG (1).png`. Jangan mewarnai ulang,
  jangan memotong, jangan menarik (stretch) logo, dan jangan memakai
  warna-warna di dalam logo (ungu, biru, oranye, krem) sebagai palet UI.
  Palet UI tetap Bagian 2 dokumen ini.
- **Penempatan:** atas sidebar (desktop) dan header halaman login/
  halaman awal. Di sidebar, logo 40–48px berdampingan dengan teks.
  Di halaman login, logo 56–64px di atas form.
- **Nama:** tulis lengkap `Universitas Negeri Gorontalo` di samping logo
  pada tampilan desktop dan halaman login. Singkatan `UNG` hanya boleh
  dipakai bila ruang benar-benar tidak memungkinkan (misalnya sidebar
  yang diciutkan menjadi ikon), dan logo tetap harus memiliki teks
  alternatif `Universitas Negeri Gorontalo` untuk aksesibilitas.
- **Latar logo:** selalu di atas permukaan terang (`#f8f8f7` atau putih).
  Jangan menaruh logo di atas tombol maroon, gradien, atau background gelap.
- **Penempatan di proyek Laravel:** salin `LOGO UNG (1).png` menjadi
  `public/images/logo-ung.png` saat implementasi, lalu rujuk file itu dari
  layout Blade.

### 5.11 Komponen Kondisional (hanya bila fiturnya ada)

| Komponen | Dibuat bila | Contoh di sistem UKT |
|---|---|---|
| Stepper | Alur multi-langkah yang berurutan | Pengajuan keberatan bertahap (data, alasan, dokumen, review) |
| Breadcrumb | Halaman detail yang dalam | Beranda / Daftar Pengajuan / Detail Pengajuan |
| Pagination | Daftar melebihi satu layar | Tabel peninjauan admin |
| Grafik | Ada kebutuhan rekap/visualisasi | Rekap pengajuan per status untuk admin (bila diminta) |
| Kalender | Ada tenggat atau jadwal yang dipilih pengguna | Batas waktu pengajuan, jadwal peninjauan (bila diminta) |
| Upload file | Ada dokumen pendukung | Upload bukti pendukung pengajuan |

Bila fitur di kolom tengah tidak ada, komponennya tidak dibuat dan tidak
perlu dicari-cari alasannya.

---

## 6. Border Radius System

Kepribadian sudut: **seimbang**. Komponen utama memakai 8–12px.

| Token | Value | Penggunaan |
|---|---|---|
| Radius XS | 6px | Elemen sangat kecil (checkbox, dot) |
| Radius SM | 8px | Badge, chip, tag kecil |
| Radius MD | 10px | Input, button, dropdown |
| Radius LG | 12px | Cards, modal, panel besar |
| Radius Full | 9999px | Pills dan avatar |

Aturan sudut bersarang: radius dalam = radius luar − padding. Jangan
memakai radius 20px ke atas pada kartu.

---

## 7. Spacing System

Menggunakan kelipatan 4px sebagai basis.

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
- Ikon dipakai pada navigasi, aksi utama, status, dan empty state.
  Tidak perlu ikon di setiap baris tabel bila tidak menambah kejelasan.

Hindari mencampurkan berbagai gaya ikon dalam satu halaman.

---

## 9. Motion & Interaction

Ringkasan saja; spesifikasi lengkap ada di `Motion.md`.

| Interaction | Duration | Behavior |
|---|---|---|
| Hover | 150ms | Shadow transition |
| Button Press | 100ms | Inset shadow |
| Modal | 200ms | Fade + scale |
| Dropdown | 150ms | Fade + translate |
| Card Hover | 200ms | Subtle elevation |

Rekomendasi easing:

```css
transition-timing-function:
  cubic-bezier(0.2, 0.8, 0.2, 1);
```

Motion harus membantu pengguna memahami perubahan state, bukan sekadar
dekorasi. Hormati preferensi `prefers-reduced-motion` (lihat `Motion.md`
Bagian 15).

---

## 10. Accessibility Guidelines

Neumorphism memiliki kelemahan alami dalam hal kontras karena warna
background, surface, dan shadow cenderung berdekatan. Untuk mengatasinya,
dokumen ini mewajibkan (bukan sekadar menyarankan):

1. Jangan menggunakan shadow sebagai satu-satunya penanda interaksi.
2. Pastikan teks memenuhi standar kontras WCAG AA (Bagian 2.6).
3. Gunakan outline fokus keyboard yang jelas: sekunder `#550b14` 2px
   dengan offset 2px.
4. Bedakan disabled, hover, active, dan selected state dengan lebih dari
   satu isyarat visual.
5. Jangan hanya mengandalkan warna untuk menunjukkan status; selalu ada
   ikon dan label.
6. Pastikan input memiliki label yang terlihat.
7. Hindari teks terlalu kecil pada background berwarna.
8. Target sentuh minimal 44×44px, terutama pada tampilan mobile.
9. Gunakan elemen semantik (`nav`, `main`, `table`, `button`, `label`)
   dan nama aksesibel untuk kontrol ikon saja.

Target kontras:

- Teks normal: minimal 4,5:1.
- Teks besar: minimal 3:1.
- Komponen UI dan indikator grafis penting: minimal 3:1 terhadap warna
  di sekitarnya.

---

## 11. CSS Design Tokens & Tailwind Config

Berikut implementasi awal variabel CSS yang dapat dijadikan fondasi global,
dan konfigurasi Tailwind siap salin.

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
  --color-background-soft: #F3F2F0;

  /* Surface */
  --color-surface: #f8f8f7;
  --color-surface-raised: #FFFFFF;
  --color-surface-inset: #EFEDEA;
  --color-surface-hover: #F1EFED;

  /* Text */
  --color-text-primary: #1C1917;
  --color-text-secondary: #57534E;
  --color-text-muted: #78716C;
  --color-text-disabled: #A8A29E;

  /* Border */
  --color-border-subtle: #E2DFDC;
  --color-divider: #DDD9D6;
  --color-border-input: #78716C;

  /* Status: teks + background */
  --color-success: #166534;
  --color-success-bg: #DCFCE7;
  --color-warning: #92400E;
  --color-warning-bg: #FEF3C7;
  --color-error: #991B1B;
  --color-error-bg: #FEE2E2;
  --color-info: #1E40AF;
  --color-info-bg: #DBEAFE;

  /* Shadows (neumorphism ganda, netral hangat) */
  --shadow-xs:
    3px 3px 6px #D9D3CD,
    -3px -3px 6px #FFFFFF;

  --shadow-sm:
    5px 5px 10px #D9D3CD,
    -5px -5px 10px #FFFFFF;

  --shadow-md:
    8px 8px 16px #D9D3CD,
    -8px -8px 16px #FFFFFF;

  --shadow-lg:
    12px 12px 24px #D9D3CD,
    -12px -12px 24px #FFFFFF;

  --shadow-inset:
    inset 4px 4px 8px #D9D3CD,
    inset -4px -4px 8px #FFFFFF;

  /* Radius: seimbang */
  --radius-xs: 6px;
  --radius-sm: 8px;
  --radius-md: 10px;
  --radius-lg: 12px;
  --radius-full: 9999px;

  /* Typography */
  --font-heading: "Poppins", sans-serif;
  --font-body: "Inter", sans-serif;
}
```

### 11.2 Tailwind Config (siap salin)

Sesuaikan dengan versi Tailwind di proyek. Contoh di bawah memakai format
`tailwind.config.js` (Tailwind v3). Bila proyek memakai Tailwind v4, pindahkan
nilai yang sama ke `@theme` di CSS dan jangan mempertahankan dua sumber token
yang berbeda.

```js
// tailwind.config.js
export default {
  content: [
    "./resources/**/*.blade.php",
    "./resources/**/*.js",
  ],
  theme: {
    extend: {
      colors: {
        primer: "#f8f8f7",
        sekunder: {
          DEFAULT: "#550b14",
          hover: "#470912",
          active: "#3A0710",
          light: "#F5E8E9",
          soft: "#FBEFF0",
        },
        highlight: {
          DEFAULT: "#7e6961",
          dark: "#6B574F",
          light: "#EDE6E3",
        },
        surface: {
          DEFAULT: "#f8f8f7",
          raised: "#FFFFFF",
          inset: "#EFEDEA",
          hover: "#F1EFED",
        },
        ink: {
          DEFAULT: "#1C1917",
          secondary: "#57534E",
          muted: "#78716C",
          disabled: "#A8A29E",
        },
        success: { DEFAULT: "#166534", bg: "#DCFCE7" },
        warning: { DEFAULT: "#92400E", bg: "#FEF3C7" },
        error: { DEFAULT: "#991B1B", bg: "#FEE2E2" },
        info: { DEFAULT: "#1E40AF", bg: "#DBEAFE" },
      },
      fontFamily: {
        heading: ["Poppins", "sans-serif"],
        body: ["Inter", "sans-serif"],
      },
      borderRadius: {
        xs: "6px",
        sm: "8px",
        md: "10px",
        lg: "12px",
      },
      boxShadow: {
        "neu-xs": "3px 3px 6px #D9D3CD, -3px -3px 6px #FFFFFF",
        "neu-sm": "5px 5px 10px #D9D3CD, -5px -5px 10px #FFFFFF",
        "neu-md": "8px 8px 16px #D9D3CD, -8px -8px 16px #FFFFFF",
        "neu-lg": "12px 12px 24px #D9D3CD, -12px -12px 24px #FFFFFF",
        "neu-inset": "inset 4px 4px 8px #D9D3CD, inset -4px -4px 8px #FFFFFF",
      },
    },
  },
  plugins: [],
};
```

Aturan pemakaian token: komponen merujuk ke token, bukan nilai hex mentah
di dalam markup. Bila perlu nilai baru, tambahkan token dulu.

---

## 12. Responsive: Desktop-First

Desain dirancang dari desktop dulu, lalu disesuaikan
ke bawah. Desktop-first bukan berarti mobile diabaikan.

| Lebar | Perilaku |
|---|---|
| Desktop (1280px ke atas) | Sidebar kiri penuh + grid konten fluid; desain utama dibuat di sini |
| Tablet (768–1279px) | Sidebar menjadi rail ikon atau drawer; grid 2 kolom menjadi 1–2 kolom |
| Mobile (di bawah 768px) | Sidebar menjadi drawer; tabel menjadi kartu/key-value; filter masuk ke panel bawah |

Aturan yang tetap berlaku di semua lebar:

- Tabel di mobile diubah menjadi kartu (judul, status, 1–2 field kunci,
  menu aksi), bukan tabel yang mengecil sampai tidak terbaca.
- Target sentuh minimal 44×44px di tablet dan mobile.
- Konten tidak boleh menempel ke tepi container; padding halaman mobile
  minimal 16px.
- Periksa hasil di tiga lebar (≈390, 768, 1280) sebelum sebuah layar
  dinyatakan selesai.

---

## 13. Recommended Design Direction

Kombinasi final yang direkomendasikan:

| Category | Selection |
|---|---|
| Main Style | Light Neumorphism |
| Karakter | Profesional, simple, tegas |
| Tema | Light saja |
| Primer | `#f8f8f7` (hampir putih, permukaan dasar) |
| Sekunder | `#550b14` (maroon gelap, tindakan utama) |
| Highlight | `#7e6961` (aksen sekunder) |
| Gradien | Tipis saja: tombol utama dan garis aksen 3px |
| Background | Hampir putih hangat |
| Heading Font | Poppins |
| Body Font | Inter |
| Icon Style | Lucide Outline |
| Shadow Style | Soft Dual Shadow (netral hangat) |
| Border Radius | 8–12px (seimbang) |
| Density | Lega |
| Navigasi | Sidebar kiri (desktop-first) |
| Motion | Subtle & Smooth (lihat `Motion.md`) |
| Brand | Logo UNG + `Universitas Negeri Gorontalo` |

Final Design Principle:

> Antarmuka yang tenang, tegas, dan mudah dibaca: permukaan lembut,
> satu warna tindakan yang jelas, dan ruang yang lega — tanpa satu halaman
> pun yang dipadatkan sampai sesak.

Desain harus tetap mengutamakan keterbacaan, aksesibilitas, konsistensi,
dan kemudahan penggunaan. Efek visual neumorphism merupakan pendukung
pengalaman pengguna, bukan pengganti hierarki visual dan usability.
