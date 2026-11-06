@props([
    'title' => 'Dasbor SPPK-UKT UNG',
    'subtitle' => null,
])
@php
    /** @var \App\Models\User $currentUser */
    $currentUser = auth()->user();
    $unreadCount = $currentUser ? $currentUser->unreadNotifications()->count() : 0;

    $menuItems = match ($currentUser?->peran) {
        'petugas' => [
            ['route' => 'petugas.beranda', 'pattern' => 'petugas.beranda', 'label' => 'Beranda Petugas', 'icon' => 'M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6'],
            ['route' => 'petugas.hasil.index', 'pattern' => 'petugas.hasil.*', 'label' => 'Entri & Kelola Hasil', 'icon' => 'M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z'],
            ['route' => 'petugas.parameter.index', 'pattern' => 'petugas.parameter.*', 'label' => 'Parameter Penilaian', 'icon' => 'M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4'],
            ['route' => 'petugas.konten.index', 'pattern' => 'petugas.konten.*', 'label' => 'Kelola Konten Publik', 'icon' => 'M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z'],
            ['route' => 'petugas.keberatan.index', 'pattern' => 'petugas.keberatan.*', 'label' => 'Peninjauan Keberatan', 'icon' => 'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z'],
            ['route' => 'petugas.pertanyaan.index', 'pattern' => 'petugas.pertanyaan.*', 'label' => 'Daftar Pertanyaan', 'icon' => 'M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z'],
            ['route' => 'petugas.riwayat.index', 'pattern' => 'petugas.riwayat.*', 'label' => 'Riwayat & Audit', 'icon' => 'M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z'],
        ],
        'pimpinan' => [
            ['route' => 'pimpinan.rekap', 'pattern' => 'pimpinan.rekap', 'label' => 'Rekap Agregat UKT', 'icon' => 'M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z'],
            ['route' => 'pimpinan.riwayat', 'pattern' => 'pimpinan.riwayat', 'label' => 'Riwayat Agregat', 'icon' => 'M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z'],
        ],
        default => [
            ['route' => 'mahasiswa.beranda', 'pattern' => 'mahasiswa.beranda', 'label' => 'Beranda Saya', 'icon' => 'M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6'],
            ['route' => 'mahasiswa.hasil', 'pattern' => 'mahasiswa.hasil*', 'label' => 'Hasil & Rincian Saya', 'icon' => 'M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z'],
            ['route' => 'mahasiswa.keberatan.index', 'pattern' => 'mahasiswa.keberatan.*', 'label' => 'Keberatan Saya', 'icon' => 'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z'],
            ['route' => 'mahasiswa.pertanyaan.index', 'pattern' => 'mahasiswa.pertanyaan.*', 'label' => 'Pertanyaan Saya', 'icon' => 'M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z'],
            ['route' => 'notifikasi.index', 'pattern' => 'notifikasi.*', 'label' => 'Notifikasi', 'icon' => 'M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9'],
            ['route' => 'profil.index', 'pattern' => 'profil.*', 'label' => 'Profil & Sandi', 'icon' => 'M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z'],
        ],
    };
@endphp
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title }} — SPPK-UKT UNG</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&family=Poppins:wght@600;700;800&display=swap" rel="stylesheet">

    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.14.8/dist/cdn.min.js"></script>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body
    x-data="{ sidebarOpen: false }"
    class="min-h-screen glass-bg-canvas text-ink font-body antialiased flex relative"
>
    <!-- Bentuk Dekoratif Lembut di Latar Belakang (Glassmorphism.md §2.2) -->
    <div class="glass-blob-container" aria-hidden="true">
        <div class="glass-blob glass-blob-rose"></div>
        <div class="glass-blob glass-blob-taupe"></div>
        <div class="glass-blob glass-blob-warm"></div>
    </div>

    <!-- Overlay Drawer untuk Mobile & Tablet (<1280px) -->
    <div
        x-cloak
        x-show="sidebarOpen"
        @click="sidebarOpen = false"
        x-transition.opacity.duration.200ms
        class="fixed inset-0 z-40 bg-ink/35 backdrop-blur-[4px] xl:hidden"
    ></div>

    <!-- Sidebar Kiri (Desktop-first >=1280px penuh, <1280px drawer — Glassmorphism.md §5.5) -->
    <aside
        aria-label="Navigasi Utama Sistem"
        :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full xl:translate-x-0'"
        class="fixed xl:static inset-y-0 left-0 z-50 w-72 shrink-0 glass-sidebar flex flex-col justify-between transition-transform duration-200 ease-out"
    >
        <div>
            <!-- Brand Header: Logo UNG + Universitas Negeri Gorontalo di atas permukaan terang (Glassmorphism.md §5.10) -->
            <div class="p-6 border-b border-divider/80 flex items-center justify-between gap-3 bg-primer/90">
                <a href="{{ route('publik.beranda') }}" class="flex items-center gap-3">
                    <img
                        src="{{ asset('images/logo-ung.png') }}"
                        alt="Universitas Negeri Gorontalo"
                        class="w-11 h-11 object-contain shrink-0"
                    >
                    <div>
                        <div class="font-heading font-bold text-[14px] text-ink leading-snug">
                            Universitas Negeri Gorontalo
                        </div>
                        <div class="text-[12px] text-ink-secondary font-medium">
                            SPPK-UKT UNG
                        </div>
                    </div>
                </a>

                <button
                    type="button"
                    @click="sidebarOpen = false"
                    aria-label="Tutup menu samping"
                    class="xl:hidden w-11 h-11 rounded-[10px] inline-flex items-center justify-center text-ink-secondary hover:bg-glass-hover"
                >
                    ✕
                </button>
            </div>

            <!-- Profil Singkat & Badge Peran Pengguna (Glassmorphism.md §5.5) -->
            @if($currentUser)
                <div class="px-6 py-4 border-b border-divider/80 bg-white/50">
                    <div class="flex items-center justify-between gap-2">
                        <span class="font-heading font-bold text-[14px] text-ink truncate">
                            {{ $currentUser->nama }}
                        </span>
                        <span class="px-2.5 py-0.5 rounded-[8px] text-[11px] font-heading font-bold bg-sekunder-light text-sekunder border border-border-subtle">
                            {{ $currentUser->labelPeran() }}
                        </span>
                    </div>
                    <div class="text-[12px] text-ink-secondary truncate mt-0.5">
                        {{ $currentUser->email }}
                    </div>
                    @if($currentUser->mahasiswa)
                        <div class="text-[12px] text-ink-muted mt-0.5">
                            NIM {{ $currentUser->mahasiswa->nim }} • {{ $currentUser->mahasiswa->program_studi }}
                        </div>
                    @endif
                </div>
            @endif

            <!-- Daftar Menu Peran -->
            <nav class="p-4 space-y-1.5" aria-label="Menu Peran">
                @foreach($menuItems as $item)
                    @php $isActive = request()->routeIs($item['pattern']); @endphp
                    <a
                        href="{{ route($item['route']) }}"
                        class="flex items-center gap-3 px-4 py-3 min-h-[44px] rounded-[10px] font-heading text-[14px] transition-all duration-150 {{ $isActive ? 'bg-sekunder-light/90 text-sekunder font-bold shadow-glass-xs border border-border-glass' : 'text-ink font-semibold hover:bg-glass-hover' }}"
                    >
                        <svg
                            class="w-5 h-5 shrink-0 {{ $isActive ? 'text-sekunder' : 'text-ink-secondary' }}"
                            fill="none"
                            stroke="currentColor"
                            stroke-width="1.9"
                            viewBox="0 0 24 24"
                            aria-hidden="true"
                        >
                            <path stroke-linecap="round" stroke-linejoin="round" d="{{ $item['icon'] }}" />
                        </svg>
                        <span>{{ $item['label'] }}</span>
                    </a>
                @endforeach

                <div class="pt-4 mt-4 border-t border-divider/80">
                    <div class="px-4 pb-2 text-[11px] font-heading font-bold uppercase tracking-wider text-ink-muted">
                        Informasi Terbuka
                    </div>
                    <a href="{{ route('publik.alur') }}" class="flex items-center gap-3 px-4 py-2.5 min-h-[44px] rounded-[10px] text-[13px] font-medium text-ink-secondary hover:bg-glass-hover hover:text-ink">
                        <span>Alur Penentuan UKT</span>
                    </a>
                    <a href="{{ route('publik.faktor') }}" class="flex items-center gap-3 px-4 py-2.5 min-h-[44px] rounded-[10px] text-[13px] font-medium text-ink-secondary hover:bg-glass-hover hover:text-ink">
                        <span>Daftar Faktor Penentu</span>
                    </a>
                    <a href="{{ route('publik.golongan') }}" class="flex items-center gap-3 px-4 py-2.5 min-h-[44px] rounded-[10px] text-[13px] font-medium text-ink-secondary hover:bg-glass-hover hover:text-ink">
                        <span>Rentang Nominal Golongan</span>
                    </a>
                    <a href="{{ route('publik.prosedur-keberatan') }}" class="flex items-center gap-3 px-4 py-2.5 min-h-[44px] rounded-[10px] text-[13px] font-medium text-ink-secondary hover:bg-glass-hover hover:text-ink">
                        <span>Prosedur Keberatan</span>
                    </a>
                </div>
            </nav>
        </div>

        <!-- Tombol Keluar -->
        <div class="p-4 border-t border-divider/80">
            <form method="POST" action="{{ route('logout') }}" data-prevent-double-submit>
                @csrf
                <button
                    type="submit"
                    class="glass-btn-secondary w-full px-4 py-2.5 flex items-center justify-center gap-2 text-[14px] text-error"
                >
                    <span>Keluar dari Sesi</span>
                </button>
            </form>
        </div>
    </aside>

    <!-- Area Konten Utama -->
    <div class="flex-1 flex flex-col min-w-0 relative z-10">
        <!-- Header Halaman Sticky -->
        <header class="sticky top-0 z-30 glass-header px-4 md:px-6 xl:px-8 py-4 flex items-center justify-between gap-4">
            <div class="flex items-center gap-3">
                <button
                    type="button"
                    @click="sidebarOpen = true"
                    aria-label="Buka navigasi samping"
                    class="xl:hidden glass-btn-secondary w-11 h-11 inline-flex items-center justify-center"
                >
                    <svg class="w-5 h-5 text-ink" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16" />
                    </svg>
                </button>
                <div>
                    <h1 class="text-[21px] md:text-[26px] font-heading font-bold text-ink leading-tight">
                        {{ $title }}
                    </h1>
                    @if($subtitle)
                        <p class="text-[13px] md:text-[14px] text-ink-secondary mt-0.5">{{ $subtitle }}</p>
                    @endif
                </div>
            </div>

            <div class="flex items-center gap-3">
                @if(isset($headerActions))
                    {{ $headerActions }}
                @endif

                <!-- Lonceng Notifikasi (KF-15) -->
                <a
                    href="{{ route('notifikasi.index') }}"
                    aria-label="Notifikasi Sistem ({{ $unreadCount }} belum dibaca)"
                    class="relative glass-btn-secondary px-3.5 py-2.5 inline-flex items-center gap-2 text-[13px]"
                >
                    <svg class="w-5 h-5 text-sekunder" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                    </svg>
                    <span class="hidden sm:inline font-heading font-semibold text-ink">Notifikasi</span>
                    @if($unreadCount > 0)
                        <span class="px-2 py-0.5 rounded-full text-[11px] font-bold bg-sekunder text-white">
                            {{ $unreadCount }}
                        </span>
                    @endif
                </a>
            </div>
        </header>

        <!-- Toast Umpan Balik (Glassmorphism.md §5.7) -->
        @if(session('toast_success') || session('toast_error'))
            <div
                x-data="{ show: true }"
                x-show="show"
                role="status"
                aria-live="polite"
                class="px-4 md:px-6 xl:px-8 pt-6"
            >
                @if(session('toast_success'))
                    <div class="rounded-[12px] bg-glass-raised backdrop-blur-[24px] border border-[#BBF7D0] shadow-glass-lg p-4 flex items-center justify-between gap-3">
                        <div class="flex items-center gap-3">
                            <span class="px-2.5 py-1 rounded-[8px] bg-success-bg text-success font-bold text-xs">✓ Berhasil</span>
                            <span class="text-ink font-medium">{{ session('toast_success') }}</span>
                        </div>
                        <button type="button" @click="show = false" aria-label="Tutup pesan" class="w-11 h-11 inline-flex items-center justify-center text-ink-secondary hover:bg-glass-hover rounded-[8px]">✕</button>
                    </div>
                @endif
                @if(session('toast_error'))
                    <div class="rounded-[12px] bg-glass-raised backdrop-blur-[24px] border border-[#FECACA] shadow-glass-lg p-4 flex items-center justify-between gap-3">
                        <div class="flex items-center gap-3">
                            <span class="px-2.5 py-1 rounded-[8px] bg-error-bg text-error font-bold text-xs">! Perhatian</span>
                            <span class="text-ink font-medium">{{ session('toast_error') }}</span>
                        </div>
                        <button type="button" @click="show = false" aria-label="Tutup pesan" class="w-11 h-11 inline-flex items-center justify-center text-ink-secondary hover:bg-glass-hover rounded-[8px]">✕</button>
                    </div>
                @endif
            </div>
        @endif

        <!-- Isi Halaman -->
        <main class="flex-1 page-content p-4 md:p-6 xl:p-8 space-y-8">
            {{ $slot }}
        </main>

        <!-- Footer Aplikasi (Putih Solid #FFFFFF) -->
        <footer class="border-t border-divider bg-white px-4 md:px-6 xl:px-8 py-4 text-[12px] text-ink-secondary flex flex-col sm:flex-row items-start sm:items-center justify-between gap-2">
            <div>
                <strong class="text-ink font-heading">Universitas Negeri Gorontalo</strong> — SPPK-UKT UNG
            </div>
            <div>
                Penetapan resmi golongan UKT tetap menjadi kewenangan penuh Universitas Negeri Gorontalo.
            </div>
        </footer>
    </div>
</body>
</html>
