<x-layouts.app
    title="Beranda Mahasiswa"
    :subtitle="'Ringkasan status peninjauan UKT untuk ' . $user->nama . ($mahasiswa ? ' (NIM ' . $mahasiswa->nim . ')' : '')"
>
    <!-- Kartu Utama Status Hasil UKT -->
    <section class="glass-card glass-card-glow p-6 md:p-8 relative overflow-hidden" data-reveal="up">
        <div class="accent-bar absolute top-0 inset-x-0" aria-hidden="true"></div>

        @if($hasilTerbaru)
            <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-6">
                <div class="space-y-2.5 max-w-2xl">
                    @if($hasilTerbaru->isResmi())
                        <x-badge-status status="resmi" label="Hasil penetapan resmi kampus" />
                        <h2 class="text-[26px] font-heading font-extrabold text-ink">
                            {{ $hasilTerbaru->golongan?->nama ?? 'Golongan UKT' }} — {{ $hasilTerbaru->formatNominal() }} / Semester
                        </h2>
                        <p class="text-[14px] text-ink-secondary">
                            Ditetapkan pada {{ $hasilTerbaru->tanggal_penetapan?->locale('id')->translatedFormat('d M Y') }} • Sumber: {{ $hasilTerbaru->sumber_penetapan }}
                        </p>
                    @else
                        <span class="inline-block px-3 py-1 rounded-[8px] bg-highlight-light text-highlight-dark font-heading font-bold text-[12px]">
                            Rincian Komponen Penilaian Tersedia
                        </span>
                        <h2 class="text-[26px] font-heading font-extrabold text-ink">
                            Rincian Komponen Penilaian UKT Anda ({{ $hasilTerbaru->rincianPenilaian->count() }} Komponen)
                        </h2>
                        <p class="text-[14px] text-ink-secondary">
                            Total Skor Komponen: <strong class="text-ink tabular-nums">{{ number_format((float) $hasilTerbaru->skor_total, 2, ',', '.') }} poin</strong>
                            • Versi Parameter: {{ $hasilTerbaru->versiParameter?->versi ?? '-' }}
                        </p>
                    @endif
                </div>

                <div class="shrink-0 flex flex-wrap gap-3">
                    <x-button :href="route('mahasiswa.hasil')" variant="primary">
                        Buka Rincian &amp; Alasan Lengkap
                    </x-button>
                    <x-button :href="route('publik.prosedur-keberatan')" variant="secondary">
                        Pelajari Prosedur Keberatan
                    </x-button>
                </div>
            </div>
        @else
            <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-6">
                <div class="space-y-2 max-w-2xl">
                    <span class="inline-block px-3 py-1 rounded-[8px] bg-info-bg text-info font-heading font-bold text-[12px]">
                        Menunggu Data Hasil Penilaian
                    </span>
                    <h2 class="text-[24px] font-heading font-bold text-ink">
                        Belum Ada Hasil Penilaian UKT Tercatat pada Akun Anda
                    </h2>
                    <p class="text-[14px] text-ink-secondary leading-relaxed">
                        Data rincian komponen penilaian UKT Anda sedang menunggu pemrosesan atau entri oleh petugas. Silakan buka halaman Hasil &amp; Rincian Saya untuk melihat panduan langkah selanjutnya.
                    </p>
                </div>
                <div class="shrink-0 flex flex-wrap gap-3">
                    <x-button :href="route('mahasiswa.hasil')" variant="primary">
                        Periksa Status Hasil Saya
                    </x-button>
                    <x-button :href="route('publik.alur')" variant="secondary">
                        Lihat Alur Penentuan UKT
                    </x-button>
                </div>
            </div>
        @endif
    </section>

    <!-- Ringkasan Cepat & Notifikasi Terbaru -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="glass-card p-6 space-y-2" data-reveal="up" style="--reveal-delay: 0ms">
            <div class="text-[13px] font-medium text-ink-secondary">Identitas Akademik</div>
            <div class="text-[21px] font-heading font-bold text-ink">{{ $mahasiswa?->nim ?? '-' }}</div>
            <div class="text-[13px] text-ink-secondary">
                {{ $mahasiswa?->program_studi ?? '-' }} • Angkatan {{ $mahasiswa?->angkatan ?? '-' }}
            </div>
        </div>

        <div class="glass-card p-6 space-y-2" data-reveal="up" style="--reveal-delay: 80ms">
            <div class="text-[13px] font-medium text-ink-secondary">Komponen Penilaian Tercatat</div>
            <div class="text-[28px] font-heading font-extrabold text-sekunder tabular-nums">
                {{ $hasilTerbaru ? $hasilTerbaru->rincianPenilaian->count() : 0 }} Komponen
            </div>
            <div class="text-[13px] text-ink-secondary">
                Setiap komponen disertai langkah hitung dan kalimat alasan.
            </div>
        </div>

        <div class="glass-card p-6 space-y-2" data-reveal="up" style="--reveal-delay: 160ms">
            <div class="text-[13px] font-medium text-ink-secondary">Pemberitahuan Sistem (KF-15)</div>
            <div class="text-[28px] font-heading font-extrabold text-ink tabular-nums">
                {{ $jumlahNotifikasiBelumDibaca }} Belum Dibaca
            </div>
            <div>
                <a href="{{ route('notifikasi.index') }}" class="text-[13px] font-heading font-bold text-sekunder underline hover:text-sekunder-hover">
                    Lihat Semua Notifikasi →
                </a>
            </div>
        </div>
    </div>
</x-layouts.app>
