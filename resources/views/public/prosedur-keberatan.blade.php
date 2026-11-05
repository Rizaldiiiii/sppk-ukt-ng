@php
    $dataProsedur = $konten ? json_decode($konten->isi, true) : null;
    $siapaBoleh = $dataProsedur['siapa_boleh_mengajukan'] ?? [];
    $dokumenDiperlukan = $dataProsedur['dokumen_diperlukan'] ?? [];
    $tujuanPengajuan = $dataProsedur['tujuan_pengajuan'] ?? 'Tim Verifikasi dan Petugas UKT melalui Biro Akademik, Kemahasiswaan, dan Perencanaan (BAKP) / Bagian Keuangan Universitas Negeri Gorontalo.';
    $tahapanPeninjauan = $dataProsedur['tahapan_peninjauan'] ?? [];
@endphp
<x-layouts.guest title="Prosedur Pengajuan Keberatan UKT" :showScrollProgress="true">
    <div class="space-y-10">
        <!-- Header & Tautan Langsung sesuai Status Login (KF-06) -->
        <section class="glass-card p-6 md:p-8 relative overflow-hidden">
            <div class="accent-bar absolute top-0 inset-x-0" aria-hidden="true"></div>
            <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-6">
                <div class="max-w-3xl space-y-3">
                    <span class="inline-block px-3 py-1 rounded-[8px] bg-highlight-light text-highlight-dark font-heading font-bold text-[12px]">
                        Informasi Publik • Prosedur Peninjauan Ulang
                    </span>
                    <h1 class="text-[28px] md:text-[32px] font-heading font-extrabold text-ink">
                        {{ $konten?->judul ?? 'Prosedur Pengajuan Keberatan dan Peninjauan Ulang UKT' }}
                    </h1>
                    <p class="text-[16px] text-ink-secondary leading-relaxed">
                        {{ $konten?->ringkasan ?? 'Panduan syarat, dokumen bukti, tahapan pemeriksaan, serta tujuan penerusan pengajuan keberatan UKT.' }}
                    </p>
                </div>

                <!-- CTA Pengajuan (Tertaut ke formulir bila login, atau ke login bila belum login — Kriteria KF-06) -->
                <div class="glass-card-raised p-5 shrink-0 lg:w-80 space-y-3">
                    @auth
                        @if(auth()->user()->isMahasiswa())
                            <div class="text-[13px] font-medium text-ink">
                                Anda sudah masuk sebagai mahasiswa. Periksa rincian Anda atau buka halaman pengajuan keberatan:
                            </div>
                            <x-button :href="route('mahasiswa.keberatan.create')" variant="primary" class="w-full">
                                Buka Halaman Pengajuan Keberatan
                            </x-button>
                        @else
                            <div class="text-[13px] font-medium text-ink-secondary">
                                Pengajuan keberatan dilakukan oleh akun mahasiswa yang bersangkutan.
                            </div>
                        @endif
                    @else
                        <div class="text-[13px] text-ink-secondary leading-relaxed">
                            <strong class="text-ink">Perlu Login untuk Mengajukan:</strong> Informasi prosedur di halaman ini terbuka untuk umum, namun pengiriman pengajuan keberatan memerlukan login menggunakan akun mahasiswa.
                        </div>
                        <x-button :href="route('login')" variant="primary" class="w-full">
                            Masuk untuk Mengajukan Keberatan
                        </x-button>
                    @endauth
                </div>
            </div>
        </section>

        @if(is_array($dataProsedur))
            <!-- Grid Syarat & Dokumen yang Diperlukan -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <section class="glass-card p-6 md:p-7 space-y-4">
                    <h2 class="text-[21px] font-heading font-bold text-ink">
                        1. Siapa yang Boleh Mengajukan Keberatan?
                    </h2>
                    <ul class="space-y-3 text-[14px] text-ink">
                        @foreach($siapaBoleh as $butir)
                            <li class="flex items-start gap-3">
                                <span class="w-6 h-6 rounded-full bg-sekunder-light text-sekunder font-bold text-xs flex items-center justify-center shrink-0 mt-0.5">✓</span>
                                <span class="leading-relaxed">{{ $butir }}</span>
                            </li>
                        @endforeach
                    </ul>
                </section>

                <section class="glass-card p-6 md:p-7 space-y-4">
                    <h2 class="text-[21px] font-heading font-bold text-ink">
                        2. Dokumen Bukti Pendukung yang Diperlukan
                    </h2>
                    <ul class="space-y-3 text-[14px] text-ink">
                        @foreach($dokumenDiperlukan as $dok)
                            <li class="flex items-start gap-3">
                                <span class="w-6 h-6 rounded-full bg-highlight-light text-highlight-dark font-bold text-xs flex items-center justify-center shrink-0 mt-0.5">•</span>
                                <span class="leading-relaxed">{{ $dok }}</span>
                            </li>
                        @endforeach
                    </ul>
                </section>
            </div>

            <!-- Tujuan Penerusan & Perkiraan Waktu Tunggu (KF-06) -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <section class="glass-card p-6 md:p-7 space-y-2">
                    <h2 class="text-[21px] font-heading font-bold text-ink">
                        3. Ke Mana Pengajuan Diteruskan?
                    </h2>
                    <p class="text-[14px] text-ink leading-relaxed">
                        {{ $tujuanPengajuan }}
                    </p>
                </section>

                <section class="glass-card p-6 md:p-7 space-y-2">
                    <h2 class="text-[21px] font-heading font-bold text-ink">
                        4. Perkiraan Waktu Peninjauan
                    </h2>
                    @if($perkiraanWaktu)
                        <p class="text-[14px] text-ink font-semibold">
                            {{ $perkiraanWaktu->teks_perkiraan }}
                        </p>
                        <p class="text-[12px] text-ink-secondary">
                            Sumber: {{ $perkiraanWaktu->sumber }} (diperbarui {{ $perkiraanWaktu->tanggal?->locale('id')->translatedFormat('d M Y') }})
                        </p>
                    @else
                        <p class="text-[14px] text-ink-secondary leading-relaxed">
                            <strong class="text-ink">Waktu tunggu belum ditetapkan secara resmi oleh kampus.</strong>
                            Sistem tidak mencantumkan angka perkiraan tanpa dasar penetapan resmi; setiap tahapan pemeriksaan akan diberitahukan melalui notifikasi sistem dan email.
                        </p>
                    @endif
                </section>
            </div>

            <!-- Tahapan Peninjauan -->
            <section class="glass-card p-6 md:p-8 space-y-5">
                <h2 class="text-[21px] font-heading font-bold text-ink">
                    5. Tahapan Peninjauan Pengajuan Keberatan
                </h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    @foreach($tahapanPeninjauan as $tahap)
                        <div class="p-4 rounded-[10px] bg-white/80 border border-border-glass flex items-start gap-3.5">
                            <span class="w-8 h-8 rounded-[8px] bg-sekunder text-[#ffffff] font-heading font-bold text-sm flex items-center justify-center shrink-0">
                                {{ $loop->iteration }}
                            </span>
                            <p class="text-[14px] text-ink leading-relaxed">
                                {{ $tahap }}
                            </p>
                        </div>
                    @endforeach
                </div>
            </section>
        @else
            <section class="glass-card p-6 md:p-8 text-[14px] text-ink whitespace-pre-line leading-relaxed">
                {{ $konten?->isi }}
            </section>
        @endif
    </div>
</x-layouts.guest>
