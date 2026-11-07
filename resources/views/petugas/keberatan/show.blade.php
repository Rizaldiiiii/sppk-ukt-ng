@php
    $infoTenggat = $pengajuan->infoTenggat();
@endphp
<x-layouts.app
    :title="'Peninjauan Keberatan UKT #' . $pengajuan->id"
    subtitle="Periksa alasan keberatan, dokumen bukti privat, dan perbarui status peninjauan (KF-07, KF-11 & KF-15)"
>
    <x-slot:headerActions>
        <x-button :href="route('petugas.keberatan.index')" variant="secondary">
            ← Daftar Keberatan
        </x-button>
    </x-slot:headerActions>

    <!-- Identitas Mahasiswa & Status Saat Ini -->
    <section class="glass-card glass-card-glow p-6 md:p-8 relative overflow-hidden space-y-6">
        <div class="accent-bar absolute top-0 inset-x-0" aria-hidden="true"></div>

        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 border-b border-divider/80 pb-5">
            <div class="space-y-1">
                <div class="flex flex-wrap items-center gap-2.5">
                    <span class="text-[12px] font-heading font-bold uppercase tracking-wider text-sekunder">
                        Pengajuan #{{ $pengajuan->id }}
                    </span>
                    <x-badge-status :status="$pengajuan->status" :label="$pengajuan->status" />
                    @if($infoTenggat['badge_label'])
                        <x-badge-status :status="$infoTenggat['badge_status']" :label="$infoTenggat['badge_label']" />
                    @endif
                    @if($apakahTerminal)
                        <span class="px-2.5 py-0.5 rounded-[8px] bg-surface-soft border border-divider text-[11px] font-heading font-bold text-ink-secondary">
                            Status Keputusan Final (Terminal)
                        </span>
                    @endif
                </div>
                <h2 class="text-[22px] font-heading font-extrabold text-ink">
                    {{ $pengajuan->mahasiswa?->user?->nama ?? '-' }} — NIM {{ $pengajuan->mahasiswa?->nim ?? '-' }}
                </h2>
                <p class="text-[13px] text-ink-secondary">
                    Program Studi {{ $pengajuan->mahasiswa?->program_studi ?? '-' }} • Angkatan {{ $pengajuan->mahasiswa?->angkatan ?? '-' }} • Email: {{ $pengajuan->mahasiswa?->user?->email ?? '-' }}
                </p>
            </div>

            <div class="text-[13px] text-ink-secondary md:text-right space-y-1">
                <div>Diajukan pada: <strong class="text-ink">{{ $pengajuan->diajukan_pada?->locale('id')->translatedFormat('d M Y H:i') ?? '-' }}</strong></div>
                <div>Perkiraan &amp; Tenggat: <strong class="text-ink">{{ $infoTenggat['teks_perkiraan'] }}</strong></div>
                <div>Perubahan terakhir: <strong class="text-ink">{{ ($pengajuan->diputuskan_pada ?? $pengajuan->updated_at)?->locale('id')->translatedFormat('d M Y H:i') }}</strong></div>
                @if($pengajuan->pemutus)
                    <div>Diputuskan oleh: <strong class="text-ink">{{ $pengajuan->pemutus->nama }}</strong></div>
                @endif
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
            <!-- Uraian Alasan Keberatan Mahasiswa -->
            <div class="lg:col-span-7 space-y-3">
                <h3 class="text-[16px] font-heading font-bold text-ink">
                    Uraian Alasan Keberatan &amp; Klarifikasi Mahasiswa
                </h3>
                <div class="p-4 rounded-[12px] bg-white/85 border border-border-glass text-[14px] text-ink leading-relaxed whitespace-pre-line">{{ $pengajuan->alasan }}</div>

                @if($pengajuan->hasil)
                    <div class="p-4 rounded-[12px] bg-white/75 border border-border-glass space-y-2 text-[13px]">
                        <div class="flex flex-wrap items-center justify-between gap-2">
                            <span class="font-heading font-bold text-ink">
                                Hasil UKT yang Diajukan: {{ $pengajuan->hasil->golongan?->nama ?? '-' }} ({{ $pengajuan->hasil->formatNominal() }})
                            </span>
                            <x-badge-status :status="$pengajuan->hasil->sumber" :label="$pengajuan->hasil->isResmi() ? 'Resmi' : 'Simulasi'" />
                        </div>
                        @if($pengajuan->hasil->rincianPenilaian->isNotEmpty())
                            <div class="pt-2 border-t border-divider/70 space-y-1">
                                <div class="text-[12px] font-heading font-semibold text-ink-secondary">Ringkasan Komponen Hasil Saat Itu:</div>
                                <ul class="grid grid-cols-1 sm:grid-cols-2 gap-1.5 text-[12px] text-ink">
                                    @foreach($pengajuan->hasil->rincianPenilaian as $rincian)
                                        <li>• <strong>{{ $rincian->faktor?->nama ?? '-' }}:</strong> {{ $rincian->data_masukan }} (Skor {{ $rincian->skor_baku }})</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif
                    </div>
                @endif
            </div>

            <!-- Dokumen Bukti Privat -->
            <div class="lg:col-span-5 space-y-3">
                <div class="flex items-center justify-between">
                    <h3 class="text-[16px] font-heading font-bold text-ink">
                        Dokumen Bukti Pendukung (Privat)
                    </h3>
                    <span class="text-[12px] font-heading font-bold text-sekunder">
                        {{ $pengajuan->dokumen->count() }} Berkas
                    </span>
                </div>

                @if($pengajuan->dokumen->isEmpty())
                    <div class="p-4 rounded-[12px] bg-white/75 border border-border-glass text-[13px] text-ink-secondary">
                        Mahasiswa belum melampirkan berkas dokumen pendukung pada pengajuan ini.
                    </div>
                @else
                    <ul class="space-y-2.5">
                        @foreach($pengajuan->dokumen as $dok)
                            <li class="p-3.5 rounded-[12px] bg-white/85 border border-border-glass flex items-center justify-between gap-3">
                                <div class="min-w-0">
                                    <div class="font-heading font-semibold text-[13px] text-ink truncate">
                                        {{ $dok->nama_asli }}
                                    </div>
                                    <div class="text-[11px] text-ink-muted">
                                        {{ strtoupper($dok->mime) }} • {{ number_format(max(1, (int) round($dok->ukuran / 1024)), 0, ',', '.') }} KB
                                    </div>
                                </div>
                                <x-button :href="route('dokumen.unduh', $dok)" variant="secondary" class="shrink-0 text-[12px] px-3 py-1.5 min-h-[38px]">
                                    Unduh Berkas
                                </x-button>
                            </li>
                        @endforeach
                    </ul>
                @endif
            </div>
        </div>
    </section>

    <!-- Formulir Ubah Status Peninjauan (ObjectionService::ubahStatus) -->
    <section class="glass-card p-6 md:p-8 space-y-5">
        <div class="space-y-1">
            <h3 class="text-[19px] font-heading font-bold text-ink">
                Keputusan &amp; Perubahan Status Peninjauan (KF-07 &amp; KF-15)
            </h3>
            <p class="text-[13px] text-ink-secondary leading-relaxed">
                Perubahan status dan catatan di bawah ini akan otomatis dicatat di <code>audit_logs</code> dan memicu pemberitahuan sistem serta antrean email kepada mahasiswa. Status <strong>Disetujui</strong> dan <strong>Ditolak</strong> bersifat final (terminal).
            </p>
        </div>

        @if($apakahTerminal)
            <div class="p-4 rounded-[12px] bg-surface-soft border border-divider text-[14px] text-ink space-y-1">
                <div class="font-heading font-bold text-sekunder">
                    Pengajuan Ini Telah Diputuskan Secara Final ({{ $pengajuan->status }})
                </div>
                <p class="text-[13px] text-ink-secondary">
                    Sesuai aturan alur status usulan (Blueprint §7.3), pengajuan keberatan yang sudah berada pada status <strong>{{ $pengajuan->status }}</strong> bersifat terminal dan tidak dapat diubah lagi melalui aksi biasa.
                </p>
                @if($pengajuan->catatan_petugas)
                    <p class="text-[13px] text-ink pt-1">
                        <strong>Catatan Keputusan:</strong> {{ $pengajuan->catatan_petugas }}
                    </p>
                @endif
            </div>
        @else
            <form
                method="POST"
                action="{{ route('petugas.keberatan.ubah-status', $pengajuan) }}"
                data-prevent-double-submit
                class="space-y-5"
            >
                @csrf
                @method('PATCH')

                <div class="grid grid-cols-1 md:grid-cols-12 gap-5">
                    <div class="md:col-span-4 space-y-1.5">
                        <label for="status_keberatan" class="block text-[14px] font-heading font-bold text-ink">
                            Status Peninjauan Baru <span class="text-error">*</span>
                        </label>
                        <select id="status_keberatan" name="status" required class="glass-input w-full px-4 py-3 text-[14px]">
                            @foreach($daftarStatus as $statusOption)
                                <option value="{{ $statusOption }}" @selected(old('status', $pengajuan->status) === $statusOption)>
                                    {{ $statusOption }} {{ in_array($statusOption, ['Disetujui', 'Ditolak'], true) ? '(Final / Terminal)' : '' }}
                                </option>
                            @endforeach
                        </select>
                        @error('status')
                            <p class="text-[13px] font-medium text-error mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="md:col-span-8 space-y-1.5">
                        <label for="catatan_petugas" class="block text-[14px] font-heading font-bold text-ink">
                            Catatan Petugas (Dapat Dibaca Mahasiswa pada Halaman Statusnya)
                        </label>
                        <textarea
                            id="catatan_petugas"
                            name="catatan_petugas"
                            rows="3"
                            placeholder="Tuliskan alasan keputusan atau rincian berkas yang perlu diperbaiki mahasiswa..."
                            class="glass-input w-full px-4 py-3 text-[14px]"
                        >{{ old('catatan_petugas', $pengajuan->catatan_petugas) }}</textarea>
                        @error('catatan_petugas')
                            <p class="text-[13px] font-medium text-error mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="flex justify-end">
                    <x-button type="submit" variant="primary">
                        Simpan Perubahan Status &amp; Kirim Pemberitahuan
                    </x-button>
                </div>
            </form>
        @endif
    </section>

    <!-- Riwayat Audit Pengajuan Ini -->
    <section class="glass-card p-6 space-y-4">
        <h3 class="text-[18px] font-heading font-bold text-ink">
            Riwayat Peninjauan &amp; Audit Pengajuan #{{ $pengajuan->id }}
        </h3>

        @if($riwayatStatus->isEmpty())
            <p class="text-[13px] text-ink-secondary">Belum ada catatan perubahan status pada buku audit untuk pengajuan ini.</p>
        @else
            <div class="space-y-3">
                @foreach($riwayatStatus as $log)
                    <div class="p-4 rounded-[12px] bg-white/80 border border-border-glass flex flex-col sm:flex-row sm:items-center justify-between gap-2 text-[13px]">
                        <div class="space-y-1">
                            <div class="font-heading font-bold text-ink">
                                {{ $log->aksi }} — oleh {{ $log->pelaku?->nama ?? 'Sistem' }} ({{ $log->pelaku?->labelPeran() ?? '-' }})
                            </div>
                            @if(!empty($log->nilai_sebelum['status']) || !empty($log->nilai_sesudah['status']))
                                <div class="text-ink-secondary">
                                    Status: <strong>{{ $log->nilai_sebelum['status'] ?? '-' }}</strong> → <strong>{{ $log->nilai_sesudah['status'] ?? '-' }}</strong>
                                </div>
                            @endif
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

