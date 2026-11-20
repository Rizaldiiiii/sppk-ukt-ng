<x-layouts.app
    title="Riwayat Akses dan Perubahan Data (Audit Log)"
    subtitle="Penelusuran hanya-baca atas seluruh aktivitas perubahan data, status, parameter, dan penolakan akses pada sistem (KF-13)"
>
    <!-- Pemberitahuan Append-Only (Blueprint §7.7) -->
    <section class="glass-card glass-card-glow p-6 relative overflow-hidden space-y-2">
        <div class="accent-bar absolute top-0 inset-x-0" aria-hidden="true"></div>
        <div class="flex flex-wrap items-center justify-between gap-2 pt-1">
            <h2 class="text-[18px] font-heading font-bold text-ink">
                Catatan Audit Hanya-Tambah (Append-Only) — KF-13
            </h2>
            <span class="px-3 py-1 rounded-[8px] bg-sekunder-light text-sekunder font-heading font-bold text-[12px]">
                Tidak Dapat Diubah / Dihapus dari Antarmuka
            </span>
        </div>
        <p class="text-[13px] text-ink-secondary leading-relaxed">
            Setiap entri di bawah ini mencatat pelaku, waktu kejadian, jenis aksi, tipe dan ID subjek data, serta nilai sebelum dan sesudah perubahan (tanpa menyimpan kata sandi atau token sesi).
        </p>
    </section>

    <!-- Ringkasan Jumlah per Aksi -->
    @if(!empty($ringkasanAksi))
        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-3">
            @foreach(array_slice($ringkasanAksi, 0, 6, true) as $aksiNama => $jumlahAksi)
                @php
                    $labelRingkasan = \App\Models\AuditLog::labelUntukAksi($aksiNama);
                @endphp
                <a
                    href="{{ route('petugas.riwayat.index', ['aksi' => $aksiNama, 'kategori' => $kategoriFilter ?? '', 'subjek_tipe' => $subjekFilter, 'q' => $kataKunci]) }}"
                    class="glass-card glass-card-light p-3.5 space-y-1 transition-transform hover:-translate-y-0.5 {{ $aksiFilter === $aksiNama ? 'ring-2 ring-sekunder' : '' }}"
                >
                    <div class="text-[12px] font-heading font-semibold text-ink truncate" title="{{ $labelRingkasan }} ({{ $aksiNama }})">
                        {{ $labelRingkasan }}
                    </div>
                    <div class="text-[20px] font-heading font-extrabold text-sekunder tabular-nums">{{ $jumlahAksi }}</div>
                </a>
            @endforeach
        </div>
    @endif

    <!-- Filter & Pencarian -->
    <section class="glass-card p-6">
        <form method="GET" action="{{ route('petugas.riwayat.index') }}" class="grid grid-cols-1 md:grid-cols-12 gap-4 items-end">
            <div class="md:col-span-3">
                <label for="filter_kategori" class="block text-[13px] font-heading font-semibold text-ink mb-1">
                    Filter Kategori
                </label>
                <select id="filter_kategori" name="kategori" class="glass-input w-full px-3.5 py-2.5 text-[13px]">
                    <option value="">Semua Kategori</option>
                    @foreach(($daftarKategori ?? \App\Models\AuditLog::DAFTAR_KATEGORI) as $katOpt)
                        <option value="{{ $katOpt }}" @selected(($kategoriFilter ?? '') === $katOpt)>
                            {{ $katOpt }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="md:col-span-3">
                <label for="filter_aksi" class="block text-[13px] font-heading font-semibold text-ink mb-1">
                    Filter Jenis Aksi
                </label>
                <select id="filter_aksi" name="aksi" class="glass-input w-full px-3.5 py-2.5 text-[13px]">
                    <option value="">Semua Aksi</option>
                    @foreach($daftarAksi as $aksiOpt)
                        <option value="{{ $aksiOpt }}" @selected($aksiFilter === $aksiOpt)>
                            {{ \App\Models\AuditLog::labelUntukAksi($aksiOpt) }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="md:col-span-2">
                <label for="filter_subjek" class="block text-[13px] font-heading font-semibold text-ink mb-1">
                    Filter Tipe Subjek
                </label>
                <select id="filter_subjek" name="subjek_tipe" class="glass-input w-full px-3.5 py-2.5 text-[13px]">
                    <option value="">Semua Tipe Subjek</option>
                    @foreach($daftarSubjekTipe as $subjekOpt)
                        <option value="{{ $subjekOpt }}" @selected($subjekFilter === $subjekOpt)>
                            {{ \App\Models\AuditLog::labelUntukSubjekTipe($subjekOpt) }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="md:col-span-2">
                <label for="filter_q" class="block text-[13px] font-heading font-semibold text-ink mb-1">
                    Cari Kata Kunci
                </label>
                <input
                    id="filter_q"
                    type="search"
                    name="q"
                    value="{{ $kataKunci }}"
                    placeholder="Pelaku, ID, atau isi..."
                    class="glass-input w-full px-3.5 py-2.5 text-[13px]"
                >
            </div>

            <div class="md:col-span-2 flex gap-2">
                <x-button type="submit" variant="primary" class="flex-1">
                    Filter
                </x-button>
                @if($aksiFilter !== '' || ($kategoriFilter ?? '') !== '' || $subjekFilter !== '' || $kataKunci !== '')
                    <x-button :href="route('petugas.riwayat.index')" variant="secondary">
                        Reset
                    </x-button>
                @endif
            </div>
        </form>
    </section>

    <!-- Tabel Riwayat Audit -->
    @if($daftarLog->isEmpty())
        <x-empty-state
            title="Tidak Ada Catatan Riwayat yang Cocok"
            description="Belum ada entri audit_logs yang sesuai dengan kriteria pencarian atau filter yang Anda pilih."
        />
    @else
        <x-table>
            <thead>
                <tr class="border-b border-divider bg-surface-soft/90">
                    <th scope="col" class="px-4 py-3.5 font-heading font-semibold text-ink">Waktu</th>
                    <th scope="col" class="px-4 py-3.5 font-heading font-semibold text-ink">Pelaku</th>
                    <th scope="col" class="px-4 py-3.5 font-heading font-semibold text-ink">Aksi &amp; Kategori</th>
                    <th scope="col" class="px-4 py-3.5 font-heading font-semibold text-ink">Subjek &amp; ID</th>
                    <th scope="col" class="px-4 py-3.5 font-heading font-semibold text-ink">Perubahan Data (Sebelum &rarr; Sesudah)</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-divider align-top">
                @foreach($daftarLog as $log)
                    @php
                        $kategoriLog = $log->kategori();
                        $badgeStatusKategori = match ($kategoriLog) {
                            \App\Models\AuditLog::KATEGORI_KEBERATAN => 'Dalam Peninjauan',
                            \App\Models\AuditLog::KATEGORI_PERTANYAAN => 'info',
                            \App\Models\AuditLog::KATEGORI_KONTEN_PARAMETER => 'resmi',
                            \App\Models\AuditLog::KATEGORI_AKUN => 'netral',
                            \App\Models\AuditLog::KATEGORI_KEAMANAN => 'Ditolak',
                            default => 'Perlu Perbaikan',
                        };
                        $perubahanTerbaca = $log->daftarPerubahanTerbaca();
                    @endphp
                    <tr class="hover:bg-surface-soft/80">
                        <td class="px-4 py-3.5 text-[12px] text-ink-secondary whitespace-nowrap tabular-nums">
                            {{ $log->dibuat_pada?->locale('id')->translatedFormat('d M Y H:i:s') ?? '-' }}
                        </td>
                        <td class="px-4 py-3.5 text-[13px]">
                            <div class="font-heading font-bold text-ink">
                                {{ $log->pelaku?->nama ?? 'Sistem / Publik' }}
                            </div>
                            <div class="text-[11px] text-ink-secondary">
                                {{ $log->pelaku?->labelPeran() ?? 'Tanpa sesi' }}
                            </div>
                        </td>
                        <td class="px-4 py-3.5 space-y-1.5">
                            <div>
                                <x-badge-status :status="$badgeStatusKategori" :label="$kategoriLog" />
                            </div>
                            <div class="font-heading font-bold text-[13px] text-ink leading-snug">
                                {{ $log->labelAksi() }}
                            </div>
                            <div class="font-mono text-[11px] text-ink-muted">
                                {{ $log->aksi }}
                            </div>
                        </td>
                        <td class="px-4 py-3.5 text-[12px]">
                            <div class="font-heading font-semibold text-ink">{{ $log->labelSubjekTipe() }}</div>
                            <div class="text-ink-muted font-mono">ID: {{ $log->subjek_id ?? '—' }}</div>
                        </td>
                        <td class="px-4 py-3.5 text-[12px] max-w-md">
                            @if($perubahanTerbaca !== [])
                                <ul class="space-y-1 text-[12px] text-ink">
                                    @foreach($perubahanTerbaca as $baris)
                                        <li class="leading-relaxed break-words">
                                            <strong class="font-heading font-semibold text-ink">{{ $baris['label'] }}:</strong>
                                            <span class="text-ink-secondary">{{ $baris['sebelum'] }}</span>
                                            <span class="text-sekunder font-bold px-0.5">&rarr;</span>
                                            <span class="font-medium text-ink">{{ $baris['sesudah'] }}</span>
                                        </li>
                                    @endforeach
                                </ul>

                                <details class="mt-2 text-[11px]">
                                    <summary class="cursor-pointer font-heading font-semibold text-sekunder hover:underline select-none">
                                        Lihat JSON mentah
                                    </summary>
                                    <div class="mt-1.5 grid grid-cols-1 gap-2">
                                        <div>
                                            <div class="text-[10px] font-semibold uppercase text-ink-muted">Sebelum:</div>
                                            <pre class="p-2 rounded-[8px] bg-white/80 border border-border-glass text-[11px] font-mono text-ink-secondary whitespace-pre-wrap break-words">{{ !empty($log->nilai_sebelum) ? json_encode($log->nilai_sebelum, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT) : '—' }}</pre>
                                        </div>
                                        <div>
                                            <div class="text-[10px] font-semibold uppercase text-ink-muted">Sesudah:</div>
                                            <pre class="p-2 rounded-[8px] bg-white/80 border border-border-glass text-[11px] font-mono text-ink whitespace-pre-wrap break-words">{{ !empty($log->nilai_sesudah) ? json_encode($log->nilai_sesudah, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT) : '—' }}</pre>
                                        </div>
                                    </div>
                                </details>
                            @else
                                <span class="text-ink-muted">&mdash;</span>
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

