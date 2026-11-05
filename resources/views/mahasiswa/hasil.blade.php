<x-layouts.app
    title="Hasil dan Rincian Komponen Penilaian Saya"
    :subtitle="$mahasiswa ? 'Mahasiswa: ' . $user->nama . ' • NIM ' . $mahasiswa->nim . ' • ' . $mahasiswa->program_studi : $user->nama"
>
    @if(!$hasil)
        <!-- Empty State bila mahasiswa belum memiliki hasil (Kriteria Penerimaan KF-01, Akun Citra Kirana) -->
        <x-empty-state
            title="Hasil Penilaian UKT Anda Belum Tersedia"
            description="Saat ini belum terdapat rincian komponen penilaian maupun penetapan UKT yang tercatat untuk akun Anda (NIM {{ $mahasiswa?->nim ?? '-' }}). Hal ini terjadi apabila data verifikasi masih dalam antrean pemeriksaan petugas UKT atau belum dimasukkan ke dalam sistem. Anda akan menerima pemberitahuan di sistem dan email segera setelah rincian hasil tersedia."
        >
            <x-slot:actions>
                <x-button :href="route('publik.alur')" variant="primary">
                    Pelajari Tahapan Penentuan UKT
                </x-button>
                <x-button :href="route('publik.faktor')" variant="secondary">
                    Lihat Daftar Faktor Penilaian
                </x-button>
                <x-button :href="route('publik.bantuan')" variant="secondary">
                    Hubungi Layanan Bantuan UKT
                </x-button>
            </x-slot:actions>
        </x-empty-state>
    @else
        <!-- Ringkasan Atas: Berbeda antara Hasil Resmi vs Simulasi (Blueprint §6.3) -->
        <section class="glass-card p-6 md:p-8 relative overflow-hidden">
            <div class="accent-bar absolute top-0 inset-x-0" aria-hidden="true"></div>

            @if($hasil->isResmi())
                <!-- Tampilan Hasil Penetapan Resmi Kampus (Contoh: Budi Pratama) -->
                <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-6">
                    <div class="space-y-3">
                        <div class="flex flex-wrap items-center gap-2.5">
                            <x-badge-status status="resmi" label="Hasil penetapan resmi kampus" />
                            <span class="text-[13px] text-ink-secondary">
                                Tanggal penetapan: <strong class="text-ink">{{ $hasil->tanggal_penetapan?->locale('id')->translatedFormat('d M Y') ?? '-' }}</strong>
                            </span>
                        </div>

                        <h2 class="text-[28px] md:text-[32px] font-heading font-extrabold text-ink">
                            {{ $hasil->golongan?->nama ?? 'Golongan UKT' }} — <span class="text-sekunder tabular-nums">{{ $hasil->formatNominal() }}</span> <span class="text-[18px] font-semibold text-ink-secondary">/ semester</span>
                        </h2>

                        <p class="text-[14px] text-ink-secondary leading-relaxed">
                            <strong class="text-ink">Sumber Penetapan Resmi:</strong> {{ $hasil->sumber_penetapan ?? 'Keputusan Penetapan UKT Universitas Negeri Gorontalo' }}
                            @if($hasil->versiParameter)
                                • Rincian diuraikan dengan parameter penjelasan <strong>{{ $hasil->versiParameter->versi }}</strong>
                            @endif
                        </p>
                    </div>

                    <div class="glass-card-raised p-5 shrink-0 lg:w-72 space-y-1.5">
                        <div class="text-[12px] text-ink-secondary font-medium">Total Poin Komponen Penjelasan</div>
                        <div class="text-[28px] font-heading font-extrabold text-sekunder tabular-nums">
                            {{ number_format((float) $hasil->skor_total, 2, ',', '.') }} <span class="text-[14px] font-normal text-ink-secondary">/ 5,00</span>
                        </div>
                        <div class="text-[12px] text-ink-secondary">
                            Dari {{ $hasil->rincianPenilaian->count() }} komponen yang diperhitungkan
                        </div>
                    </div>
                </div>
            @else
                <!-- Tampilan Hasil Simulasi / Penjelasan Komponen (Contoh: Ayu Lestari) -->
                <!-- Jangan tampilkan golongan simulasi seolah-olah keputusan resmi kampus; penjelasan status seeder ada di footer halaman -->
                <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-6">
                    <div class="space-y-2.5 max-w-3xl">
                        <div class="text-[13px] font-heading font-semibold text-highlight">
                            Rincian Perhitungan Komponen UKT • Versi Parameter {{ $hasil->versiParameter?->versi ?? '-' }}
                        </div>
                        <h2 class="text-[26px] md:text-[28px] font-heading font-extrabold text-ink">
                            Uraian Komponen dan Langkah Perhitungan Penilaian UKT
                        </h2>
                        <p class="text-[14px] text-ink-secondary leading-relaxed">
                            Tabel di bawah menampilkan seluruh faktor masukan yang tercatat untuk NIM <strong>{{ $mahasiswa?->nim }}</strong>, cara pengolahannya menjadi poin tiap komponen, serta kalimat alasan mengapa nilai tersebut muncul.
                            @if($hasil->golongan)
                                Berdasarkan total poin komponen sebesar <strong class="text-ink tabular-nums">{{ number_format((float) $hasil->skor_total, 2, ',', '.') }}</strong>, posisi ini berada pada rentang referensi <strong>{{ $hasil->golongan->nama }} ({{ $hasil->golongan->formatRentang() }})</strong>.
                            @endif
                        </p>
                    </div>

                    <div class="glass-card-raised p-5 shrink-0 lg:w-72 space-y-1.5">
                        <div class="text-[12px] text-ink-secondary font-medium">Total Poin Komponen</div>
                        <div class="text-[28px] font-heading font-extrabold text-sekunder tabular-nums">
                            {{ number_format((float) $hasil->skor_total, 2, ',', '.') }} <span class="text-[14px] font-normal text-ink-secondary">/ 5,00</span>
                        </div>
                        <div class="text-[12px] text-ink-secondary">
                            Versi aturan: {{ $hasil->versiParameter?->versi ?? '-' }} (berlaku {{ $hasil->versiParameter?->berlaku_mulai?->locale('id')->translatedFormat('d M Y') ?? '-' }})
                        </div>
                    </div>
                </div>
            @endif
        </section>

        <!-- Tabel Utama Rincian Komponen Penilaian & Kalimat Alasan (KF-01, KF-02, KF-04) -->
        <section class="space-y-4" aria-labelledby="heading-rincian-komponen">
            <div class="flex flex-col md:flex-row md:items-end justify-between gap-2">
                <div>
                    <h2 id="heading-rincian-komponen" class="text-[21px] md:text-[26px] font-heading font-bold text-ink">
                        Tabel Rincian Komponen Penilaian ({{ $hasil->rincianPenilaian->count() }} Komponen)
                    </h2>
                    <p class="text-[14px] text-ink-secondary">
                        Semua faktor yang dihitung ditampilkan secara utuh beserta masukan yang dipakai, langkah perhitungannya, dan kalimat alasan per komponen.
                    </p>
                </div>
            </div>

            <!-- Tabel Desktop (>=768px) — Tanpa badge SIMULASI berulang di tiap baris! -->
            <div class="hidden md:block">
                <x-table>
                    <thead>
                        <tr class="border-b border-divider bg-surface-soft/90">
                            <th scope="col" class="px-6 py-4 font-heading font-semibold text-ink w-64">Komponen &amp; Penjelasan</th>
                            <th scope="col" class="px-6 py-4 font-heading font-semibold text-ink w-48">Masukan yang Dipakai</th>
                            <th scope="col" class="px-6 py-4 font-heading font-semibold text-ink text-right w-36">Nilai Komponen</th>
                            <th scope="col" class="px-6 py-4 font-heading font-semibold text-ink">Langkah Perhitungan (KF-02 / KF-05)</th>
                            <th scope="col" class="px-6 py-4 font-heading font-semibold text-ink">Kalimat Alasan Hasil (KF-04)</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-divider">
                        @foreach($hasil->rincianPenilaian as $baris)
                            <tr class="hover:bg-surface-soft/80 transition-colors align-top">
                                <td class="px-6 py-5">
                                    <div class="font-heading font-bold text-[15px] text-ink">
                                        {{ $baris->faktor?->nama ?? 'Komponen Penilaian' }}
                                    </div>
                                    <p class="text-[13px] text-ink-secondary mt-1 leading-relaxed">
                                        {{ $baris->faktor?->penjelasan_singkat }}
                                    </p>
                                </td>
                                <td class="px-6 py-5 font-medium text-ink">
                                    <div class="p-2.5 rounded-[8px] bg-surface-soft border border-border-subtle text-[13px]">
                                        {{ $baris->nilai_masukan }}
                                    </div>
                                </td>
                                <td class="px-6 py-5 text-right tabular-nums font-heading font-bold text-[16px] text-sekunder">
                                    {{ number_format((float) $baris->nilai_komponen, 2, ',', '.') }} poin
                                </td>
                                <td class="px-6 py-5 text-[13px] text-ink-secondary leading-relaxed">
                                    {{ $baris->langkah_perhitungan }}
                                </td>
                                <td class="px-6 py-5 text-[14px] text-ink leading-relaxed font-medium">
                                    {{ $baris->kalimat_alasan }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </x-table>
            </div>

            <!-- Kartu Mobile (<768px) — Responsif sesuai Glassmorphism.md §12 -->
            <div class="md:hidden space-y-4">
                @foreach($hasil->rincianPenilaian as $baris)
                    <article class="glass-card p-5 space-y-3">
                        <div class="flex items-start justify-between gap-3">
                            <div>
                                <h3 class="font-heading font-bold text-[16px] text-ink">
                                    {{ $baris->faktor?->nama ?? 'Komponen Penilaian' }}
                                </h3>
                                <p class="text-[12px] text-ink-secondary mt-0.5">
                                    {{ $baris->faktor?->penjelasan_singkat }}
                                </p>
                            </div>
                            <span class="px-3 py-1 rounded-[8px] bg-sekunder-light text-sekunder font-heading font-bold text-[13px] tabular-nums shrink-0">
                                {{ number_format((float) $baris->nilai_komponen, 2, ',', '.') }} poin
                            </span>
                        </div>

                        <div class="p-3 rounded-[8px] bg-white/80 border border-border-glass text-[13px]">
                            <span class="text-ink-secondary">Masukan yang dipakai:</span>
                            <strong class="text-ink block mt-0.5">{{ $baris->nilai_masukan }}</strong>
                        </div>

                        <div class="text-[13px] text-ink leading-relaxed">
                            <strong class="text-sekunder font-heading">Alasan Komponen:</strong>
                            {{ $baris->kalimat_alasan }}
                        </div>

                        <div class="pt-2 border-t border-divider/80 text-[12px] text-ink-secondary leading-relaxed">
                            <strong>Langkah perhitungan:</strong> {{ $baris->langkah_perhitungan }}
                        </div>
                    </article>
                @endforeach
            </div>
        </section>

        <!-- Informasi Tindak Lanjut & Perkiraan Waktu Tunggu (KF-06 & KF-11) -->
        <section class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <div class="glass-card p-6 space-y-2">
                <h3 class="text-[18px] font-heading font-bold text-ink">
                    Merasa Rincian atau Hasil Tidak Sesuai Kondisi Ekonomi?
                </h3>
                <p class="text-[14px] text-ink-secondary leading-relaxed">
                    Anda dapat mempelajari syarat, daftar dokumen bukti, dan tahapan pengajuan keberatan sebelum mengajukan peninjauan ulang.
                </p>
                <div class="pt-2 flex flex-wrap gap-3">
                    <x-button :href="route('publik.prosedur-keberatan')" variant="secondary">
                        Baca Prosedur Pengajuan Keberatan
                    </x-button>
                </div>
            </div>

            <div class="glass-card p-6 space-y-2">
                <h3 class="text-[18px] font-heading font-bold text-ink">
                    Informasi Perkiraan Waktu Proses
                </h3>
                @if($perkiraanWaktu)
                    <p class="text-[14px] text-ink font-semibold">
                        {{ $perkiraanWaktu->teks_perkiraan }}
                    </p>
                    <p class="text-[12px] text-ink-secondary">
                        Sumber: {{ $perkiraanWaktu->sumber }} (diperbarui {{ $perkiraanWaktu->tanggal?->locale('id')->translatedFormat('d M Y') }})
                    </p>
                @else
                    <p class="text-[14px] text-ink-secondary leading-relaxed">
                        <strong>Waktu tunggu belum ditetapkan secara resmi.</strong> Status saat ini: {{ $hasil->isResmi() ? 'Penetapan resmi telah diterbitkan' : 'Rincian penjelasan simulasi tercatat pada sistem' }}.
                    </p>
                @endif
            </div>
        </section>

        <!-- Footer Khusus Penjelasan Status Seeder/Simulasi (PRD KF-05, Blueprint §6.3, Glassmorphism.md §5.12 — Wajib hanya di footer untuk hasil simulasi) -->
        @if($catatanFooterSimulasi)
            <footer
                id="footer-status-simulasi"
                aria-label="Penjelasan Status Data Simulasi dan Seeder"
                class="mt-8 p-6 glass-footer-panel text-[13px] text-ink-secondary leading-relaxed"
            >
                <div class="font-heading font-bold text-ink mb-1">
                    Keterangan Status Data Halaman Hasil (Prototipe Seeder / Simulasi)
                </div>
                <p>{{ $catatanFooterSimulasi }}</p>
            </footer>
        @endif
    @endif
</x-layouts.app>
