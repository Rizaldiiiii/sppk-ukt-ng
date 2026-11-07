<x-layouts.app
    :title="'Detail Pengajuan Keberatan #' . $pengajuan->id"
    subtitle="Pemantauan status pengajuan, catatan petugas, dokumen bukti, dan riwayat peninjauan (KF-07)"
>
    <x-slot:headerActions>
        <x-button :href="route('mahasiswa.keberatan.index')" variant="secondary">
            ← Daftar Keberatan Saya
        </x-button>
    </x-slot:headerActions>

    <!-- Ringkasan Status & Tanggal Perubahan Terakhir -->
    <section class="glass-card glass-card-glow p-6 md:p-8 relative overflow-hidden space-y-6">
        <div class="accent-bar absolute top-0 inset-x-0" aria-hidden="true"></div>

        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 border-b border-divider/80 pb-5">
            <div class="space-y-1.5">
                <div class="flex flex-wrap items-center gap-3">
                    <span class="text-[12px] font-heading font-bold uppercase tracking-wider text-sekunder">
                        Nomor Tanda Terima #{{ $pengajuan->id }}
                    </span>
                    <x-badge-status :status="$pengajuan->status" :label="'Status: ' . $pengajuan->status" />
                </div>
                <h2 class="text-[22px] font-heading font-extrabold text-ink">
                    Pengajuan Peninjauan Ulang UKT — {{ $pengajuan->mahasiswa?->user?->nama }}
                </h2>
                <p class="text-[13px] text-ink-secondary">
                    NIM {{ $pengajuan->mahasiswa?->nim }} • {{ $pengajuan->mahasiswa?->program_studi }}
                </p>
            </div>

            @if($pengajuan->hasil)
                <div class="p-4 rounded-[12px] bg-white/85 border border-border-glass text-right shrink-0">
                    <div class="text-[11px] font-heading font-bold uppercase tracking-wider text-ink-muted">
                        Hasil UKT yang Diajukan
                    </div>
                    <div class="text-[16px] font-heading font-bold text-sekunder mt-0.5">
                        {{ $pengajuan->hasil->golongan?->nama ?? 'Golongan' }} — {{ $pengajuan->hasil->formatNominal() }}
                    </div>
                    <div class="text-[12px] text-ink-secondary">
                        {{ $pengajuan->hasil->isResmi() ? 'Penetapan Resmi' : 'Simulasi' }}
                    </div>
                </div>
            @endif
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 text-[13px]">
            <div class="p-4 rounded-[12px] bg-white/75 border border-border-glass">
                <div class="text-ink-muted font-medium">Tanggal Pengajuan</div>
                <div class="font-heading font-bold text-ink text-[15px] mt-1">
                    {{ $pengajuan->diajukan_pada?->locale('id')->translatedFormat('d M Y H:i') ?? $pengajuan->created_at?->locale('id')->translatedFormat('d M Y H:i') }}
                </div>
            </div>
            <div class="p-4 rounded-[12px] bg-white/75 border border-border-glass">
                <div class="text-ink-muted font-medium">Perubahan Status Terakhir</div>
                <div class="font-heading font-bold text-ink text-[15px] mt-1">
                    {{ ($pengajuan->diputuskan_pada ?? $pengajuan->updated_at)?->locale('id')->translatedFormat('d M Y H:i') }}
                </div>
            </div>
            <div class="p-4 rounded-[12px] bg-white/75 border border-border-glass">
                <div class="text-ink-muted font-medium">Status Keputusan Akhir</div>
                <div class="font-heading font-bold text-ink text-[15px] mt-1">
                    @if($pengajuan->diputuskan_pada)
                        Diputuskan pada {{ $pengajuan->diputuskan_pada->locale('id')->translatedFormat('d M Y H:i') }}
                    @else
                        Sedang dalam alur peninjauan
                    @endif
                </div>
            </div>
        </div>

        <!-- Catatan Petugas untuk Mahasiswa -->
        <div class="p-5 rounded-[12px] {{ $pengajuan->catatan_petugas ? 'bg-highlight-light/85 border border-divider' : 'bg-white/75 border border-border-glass' }} space-y-1.5">
            <div class="text-[13px] font-heading font-bold text-ink">
                Catatan Petugas Peninjau yang Dapat Dibaca Mahasiswa:
            </div>
            @if($pengajuan->catatan_petugas)
                <p class="text-[14px] text-ink font-medium leading-relaxed whitespace-pre-line">{{ $pengajuan->catatan_petugas }}</p>
            @else
                <p class="text-[13px] text-ink-secondary">
                    Belum ada catatan dari petugas peninjau untuk pengajuan ini.
                </p>
            @endif
        </div>
    </section>

    <!-- Formulir Kelengkapan Perbaikan bila Status = Perlu Perbaikan (Blueprint §7.3 & KF-09) -->
    @if($pengajuan->status === 'Perlu Perbaikan')
        <section class="glass-card p-6 md:p-8 border-l-4 border-l-warning space-y-6">
            <div class="space-y-1.5">
                <span class="inline-block px-3 py-1 rounded-[8px] bg-warning-bg text-warning font-heading font-bold text-[12px]">
                    Tindak Lanjut Diperlukan • Status: Perlu Perbaikan
                </span>
                <h2 class="text-[20px] font-heading font-bold text-ink">
                    Lengkapi Penjelasan atau Unggah Dokumen Bukti Tambahan
                </h2>
                <p class="text-[14px] text-ink-secondary leading-relaxed">
                    Petugas meminta perbaikan atau kelengkapan berkas pada pengajuan Anda. Setelah Anda mengirim formulir di bawah ini, status pengajuan akan otomatis kembali menjadi <strong>Dalam Peninjauan</strong>.
                </p>
            </div>

            @if($errors->any())
                <div role="alert" class="p-4 rounded-[12px] bg-error-bg border border-[#FECACA] text-[13px] text-error space-y-1">
                    <div class="font-heading font-bold">Periksa kembali isian perbaikan Anda:</div>
                    <ul class="list-disc pl-5">
                        @foreach($errors->all() as $err)
                            <li>{{ $err }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form
                method="POST"
                action="{{ route('mahasiswa.keberatan.perbaikan', $pengajuan) }}"
                enctype="multipart/form-data"
                data-prevent-double-submit
                class="space-y-6"
            >
                @csrf

                <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">
                    <div class="lg:col-span-7 space-y-2">
                        <label for="catatan_perbaikan" class="block font-heading font-bold text-[14px] text-ink">
                            Keterangan Kelengkapan Perbaikan <span class="text-error">*</span>
                        </label>
                        <textarea
                            id="catatan_perbaikan"
                            name="catatan_perbaikan"
                            rows="4"
                            required
                            placeholder="Jelaskan dokumen tambahan yang Anda lampirkan atau klarifikasi atas catatan petugas..."
                            class="glass-input w-full px-4 py-3 text-[14px]"
                        >{{ old('catatan_perbaikan') }}</textarea>
                        @error('catatan_perbaikan')
                            <p class="text-[13px] font-medium text-error mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="lg:col-span-5">
                        <x-field-hint id="hint-keberatan-perbaikan" :hint="$petunjuk['keberatan_perbaikan'] ?? []" />
                    </div>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start border-t border-divider/70 pt-5">
                    <div class="lg:col-span-7 space-y-2">
                        <label for="dokumen_perbaikan" class="block font-heading font-bold text-[14px] text-ink">
                            Unggah Dokumen Bukti Tambahan (Opsional)
                        </label>
                        <input
                            id="dokumen_perbaikan"
                            name="dokumen[]"
                            type="file"
                            multiple
                            accept=".pdf,.jpg,.jpeg,.png,application/pdf,image/jpeg,image/png"
                            class="glass-input w-full px-4 py-3 text-[14px]"
                        >
                        <p class="text-[12px] text-ink-secondary">
                            Maksimal {{ $maxFiles }} berkas tambahan sekali unggah (PDF/JPG/PNG, maks. 2 MB per berkas).
                        </p>
                    </div>

                    <div class="lg:col-span-5">
                        <x-field-hint :hint="$petunjuk['keberatan_dokumen'] ?? []" />
                    </div>
                </div>

                <div class="flex justify-end">
                    <x-button type="submit" variant="primary">
                        Kirim Kelengkapan Perbaikan
                    </x-button>
                </div>
            </form>
        </section>
    @endif

    <!-- Rincian Alasan & Dokumen Bukti Pendukung -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
        <section class="lg:col-span-7 glass-card p-6 space-y-3">
            <h3 class="text-[18px] font-heading font-bold text-ink">
                Uraian Alasan Keberatan &amp; Riwayat Klarifikasi
            </h3>
            <div class="p-4 rounded-[12px] bg-white/80 border border-border-glass text-[14px] text-ink leading-relaxed whitespace-pre-line">{{ $pengajuan->alasan }}</div>
        </section>

        <section class="lg:col-span-5 glass-card p-6 space-y-4">
            <div class="flex items-center justify-between">
                <h3 class="text-[18px] font-heading font-bold text-ink">
                    Dokumen Bukti Terlampir
                </h3>
                <span class="text-[12px] font-heading font-bold text-sekunder">
                    {{ $pengajuan->dokumen->count() }} Berkas
                </span>
            </div>

            @if($pengajuan->dokumen->isEmpty())
                <p class="text-[13px] text-ink-secondary">
                    Tidak ada berkas bukti pendukung yang diunggah pada pengajuan ini.
                </p>
            @else
                <ul class="space-y-3">
                    @foreach($pengajuan->dokumen as $dok)
                        <li class="p-3.5 rounded-[12px] bg-white/80 border border-border-glass flex items-center justify-between gap-3">
                            <div class="min-w-0">
                                <div class="font-heading font-semibold text-[13px] text-ink truncate">
                                    {{ $dok->nama_asli }}
                                </div>
                                <div class="text-[11px] text-ink-muted">
                                    {{ strtoupper($dok->mime) }} • {{ number_format(max(1, (int) round($dok->ukuran / 1024)), 0, ',', '.') }} KB
                                </div>
                            </div>
                            <x-button :href="route('dokumen.unduh', $dok)" variant="secondary" class="shrink-0 text-[12px] px-3 py-1.5 min-h-[38px]">
                                Unduh Privat
                            </x-button>
                        </li>
                    @endforeach
                </ul>
            @endif
        </section>
    </div>

    <!-- Riwayat Perubahan Status Pengajuan Ini -->
    <section class="glass-card p-6 space-y-4">
        <h3 class="text-[18px] font-heading font-bold text-ink">
            Riwayat Perubahan Status Pengajuan #{{ $pengajuan->id }}
        </h3>

        @if($riwayatStatus->isEmpty())
            <p class="text-[13px] text-ink-secondary">
                Pengajuan tercatat dengan status awal <strong>{{ $pengajuan->status }}</strong> pada {{ $pengajuan->diajukan_pada?->locale('id')->translatedFormat('d M Y H:i') }}.
            </p>
        @else
            <div class="space-y-3">
                @foreach($riwayatStatus as $log)
                    <div class="p-4 rounded-[12px] bg-white/80 border border-border-glass flex flex-col sm:flex-row sm:items-center justify-between gap-2 text-[13px]">
                        <div class="space-y-1">
                            <div class="font-heading font-bold text-ink">
                                @if($log->aksi === 'ajukan_keberatan')
                                    Pengajuan Keberatan Dikirim (Status Awal: {{ $log->nilai_sesudah['status'] ?? 'Diajukan' }})
                                @elseif($log->aksi === 'lengkapi_perbaikan_keberatan')
                                    Kelengkapan Perbaikan Dikirim Mahasiswa ({{ $log->nilai_sebelum['status'] ?? '-' }} → {{ $log->nilai_sesudah['status'] ?? 'Dalam Peninjauan' }})
                                @elseif($log->aksi === 'ubah_status_keberatan')
                                    Perubahan Status oleh Petugas ({{ $log->nilai_sebelum['status'] ?? '-' }} → {{ $log->nilai_sesudah['status'] ?? '-' }})
                                @else
                                    {{ $log->aksi }}
                                @endif
                            </div>
                            @if(!empty($log->nilai_sesudah['catatan_petugas']))
                                <div class="text-ink-secondary">
                                    Catatan Petugas: "{{ $log->nilai_sesudah['catatan_petugas'] }}"
                                </div>
                            @endif
                        </div>
                        <div class="text-[12px] text-ink-muted shrink-0">
                            {{ $log->dibuat_pada?->locale('id')->translatedFormat('d M Y H:i') }}
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </section>
</x-layouts.app>

