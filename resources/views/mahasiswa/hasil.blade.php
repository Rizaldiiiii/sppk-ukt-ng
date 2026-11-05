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
        <section class="neu-card p-6 md:p-8 relative overflow-hidden">
            <div class="accent-bar absolute top-0 inset-x-0" aria-hidden="true"></div>

            @if($hasil->isResmi())
                <!-- Tampilan Hasil Penetapan Resmi Kampus (Contoh: Budi Pratama) -->
                <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-6">
                    <div class="space-y-3">
                        <div class="flex flex-wrap items-center gap-2.5">
                            <x-badge-status status="resmi" label="Hasil penetapan resmi kampus" />
                            <span class="text-[13px] text-[#57534E]">
                                Tanggal penetapan: <strong class="text-[#1C1917]">{{ $hasil->tanggal_penetapan?->locale('id')->translatedFormat('d M Y') ?? '-' }}</strong>
                            </span>
                        </div>

                        <h2 class="text-[28px] md:text-[32px] font-heading font-extrabold text-[#1C1917]">
                            {{ $hasil->golongan?->nama ?? 'Golongan UKT' }} — <span class="text-[#550b14] tabular-nums">{{ $hasil->formatNominal() }}</span> <span class="text-[18px] font-semibold text-[#57534E]">/ semester</span>
                        </h2>

                        <p class="text-[14px] text-[#57534E] leading-relaxed">
                            <strong class="text-[#1C1917]">Sumber Penetapan Resmi:</strong> {{ $hasil->sumber_penetapan ?? 'Keputusan Penetapan UKT Universitas Negeri Gorontalo' }}
                            @if($hasil->versiParameter)
                                • Rincian diuraikan dengan parameter penjelasan <strong>{{ $hasil->versiParameter->versi }}</strong>
                            @endif
                        </p>
                    </div>

                    <div class="neu-card-raised p-5 shrink-0 lg:w-72 space-y-1.5">
                        <div class="text-[12px] text-[#57534E] font-medium">Total Poin Komponen Penjelasan</div>
                        <div class="text-[28px] font-heading font-extrabold text-[#550b14] tabular-nums">
                            {{ number_format((float) $hasil->skor_total, 2, ',', '.') }} <span class="text-[14px] font-normal text-[#57534E]">/ 5,00</span>
                        </div>
                        <div class="text-[12px] text-[#57534E]">
                            Dari {{ $hasil->rincianPenilaian->count() }} komponen yang diperhitungkan
                        </div>
                    </div>
                </div>
            @else
                <!-- Tampilan Hasil Simulasi / Penjelasan Komponen (Contoh: Ayu Lestari) -->
                <!-- Jangan tampilkan golongan simulasi seolah-olah keputusan resmi kampus; penjelasan status seeder ada di footer halaman -->
                <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-6">
                    <div class="space-y-2.5 max-w-3xl">
                        <div class="text-[13px] font-heading font-semibold text-[#7e6961]">
                            Rincian Perhitungan Komponen UKT • Versi Parameter {{ $hasil->versiParameter?->versi ?? '-' }}
                        </div>
                        <h2 class="text-[26px] md:text-[28px] font-heading font-extrabold text-[#1C1917]">
                            Uraian Komponen dan Langkah Perhitungan Penilaian UKT
                        </h2>
                        <p class="text-[14px] text-[#57534E] leading-relaxed">
                            Tabel di bawah menampilkan seluruh faktor masukan yang tercatat untuk NIM <strong>{{ $mahasiswa?->nim }}</strong>, cara pengolahannya menjadi poin tiap komponen, serta kalimat alasan mengapa nilai tersebut muncul.
                            @if($hasil->golongan)
                                Berdasarkan total poin komponen sebesar <strong class="text-[#1C1917] tabular-nums">{{ number_format((float) $hasil->skor_total, 2, ',', '.') }}</strong>, posisi ini berada pada rentang referensi <strong>{{ $hasil->golongan->nama }} ({{ $hasil->golongan->formatRentang() }})</strong>.
                            @endif
                        </p>
                    </div>

                    <div class="neu-card-raised p-5 shrink-0 lg:w-72 space-y-1.5">
                        <div class="text-[12px] text-[#57534E] font-medium">Total Poin Komponen</div>
                        <div class="text-[28px] font-heading font-extrabold text-[#550b14] tabular-nums">
                            {{ number_format((float) $hasil->skor_total, 2, ',', '.') }} <span class="text-[14px] font-normal text-[#57534E]">/ 5,00</span>
                        </div>
                        <div class="text-[12px] text-[#57534E]">
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
                    <h2 id="heading-rincian-komponen" class="text-[21px] md:text-[26px] font-heading font-bold text-[#1C1917]">
                        Tabel Rincian Komponen Penilaian ({{ $hasil->rincianPenilaian->count() }} Komponen)
                    </h2>
                    <p class="text-[14px] text-[#57534E]">
                        Semua faktor yang dihitung ditampilkan secara utuh beserta masukan yang dipakai, langkah perhitungannya, dan kalimat alasan per komponen.
                    </p>
                </div>
            </div>

            <!-- Tabel Desktop (>=768px) — Tanpa badge SIMULASI berulang di tiap baris! -->
            <div class="hidden md:block">
                <x-table>
                    <thead>
                        <tr class="border-b border-[#DDD9D6] bg-[#F3F2F0]">
                            <th scope="col" class="px-6 py-4 font-heading font-semibold text-[#1C1917] w-64">Komponen &amp; Penjelasan</th>
                            <th scope="col" class="px-6 py-4 font-heading font-semibold text-[#1C1917] w-48">Masukan yang Dipakai</th>
                            <th scope="col" class="px-6 py-4 font-heading font-semibold text-[#1C1917] text-right w-36">Nilai Komponen</th>
                            <th scope="col" class="px-6 py-4 font-heading font-semibold text-[#1C1917]">Langkah Perhitungan (KF-02 / KF-05)</th>
                            <th scope="col" class="px-6 py-4 font-heading font-semibold text-[#1C1917]">Kalimat Alasan Hasil (KF-04)</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-[#DDD9D6]">
                        @foreach($hasil->rincianPenilaian as $baris)
                            <tr class="hover:bg-[#F1EFED] transition-colors align-top">
                                <td class="px-6 py-5">
                                    <div class="font-heading font-bold text-[15px] text-[#1C1917]">
                                        {{ $baris->faktor?->nama ?? 'Komponen Penilaian' }}
                                    </div>
                                    <p class="text-[13px] text-[#57534E] mt-1 leading-relaxed">
                                        {{ $baris->faktor?->penjelasan_singkat }}
                                    </p>
                                </td>
                                <td class="px-6 py-5 font-medium text-[#1C1917]">
                                    <div class="p-2.5 rounded-[8px] bg-[#F3F2F0] border border-[#E2DFDC] text-[13px]">
                                        {{ $baris->nilai_masukan }}
                                    </div>
                                </td>
                                <td class="px-6 py-5 text-right tabular-nums font-heading font-bold text-[16px] text-[#550b14]">
                                    {{ number_format((float) $baris->nilai_komponen, 2, ',', '.') }} poin
                                </td>
                                <td class="px-6 py-5 text-[13px] text-[#57534E] leading-relaxed">
                                    {{ $baris->langkah_perhitungan }}
                                </td>
                                <td class="px-6 py-5 text-[14px] text-[#1C1917] leading-relaxed font-medium">
                                    {{ $baris->kalimat_alasan }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </x-table>
            </div>

            <!-- Kartu Mobile (<768px) — Responsif sesuai Neumorphism.md §12 -->
            <div class="md:hidden space-y-4">
                @foreach($hasil->rincianPenilaian as $baris)
                    <article class="neu-card p-5 space-y-3">
                        <div class="flex items-start justify-between gap-3">
                            <div>
                                <h3 class="font-heading font-bold text-[16px] text-[#1C1917]">
                                    {{ $baris->faktor?->nama ?? 'Komponen Penilaian' }}
                                </h3>
                                <p class="text-[12px] text-[#57534E] mt-0.5">
                                    {{ $baris->faktor?->penjelasan_singkat }}
                                </p>
                            </div>
                            <span class="px-3 py-1 rounded-[8px] bg-[#F5E8E9] text-[#550b14] font-heading font-bold text-[13px] tabular-nums shrink-0">
                                {{ number_format((float) $baris->nilai_komponen, 2, ',', '.') }} poin
                            </span>
                        </div>

                        <div class="p-3 rounded-[8px] bg-white border border-[#E2DFDC] text-[13px]">
                            <span class="text-[#57534E]">Masukan yang dipakai:</span>
                            <strong class="text-[#1C1917] block mt-0.5">{{ $baris->nilai_masukan }}</strong>
                        </div>

                        <div class="text-[13px] text-[#1C1917] leading-relaxed">
                            <strong class="text-[#550b14] font-heading">Alasan Komponen:</strong>
                            {{ $baris->kalimat_alasan }}
                        </div>

                        <div class="pt-2 border-t border-[#DDD9D6] text-[12px] text-[#57534E] leading-relaxed">
                            <strong>Langkah perhitungan:</strong> {{ $baris->langkah_perhitungan }}
                        </div>
                    </article>
                @endforeach
            </div>
        </section>

        <!-- Informasi Tindak Lanjut & Perkiraan Waktu Tunggu (KF-06 & KF-11) -->
        <section class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <div class="neu-card p-6 space-y-2">
                <h3 class="text-[18px] font-heading font-bold text-[#1C1917]">
                    Merasa Rincian atau Hasil Tidak Sesuai Kondisi Ekonomi?
                </h3>
                <p class="text-[14px] text-[#57534E] leading-relaxed">
                    Anda dapat mempelajari syarat, daftar dokumen bukti, dan tahapan pengajuan keberatan sebelum mengajukan peninjauan ulang.
                </p>
                <div class="pt-2 flex flex-wrap gap-3">
                    <x-button :href="route('publik.prosedur-keberatan')" variant="secondary">
                        Baca Prosedur Pengajuan Keberatan
                    </x-button>
                </div>
            </div>

            <div class="neu-card p-6 space-y-2">
                <h3 class="text-[18px] font-heading font-bold text-[#1C1917]">
                    Informasi Perkiraan Waktu Proses
                </h3>
                @if($perkiraanWaktu)
                    <p class="text-[14px] text-[#1C1917] font-semibold">
                        {{ $perkiraanWaktu->teks_perkiraan }}
                    </p>
                    <p class="text-[12px] text-[#57534E]">
                        Sumber: {{ $perkiraanWaktu->sumber }} (diperbarui {{ $perkiraanWaktu->tanggal?->locale('id')->translatedFormat('d M Y') }})
                    </p>
                @else
                    <p class="text-[14px] text-[#57534E] leading-relaxed">
                        <strong>Waktu tunggu belum ditetapkan secara resmi.</strong> Status saat ini: {{ $hasil->isResmi() ? 'Penetapan resmi telah diterbitkan' : 'Rincian penjelasan simulasi tercatat pada sistem' }}.
                    </p>
                @endif
            </div>
        </section>

        <!-- Footer Khusus Penjelasan Status Seeder/Simulasi (PRD KF-05 & Blueprint §6.3 — Wajib hanya di footer untuk hasil simulasi) -->
        @if($catatanFooterSimulasi)
            <footer
                id="footer-status-simulasi"
                aria-label="Penjelasan Status Data Simulasi dan Seeder"
                class="mt-8 p-6 rounded-[12px] bg-[#F3F2F0] border border-[#DDD9D6] text-[13px] text-[#57534E] leading-relaxed"
            >
                <div class="font-heading font-bold text-[#1C1917] mb-1">
                    Keterangan Status Data Halaman Hasil (Prototipe Seeder / Simulasi)
                </div>
                <p>{{ $catatanFooterSimulasi }}</p>
            </footer>
        @endif
    @endif
</x-layouts.app>

