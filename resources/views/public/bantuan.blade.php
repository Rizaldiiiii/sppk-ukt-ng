<x-layouts.guest title="Bantuan dan Jalur Bertanya">
    <div class="space-y-8">
        <section class="neu-card p-6 md:p-8 relative overflow-hidden">
            <div class="accent-bar absolute top-0 inset-x-0" aria-hidden="true"></div>
            <div class="max-w-3xl space-y-3">
                <span class="inline-block px-3 py-1 rounded-[8px] bg-[#F5E8E9] text-[#550b14] font-heading font-bold text-[12px]">
                    Informasi Layanan &amp; Bantuan
                </span>
                <h1 class="text-[28px] md:text-[32px] font-heading font-extrabold text-[#1C1917]">
                    {{ $kontenKontak?->judul ?? 'Kanal Bantuan dan Informasi Layanan Petugas UKT' }}
                </h1>
                <p class="text-[16px] text-[#57534E] leading-relaxed">
                    {{ $kontenKontak?->ringkasan ?? 'Informasi jalur bertanya resmi bagi mahasiswa Universitas Negeri Gorontalo mengenai penjelasan hasil maupun prosedur keberatan UKT.' }}
                </p>
            </div>
        </section>

        @if($kontakPublikAktif)
            <section class="neu-card p-6 md:p-8 space-y-3">
                <h2 class="text-[21px] font-heading font-bold text-[#1C1917]">
                    Unit Layanan Informasi UKT Kampus
                </h2>
                <p class="text-[14px] text-[#1C1917] leading-relaxed">
                    {{ $kontenKontak?->isi ?? 'Layanan Informasi UKT — Biro Akademik, Kemahasiswaan, dan Perencanaan (BAKP) & Bagian Keuangan Universitas Negeri Gorontalo.' }}
                </p>
            </section>
        @endif

        <x-empty-state
            title="Pengiriman Pertanyaan Daring Belum Tersedia di Tahap Ini"
            description="Fasilitas mengirim pertanyaan langsung kepada petugas dari dalam sistem beserta pelacakan jawabannya (KF-08) dijadwalkan pada pembangunan Tahap 2 (Pendukung), dan memerlukan login akun mahasiswa."
        >
            <x-slot:actions>
                <x-button :href="route('publik.alur')" variant="secondary">
                    Lihat Alur Penentuan UKT
                </x-button>
                @guest
                    <x-button :href="route('login')" variant="primary">
                        Masuk Akun Mahasiswa
                    </x-button>
                @endguest
            </x-slot:actions>
        </x-empty-state>
    </div>
</x-layouts.guest>

