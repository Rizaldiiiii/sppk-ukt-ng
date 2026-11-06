@props([
    'title' => 'SPPK-UKT UNG',
    'showScrollProgress' => false,
])
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title }} — Universitas Negeri Gorontalo</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&family=Poppins:wght@600;700;800&display=swap" rel="stylesheet">

    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.14.8/dist/cdn.min.js"></script>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen flex flex-col glass-bg-canvas text-ink font-body antialiased relative">
    <!-- Bentuk Dekoratif Lembut di Latar Belakang (Glassmorphism.md §2.2) -->
    <div class="glass-blob-container" aria-hidden="true">
        <div class="glass-blob glass-blob-rose"></div>
        <div class="glass-blob glass-blob-taupe"></div>
        <div class="glass-blob glass-blob-warm"></div>
        <div class="glass-blob glass-blob-maroon glass-blob-maroon-top"></div>
        <div class="glass-blob glass-blob-maroon glass-blob-maroon-bottom"></div>
    </div>

    @if($showScrollProgress)
        <div class="scroll-progress" aria-hidden="true"></div>
    @endif

    <!-- Header Navigasi Publik (Bar Kaca Tegas + Pill Navigasi Mengambang — Glassmorphism.md §5.5 & §5.10) -->
    <header
        x-data="{ mobileOpen: false, scrolled: false }"
        @scroll.window="scrolled = window.scrollY > 40"
        :class="scrolled ? 'py-2.5 shadow-glass-lg' : 'py-3.5 shadow-glass-md'"
        class="sticky top-0 z-40 glass-header transition-[padding,box-shadow] duration-300"
    >
        <div class="max-w-7xl mx-auto px-4 md:px-6 xl:px-8 flex items-center justify-between gap-4">
            <a href="{{ route('publik.beranda') }}" class="flex items-center gap-3.5 min-h-[44px] rounded-[12px] px-3 py-1.5 bg-primer/95 border border-white/90 shadow-glass-xs shrink-0">
                <img
                    src="{{ asset('images/logo-ung.png') }}"
                    alt="Universitas Negeri Gorontalo"
                    class="w-11 h-11 object-contain shrink-0"
                >
                <div>
                    <div class="font-heading font-bold text-[15px] md:text-[16px] text-ink leading-tight">
                        Universitas Negeri Gorontalo
                    </div>
                    <div class="text-[12px] text-ink-secondary font-medium">
                        SPPK-UKT UNG — Peninjauan &amp; Keberatan UKT
                    </div>
                </div>
            </a>

            <!-- Navigasi Desktop (Bar Pill Kaca Mengambang meniru glassmorphism-top-navbar.jpeg) -->
            <nav aria-label="Navigasi Publik Utama" class="hidden lg:flex items-center gap-1 p-1.5 glass-top-nav-pill">
                @php
                    $navItems = [
                        ['route' => 'publik.beranda', 'label' => 'Beranda'],
                        ['route' => 'publik.alur', 'label' => 'Alur Penentuan'],
                        ['route' => 'publik.faktor', 'label' => 'Faktor Penentu'],
                        ['route' => 'publik.golongan', 'label' => 'Rentang Golongan'],
                        ['route' => 'publik.prosedur-keberatan', 'label' => 'Prosedur Keberatan'],
                        ['route' => 'publik.bantuan', 'label' => 'Bantuan'],
                    ];
                @endphp
                @foreach($navItems as $item)
                    @php $active = request()->routeIs($item['route']); @endphp
                    <a
                        href="{{ route($item['route']) }}"
                        class="px-4 py-2 min-h-[40px] inline-flex items-center gap-2 rounded-full font-heading text-[13px] xl:text-[14px] transition-colors duration-150 {{ $active ? 'glass-top-nav-item-active font-bold' : 'text-ink-secondary font-medium hover:text-ink hover:bg-white/65' }}"
                    >
                        @if($active)
                            <span class="w-1.5 h-1.5 rounded-full bg-sekunder shrink-0" aria-hidden="true"></span>
                        @endif
                        <span>{{ $item['label'] }}</span>
                    </a>
                @endforeach
            </nav>

            <!-- Tombol Login / Dashboard -->
            <div class="hidden lg:flex items-center gap-3">
                @auth
                    @php
                        $dashboardUrl = match(auth()->user()->peran) {
                            'petugas' => route('petugas.beranda'),
                            'pimpinan' => route('pimpinan.rekap'),
                            default => route('mahasiswa.beranda'),
                        };
                    @endphp
                    <a href="{{ $dashboardUrl }}" class="glass-btn-primary px-5 py-2.5 inline-flex items-center gap-2 text-[14px]">
                        <span>Buka Dasbor</span>
                    </a>
                @else
                    <a href="{{ route('login') }}" class="glass-btn-primary px-5 py-2.5 inline-flex items-center gap-2 text-[14px]">
                        <span>Masuk Akun</span>
                    </a>
                @endauth
            </div>

            <!-- Tombol Drawer Mobile/Tablet -->
            <button
                type="button"
                @click="mobileOpen = !mobileOpen"
                :aria-expanded="mobileOpen"
                aria-label="Buka menu navigasi"
                class="lg:hidden glass-btn-secondary w-11 h-11 inline-flex items-center justify-center"
            >
                <svg class="w-5 h-5 text-ink" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16" />
                </svg>
            </button>
        </div>

        <!-- Drawer Navigasi Mobile/Tablet -->
        <div
            x-cloak
            x-show="mobileOpen"
            x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0 -translate-y-2"
            x-transition:enter-end="opacity-100 translate-y-0"
            class="lg:hidden border-t border-border-glass bg-glass-raised backdrop-blur-[16px] px-4 pt-3 pb-5 mt-2 space-y-1.5"
        >
            @foreach($navItems as $item)
                @php $active = request()->routeIs($item['route']); @endphp
                <a
                    href="{{ route($item['route']) }}"
                    class="flex items-center px-4 py-3 min-h-[44px] rounded-[10px] font-heading text-[14px] {{ $active ? 'bg-sekunder-light/90 text-sekunder font-bold shadow-glass-xs' : 'text-ink font-semibold hover:bg-glass-hover' }}"
                >
                    {{ $item['label'] }}
                </a>
            @endforeach
            <div class="pt-3 border-t border-divider/80">
                @auth
                    <a href="{{ $dashboardUrl }}" class="glass-btn-primary w-full px-5 py-3 inline-flex items-center justify-center text-[14px]">
                        Buka Dasbor Saya
                    </a>
                @else
                    <a href="{{ route('login') }}" class="glass-btn-primary w-full px-5 py-3 inline-flex items-center justify-center text-[14px]">
                        Masuk Akun
                    </a>
                @endauth
            </div>
        </div>
    </header>

    <!-- Toast Umpan Balik: overlay mengambang (fixed) di atas hero, tidak menggeser layout -->
    @if(session('toast_success') || session('toast_error'))
        <div
            x-data="{ show: true }"
            x-show="show"
            x-transition.opacity.duration.200ms
            role="status"
            aria-live="polite"
            class="fixed top-[88px] inset-x-0 z-[60] px-4 md:px-6 xl:px-8 pointer-events-none"
        >
            <div class="max-w-7xl mx-auto flex justify-end">
                <div class="w-full max-w-xl pointer-events-auto">
                    @if(session('toast_success'))
                        <div class="rounded-[12px] bg-glass-raised backdrop-blur-[24px] border border-[#BBF7D0] shadow-glass-lg p-4 flex items-center justify-between gap-3">
                            <div class="flex items-center gap-3">
                                <span class="px-2.5 py-1 rounded-[8px] bg-success-bg text-success font-bold text-xs shrink-0">✓ Berhasil</span>
                                <span class="text-ink font-medium">{{ session('toast_success') }}</span>
                            </div>
                            <button type="button" @click="show = false" aria-label="Tutup notifikasi" class="w-11 h-11 shrink-0 inline-flex items-center justify-center text-ink-secondary hover:bg-glass-hover rounded-[8px]">✕</button>
                        </div>
                    @endif
                    @if(session('toast_error'))
                        <div class="rounded-[12px] bg-glass-raised backdrop-blur-[24px] border border-[#FECACA] shadow-glass-lg p-4 flex items-center justify-between gap-3">
                            <div class="flex items-center gap-3">
                                <span class="px-2.5 py-1 rounded-[8px] bg-error-bg text-error font-bold text-xs shrink-0">! Perhatian</span>
                                <span class="text-ink font-medium">{{ session('toast_error') }}</span>
                            </div>
                            <button type="button" @click="show = false" aria-label="Tutup notifikasi" class="w-11 h-11 shrink-0 inline-flex items-center justify-center text-ink-secondary hover:bg-glass-hover rounded-[8px]">✕</button>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    @endif

    <!-- Slot Hero Full-Width (Khusus Beranda Publik Viewport Pertama) -->
    @if(isset($hero))
        {{ $hero }}
    @endif

    <!-- Konten Utama -->
    <main class="flex-1 page-content max-w-7xl w-full mx-auto px-4 md:px-6 xl:px-8 py-8 md:py-10 relative z-10">
        {{ $slot }}
    </main>

    <!-- Footer Publik (Putih Solid #FFFFFF) -->
    <footer class="border-t border-divider bg-white mt-16 relative z-10">
        <div class="max-w-7xl mx-auto px-4 md:px-6 xl:px-8 py-8 flex flex-col md:flex-row items-start md:items-center justify-between gap-6">
            <div class="flex items-center gap-3.5 px-2.5 py-1.5 rounded-[10px] bg-white">
                <img
                    src="{{ asset('images/logo-ung.png') }}"
                    alt="Universitas Negeri Gorontalo"
                    class="w-10 h-10 object-contain shrink-0"
                >
                <div>
                    <div class="font-heading font-bold text-ink">Universitas Negeri Gorontalo</div>
                    <div class="text-[13px] text-ink-secondary">
                        Sistem Informasi Peninjauan dan Pengajuan Keberatan Uang Kuliah Tunggal (SPPK-UKT UNG)
                    </div>
                </div>
            </div>
            <div class="text-[13px] text-ink-secondary max-w-xl leading-relaxed">
                <strong class="text-ink">Catatan Keterbukaan:</strong> Sistem ini menyediakan rincian penjelasan komponen penilaian dan alur pengajuan keberatan UKT. Penetapan resmi golongan UKT tetap menjadi kewenangan penuh Universitas Negeri Gorontalo.
            </div>
        </div>
    </footer>
</body>
</html>
