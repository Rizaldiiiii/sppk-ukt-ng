<x-layouts.app
    title="Rekapitulasi Agregat UKT — Pimpinan / Auditor"
    subtitle="Hanya menampilkan ringkasan agregat tanpa daftar mahasiswa, tanpa NIM, dan tanpa data ekonomi perorangan (PRD Bagian 3.3)"
>
    <!-- Peringatan Batas Kewenangan Pimpinan -->
    <section class="glass-card-raised glass-card-glow p-6 border-l-4 border-l-sekunder">
        <h2 class="text-[16px] font-heading font-bold text-ink">
            Pembatasan Akses Data Pribadi (Default Terbatas)
        </h2>
        <p class="text-[13px] text-ink-secondary mt-1 leading-relaxed">
            Sesuai kebijakan perlindungan privasi mahasiswa pada PRD Bagian 3.3, akun Pimpinan / Auditor hanya menampilkan data rekapitulasi agregat. Nama mahasiswa, NIM, dokumen bukti, dan nilai ekonomi perorangan tidak ditampilkan pada halaman ini.
        </p>
    </section>

    <!-- Kartu Ringkasan Agregat -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="glass-card glass-card-light p-6 space-y-2">
            <div class="text-[13px] font-medium text-ink-secondary">Total Hasil Tercatat (Agregat)</div>
            <div class="text-[28px] font-heading font-extrabold text-ink tabular-nums">
                {{ $totalHasilTercatat }} Hasil
            </div>
            <div class="text-[12px] text-ink-secondary">Gabungan hasil resmi dan simulasi</div>
        </div>

        <div class="glass-card glass-card-light p-6 space-y-2">
            <div class="text-[13px] font-medium text-ink-secondary">Hasil Penetapan Resmi</div>
            <div class="text-[28px] font-heading font-extrabold text-sekunder tabular-nums">
                {{ $rekapPerSumber['resmi'] ?? 0 }} Hasil
            </div>
            <div class="text-[12px] text-ink-secondary">Ditetapkan melalui entri resmi petugas</div>
        </div>

        <div class="glass-card glass-card-light p-6 space-y-2">
            <div class="text-[13px] font-medium text-ink-secondary">Hasil Simulasi Berparameter</div>
            <div class="text-[28px] font-heading font-extrabold text-highlight tabular-nums">
                {{ $rekapPerSumber['simulasi'] ?? 0 }} Hasil
            </div>
            <div class="text-[12px] text-ink-secondary">Versi parameter aktif: {{ $versiAktif?->versi ?? '-' }}</div>
        </div>
    </div>

    <!-- Tabel Agregat Jumlah Hasil per Golongan UKT -->
    <section class="space-y-4">
        <h2 class="text-[21px] font-heading font-bold text-ink">
            Sebaran Jumlah Hasil per Golongan UKT (Agregat)
        </h2>

        <x-table>
            <thead>
                <tr class="border-b border-divider bg-surface-soft/90">
                    <th scope="col" class="px-6 py-4 font-heading font-semibold text-ink">Kode</th>
                    <th scope="col" class="px-6 py-4 font-heading font-semibold text-ink">Golongan UKT</th>
                    <th scope="col" class="px-6 py-4 font-heading font-semibold text-ink text-right">Rentang Tarif / Semester</th>
                    <th scope="col" class="px-6 py-4 font-heading font-semibold text-ink text-right">Jumlah Hasil Tercatat</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-divider">
                @foreach($daftarGolongan as $g)
                    <tr class="hover:bg-surface-soft/80">
                        <td class="px-6 py-4 font-heading font-bold text-sekunder">{{ $g->kode }}</td>
                        <td class="px-6 py-4 font-heading font-bold text-ink">{{ $g->nama }}</td>
                        <td class="px-6 py-4 text-right tabular-nums text-ink-secondary">{{ $g->formatRentang() }}</td>
                        <td class="px-6 py-4 text-right tabular-nums font-heading font-bold text-ink">{{ $g->hasil_ukt_count }}</td>
                    </tr>
                @endforeach
            </tbody>
        </x-table>
    </section>

    <!-- Rekap Status Pengajuan Keberatan (Agregat) -->
    <section class="glass-card p-6 space-y-3">
        <h2 class="text-[18px] font-heading font-bold text-ink">
            Rekapitulasi Pengajuan Keberatan per Status
        </h2>
        @if($rekapKeberatanPerStatus === [])
            <p class="text-[14px] text-ink-secondary">
                Belum ada pengajuan keberatan yang tercatat pada sistem saat ini.
            </p>
        @else
            <div class="grid grid-cols-2 md:grid-cols-5 gap-4">
                @foreach($rekapKeberatanPerStatus as $status => $jumlah)
                    <div class="p-4 rounded-[10px] bg-white/80 border border-border-glass">
                        <div class="text-[12px] text-ink-secondary">{{ $status }}</div>
                        <div class="text-[21px] font-heading font-bold text-ink tabular-nums mt-1">{{ $jumlah }}</div>
                    </div>
                @endforeach
            </div>
        @endif
    </section>

    <!-- Panel Pemantauan SLA & Tenggat Peninjauan Keberatan (KF-11) -->
    <section class="glass-card glass-card-glow p-6 md:p-8 relative overflow-hidden space-y-6" aria-labelledby="heading-sla-pimpinan">
        <div class="accent-bar absolute top-0 inset-x-0" aria-hidden="true"></div>

        <div class="space-y-1.5 pt-1">
            <span class="inline-block px-3 py-1 rounded-[8px] bg-sekunder-light text-sekunder font-heading font-bold text-[12px]">
                KF-11 • Pemantauan Ketercapaian Waktu Layanan (SLA)
            </span>
            <h2 id="heading-sla-pimpinan" class="text-[21px] font-heading font-bold text-ink">
                Panel Pemantauan Tenggat Waktu Peninjauan Keberatan
            </h2>
            <p class="text-[13px] text-ink-secondary leading-relaxed">
                Menampilkan ringkasan pengajuan keberatan yang mendekati atau melewati batas waktu layanan tanpa menampilkan nama, NIM, maupun data ekonomi pribadi mahasiswa. <strong>Pimpinan hanya memantau dan menindaklanjuti keterlambatan proses; keputusan atas pengajuan keberatan tetap menjadi wewenang Petugas UKT.</strong>
            </p>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div class="p-5 rounded-[12px] bg-warning-bg/80 border border-[#FDE68A] space-y-1">
                <div class="text-[13px] font-heading font-semibold text-warning">Mendekati Batas Waktu (≥ 80% / Sisa ≤ 1 Hari Kerja)</div>
                <div class="text-[28px] font-heading font-extrabold text-warning tabular-nums">
                    {{ $jumlahMendekatiTenggat ?? 0 }} Pengajuan
                </div>
            </div>

            <div class="p-5 rounded-[12px] bg-error-bg/80 border border-[#FECACA] space-y-1">
                <div class="text-[13px] font-heading font-semibold text-error">Melewati Batas Waktu (Terlewat Tenggat SLA)</div>
                <div class="text-[28px] font-heading font-extrabold text-error tabular-nums">
                    {{ $jumlahMelewatiTenggat ?? 0 }} Pengajuan
                </div>
            </div>
        </div>

        @if(empty($daftarPantauanSla))
            <div class="p-4 rounded-[12px] bg-white/80 border border-border-glass text-[14px] text-ink-secondary">
                Saat ini tidak ada pengajuan keberatan aktif yang mendekati atau melewati tenggat waktu layanan.
            </div>
        @else
            <x-table>
                <thead>
                    <tr class="border-b border-divider bg-surface-soft/90">
                        <th scope="col" class="px-5 py-3.5 font-heading font-semibold text-ink">ID Pengajuan</th>
                        <th scope="col" class="px-5 py-3.5 font-heading font-semibold text-ink">Tanggal Diajukan</th>
                        <th scope="col" class="px-5 py-3.5 font-heading font-semibold text-ink">Tenggat Waktu</th>
                        <th scope="col" class="px-5 py-3.5 font-heading font-semibold text-ink">Status</th>
                        <th scope="col" class="px-5 py-3.5 font-heading font-semibold text-ink">Hari Terlewat / Sisa</th>
                        <th scope="col" class="px-5 py-3.5 font-heading font-semibold text-ink">Keterangan Pemantauan</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-divider">
                    @foreach($daftarPantauanSla as $tugas)
                        <tr class="hover:bg-surface-soft/80">
                            <td class="px-5 py-3.5 font-heading font-bold text-sekunder tabular-nums">
                                #{{ $tugas['id'] }}
                            </td>
                            <td class="px-5 py-3.5 text-[13px] text-ink tabular-nums">
                                {{ $tugas['diajukan_pada']?->locale('id')->translatedFormat('d M Y') ?? '-' }}
                            </td>
                            <td class="px-5 py-3.5 text-[13px] font-semibold text-ink tabular-nums">
                                {{ $tugas['tenggat_waktu']?->locale('id')->translatedFormat('d M Y') ?? '-' }}
                            </td>
                            <td class="px-5 py-3.5">
                                <x-badge-status :status="$tugas['status']" :label="$tugas['status']" />
                            </td>
                            <td class="px-5 py-3.5">
                                <x-badge-status :status="$tugas['badge_status']" :label="$tugas['badge_label']" />
                            </td>
                            <td class="px-5 py-3.5">
                                @if($tugas['perlu_tindak_lanjut_pimpinan'])
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-[8px] text-[12px] font-heading font-bold bg-error-bg text-error border border-[#FECACA]">
                                        ! Perlu tindak lanjut pimpinan
                                    </span>
                                @else
                                    <span class="text-[12px] text-ink-secondary">
                                        Dalam pantauan mendekati tenggat
                                    </span>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </x-table>
        @endif
    </section>
</x-layouts.app>
