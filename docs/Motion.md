# UI Animation & Motion Design System

Design Direction: Smooth, Subtle, Responsive Motion
Primary Stack: Laravel + Blade + Tailwind CSS + Alpine.js + Vanilla JavaScript
Visual Style: Light Neumorphism + Profesional, Simple, Tegas

**Untuk:** Sistem Peninjauan dan Pengajuan Keberatan UKT (SPPK-UKT UNG),
Universitas Negeri Gorontalo (UNG)
**Pengguna:** Mahasiswa dan admin/petugas
**Tema:** Light saja

**Cara memakai dokumen ini:** Dokumen ini berdiri sendiri untuk urusan
animasi. File yang menyertainya adalah `AGENTS.md`, `DESIGN.md`,
`Neumorphism.md`, `LOGO UNG (1).png`, dan `ss contoh neumorphism.jpeg`.
Bila ada hal yang tidak diatur di sini atau di file penyerta tersebut,
ikuti spesifikasi sistem yang diberikan bersama tugas — jangan mencari
sumber animasi lain di luar daftar itu.

**Token yang dipakai di contoh-contoh dokumen ini** (nilai lengkapnya ada
di `Neumorphism.md`, tetapi contoh di sini sudah bisa dibaca tanpa
membuka file lain):

| Token | Nilai | Dipakai untuk |
|---|---|---|
| Sekunder | `#550b14` | Tombol utama, scroll progress, toggle aktif, outline fokus |
| Sekunder Hover | `#470912` | Hover tombol utama |
| Primer / Background | `#f8f8f7` | Background halaman dan kartu |
| Surface Raised | `#FFFFFF` | Modal, dropdown, popover |
| Teks Utama | `#1C1917` | Tooltip dan teks gelap |
| Bayangan gelap / terang | `#D9D3CD` / `#FFFFFF` | Bayangan ganda neumorphism |
| Radius tombol / input | 10px | Tombol, input, dropdown |
| Radius kartu / modal | 12px | Kartu, modal, panel |

**Intensitas:** subtle — umpan balik hover/klik, transisi komponen, page
entrance, dan scroll reveal. Bukan hanya hover, dan bukan animasi kaya
seperti parallax atau gerakan berulang. Bila sebuah halaman terasa terlalu
ramai secara gerak, yang dikurangi pertama kali adalah stagger dan scroll
progress, bukan umpan balik tombol (lihat Bagian 14).

## 1. Animation Philosophy

Animasi merupakan bagian dari pengalaman pengguna (User Experience), bukan
sekadar dekorasi visual.

Setiap animasi harus memiliki tujuan, seperti:

- Memberikan feedback terhadap interaksi pengguna.
- Membantu pengguna memahami perubahan keadaan komponen.
- Menunjukkan hubungan antara elemen sebelum dan sesudah perubahan.
- Membuat perpindahan antarkomponen terasa natural.
- Memberikan kesan aplikasi yang responsif dan hidup.

Prinsip utama

1. Smooth: Gerakan tidak terasa patah atau mendadak.
2. Subtle: Animasi tidak berlebihan dan tidak mengganggu fokus.
3. Consistent: Durasi dan karakter animasi memiliki standar yang seragam.
4. Responsive: Animasi tetap nyaman pada perangkat mobile maupun desktop.
5. Purposeful: Setiap animasi memiliki alasan untuk digunakan.
6. Accessible: Pengguna yang mengurangi preferensi animasi tetap dapat
   menggunakan aplikasi dengan nyaman.

Design principle:

> Motion should explain, respond, and guide — never distract.

Pada sistem UKT, prinsip ini berarti: animasi boleh menegaskan bahwa
pengajuan sudah terkirim, bahwa baris tabel sudah difilter, atau bahwa
modal konfirmasi sudah terbuka — tetapi tidak boleh mengalihkan perhatian
dari isi pengajuan itu sendiri.

---

## 2. Animation Tokens

Seluruh animasi harus menggunakan standar durasi dan easing yang konsisten.
Token di bawah adalah standar yang berlaku untuk dokumen ini.

### 2.1 Duration Scale

| Token | Duration | Penggunaan |
|---|---|---|
| Instant | 100ms | Button press, micro-interaction |
| Fast | 150ms | Hover, icon transition |
| Normal | 200ms | Dropdown, toggle |
| Moderate | 300ms | Modal, accordion |
| Relaxed | 450ms | Card entrance, panel transition |
| Slow | 600ms | Scroll reveal |
| Cinematic | 800ms | Hero section, decorative animation |

Rekomendasi penggunaan:

- Hover tidak perlu lebih dari 200ms.
- Dropdown idealnya 150–250ms.
- Modal idealnya 200–350ms.
- Scroll reveal idealnya 450–650ms.

Hindari menggunakan animasi berdurasi panjang untuk interaksi yang sering
dilakukan — misalnya memfilter tabel peninjauan atau berpindah tab detail
pengajuan.

### 2.2 Easing Functions

Easing menentukan bagaimana kecepatan animasi berubah selama transisi
berlangsung.

| Easing | Karakter | Penggunaan |
|---|---|---|
| ease-out | Cepat di awal, melambat di akhir | Entrance |
| ease-in | Lambat di awal, cepat di akhir | Exit |
| ease-in-out | Lembut di awal dan akhir | General transition |
| linear | Kecepatan konstan | Loading dan progress |
| cubic-bezier | Kurva gerakan khusus | Custom interaction |

Rekomendasi easing:

```css
:root {
    --ease-standard: cubic-bezier(0.2, 0.8, 0.2, 1);
    --ease-smooth: cubic-bezier(0.25, 0.1, 0.25, 1);
    --ease-enter: cubic-bezier(0, 0, 0.2, 1);
    --ease-exit: cubic-bezier(0.4, 0, 1, 1);
}
```

Easing default untuk hampir semua transisi adalah `--ease-standard`.
Jangan membuat easing baru per komponen tanpa alasan.

---

## 3. Scroll Reveal Animation

### 3.1 Fade Up

Elemen muncul secara perlahan dari bawah menuju posisi normal ketika
memasuki viewport.

Karakteristik:

- Initial opacity: 0.
- Initial Y position: 24px.
- Final opacity: 1.
- Final Y position: 0.
- Duration: 500–650ms.

Contoh:

```css
.reveal {
    opacity: 0;
    transform: translateY(24px);

    transition:
        opacity 600ms var(--ease-standard),
        transform 600ms var(--ease-standard);
}

.reveal.is-visible {
    opacity: 1;
    transform: translateY(0);
}
```

### 3.2 Variasi Scroll Reveal

Selain Fade Up, gunakan variasi berikut sesuai konteks.

| Animation | Initial State | Final State |
|---|---|---|
| Fade Up | Y: +24px, opacity: 0 | Y: 0, opacity: 1 |
| Fade Down | Y: -24px, opacity: 0 | Y: 0, opacity: 1 |
| Fade Left | X: +24px, opacity: 0 | X: 0, opacity: 1 |
| Fade Right | X: -24px, opacity: 0 | X: 0, opacity: 1 |
| Fade In | Opacity: 0 | Opacity: 1 |
| Zoom In | Scale: 0.95 | Scale: 1 |
| Zoom Out | Scale: 1.05 | Scale: 1 |

Gunakan Fade Up sebagai animasi default untuk konten umum.

### 3.3 Implementation dengan IntersectionObserver

Untuk mendeteksi ketika elemen masuk ke viewport, gunakan JavaScript
"IntersectionObserver".

Letakkan kode berikut di:

`resources/js/app.js`

```js
const revealElements = document.querySelectorAll("[data-reveal]");

if (revealElements.length > 0) {
    if ("IntersectionObserver" in window) {
        document.documentElement.classList.add("has-reveal");

        const observer = new IntersectionObserver(
            (entries, observer) => {
                entries.forEach((entry) => {
                    if (entry.isIntersecting) {
                        entry.target.classList.add("is-visible");

                        // Hanya dianimasikan satu kali.
                        observer.unobserve(entry.target);
                    }
                });
            },
            {
                threshold: 0.12,
                rootMargin: "0px 0px -40px 0px"
            }
        );

        revealElements.forEach((element) => {
            observer.observe(element);
        });
    }
}
```

CSS:

```css
html.has-reveal [data-reveal] {
    opacity: 0;
    visibility: hidden;

    transform: translateY(24px);

    transition:
        opacity 600ms var(--ease-standard),
        transform 600ms var(--ease-standard),
        visibility 0s;
}

html.has-reveal [data-reveal].is-visible {
    opacity: 1;
    visibility: visible;

    transform: translateY(0);
}

/* Variations */

html.has-reveal [data-reveal="down"] {
    transform: translateY(-24px);
}

html.has-reveal [data-reveal="left"] {
    transform: translateX(24px);
}

html.has-reveal [data-reveal="right"] {
    transform: translateX(-24px);
}

html.has-reveal [data-reveal="zoom"] {
    transform: scale(0.95);
}

html.has-reveal [data-reveal].is-visible {
    transform: none;
}
```

Penting: class `has-reveal` hanya ditambahkan bila IntersectionObserver
tersedia. Bila JavaScript gagal atau tidak didukung, konten tetap terlihat
— reveal tidak boleh menyembunyikan informasi penting seperti status
pengajuan.

Penggunaan di Blade:

```blade
<section>
    <div data-reveal="up">
        <h2>Ringkasan Pengajuan</h2>
        <p>Status dan progres pengajuan keberatan UKT Anda.</p>
    </div>

    <div data-reveal="up">
        <!-- Konten dashboard -->
    </div>
</section>
```

### 3.4 Stagger Animation

Stagger adalah teknik untuk membuat beberapa elemen muncul secara berurutan
dengan jeda waktu tertentu.

Contohnya, empat kartu ringkasan muncul satu per satu, bukan secara
bersamaan.

Gunakan CSS custom property:

```css
html.has-reveal [data-reveal] {
    transition-delay: var(--reveal-delay, 0ms);
}
```

Penggunaan:

```blade
<div data-reveal="up" style="--reveal-delay: 0ms">
    Kartu 1
</div>

<div data-reveal="up" style="--reveal-delay: 100ms">
    Kartu 2
</div>

<div data-reveal="up" style="--reveal-delay: 200ms">
    Kartu 3
</div>

<div data-reveal="up" style="--reveal-delay: 300ms">
    Kartu 4
</div>
```

Rekomendasi:

- Delay antar-elemen: 60–120ms.
- Maksimum delay dalam satu kelompok: sekitar 400ms.
- Hindari delay terlalu panjang untuk elemen penting.

Catatan: Jangan menggunakan stagger pada seluruh isi halaman. Terapkan
hanya pada kelompok elemen yang memiliki hubungan visual — misalnya
deretan kartu ringkasan di beranda. Jangan memakai stagger pada baris
tabel peninjauan: tabel harus bisa dipindai langsung, bukan muncul
satu per satu.

---

## 4. Hover Animation

Hover memberikan feedback ketika pointer berada di atas elemen interaktif.
Pada perangkat sentuh, hover tidak bisa diandalkan — pastikan informasi
yang sama juga tersedia tanpa hover (lihat Bagian 15.3).

### 4.1 Button Hover

Ketika pointer berada di atas tombol:

- Tombol sedikit terangkat.
- Shadow berubah menjadi lebih kuat atau menyebar.
- Warna dapat berubah sedikit.
- Transisi berjalan secara halus.

Contoh Tailwind CSS:

```blade
<button
    class="
        rounded-[10px]
        px-6 py-3
        bg-[#550b14]
        text-white

        shadow-md

        transition-all
        duration-200
        ease-out

        hover:-translate-y-0.5
        hover:shadow-lg
        hover:bg-[#470912]
    "
>
    Kirim Pengajuan
</button>
```

### 4.2 Card Hover

Card dapat sedikit terangkat ketika pointer berada di atasnya. Efek ini
hanya untuk kartu yang bisa diklik (misalnya kartu ringkasan yang membuka
detail). Kartu statis yang hanya menampilkan informasi tidak perlu efek
hover.

```css
.interactive-card {
    transition:
        transform 250ms var(--ease-standard),
        box-shadow 250ms var(--ease-standard);
}

@media (hover: hover) {
    .interactive-card:hover {
        transform: translateY(-4px);

        box-shadow:
            12px 12px 24px #D9D3CD,
            -12px -12px 24px #FFFFFF;
    }
}
```

Contoh penggunaan:

```blade
<div class="interactive-card">
    <!-- Konten kartu yang bisa diklik -->
</div>
```

### 4.3 Icon Hover

Ikon dapat menggunakan animasi sederhana.

```blade
<button class="group">
    <svg
        class="
            transition-transform
            duration-200
            group-hover:scale-110
        "
    >
        <!-- SVG icon -->
    </svg>
</button>
```

Rekomendasi:

- Scale: 1.05–1.10.
- Rotation: maksimal 10–15 derajat untuk ikon yang sesuai.
- Hindari animasi rotasi pada semua ikon secara otomatis.

---

## 5. Button Interaction Animation

Tombol perlu memberikan feedback yang jelas ketika pengguna melakukan
interaksi.

### 5.1 Press / Click Effect

Ketika tombol ditekan:

- Ukuran visual sedikit mengecil.
- Posisi tombol dapat turun sedikit.
- Shadow menjadi inset.
- Ketika dilepaskan, tombol kembali ke keadaan normal.

Contoh:

```blade
<button
    class="
        rounded-[10px]
        px-6 py-3

        bg-[#550b14]
        text-white

        shadow-[5px_5px_10px_#D9D3CD,-5px_-5px_10px_#FFFFFF]

        transition-all
        duration-150
        ease-out

        hover:-translate-y-0.5

        active:translate-y-0
        active:scale-[0.97]
        active:shadow-inner

        focus-visible:outline
        focus-visible:outline-2
        focus-visible:outline-offset-4
        focus-visible:outline-[#550b14]
    "
>
    Simpan
</button>
```

Untuk tombol Neumorphism, dapat menggunakan custom shadow agar efek
pressed lebih konsisten.

```css
.neu-button {
    box-shadow:
        5px 5px 10px #D9D3CD,
        -5px -5px 10px #FFFFFF;

    transition:
        transform 150ms var(--ease-standard),
        box-shadow 150ms var(--ease-standard);
}

.neu-button:active {
    transform: scale(0.97);

    box-shadow:
        inset 3px 3px 6px #D9D3CD,
        inset -3px -3px 6px #FFFFFF;
}
```

### 5.2 Button Loading

Ketika tombol menjalankan proses:

- Tampilkan loading indicator.
- Cegah pengiriman berulang.
- Pertahankan ukuran tombol agar layout tidak bergeser.
- Berikan feedback ketika proses selesai.

Contoh menggunakan Alpine.js:

```blade
<div x-data="{ loading: false }">
    <button
        @click="
            loading = true;
            setTimeout(() => loading = false, 2000);
        "
        :disabled="loading"
        class="rounded-[10px] px-6 py-3 bg-[#550b14] text-white
               disabled:opacity-60 disabled:cursor-not-allowed"
    >
        <span x-show="!loading">
            Kirim Pengajuan
        </span>

        <span
            x-show="loading"
            class="flex items-center gap-2"
        >
            <span class="loading-spinner"></span>
            Memproses...
        </span>
    </button>
</div>
```

Catatan: `setTimeout` di atas hanya simulasi proses. Pada implementasi
sebenarnya, status loading harus mengikuti keberhasilan atau kegagalan
request backend — misalnya pengajuan benar-benar tersimpan atau gagal
disimpan — bukan timer.

---

## 6. Dropdown Animation

Dropdown harus membuka dan menutup dengan transisi yang halus.

Karakteristik:

- Muncul dari bawah atau atas secara halus.
- Opacity meningkat dari 0 menjadi 1.
- Scale meningkat dari 0.95 menjadi 1.
- Ketika ditutup, transisi berjalan ke arah sebaliknya.

### 6.1 Alpine.js Implementation

```blade
<div
    x-data="{ open: false }"
    @click.outside="open = false"
    @keydown.escape.window="open = false"
    class="relative"
>
    <button
        type="button"
        @click="open = !open"
        :aria-expanded="open"
        aria-controls="profile-menu"
        class="rounded-[10px] px-4 py-3 bg-[#f8f8f7] shadow-md"
    >
        Profil

        <span
            class="inline-block transition-transform duration-200"
            :class="open ? 'rotate-180' : ''"
        >
            ▼
        </span>
    </button>

    <div
        id="profile-menu"
        x-cloak
        x-show="open"

        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0 -translate-y-2 scale-95"
        x-transition:enter-end="opacity-100 translate-y-0 scale-100"

        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100 translate-y-0 scale-100"
        x-transition:leave-end="opacity-0 -translate-y-2 scale-95"

        class="
            absolute right-0 top-full
            mt-3
            w-52
            rounded-xl
            bg-white
            shadow-lg
            p-2
        "
    >
        <a href="#" class="block rounded-lg p-3 hover:bg-[#F1EFED]">
            Profil Saya
        </a>

        <a href="#" class="block rounded-lg p-3 hover:bg-[#F1EFED]">
            Pengaturan
        </a>

        <a href="#" class="block rounded-lg p-3 hover:bg-[#F1EFED]">
            Keluar
        </a>
    </div>
</div>
```

Tambahkan CSS:

```css
[x-cloak] {
    display: none !important;
}
```

Alpine.js menangani state `open`, sementara `x-transition` menangani
animasi masuk dan keluar.

Pastikan Alpine.js sudah diinisialisasi melalui Vite.

---

## 7. Modal Animation

Modal sebaiknya memiliki dua lapisan animasi:

1. Background overlay.
2. Modal panel.

### 7.1 Modal Entrance

Overlay:

- Opacity 0 → 1.
- Duration 200ms.

Modal panel:

- Opacity 0 → 1.
- Scale 0.95 → 1.
- Translate Y 12px → 0.
- Duration 250ms.

Contoh Alpine.js:

```blade
<div x-data="{ open: false }">

    <button @click="open = true">
        Buka Modal
    </button>

    <div
        x-cloak
        x-show="open"
        @keydown.escape.window="open = false"
        class="fixed inset-0 z-50 flex items-center justify-center p-4"
    >

        <!-- Overlay -->
        <div
            class="absolute inset-0 bg-black/30"
            x-show="open"
            x-transition.opacity.duration.200ms
            @click="open = false"
        ></div>

        <!-- Modal -->
        <div
            class="relative w-full max-w-md rounded-xl bg-white p-6 shadow-2xl"

            x-transition:enter="transition ease-out duration-250"
            x-transition:enter-start="opacity-0 scale-95 translate-y-3"
            x-transition:enter-end="opacity-100 scale-100 translate-y-0"

            x-transition:leave="transition ease-in duration-150"
            x-transition:leave-start="opacity-100 scale-100 translate-y-0"
            x-transition:leave-end="opacity-0 scale-95 translate-y-3"
        >
            <h2 class="text-xl font-bold">
                Konfirmasi Pengajuan
            </h2>

            <p class="mt-2">
                Apakah Anda yakin ingin mengirim pengajuan keberatan UKT ini?
            </p>

            <button
                @click="open = false"
                class="mt-5 rounded-[10px] bg-[#550b14] px-5 py-2 text-white"
            >
                Tutup
            </button>
        </div>
    </div>
</div>
```

Untuk implementasi produksi, modal juga harus menangani focus management,
keyboard navigation, dan penguncian scroll halaman ketika modal terbuka.
Modal konfirmasi untuk aksi destruktif (misalnya membatalkan pengajuan)
tidak boleh bisa tertutup hanya karena overlay diklik tanpa sengaja bila
aturan produk menentukan konfirmasi eksplisit — tentukan perilaku ini
sebelum implementasi, jangan menebaknya di tengah jalan.

---

## 8. Accordion Animation

Accordion digunakan untuk menampilkan atau menyembunyikan konten
berdasarkan interaksi pengguna. Di sistem UKT, contohnya adalah rincian
dokumen atau riwayat status di halaman detail pengajuan.

Contoh:

```blade
<div x-data="{ open: false }" class="rounded-xl bg-[#f8f8f7] shadow-md">

    <button
        @click="open = !open"
        :aria-expanded="open"
        class="flex w-full items-center justify-between p-5"
    >
        <span>Riwayat Status Pengajuan</span>

        <span
            class="transition-transform duration-200"
            :class="open ? 'rotate-180' : ''"
        >
            ▼
        </span>
    </button>

    <div
        x-cloak
        x-show="open"
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0 -translate-y-2"
        x-transition:enter-end="opacity-100 translate-y-0"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100 translate-y-0"
        x-transition:leave-end="opacity-0 -translate-y-2"
        class="px-5 pb-5"
    >
        Riwayat perubahan status pengajuan ditampilkan di sini,
        dari Diajukan sampai keputusan akhir.
    </div>

</div>
```

Catatan: Contoh ini mengatur transisi opacity dan posisi. Jika ingin
animasi tinggi konten yang benar-benar mengembang dan mengecil, dapat
menggunakan Alpine Collapse plugin atau teknik CSS khusus.

---

## 9. Page Transition

Page transition memberikan efek ketika pengguna berpindah halaman.

Untuk aplikasi Laravel berbasis Blade, navigasi tradisional menyebabkan
browser melakukan perpindahan halaman secara penuh.

Pendekatan yang direkomendasikan:

- Gunakan transisi sederhana pada elemen halaman saat masuk.
- Jangan menghambat navigasi dengan animasi yang terlalu lama.
- Pertahankan navigasi native agar aksesibilitas dan perilaku browser
  tetap baik.

Contoh:

```css
.page-content {
    animation: pageEnter 350ms var(--ease-standard) both;
}

@keyframes pageEnter {
    from {
        opacity: 0;
        transform: translateY(8px);
    }

    to {
        opacity: 1;
        transform: translateY(0);
    }
}
```

Penggunaan:

```blade
<main class="page-content">
    <!-- Konten halaman -->
</main>
```

Efek ini berjalan saat halaman baru dimuat, bukan transisi lintas halaman
yang berkesinambungan. Jangan menunda navigasi demi menunggu animasi
selesai.

---

## 10. Loading Animation

Loading indicator digunakan ketika sistem sedang melakukan proses yang
membutuhkan waktu.

Beberapa jenis yang direkomendasikan:

### 10.1 Spinner

```css
.loading-spinner {
    width: 18px;
    height: 18px;

    border: 2px solid rgba(255,255,255,0.3);
    border-top-color: white;

    border-radius: 50%;

    animation: spin 700ms linear infinite;
}

@keyframes spin {
    to {
        transform: rotate(360deg);
    }
}
```

Spinner dipakai di dalam tombol dan untuk proses singkat. Untuk proses
yang memuat daftar (misalnya tabel peninjauan), pakai skeleton, bukan
spinner di tengah halaman kosong.

### 10.2 Skeleton Loading

Digunakan untuk memberikan gambaran struktur konten sebelum data selesai
dimuat. Warna skeleton disesuaikan ke netral hangat.

```css
.skeleton {
    background: linear-gradient(
        90deg,
        #E9E4E0 25%,
        #F8F7F5 50%,
        #E9E4E0 75%
    );

    background-size: 200% 100%;

    animation: skeletonLoading 1.5s ease-in-out infinite;

    border-radius: 12px;
}

@keyframes skeletonLoading {
    from {
        background-position: 200% 0;
    }

    to {
        background-position: -200% 0;
    }
}
```

Penggunaan:

```blade
<div class="skeleton h-5 w-48"></div>
<div class="skeleton mt-3 h-4 w-full"></div>
<div class="skeleton mt-2 h-4 w-3/4"></div>
```

Skeleton harus meniru struktur konten yang akan muncul (misalnya baris
tabel atau kartu ringkasan), bukan sekadar balok acak.

---

## 11. Toggle & Switch Animation

Toggle harus memberikan feedback visual yang langsung terlihat.

Contoh:

```blade
<div x-data="{ enabled: false }">

    <button
        type="button"
        role="switch"
        :aria-checked="enabled"
        @click="enabled = !enabled"

        class="
            relative
            h-7 w-12
            rounded-full
            transition-colors duration-200
        "

        :class="enabled ? 'bg-[#550b14]' : 'bg-[#E2DFDC]'"
    >

        <span
            class="
                absolute top-1
                h-5 w-5
                rounded-full bg-white
                shadow-md
                transition-transform duration-200
            "

            :class="enabled ? 'translate-x-6' : 'translate-x-1'"
        ></span>

    </button>

</div>
```

Rekomendasi:

- Knob bergerak dalam 150–200ms.
- Warna background berubah secara bersamaan.
- Gunakan ukuran target interaksi yang nyaman (minimal 44×44px area
  sentuh pada mobile, meskipun visual toggle-nya lebih kecil).
- Pastikan tombol dapat dioperasikan dengan keyboard.
- Toggle hanya untuk pengaturan on/off, bukan untuk aksi sekali jalan
  seperti mengirim pengajuan.

---

## 12. Tooltip Animation

Tooltip digunakan untuk menampilkan informasi tambahan ketika pengguna
mengarahkan pointer ke suatu elemen. Tooltip tidak boleh menjadi
satu-satunya tempat informasi penting berada.

Contoh:

```blade
<div
    x-data="{ visible: false }"
    class="relative inline-block"
    @mouseenter="visible = true"
    @mouseleave="visible = false"
    @focusin="visible = true"
    @focusout="visible = false"
>
    <button
        type="button"
        aria-describedby="help-tooltip"
        class="rounded-lg p-2"
    >
        Bantuan
    </button>

    <div
        id="help-tooltip"
        role="tooltip"
        x-cloak
        x-show="visible"

        x-transition:enter="transition ease-out duration-150"
        x-transition:enter-start="opacity-0 translate-y-1"
        x-transition:enter-end="opacity-100 translate-y-0"

        x-transition:leave="transition ease-in duration-100"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"

        class="
            absolute bottom-full left-1/2
            mb-2 -translate-x-1/2
            whitespace-nowrap
            rounded-lg
            bg-[#1C1917]
            px-3 py-2
            text-sm text-white
        "
    >
        Informasi tambahan
    </div>
</div>
```

---

## 13. Scroll Behavior

Selain animasi elemen, perilaku scrolling juga berpengaruh terhadap
pengalaman pengguna.

### 13.1 Smooth Scrolling

```css
html {
    scroll-behavior: smooth;
}
```

Digunakan ketika pengguna mengklik anchor link atau navigasi menuju
bagian tertentu. Smooth scrolling tidak berlaku bila pengguna memilih
reduced motion (lihat Bagian 15.1).

### 13.2 Scroll Progress Indicator

Indikator yang menunjukkan sejauh mana pengguna telah melakukan scrolling
pada halaman. Bentuknya tetap tipis (3px) dan warnanya sekunder — bukan aksen yang mencolok.

Contoh JavaScript:

```js
window.addEventListener("scroll", () => {
    const scrollTop = window.scrollY;

    const scrollHeight =
        document.documentElement.scrollHeight -
        window.innerHeight;

    const progress =
        scrollHeight > 0
            ? (scrollTop / scrollHeight) * 100
            : 0;

    document.documentElement.style.setProperty(
        "--scroll-progress",
        `${progress}%`
    );
}, { passive: true });
```

CSS:

```css
.scroll-progress {
    position: fixed;
    top: 0;
    left: 0;

    width: var(--scroll-progress, 0%);
    height: 3px;

    background: #550b14;

    z-index: 9999;
}
```

HTML:

```blade
<div class="scroll-progress"></div>
```

Gunakan scroll progress hanya pada halaman yang memang panjang (misalnya
halaman detail atau panduan). Pada halaman dashboard yang pendek,
indikator ini tidak perlu ditampilkan.

### 13.3 Sticky Header

Header dapat berubah sedikit ketika pengguna melakukan scrolling.

Contoh menggunakan Alpine.js:

```blade
<header
    x-data="{ scrolled: false }"
    @scroll.window="scrolled = window.scrollY > 40"

    :class="scrolled
        ? 'py-2 shadow-md'
        : 'py-5 shadow-none'"

    class="sticky top-0 z-40 bg-[#f8f8f7]
           transition-all duration-300"
>
    <!-- Konten header -->
</header>
```

Perubahan yang diizinkan saat scroll: padding mengecil dan shadow
muncul. Jangan menyembunyikan navigasi utama atau logo UNG saat scroll,
karena pengguna sistem UKT perlu kembali ke menu tanpa harus scroll ke atas.

---

## 14. Recommended Motion Guidelines

Tidak semua elemen harus memiliki animasi yang sama.

| Component | Animation | Duration |
|---|---|---|
| Page content | Fade Up | 350ms |
| Section | Fade Up | 600ms |
| Card | Fade Up + Stagger | 500ms |
| Button | Press + Hover | 150–200ms |
| Icon | Scale / Rotate | 150ms |
| Dropdown | Fade + Translate | 200ms |
| Modal | Fade + Scale | 250ms |
| Accordion | Fade + Translate | 200ms |
| Tooltip | Fade + Translate | 150ms |
| Toggle | Translate + Color | 200ms |
| Skeleton | Shimmer | 1500ms |
| Spinner | Rotate | 700ms |
| Page entrance | Fade + Translate | 350ms |

### Yang tidak dilakukan

- Tidak ada parallax dan tidak ada gerakan latar berulang.
- Tidak ada animasi memantul (bounce) pada komponen produk.
- Tidak ada gradien yang bergerak.
- Tidak ada animasi pada seluruh baris tabel sekaligus.
- Tidak ada reveal yang menyembunyikan konten bila JavaScript gagal.

Bila sebuah halaman terasa terlalu ramai secara gerak, kurangi dulu
stagger dan scroll progress. Umpan balik tombol, dropdown, dan modal tetap dipakai karena itu adalah feedback, bukan dekorasi.

---

## 15. Accessibility & Performance

### 15.1 Reduced Motion

Aplikasi harus menghormati preferensi sistem pengguna yang memilih
mengurangi animasi. Bagian ini wajib, bukan opsional.

Gunakan:

```css
@media (prefers-reduced-motion: reduce) {

    *,
    *::before,
    *::after {
        animation-duration: 0.01ms !important;
        animation-iteration-count: 1 !important;

        transition-duration: 0.01ms !important;

        scroll-behavior: auto !important;
    }

    html.has-reveal [data-reveal] {
        opacity: 1 !important;
        visibility: visible !important;
        transform: none !important;
    }
}
```

Pada mode reduced motion, perubahan state tetap terjadi — hanya gerakannya
yang dihilangkan atau diganti fade singkat. Konten tidak boleh hilang
karena animasinya dimatikan.

### 15.2 Performance Guidelines

Prioritaskan properti berikut:

- `transform`
- `opacity`

Keduanya umumnya lebih efisien untuk animasi karena dapat menghindari
layout recalculation.

Batasi animasi yang melibatkan:

- `width` dan `height`.
- `top` dan `left`.
- Margin dan padding.
- Shadow yang berubah sangat besar dan terus-menerus.

Jangan menggunakan `will-change` pada semua elemen. Gunakan hanya pada
elemen yang memang memerlukan optimasi khusus.

Khusus neumorphism: perubahan shadow ganda pada hover memang melibatkan
`box-shadow` dan itu diizinkan untuk interaksi singkat (150–250ms),
tetapi jangan menganimasikan shadow besar secara berulang atau pada
banyak elemen sekaligus.

### 15.3 Interaction Guidelines

- Jangan menjadikan hover sebagai satu-satunya cara untuk mengakses fitur.
- Pastikan tombol memberikan feedback ketika diklik.
- Jangan menyembunyikan konten penting terlalu lama.
- Hindari animasi berulang yang tidak memiliki fungsi.
- Pastikan keyboard focus tetap terlihat (outline sekunder `#550b14`).
- Hindari gerakan besar yang dapat mengganggu pengguna.
- Jangan menggunakan animasi sebagai pengganti loading state yang
  sebenarnya.
- Pada perangkat sentuh, pastikan semua aksi yang tadinya hanya muncul
  saat hover juga bisa diakses lewat tap atau menu aksi.

---

## 16. Recommended File Structure

Untuk implementasi pada Laravel + Blade + Tailwind CSS + Alpine.js:

```
resources/
│
├── css/
│   └── app.css
│
├── js/
│   ├── app.js
│   │
│   └── animations/
│       ├── reveal.js
│       ├── scroll-progress.js
│       └── page-interactions.js
│
└── views/
    ├── layouts/
    │   └── app.blade.php
    │
    └── components/
        ├── button.blade.php
        ├── modal.blade.php
        ├── dropdown.blade.php
        └── accordion.blade.php
```

Pembagian tanggung jawab:

| File | Tanggung Jawab |
|---|---|
| `app.css` | Animation tokens, keyframes, global transitions |
| `app.js` | Entry point dan inisialisasi aplikasi |
| `reveal.js` | Scroll reveal dan IntersectionObserver |
| `scroll-progress.js` | Scroll progress indicator |
| `page-interactions.js` | Interaksi khusus berbasis JavaScript |
| Blade components | Struktur HTML dan state interaksi Alpine |

Contoh pemanggilan modul pada `app.js`:

```js
import "./animations/reveal.js";
import "./animations/scroll-progress.js";
import "./animations/page-interactions.js";
```

Pastikan kode modul yang digunakan memang telah dibuat. Jangan
mendefinisikan inisialisasi yang sama berulang kali di beberapa file.

Untuk komponen seperti dropdown, modal, accordion, dan toggle yang hanya
membutuhkan interaksi sederhana, Alpine.js sudah cukup. Tidak perlu
membuat JavaScript terpisah untuk setiap komponen.

---

## 17. Final Motion Design Direction

| Category | Recommendation |
|---|---|
| Animation Style | Smooth & Subtle |
| Default Entrance | Fade Up |
| Hover Behavior | Slight Elevation |
| Button Press | Scale 0.97 |
| Dropdown | Fade + Translate |
| Modal | Fade + Scale |
| Card Interaction | Elevation + Shadow |
| Easing | Custom Cubic Bezier |
| Default Duration | 150–300ms |
| Reveal Duration | 500–650ms |
| Scroll Trigger | IntersectionObserver |
| UI Interaction | Alpine.js |
| Custom Animation | Vanilla CSS / JavaScript |
| Accessibility | Reduced Motion Support |
| Warna Aksen Gerakan | Sekunder `#550b14` (bukan lavender) |
| Shadow Gerakan | Netral hangat `#D9D3CD` / `#FFFFFF` |

Final Principle

> Every interaction should feel intentional, responsive, and natural.

Animasi harus memperkuat karakter visual Neumorphism tanpa mengorbankan
keterbacaan, performa, dan kenyamanan penggunaan. Pada sistem UKT, itu
berarti gerakan yang tenang dan tegas: cukup terasa untuk memberi
kepastian — pengajuan terkirim, filter berubah, modal terbuka — dan cukup
pelan untuk tidak mengalihkan perhatian dari keputusan yang sedang dibuat.

Prioritas implementasi:

1. Basic transitions dan hover behavior.
2. Button interaction dan feedback.
3. Dropdown, toggle, modal, dan accordion.
4. Scroll reveal dan stagger animation.
5. Loading states dan page entrance.
6. Fine-tuning performa dan accessibility.

Jadikan dokumen ini sebagai standar animasi global agar seluruh halaman
memiliki karakter gerakan yang konsisten, bukan membuat animasi secara
acak pada setiap komponen.
