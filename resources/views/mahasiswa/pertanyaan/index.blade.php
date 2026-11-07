<x-layouts.app
    title="Pertanyaan kepada Petugas UKT"
    subtitle="Kirim pertanyaan dari dalam sistem dan pantau jawaban resmi dari Petugas UKT (KF-08 & KF-09)"
>
    @if($errors->any())
        <section role="alert" class="glass-card p-6 border-l-4 border-l-error bg-error-bg/90 space-y-2">
            <h2 class="text-[16px] font-heading font-bold text-error">
                Pertanyaan Belum Dapat Dikirim — Periksa Kolom yang Ditandai
            </h2>
            <p class="text-[13px] text-ink">
                Teks pertanyaan yang sudah Anda ketik tetap tersimpan di bawah ini:
            </p>
            <ul class="list-disc pl-5 text-[13px] text-error font-medium space-y-1">
                @foreach($errors->all() as $err)
                    <li>{{ $err }}</li>
                @endforeach
            </ul>
        </section>
    @endif

    <!-- Formulir Kirim Pertanyaan Baru (KF-08 & KF-09) -->
    <form
        method="POST"
        action="{{ route('mahasiswa.pertanyaan.store') }}"
        data-prevent-double-submit
        class="glass-card glass-card-glow p-6 md:p-8 relative overflow-hidden space-y-6"
    >
        <div class="accent-bar absolute top-0 inset-x-0" aria-hidden="true"></div>
        @csrf
        <input type="hidden" name="form_token" value="{{ $formToken }}">

        <div class="space-y-1.5 border-b border-divider/80 pb-4">
            <span class="inline-block px-3 py-1 rounded-[8px] bg-sekunder-light text-sekunder font-heading font-bold text-[12px]">
                KF-08 &amp; KF-09 • Jalur Bertanya Terlacak
            </span>
            <h2 class="text-[20px] font-heading font-bold text-ink">
                Kirim Pertanyaan Baru kepada Petugas UKT
            </h2>
            <p class="text-[14px] text-ink-secondary leading-relaxed">
                Setiap pertanyaan yang dikirim akan mendapatkan <strong>Nomor Tanda Terima</strong> dan statusnya dapat dilacak dari <strong>baru</strong> → <strong>dijawab</strong> → <strong>ditutup</strong>.
            </p>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">
            <div class="lg:col-span-7 space-y-2">
                <label for="isi" class="block font-heading font-bold text-[14px] text-ink">
                    Isi Pertanyaan Anda <span class="text-error">*</span>
                </label>
                <textarea
                    id="isi"
                    name="isi"
                    rows="5"
                    required
                    aria-describedby="hint-pertanyaan-isi @error('isi') error-isi @enderror"
                    placeholder="Tuliskan pertanyaan Anda mengenai rincian komponen penilaian, dokumen pendukung, atau prosedur peninjauan UKT..."
                    class="glass-input w-full px-4 py-3 text-[14px] leading-relaxed"
                >{{ old('isi') }}</textarea>

                @error('isi')
                    <p id="error-isi" class="text-[13px] font-medium text-error mt-1">
                        {{ $message }}
                    </p>
                @enderror
            </div>

            <div class="lg:col-span-5">
                <x-field-hint id="hint-pertanyaan-isi" :hint="$petunjuk['pertanyaan_isi'] ?? []" />
            </div>
        </div>

        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 border-t border-divider/70 pt-4">
            <div class="text-[12px] text-ink-secondary">
                @if($kontenKontak)
                    <span>Layanan kampus: {{ \Illuminate\Support\Str::limit($kontenKontak->isi, 110) }}</span>
                @endif
            </div>
            <x-button type="submit" variant="primary">
                Kirim Pertanyaan
            </x-button>
        </div>
    </form>

    <!-- Daftar Pertanyaan Saya -->
    <section class="space-y-4">
        <h2 class="text-[21px] font-heading font-bold text-ink">
            Riwayat Pertanyaan Saya
        </h2>

        @if($daftarPertanyaan->isEmpty())
            <x-empty-state
                title="Belum Ada Pertanyaan yang Dikirim"
                description="Anda belum pernah mengirim pertanyaan kepada petugas UKT. Gunakan formulir di atas bila Anda membutuhkan klarifikasi."
            />
        @else
            <div class="space-y-4">
                @foreach($daftarPertanyaan as $item)
                    <article class="glass-card p-6 space-y-4">
                        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 border-b border-divider/70 pb-3">
                            <div class="flex flex-wrap items-center gap-2.5">
                                <span class="font-heading font-extrabold text-[17px] text-ink">
                                    Nomor Tanda Terima #{{ $item->id }}
                                </span>
                                <x-badge-status :status="$item->status" :label="ucfirst($item->status)" />
                                <span class="text-[12px] text-ink-secondary">
                                    Dikirim pada {{ $item->created_at?->locale('id')->translatedFormat('d M Y H:i') }}
                                </span>
                            </div>

                            <x-button :href="route('mahasiswa.pertanyaan.show', $item)" variant="secondary" class="text-[13px] px-4 py-2 min-h-[38px]">
                                Buka Detail &amp; Jawaban
                            </x-button>
                        </div>

                        <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 text-[14px]">
                            <div class="space-y-1">
                                <div class="text-[12px] font-heading font-bold uppercase tracking-wider text-ink-muted">
                                    Pertanyaan Anda
                                </div>
                                <p class="text-ink leading-relaxed whitespace-pre-line">{{ \Illuminate\Support\Str::limit($item->isi, 240) }}</p>
                            </div>

                            <div class="p-4 rounded-[12px] {{ $item->jawaban ? 'bg-success-bg/75 border border-[#BBF7D0]' : 'bg-white/75 border border-border-glass' }} space-y-1">
                                <div class="text-[12px] font-heading font-bold uppercase tracking-wider text-ink-muted">
                                    Jawaban Petugas UKT
                                </div>
                                @if($item->jawaban)
                                    <p class="text-ink font-medium leading-relaxed whitespace-pre-line">{{ \Illuminate\Support\Str::limit($item->jawaban, 240) }}</p>
                                    <div class="text-[11px] text-ink-secondary pt-1">
                                        Dijawab oleh {{ $item->penjawab?->nama ?? 'Petugas UKT' }} pada {{ $item->dijawab_pada?->locale('id')->translatedFormat('d M Y H:i') ?? '-' }}
                                    </div>
                                @else
                                    <p class="text-ink-secondary text-[13px]">
                                        Menunggu jawaban dari Petugas UKT. Anda akan menerima pemberitahuan saat jawaban tersedia.
                                    </p>
                                @endif
                            </div>
                        </div>
                    </article>
                @endforeach

                <div class="pt-2">
                    {{ $daftarPertanyaan->links() }}
                </div>
            </div>
        @endif
    </section>
</x-layouts.app>

