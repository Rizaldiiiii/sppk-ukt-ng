<x-layouts.app
    title="Pengajuan Keberatan UKT Saya"
    subtitle="Pantau status pengajuan peninjauan ulang UKT Anda beserta catatan dari petugas (KF-07)"
>
    <x-slot:headerActions>
        <x-button :href="route('mahasiswa.keberatan.create')" variant="primary">
            + Ajukan Keberatan Baru
        </x-button>
    </x-slot:headerActions>

    <!-- Ringkasan Alur Status Usulan (Blueprint §7.3) -->
    <section class="glass-card glass-card-glow p-6 relative overflow-hidden space-y-4">
        <div class="accent-bar absolute top-0 inset-x-0" aria-hidden="true"></div>
        <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4 pt-1">
            <div class="space-y-1.5 max-w-3xl">
                <span class="inline-block px-3 py-1 rounded-[8px] bg-sekunder-light text-sekunder font-heading font-bold text-[12px]">
                    KF-07 • Pengajuan Keberatan Daring &amp; Pemantauan Status
                </span>
                <h2 class="text-[20px] font-heading font-bold text-ink">
                    Tahapan Status Pengajuan Peninjauan Ulang UKT
                </h2>
                <p class="text-[14px] text-ink-secondary leading-relaxed">
                    Setiap pengajuan keberatan yang Anda kirim akan memperoleh nomor tanda terima dan melewati status:
                    <strong>Diajukan</strong> → <strong>Dalam Peninjauan</strong> → <strong>Perlu Perbaikan</strong> (bila ada berkas/penjelasan yang perlu dilengkapi) → keputusan akhir <strong>Disetujui</strong> atau <strong>Ditolak</strong>.
                </p>
            </div>
            <div class="flex flex-wrap gap-2.5 shrink-0">
                <x-button :href="route('publik.prosedur-keberatan')" variant="secondary">
                    Syarat &amp; Prosedur Keberatan
                </x-button>
                <x-button :href="route('mahasiswa.keberatan.create')" variant="primary">
                    Buat Pengajuan Keberatan
                </x-button>
            </div>
        </div>
    </section>

    @if($daftarKeberatan->isEmpty())
        <x-empty-state
            title="Belum Ada Pengajuan Keberatan UKT Tercatat"
            description="Anda belum pernah mengirimkan pengajuan keberatan secara daring. Sebelum mengajukan keberatan, pastikan Anda telah memeriksa rincian nilai per komponen pada menu Hasil & Rincian Saya."
        >
            <x-slot:actions>
                <x-button :href="route('mahasiswa.hasil')" variant="secondary">
                    Periksa Rincian Hasil Saya
                </x-button>
                <x-button :href="route('mahasiswa.keberatan.create')" variant="primary">
                    Ajukan Keberatan Sekarang
                </x-button>
            </x-slot:actions>
        </x-empty-state>
    @else
        <div class="space-y-4">
            @foreach($daftarKeberatan as $item)
                @php $infoTenggat = $item->infoTenggat(); @endphp
                <article class="glass-card p-6 space-y-4">
                    <div class="flex flex-col md:flex-row md:items-center justify-between gap-3 border-b border-divider/70 pb-4">
                        <div class="space-y-1">
                            <div class="flex flex-wrap items-center gap-2.5">
                                <span class="font-heading font-extrabold text-[18px] text-ink">
                                    Tanda Terima #{{ $item->id }}
                                </span>
                                <x-badge-status :status="$item->status" :label="$item->status" />
                                @if($infoTenggat['badge_label'])
                                    <x-badge-status :status="$infoTenggat['badge_status']" :label="$infoTenggat['badge_label']" />
                                @endif
                                @if($item->hasil && $item->hasil->golongan)
                                    <span class="px-2.5 py-0.5 rounded-[8px] bg-white/85 border border-border-glass text-[12px] font-heading font-semibold text-sekunder">
                                        {{ $item->hasil->golongan->nama }} ({{ $item->hasil->formatNominal() }})
                                    </span>
                                @endif
                            </div>
                            <div class="text-[12px] text-ink-secondary flex flex-wrap gap-x-4 gap-y-1">
                                <span>Diajukan pada: <strong>{{ $item->diajukan_pada?->locale('id')->translatedFormat('d M Y H:i') ?? $item->created_at?->locale('id')->translatedFormat('d M Y H:i') }}</strong></span>
                                <span>Perubahan status terakhir: <strong>{{ ($item->diputuskan_pada ?? $item->updated_at)?->locale('id')->translatedFormat('d M Y H:i') }}</strong></span>
                                <span>Perkiraan waktu: <strong class="text-ink">{{ $infoTenggat['teks_perkiraan'] }}</strong></span>
                                <span>Dokumen bukti: <strong>{{ $item->dokumen->count() }} berkas</strong></span>
                            </div>
                        </div>

                        <div class="shrink-0">
                            <x-button
                                :href="route('mahasiswa.keberatan.show', $item)"
                                :variant="$item->status === 'Perlu Perbaikan' ? 'primary' : 'secondary'"
                            >
                                {{ $item->status === 'Perlu Perbaikan' ? 'Lengkapi Perbaikan Sekarang' : 'Lihat Detail & Riwayat' }}
                            </x-button>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 text-[14px]">
                        <div class="space-y-1">
                            <div class="text-[12px] font-heading font-bold uppercase tracking-wider text-ink-muted">
                                Ringkasan Alasan Keberatan Anda
                            </div>
                            <p class="text-ink leading-relaxed whitespace-pre-line">{{ \Illuminate\Support\Str::limit($item->alasan, 260) }}</p>
                        </div>

                        <div class="p-4 rounded-[12px] {{ $item->catatan_petugas ? 'bg-highlight-light/70 border border-divider' : 'bg-white/70 border border-border-glass' }} space-y-1">
                            <div class="text-[12px] font-heading font-bold uppercase tracking-wider text-ink-muted">
                                Catatan Petugas yang Dapat Dibaca Mahasiswa
                            </div>
                            @if($item->catatan_petugas)
                                <p class="text-ink font-medium leading-relaxed whitespace-pre-line">{{ $item->catatan_petugas }}</p>
                            @else
                                <p class="text-ink-secondary text-[13px]">Belum ada catatan tambahan dari petugas peninjau.</p>
                            @endif
                        </div>
                    </div>
                </article>
            @endforeach

            <div class="pt-2">
                {{ $daftarKeberatan->links() }}
            </div>
        </div>
    @endif
</x-layouts.app>

