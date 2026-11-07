<x-layouts.app
    :title="'Kelola Pertanyaan Mahasiswa #' . $pertanyaan->id"
    subtitle="Berikan jawaban resmi kepada mahasiswa atau kelola status pertanyaan (KF-08 & KF-15)"
>
    <x-slot:headerActions>
        <x-button :href="route('petugas.pertanyaan.index')" variant="secondary">
            ← Daftar Pertanyaan
        </x-button>
    </x-slot:headerActions>

    <section class="glass-card glass-card-glow p-6 md:p-8 relative overflow-hidden space-y-6">
        <div class="accent-bar absolute top-0 inset-x-0" aria-hidden="true"></div>

        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 border-b border-divider/80 pb-5">
            <div class="space-y-1">
                <div class="flex flex-wrap items-center gap-2.5">
                    <span class="text-[12px] font-heading font-bold uppercase tracking-wider text-sekunder">
                        Tanda Terima #{{ $pertanyaan->id }}
                    </span>
                    <x-badge-status :status="$pertanyaan->status" :label="'Status: ' . ucfirst($pertanyaan->status)" />
                </div>
                <h2 class="text-[22px] font-heading font-extrabold text-ink">
                    {{ $pertanyaan->mahasiswa?->user?->nama ?? '-' }} — NIM {{ $pertanyaan->mahasiswa?->nim ?? '-' }}
                </h2>
                <p class="text-[13px] text-ink-secondary">
                    Program Studi {{ $pertanyaan->mahasiswa?->program_studi ?? '-' }} • Email: {{ $pertanyaan->mahasiswa?->user?->email ?? '-' }}
                </p>
            </div>

            <div class="flex flex-wrap items-center gap-2.5">
                @if($pertanyaan->status !== 'ditutup')
                    <form method="POST" action="{{ route('petugas.pertanyaan.tutup', $pertanyaan) }}" data-prevent-double-submit>
                        @csrf
                        @method('PATCH')
                        <x-button type="submit" variant="secondary">
                            Tutup Pertanyaan
                        </x-button>
                    </form>
                @else
                    <form method="POST" action="{{ route('petugas.pertanyaan.buka-kembali', $pertanyaan) }}" data-prevent-double-submit>
                        @csrf
                        @method('PATCH')
                        <x-button type="submit" variant="primary">
                            Buka Kembali Pertanyaan
                        </x-button>
                    </form>
                @endif
            </div>
        </div>

        <div class="space-y-2">
            <div class="text-[13px] font-heading font-bold text-ink">
                Isi Pertanyaan Mahasiswa (Dikirim {{ $pertanyaan->created_at?->locale('id')->translatedFormat('d M Y H:i') }}):
            </div>
            <div class="p-4 rounded-[12px] bg-white/85 border border-border-glass text-[14px] text-ink leading-relaxed whitespace-pre-line">{{ $pertanyaan->isi }}</div>
        </div>

        @if($pertanyaan->status === 'ditutup')
            <div class="p-4 rounded-[12px] bg-surface-soft border border-divider space-y-2">
                <div class="font-heading font-bold text-[15px] text-sekunder">
                    Pertanyaan Ini Berstatus Ditutup
                </div>
                <p class="text-[13px] text-ink-secondary">
                    Sesuai aturan alur pertanyaan (Blueprint §7.4), pertanyaan yang sudah ditutup tidak dapat dijawab lagi kecuali dibuka kembali terlebih dahulu oleh petugas melalui tombol <strong>Buka Kembali Pertanyaan</strong> di atas.
                </p>
                @if($pertanyaan->jawaban)
                    <div class="pt-2 border-t border-divider/70 text-[13px] text-ink">
                        <strong>Jawaban Terakhir:</strong> {{ $pertanyaan->jawaban }}
                    </div>
                @endif
            </div>
        @else
            <form
                method="POST"
                action="{{ route('petugas.pertanyaan.jawab', $pertanyaan) }}"
                data-prevent-double-submit
                class="space-y-4 border-t border-divider/80 pt-5"
            >
                @csrf
                @method('PATCH')

                <div class="space-y-1.5">
                    <label for="jawaban" class="block font-heading font-bold text-[15px] text-ink">
                        Jawaban Resmi Petugas UKT <span class="text-error">*</span>
                    </label>
                    <textarea
                        id="jawaban"
                        name="jawaban"
                        rows="5"
                        required
                        placeholder="Tuliskan penjelasan resmi yang menjawab pertanyaan mahasiswa secara jelas..."
                        class="glass-input w-full px-4 py-3 text-[14px] leading-relaxed"
                    >{{ old('jawaban', $pertanyaan->jawaban) }}</textarea>
                    @error('jawaban')
                        <p class="text-[13px] font-medium text-error mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                    <span class="text-[12px] text-ink-secondary">
                        Saat disimpan, status menjadi <strong>dijawab</strong> dan pemberitahuan dikirim ke akun mahasiswa.
                    </span>
                    <x-button type="submit" variant="primary">
                        Simpan &amp; Kirim Jawaban
                    </x-button>
                </div>
            </form>
        @endif
    </section>

    @if($riwayatAksi->isNotEmpty())
        <section class="glass-card p-6 space-y-3">
            <h3 class="text-[17px] font-heading font-bold text-ink">
                Riwayat Penanganan Pertanyaan #{{ $pertanyaan->id }}
            </h3>
            <div class="space-y-2.5">
                @foreach($riwayatAksi as $log)
                    <div class="p-3.5 rounded-[12px] bg-white/80 border border-border-glass flex flex-wrap items-center justify-between gap-2 text-[13px]">
                        <div>
                            <strong class="font-heading text-ink">{{ $log->aksi }}</strong>
                            — oleh {{ $log->pelaku?->nama ?? 'Sistem' }} ({{ $log->pelaku?->labelPeran() ?? '-' }})
                        </div>
                        <div class="text-[12px] text-ink-muted">
                            {{ $log->dibuat_pada?->locale('id')->translatedFormat('d M Y H:i') }}
                        </div>
                    </div>
                @endforeach
            </div>
        </section>
    @endif
</x-layouts.app>

