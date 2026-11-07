<x-layouts.app
    title="Riwayat Audit & Perubahan Sistem — Pimpinan / Auditor"
    subtitle="Penelusuran riwayat akses dan perubahan data tanpa menampilkan identitas maupun nilai ekonomi perorangan mahasiswa (KF-13 & PRD Bagian 3.3)"
>
    <!-- Peringatan Batas Kewenangan Pimpinan -->
    <section class="glass-card-raised glass-card-glow p-6 border-l-4 border-l-sekunder space-y-1.5">
        <div class="flex flex-wrap items-center justify-between gap-2">
            <h2 class="text-[16px] font-heading font-bold text-ink">
                Jejak Audit Hanya-Baca dengan Perlindungan Privasi Mahasiswa (KF-13)
            </h2>
            <span class="px-3 py-1 rounded-[8px] bg-sekunder-light text-sekunder font-heading font-bold text-[12px]">
                Append-Only • Data Pribadi Disamarkan
            </span>
        </div>
        <p class="text-[13px] text-ink-secondary leading-relaxed">
            Halaman ini menampilkan catatan waktu, peran pelaku, jenis aksi, subjek perubahan, serta nilai sebelum dan sesudah untuk keperluan pengawasan Pimpinan/Auditor. Sesuai PRD Bagian 3.3, nama mahasiswa, NIM, dokumen bukti, dan angka ekonomi perorangan disamarkan secara otomatis.
        </p>
    </section>

    <!-- Ringkasan Jumlah per Aksi -->
    @if(!empty($rekapAksiAudit))
        <section class="space-y-3">
            <h2 class="text-[18px] font-heading font-bold text-ink">
                Ringkasan Aktivitas Tercatat per Jenis Aksi
            </h2>
            <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-4">
                @foreach($rekapAksiAudit as $aksiNama => $jumlahAksi)
                    <a
                        href="{{ route('pimpinan.riwayat', ['aksi' => $aksiNama, 'subjek_tipe' => $subjekFilter, 'q' => $kataKunci]) }}"
                        class="glass-card glass-card-light p-4 space-y-1 transition-transform hover:-translate-y-0.5 {{ $aksiFilter === $aksiNama ? 'ring-2 ring-sekunder' : '' }}"
                    >
                        <div class="text-[11px] font-mono text-ink-secondary truncate" title="{{ $aksiNama }}">{{ $aksiNama }}</div>
                        <div class="text-[22px] font-heading font-extrabold text-ink tabular-nums">{{ $jumlahAksi }}</div>
                    </a>
                @endforeach
            </div>
        </section>
    @endif

    <!-- Filter & Pencarian -->
    <section class="glass-card p-6">
        <form method="GET" action="{{ route('pimpinan.riwayat') }}" class="grid grid-cols-1 md:grid-cols-12 gap-4 items-end">
            <div class="md:col-span-4">
                <label for="filter_aksi" class="block text-[13px] font-heading font-semibold text-ink mb-1">
                    Filter Jenis Aksi
                </label>
                <select id="filter_aksi" name="aksi" class="glass-input w-full px-3.5 py-2.5 text-[13px]">
                    <option value="">Semua Aksi</option>
                    @foreach($daftarAksi as $aksiOpt)
                        <option value="{{ $aksiOpt }}" @selected($aksiFilter === $aksiOpt)>
                            {{ $aksiOpt }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="md:col-span-4">
                <label for="filter_subjek" class="block text-[13px] font-heading font-semibold text-ink mb-1">
                    Filter Tipe Subjek
                </label>
                <select id="filter_subjek" name="subjek_tipe" class="glass-input w-full px-3.5 py-2.5 text-[13px]">
                    <option value="">Semua Tipe Subjek</option>
                    @foreach($daftarSubjekTipe as $subjekOpt)
                        <option value="{{ $subjekOpt }}" @selected($subjekFilter === $subjekOpt)>
                            {{ class_basename($subjekOpt) }} ({{ $subjekOpt }})
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="md:col-span-4 flex gap-2">
                <div class="flex-1">
                    <label for="filter_q" class="block text-[13px] font-heading font-semibold text-ink mb-1">
                        Cari Aksi / Subjek / ID
                    </label>
                    <input
                        id="filter_q"
                        type="search"
                        name="q"
                        value="{{ $kataKunci }}"
                        placeholder="Contoh: ubah_status, VersiParameter..."
                        class="glass-input w-full px-3.5 py-2.5 text-[13px]"
                    >
                </div>
                <div class="flex items-end gap-2">
                    <x-button type="submit" variant="primary">
                        Cari
                    </x-button>
                    @if($aksiFilter !== '' || $subjekFilter !== '' || $kataKunci !== '')
                        <x-button :href="route('pimpinan.riwayat')" variant="secondary">
                            Reset
                        </x-button>
                    @endif
                </div>
            </div>
        </form>
    </section>

    <!-- Tabel Riwayat Audit Terlindungi -->
    @if($daftarLog->isEmpty())
        <x-empty-state
            title="Tidak Ada Catatan Riwayat yang Cocok"
            description="Belum ada entri riwayat yang sesuai dengan filter pencarian saat ini."
        />
    @else
        <x-table>
            <thead>
                <tr class="border-b border-divider bg-surface-soft/90">
                    <th scope="col" class="px-4 py-3.5 font-heading font-semibold text-ink">Waktu</th>
                    <th scope="col" class="px-4 py-3.5 font-heading font-semibold text-ink">Pelaku</th>
                    <th scope="col" class="px-4 py-3.5 font-heading font-semibold text-ink">Aksi</th>
                    <th scope="col" class="px-4 py-3.5 font-heading font-semibold text-ink">Subjek &amp; ID</th>
                    <th scope="col" class="px-4 py-3.5 font-heading font-semibold text-ink">Nilai Sebelum (Tersanitasi)</th>
                    <th scope="col" class="px-4 py-3.5 font-heading font-semibold text-ink">Nilai Sesudah (Tersanitasi)</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-divider align-top">
                @foreach($daftarLog as $log)
                    <tr class="hover:bg-surface-soft/80">
                        <td class="px-4 py-3.5 text-[12px] text-ink-secondary whitespace-nowrap tabular-nums">
                            {{ $log->dibuat_pada?->locale('id')->translatedFormat('d M Y H:i:s') ?? '-' }}
                        </td>
                        <td class="px-4 py-3.5 text-[13px] font-heading font-semibold text-ink">
                            {{ $log->label_pelaku_aman }}
                        </td>
                        <td class="px-4 py-3.5">
                            <span class="inline-block px-2.5 py-1 rounded-[6px] bg-sekunder-light/90 text-sekunder font-mono text-[12px] font-semibold">
                                {{ $log->aksi }}
                            </span>
                        </td>
                        <td class="px-4 py-3.5 text-[12px]">
                            <div class="font-heading font-semibold text-ink">{{ class_basename($log->subjek_tipe) }}</div>
                            <div class="text-ink-muted font-mono">ID: {{ $log->subjek_id ?? '-' }}</div>
                        </td>
                        <td class="px-4 py-3.5 text-[12px] max-w-xs">
                            @if(!empty($log->nilai_sebelum_aman))
                                <pre class="p-2.5 rounded-[8px] bg-white/80 border border-border-glass text-[11px] font-mono text-ink-secondary whitespace-pre-wrap break-words">{{ json_encode($log->nilai_sebelum_aman, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT) }}</pre>
                            @else
                                <span class="text-ink-muted">-</span>
                            @endif
                        </td>
                        <td class="px-4 py-3.5 text-[12px] max-w-xs">
                            @if(!empty($log->nilai_sesudah_aman))
                                <pre class="p-2.5 rounded-[8px] bg-white/80 border border-border-glass text-[11px] font-mono text-ink whitespace-pre-wrap break-words">{{ json_encode($log->nilai_sesudah_aman, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT) }}</pre>
                            @else
                                <span class="text-ink-muted">-</span>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </x-table>

        <div class="pt-2">
            {{ $daftarLog->links() }}
        </div>
    @endif
</x-layouts.app>

