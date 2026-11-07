<x-layouts.guest title="Bantuan dan Jalur Bertanya">
    <div class="space-y-8">
        <section class="glass-card glass-card-glow p-6 md:p-8 relative overflow-hidden">
            <div class="accent-bar absolute top-0 inset-x-0" aria-hidden="true"></div>
            <div class="max-w-3xl space-y-3">
                <span class="inline-block px-3 py-1 rounded-[8px] bg-sekunder-light text-sekunder font-heading font-bold text-[12px]">
                    Informasi Layanan &amp; Bantuan
                </span>
                <h1 class="text-[28px] md:text-[32px] font-heading font-extrabold text-ink">
                    {{ $kontenKontak?->judul ?? 'Kanal Bantuan dan Informasi Layanan Petugas UKT' }}
                </h1>
                <p class="text-[16px] text-ink-secondary leading-relaxed">
                    {{ $kontenKontak?->ringkasan ?? 'Informasi jalur bertanya resmi bagi mahasiswa Universitas Negeri Gorontalo mengenai penjelasan hasil maupun prosedur keberatan UKT.' }}
                </p>
            </div>
        </section>

        @if($kontakPublikAktif)
            <section class="glass-card p-6 md:p-8 space-y-3">
                <h2 class="text-[21px] font-heading font-bold text-ink">
                    Unit Layanan Informasi UKT Kampus
                </h2>
                <p class="text-[14px] text-ink leading-relaxed">
                    {{ $kontenKontak?->isi ?? 'Layanan Informasi UKT — Biro Akademik, Kemahasiswaan, dan Perencanaan (BAKP) & Bagian Keuangan Universitas Negeri Gorontalo.' }}
                </p>
            </section>
        @endif

        <section class="glass-card p-6 md:p-8 space-y-4">
            <div class="flex flex-wrap items-center gap-2">
                <span class="px-3 py-1 rounded-[8px] bg-sekunder-light text-sekunder font-heading font-bold text-[12px]">
                    KF-08 • Jalur Bertanya Terlacak
                </span>
                <span class="text-[12px] text-ink-secondary font-medium">
                    Tersedia untuk Mahasiswa Terautentikasi
                </span>
            </div>
            <h2 class="text-[21px] font-heading font-bold text-ink">
                Kirim Pertanyaan Langsung kepada Petugas UKT dari Dalam Sistem
            </h2>
            <p class="text-[14px] text-ink-secondary leading-relaxed max-w-3xl">
                Mahasiswa Universitas Negeri Gorontalo dapat mengirimkan pertanyaan tertulis mengenai rincian nilai komponen, persyaratan dokumen pendukung, maupun alur peninjauan ulang UKT langsung melalui menu <strong>Pertanyaan Saya</strong> setelah masuk ke akun. Setiap pertanyaan memperoleh nomor tanda terima dan jawabannya dapat dipantau di dalam sistem.
            </p>
            <div class="flex flex-wrap gap-3 pt-1">
                <x-button :href="route('publik.alur')" variant="secondary">
                    Lihat Alur Penentuan UKT
                </x-button>
                @auth
                    @if(auth()->user()->isMahasiswa())
                        <x-button :href="route('mahasiswa.pertanyaan.index')" variant="primary">
                            Buka Halaman Pertanyaan Saya
                        </x-button>
                    @endif
                @else
                    <x-button :href="route('login')" variant="primary">
                        Masuk Akun Mahasiswa untuk Mengirim Pertanyaan
                    </x-button>
                @endauth
            </div>
        </section>
    </div>
</x-layouts.guest>
