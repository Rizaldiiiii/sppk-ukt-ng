<x-layouts.app
    :title="'Detail Pertanyaan #' . $pertanyaan->id"
    subtitle="Rincian pertanyaan dan jawaban resmi dari Petugas UKT (KF-08)"
>
    <x-slot:headerActions>
        <x-button :href="route('mahasiswa.pertanyaan.index')" variant="secondary">
            ← Kembali ke Daftar Pertanyaan
        </x-button>
    </x-slot:headerActions>

    <section class="glass-card glass-card-glow p-6 md:p-8 relative overflow-hidden space-y-6">
        <div class="accent-bar absolute top-0 inset-x-0" aria-hidden="true"></div>

        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 border-b border-divider/80 pb-4">
            <div class="space-y-1">
                <div class="flex flex-wrap items-center gap-2.5">
                    <span class="text-[12px] font-heading font-bold uppercase tracking-wider text-sekunder">
                        Nomor Tanda Terima #{{ $pertanyaan->id }}
                    </span>
                    <x-badge-status :status="$pertanyaan->status" :label="'Status: ' . ucfirst($pertanyaan->status)" />
                </div>
                <h2 class="text-[21px] font-heading font-extrabold text-ink">
                    Pertanyaan dari {{ $pertanyaan->mahasiswa?->user?->nama }} (NIM {{ $pertanyaan->mahasiswa?->nim }})
                </h2>
            </div>

            <div class="text-[12px] text-ink-secondary sm:text-right">
                <div>Dikirim pada: <strong class="text-ink">{{ $pertanyaan->created_at?->locale('id')->translatedFormat('d M Y H:i') }}</strong></div>
                @if($pertanyaan->dijawab_pada)
                    <div>Dijawab pada: <strong class="text-ink">{{ $pertanyaan->dijawab_pada->locale('id')->translatedFormat('d M Y H:i') }}</strong></div>
                @endif
            </div>
        </div>

        <div class="space-y-2">
            <h3 class="text-[15px] font-heading font-bold text-ink">
                Isi Pertanyaan Anda
            </h3>
            <div class="p-4 rounded-[12px] bg-white/85 border border-border-glass text-[14px] text-ink leading-relaxed whitespace-pre-line">{{ $pertanyaan->isi }}</div>
        </div>

        <div class="space-y-2">
            <h3 class="text-[15px] font-heading font-bold text-ink">
                Jawaban Resmi Petugas UKT
            </h3>
            @if($pertanyaan->jawaban)
                <div class="p-5 rounded-[12px] bg-success-bg/85 border border-[#BBF7D0] space-y-2">
                    <p class="text-[14px] text-ink font-medium leading-relaxed whitespace-pre-line">{{ $pertanyaan->jawaban }}</p>
                    <div class="text-[12px] text-ink-secondary pt-2 border-t border-[#BBF7D0]/70">
                        Dijawab oleh <strong>{{ $pertanyaan->penjawab?->nama ?? 'Petugas UKT' }}</strong> pada {{ $pertanyaan->dijawab_pada?->locale('id')->translatedFormat('d M Y H:i') ?? '-' }}
                    </div>
                </div>
            @else
                <div class="p-5 rounded-[12px] bg-white/75 border border-border-glass text-[14px] text-ink-secondary">
                    Pertanyaan Anda telah diterima dengan Nomor Tanda Terima <strong>#{{ $pertanyaan->id }}</strong> dan sedang menunggu jawaban dari Petugas UKT.
                </div>
            @endif
        </div>
    </section>
</x-layouts.app>

