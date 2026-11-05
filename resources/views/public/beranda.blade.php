<x-layouts.guest title="Beranda Informasi Transparansi UKT">
    <!-- Hero Utama -->
    <section class="neu-card p-6 md:p-10 relative overflow-hidden" data-reveal="up">
        <div class="accent-bar absolute top-0 inset-x-0" aria-hidden="true"></div>

        <div class="max-w-3xl space-y-4">
            <div class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-[8px] bg-[#F5E8E9] text-[#550b14] text-[12px] font-heading font-bold">
                <span>Transparansi Peninjauan &amp; Keberatan UKT — Universitas Negeri Gorontalo</span>
            </div>

            <h1 class="text-[28px] md:text-[32px] font-heading font-extrabold text-[#1C1917] leading-tight">
                Pahami Dasar Komponen Penilaian, Rentang Golongan, dan Prosedur Keberatan UKT dalam Satu Tempat
            </h1>

            <p class="text-[16px] text-[#57534E] leading-relaxed">
                SPPK-UKT UNG menjelaskan bagaimana data ekonomi keluarga diolah menjadi komponen penilaian, mengapa nominal tertentu muncul, serta bagaimana prosedur resmi bila Anda ingin mengajukan peninjauan ulang. Informasi umum dapat dibaca langsung tanpa login.
            </p>

            <div class="pt-2 flex flex-wrap items-center gap-3.5">
                @auth
                    <x-button :href="auth()->user()->isPetugas() ? route('petugas.beranda') : (auth()->user()->isPimpinan() ? route('pimpinan.rekap') : route('mahasiswa.hasil'))" variant="primary">
                        Lihat Rincian Hasil UKT Saya
                    </x-button>
                @else
                    <x-button :href="route('login')" variant="primary">
                        Masuk untuk Melihat Rincian Saya
                    </x-button>
                @endauth
                <x-button :href="route('publik.alur')" variant="secondary">
                    Pelajari Alur Penentuan UKT
                </x-button>
            </div>
        </div>
    </section>

    <!-- Empat Pintu Informasi Publik Tanpa Login (KF-02, KF-03, KF-14, KF-06) -->
    <section class="mt-12 space-y-6">
        <div class="flex flex-col md:flex-row md:items-end justify-between gap-3">
            <div>
                <h2 class="text-[26px] font-heading font-bold text-[#1C1917]">
                    Informasi Terbuka Tanpa Login
                </h2>
                <p class="text-[14px] text-[#57534E]">
                    Sesuai kebutuhan mahasiswa, alur proses, faktor penentu, rentang nominal, dan prosedur keberatan dibuka untuk publik.
                </p>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-6">
            <a href="{{ route('publik.alur') }}" class="neu-card interactive-card p-6 flex flex-col justify-between" data-reveal="up" style="--reveal-delay: 0ms">
                <div class="space-y-3">
                    <span class="inline-block px-3 py-1 rounded-[8px] bg-[#F5E8E9] text-[#550b14] font-heading font-bold text-[12px]">
                        Tahapan &amp; Hubungan Data
                    </span>
                    <h3 class="text-[21px] font-heading font-bold text-[#1C1917]">
                        Alur Penentuan UKT
                    </h3>
                    <p class="text-[14px] text-[#57534E] leading-relaxed">
                        Urutan proses dari pengisian data sosial-ekonomi, verifikasi berkas, pengolahan komponen, hingga penetapan hasil.
                    </p>
                </div>
                <div class="mt-6 pt-4 border-t border-[#DDD9D6] font-heading font-bold text-[14px] text-[#550b14] flex items-center justify-between">
                    <span>Baca Alur Lengkap</span>
                    <span aria-hidden="true">→</span>
                </div>
            </a>

            <a href="{{ route('publik.faktor') }}" class="neu-card interactive-card p-6 flex flex-col justify-between" data-reveal="up" style="--reveal-delay: 80ms">
                <div class="space-y-3">
                    <span class="inline-block px-3 py-1 rounded-[8px] bg-[#EDE6E3] text-[#6B574F] font-heading font-bold text-[12px]">
                        {{ $jumlahFaktorAktif }} Variabel Terdaftar
                    </span>
                    <h3 class="text-[21px] font-heading font-bold text-[#1C1917]">
                        Daftar Faktor Penentu
                    </h3>
                    <p class="text-[14px] text-[#57534E] leading-relaxed">
                        Penjelasan lengkap faktor penilaian yang dipakai beserta status konfirmasi resminya dan pengaruh umumnya terhadap hasil.
                    </p>
                </div>
                <div class="mt-6 pt-4 border-t border-[#DDD9D6] font-heading font-bold text-[14px] text-[#550b14] flex items-center justify-between">
                    <span>Lihat Daftar Faktor</span>
                    <span aria-hidden="true">→</span>
                </div>
            </a>

            <a href="{{ route('publik.golongan') }}" class="neu-card interactive-card p-6 flex flex-col justify-between" data-reveal="up" style="--reveal-delay: 160ms">
                <div class="space-y-3">
                    <span class="inline-block px-3 py-1 rounded-[8px] bg-[#F5E8E9] text-[#550b14] font-heading font-bold text-[12px]">
                        {{ $daftarGolongan->count() }} Kelompok Golongan
                    </span>
                    <h3 class="text-[21px] font-heading font-bold text-[#1C1917]">
                        Rentang Nominal UKT
                    </h3>
                    <p class="text-[14px] text-[#57534E] leading-relaxed">
                        Tabel batas bawah dan batas atas nominal per semester untuk tiap golongan UKT lengkap dengan sumber dan tanggal berlakunya.
                    </p>
                </div>
                <div class="mt-6 pt-4 border-t border-[#DDD9D6] font-heading font-bold text-[14px] text-[#550b14] flex items-center justify-between">
                    <span>Periksa Rentang Golongan</span>
                    <span aria-hidden="true">→</span>
                </div>
            </a>

            <a href="{{ route('publik.prosedur-keberatan') }}" class="neu-card interactive-card p-6 flex flex-col justify-between" data-reveal="up" style="--reveal-delay: 240ms">
                <div class="space-y-3">
                    <span class="inline-block px-3 py-1 rounded-[8px] bg-[#EDE6E3] text-[#6B574F] font-heading font-bold text-[12px]">
                        Panduan Peninjauan Ulang
                    </span>
                    <h3 class="text-[21px] font-heading font-bold text-[#1C1917]">
                        Prosedur Keberatan
                    </h3>
                    <p class="text-[14px] text-[#57534E] leading-relaxed">
                        Ketahui siapa yang berhak mengajukan keberatan, dokumen bukti yang wajib disiapkan, dan tahapan pemeriksaannya.
                    </p>
                </div>
                <div class="mt-6 pt-4 border-t border-[#DDD9D6] font-heading font-bold text-[14px] text-[#550b14] flex items-center justify-between">
                    <span>Baca Prosedur Keberatan</span>
                    <span aria-hidden="true">→</span>
                </div>
            </a>
        </div>
    </section>

    <!-- Batas Peran Sistem (Peringatan Jujur PRD) -->
    <section class="mt-12 neu-card p-6 md:p-8 bg-[#F3F2F0]" data-reveal="up">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-6">
            <div class="space-y-2 max-w-3xl">
                <h2 class="text-[18px] font-heading font-bold text-[#1C1917]">
                    Batas Kewenangan Sistem SPPK-UKT UNG
                </h2>
                <p class="text-[14px] text-[#57534E] leading-relaxed">
                    Sistem ini <strong>bukan penentu resmi golongan UKT</strong>, bukan sistem pembayaran UKT, dan tidak mengubah data ekonomi mahasiswa yang sudah diverifikasi. Mesin penilaian pada sistem ini berfungsi sebagai model penjelasan agar setiap langkah komponen dapat ditelusuri dengan bahasa yang mudah dipahami.
                </p>
            </div>
            <div class="shrink-0">
                <x-button :href="route('publik.bantuan')" variant="secondary">
                    Kanal Bantuan &amp; Layanan
                </x-button>
            </div>
        </div>
    </section>
</x-layouts.guest>

