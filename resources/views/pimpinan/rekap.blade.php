<x-layouts.app
    title="Rekapitulasi Agregat UKT — Pimpinan / Auditor"
    subtitle="Hanya menampilkan ringkasan agregat tanpa daftar mahasiswa, tanpa NIM, dan tanpa data ekonomi perorangan (PRD Bagian 3.3)"
>
    <!-- Peringatan Batas Kewenangan Pimpinan -->
    <section class="neu-card p-6 bg-[#F3F2F0] border-l-4 border-l-[#550b14]">
        <h2 class="text-[16px] font-heading font-bold text-[#1C1917]">
            Pembatasan Akses Data Pribadi (Default Terbatas)
        </h2>
        <p class="text-[13px] text-[#57534E] mt-1 leading-relaxed">
            Sesuai kebijakan perlindungan privasi mahasiswa pada PRD Bagian 3.3, akun Pimpinan / Auditor hanya menampilkan data rekapitulasi agregat. Nama mahasiswa, NIM, dokumen bukti, dan nilai ekonomi perorangan tidak ditampilkan pada halaman ini.
        </p>
    </section>

    <!-- Kartu Ringkasan Agregat -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="neu-card p-6 space-y-2">
            <div class="text-[13px] font-medium text-[#57534E]">Total Hasil Tercatat (Agregat)</div>
            <div class="text-[28px] font-heading font-extrabold text-[#1C1917] tabular-nums">
                {{ $totalHasilTercatat }} Hasil
            </div>
            <div class="text-[12px] text-[#57534E]">Gabungan hasil resmi dan simulasi</div>
        </div>

        <div class="neu-card p-6 space-y-2">
            <div class="text-[13px] font-medium text-[#57534E]">Hasil Penetapan Resmi</div>
            <div class="text-[28px] font-heading font-extrabold text-[#550b14] tabular-nums">
                {{ $rekapPerSumber['resmi'] ?? 0 }} Hasil
            </div>
            <div class="text-[12px] text-[#57534E]">Ditetapkan melalui entri resmi petugas</div>
        </div>

        <div class="neu-card p-6 space-y-2">
            <div class="text-[13px] font-medium text-[#57534E]">Hasil Simulasi Berparameter</div>
            <div class="text-[28px] font-heading font-extrabold text-[#7e6961] tabular-nums">
                {{ $rekapPerSumber['simulasi'] ?? 0 }} Hasil
            </div>
            <div class="text-[12px] text-[#57534E]">Versi parameter aktif: {{ $versiAktif?->versi ?? '-' }}</div>
        </div>
    </div>

    <!-- Tabel Agregat Jumlah Hasil per Golongan UKT -->
    <section class="space-y-4">
        <h2 class="text-[21px] font-heading font-bold text-[#1C1917]">
            Sebaran Jumlah Hasil per Golongan UKT (Agregat)
        </h2>

        <x-table>
            <thead>
                <tr class="border-b border-[#DDD9D6] bg-[#F3F2F0]">
                    <th scope="col" class="px-6 py-4 font-heading font-semibold text-[#1C1917]">Kode</th>
                    <th scope="col" class="px-6 py-4 font-heading font-semibold text-[#1C1917]">Golongan UKT</th>
                    <th scope="col" class="px-6 py-4 font-heading font-semibold text-[#1C1917] text-right">Rentang Tarif / Semester</th>
                    <th scope="col" class="px-6 py-4 font-heading font-semibold text-[#1C1917] text-right">Jumlah Hasil Tercatat</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-[#DDD9D6]">
                @foreach($daftarGolongan as $g)
                    <tr class="hover:bg-[#F1EFED]">
                        <td class="px-6 py-4 font-heading font-bold text-[#550b14]">{{ $g->kode }}</td>
                        <td class="px-6 py-4 font-heading font-bold text-[#1C1917]">{{ $g->nama }}</td>
                        <td class="px-6 py-4 text-right tabular-nums text-[#57534E]">{{ $g->formatRentang() }}</td>
                        <td class="px-6 py-4 text-right tabular-nums font-heading font-bold text-[#1C1917]">{{ $g->hasil_ukt_count }}</td>
                    </tr>
                @endforeach
            </tbody>
        </x-table>
    </section>

    <!-- Rekap Status Pengajuan Keberatan (Agregat) -->
    <section class="neu-card p-6 space-y-3">
        <h2 class="text-[18px] font-heading font-bold text-[#1C1917]">
            Rekapitulasi Pengajuan Keberatan per Status
        </h2>
        @if($rekapKeberatanPerStatus === [])
            <p class="text-[14px] text-[#57534E]">
                Belum ada pengajuan keberatan tercatat (fitur pengajuan keberatan daring KF-07 dijadwalkan pada Tahap 2).
            </p>
        @else
            <div class="grid grid-cols-2 md:grid-cols-5 gap-4">
                @foreach($rekapKeberatanPerStatus as $status => $jumlah)
                    <div class="p-4 rounded-[10px] bg-white border border-[#E2DFDC]">
                        <div class="text-[12px] text-[#57534E]">{{ $status }}</div>
                        <div class="text-[21px] font-heading font-bold text-[#1C1917] tabular-nums mt-1">{{ $jumlah }}</div>
                    </div>
                @endforeach
            </div>
        @endif
    </section>
</x-layouts.app>

