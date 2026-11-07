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
        <section class="glass-card glass-card-glow p-6 md:p-8 relative overflow-hidden">
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

                    <div class="glass-card-raised glass-card-light p-5 shrink-0 lg:w-72 space-y-1.5">
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

                    <div class="glass-card-raised glass-card-light p-5 shrink-0 lg:w-72 space-y-1.5">
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
                    <article class="glass-card glass-card-light p-5 space-y-3">
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

        <!-- Bagian Simulasi What-If: "Coba Kalau Dataku Berbeda" (Perluasan KF-02 & KF-05) -->
        @if(!empty($whatIf))
            <section
                id="simulasi-what-if"
                aria-labelledby="heading-what-if"
                class="glass-card glass-card-glow p-6 md:p-8 relative overflow-hidden space-y-6"
            >
                <div class="accent-bar absolute top-0 inset-x-0" aria-hidden="true"></div>

                <!-- Header & Label Wajib Simulasi What-If -->
                <div class="flex flex-col lg:flex-row lg:items-start justify-between gap-4 border-b border-divider/80 pb-5">
                    <div class="space-y-2 max-w-3xl">
                        <div class="flex flex-wrap items-center gap-2">
                            <span class="inline-block px-3 py-1 rounded-[8px] bg-highlight-light text-highlight-dark border border-[#FDE68A] font-heading font-bold text-[12px]">
                                Simulasi what-if — bukan penetapan resmi, mengubah di sini tidak mengubah datamu
                            </span>
                            <span class="px-2.5 py-1 rounded-[8px] bg-sekunder-light text-sekunder font-heading font-semibold text-[12px]">
                                Parameter Aktif: {{ $whatIf['versi_parameter'] }}
                            </span>
                        </div>
                        <h2 id="heading-what-if" class="text-[22px] md:text-[26px] font-heading font-extrabold text-ink">
                            Coba Kalau Dataku Berbeda
                        </h2>
                        <p class="text-[14px] text-ink-secondary leading-relaxed">
                            Kelima faktor di bawah sudah terisi sesuai data Anda saat ini. Coba ubah satu atau beberapa kategori faktor melalui pilihan dropdown untuk mempelajari bagaimana perubahan data memengaruhi poin komponen dan langkah perhitungannya. Perhitungan ini hanya berjalan di memori/sesi Anda, tidak disimpan ke database, tidak menimpa hasil resmi, dan tidak dikirim ke petugas.
                        </p>
                    </div>

                    @if($whatIf['sedang_disimulasikan'])
                        <form
                            method="POST"
                            action="{{ route('mahasiswa.hasil.what-if.reset', request()->route('hasil') ? ['hasil' => $hasil->id] : []) }}"
                            data-prevent-double-submit
                            class="shrink-0"
                        >
                            @csrf
                            @method('DELETE')
                            <x-button type="submit" variant="secondary">
                                Kembalikan ke Data Asli Saya
                            </x-button>
                        </form>
                    @endif
                </div>

                @if($errors->any())
                    <div role="alert" class="p-4 rounded-[12px] bg-error-bg border border-[#FECACA] text-[13px] text-error space-y-1">
                        <div class="font-heading font-bold">Simulasi What-If tidak dapat dijalankan — periksa kembali pilihan Anda:</div>
                        <ul class="list-disc pl-5">
                            @foreach($errors->all() as $err)
                                <li>{{ $err }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <!-- Formulir 5 Faktor dengan Dropdown Kategori RuleEngine -->
                <form
                    method="POST"
                    action="{{ route('mahasiswa.hasil.what-if', request()->route('hasil') ? ['hasil' => $hasil->id] : []) }}"
                    data-prevent-double-submit
                    class="space-y-5"
                >
                    @csrf

                    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-4">
                        @foreach($whatIf['faktor_form'] as $fItem)
                            <div class="p-4 rounded-[12px] {{ $fItem['apakah_diubah'] ? 'bg-highlight-light/70 border border-[#FDE68A]' : 'bg-white/80 border border-border-glass' }} space-y-2 flex flex-col justify-between">
                                <div class="space-y-1">
                                    <div class="flex items-center justify-between gap-2">
                                        <span class="px-2.5 py-0.5 rounded-[6px] bg-sekunder-light text-sekunder font-heading font-bold text-[11px]">
                                            Bobot {{ $fItem['bobot'] }}%
                                        </span>
                                        @if($fItem['apakah_diubah'])
                                            <span class="px-2 py-0.5 rounded-[6px] bg-warning-bg text-warning font-heading font-bold text-[11px]">
                                                Diubah pada simulasi
                                            </span>
                                        @else
                                            <span class="text-[11px] text-ink-muted font-medium">
                                                Sesuai data tercatat
                                            </span>
                                        @endif
                                    </div>
                                    <label for="what_if_{{ $fItem['kode'] }}" class="block font-heading font-bold text-[14px] text-ink leading-snug pt-0.5">
                                        {{ $fItem['nama'] }} ({{ $fItem['bobot'] }}%)
                                    </label>
                                    <p class="text-[12px] text-ink-secondary">
                                        Data asli Anda: <strong class="text-ink">{{ $fItem['nilai_asli_formatted'] }}</strong>
                                    </p>
                                </div>

                                <div class="pt-1">
                                    <select
                                        id="what_if_{{ $fItem['kode'] }}"
                                        name="{{ $fItem['kode'] }}"
                                        required
                                        class="glass-input w-full px-3.5 py-2.5 text-[13px]"
                                    >
                                        @foreach($fItem['opsi'] as $opsi)
                                            <option
                                                value="{{ $opsi['nilai_opsi'] }}"
                                                @selected(old($fItem['kode'], $fItem['kategori_terpilih_label']) === $opsi['nilai_opsi'])
                                            >
                                                {{ $opsi['label'] }} — Skor {{ $opsi['skor'] }}/5 ({{ number_format($opsi['poin'], 2, ',', '.') }} poin){{ $opsi['apakah_asli'] ? ' [Data Saya]' : '' }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error($fItem['kode'])
                                        <p class="text-[12px] font-medium text-error mt-1">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 pt-1">
                        <p class="text-[12px] text-ink-secondary">
                            Pilih kategori pada salah satu atau beberapa faktor di atas, lalu tekan tombol di samping untuk membandingkan hasilnya secara berdampingan.
                        </p>
                        <x-button type="submit" variant="primary" class="shrink-0">
                            Hitung &amp; Bandingkan Simulasi What-If
                        </x-button>
                    </div>
                </form>

                <!-- Output Berdampingan: Hasil Saya (Asli, Tidak Berubah) vs Hasil Simulasi What-If -->
                <div class="pt-4 border-t border-divider/80 space-y-6">
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                        <!-- Panel Kiri: Hasil Saya (Asli, Tidak Berubah) -->
                        <div class="p-5 rounded-[14px] bg-white/85 border border-border-glass space-y-3">
                            <div class="flex flex-wrap items-center justify-between gap-2">
                                <span class="px-2.5 py-1 rounded-[8px] bg-sekunder-light text-sekunder font-heading font-bold text-[12px]">
                                    Hasil Saya (Asli — Tidak Berubah)
                                </span>
                                <span class="text-[12px] text-ink-secondary">
                                    {{ $hasil->isResmi() ? 'Penetapan Resmi Kampus' : 'Hasil Tercatat Saat Ini' }}
                                </span>
                            </div>

                            <div class="flex flex-wrap items-baseline justify-between gap-4">
                                <div>
                                    <div class="text-[12px] text-ink-secondary">Total Poin Komponen Asli</div>
                                    <div class="text-[28px] font-heading font-extrabold text-ink tabular-nums">
                                        {{ number_format($whatIf['skor_total_asli'], 2, ',', '.') }} <span class="text-[14px] font-normal text-ink-secondary">/ 5,00</span>
                                    </div>
                                </div>
                                @if($hasil->golongan)
                                    <div class="text-right">
                                        <div class="text-[12px] text-ink-secondary">
                                            {{ $hasil->isResmi() ? 'Golongan & Nominal Resmi' : 'Rentang Referensi Saat Ini' }}
                                        </div>
                                        <div class="font-heading font-bold text-[16px] text-sekunder">
                                            {{ $hasil->golongan->nama }} ({{ $hasil->isResmi() ? $hasil->formatNominal() : $hasil->golongan->formatRentang() }})
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>

                        <!-- Panel Kanan: Hasil Simulasi What-If -->
                        <div class="p-5 rounded-[14px] bg-highlight-light/70 border border-[#FDE68A] space-y-3">
                            <div class="flex flex-wrap items-center justify-between gap-2">
                                <span class="px-2.5 py-1 rounded-[8px] bg-sekunder text-white font-heading font-bold text-[12px]">
                                    Hasil Simulasi What-If
                                </span>
                                <span class="text-[12px] font-semibold text-highlight-dark">
                                    Simulasi what-if — bukan penetapan resmi, mengubah di sini tidak mengubah datamu
                                </span>
                            </div>

                            <div class="flex flex-wrap items-baseline justify-between gap-4">
                                <div>
                                    <div class="text-[12px] text-ink-secondary">Total Poin Simulasi What-If &amp; Selisih Total</div>
                                    <div class="flex flex-wrap items-baseline gap-2.5">
                                        <span class="text-[28px] font-heading font-extrabold text-sekunder tabular-nums">
                                            {{ number_format($whatIf['skor_total_what_if'], 2, ',', '.') }} <span class="text-[14px] font-normal text-ink-secondary">/ 5,00</span>
                                        </span>
                                        @php
                                            $selisihTotal = $whatIf['selisih_total'];
                                            $tandaSelisihTotal = $selisihTotal > 0 ? '+' : '';
                                        @endphp
                                        <span class="px-2.5 py-0.5 rounded-[8px] text-[12px] font-heading font-bold tabular-nums {{ abs($selisihTotal) < 0.001 ? 'bg-white/85 text-ink-secondary border border-border-glass' : ($selisihTotal < 0 ? 'bg-success-bg text-success border border-[#BBF7D0]' : 'bg-warning-bg text-warning border border-[#FDE68A]') }}">
                                            Selisih Total: {{ $tandaSelisihTotal }}{{ number_format($selisihTotal, 2, ',', '.') }} poin
                                        </span>
                                    </div>
                                </div>

                                @if($whatIf['golongan_what_if'])
                                    <div class="text-right">
                                        <div class="text-[12px] text-ink-secondary">Posisi Rentang Referensi Simulasi</div>
                                        <div class="font-heading font-bold text-[16px] text-ink">
                                            {{ $whatIf['golongan_what_if']->nama }} ({{ $whatIf['golongan_what_if']->formatRentang() }})
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- Rincian Perbandingan 5 Komponen Berdampingan (Hasil Saya vs Hasil Simulasi What-If) -->
                    <div class="space-y-4">
                        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2">
                            <h3 class="text-[18px] font-heading font-bold text-ink">
                                Perbandingan Berdampingan per Komponen (Hasil Saya vs Hasil Simulasi What-If)
                            </h3>
                            <span class="text-[12px] text-ink-secondary">
                                {{ $whatIf['jumlah_faktor_diubah'] }} dari {{ count($whatIf['perbandingan_komponen']) }} faktor diubah pada simulasi ini
                            </span>
                        </div>

                        <div class="space-y-4">
                            @foreach($whatIf['perbandingan_komponen'] as $komp)
                                @php
                                    $selisih = $komp['selisih_poin'];
                                    $tanda = $selisih > 0 ? '+' : '';
                                @endphp
                                <article
                                    data-komponen-what-if="{{ $komp['kode_faktor'] }}"
                                    class="p-5 rounded-[14px] {{ $komp['apakah_diubah'] ? 'bg-white/95 border-2 border-[#F5B301]' : 'bg-white/80 border border-border-glass' }} space-y-4"
                                >
                                    <div class="flex flex-wrap items-center justify-between gap-2 border-b border-divider/70 pb-3">
                                        <div class="flex flex-wrap items-center gap-2">
                                            <h4 class="font-heading font-bold text-[16px] text-ink">
                                                {{ $komp['nama_faktor'] }}
                                            </h4>
                                            <span class="px-2.5 py-0.5 rounded-[6px] bg-surface-soft text-ink-secondary font-heading font-semibold text-[12px]">
                                                Bobot {{ $komp['bobot'] }}%
                                            </span>
                                            @if($komp['apakah_diubah'])
                                                <span class="px-2.5 py-0.5 rounded-[6px] bg-highlight-light text-highlight-dark font-heading font-bold text-[12px]">
                                                    Faktor Diubah
                                                </span>
                                            @endif
                                        </div>

                                        <span class="px-3 py-1 rounded-[8px] font-heading font-bold text-[12px] tabular-nums {{ abs($selisih) < 0.001 ? 'bg-surface-soft text-ink-secondary border border-divider' : ($selisih < 0 ? 'bg-success-bg text-success border border-[#BBF7D0]' : 'bg-warning-bg text-warning border border-[#FDE68A]') }}">
                                            Selisih Komponen: {{ $tanda }}{{ number_format($selisih, 2, ',', '.') }} poin
                                        </span>
                                    </div>

                                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
                                        <!-- Sisi Kiri: Komponen Hasil Saya (Asli) -->
                                        <div class="p-4 rounded-[12px] bg-surface-soft/80 border border-border-subtle space-y-2">
                                            <div class="flex items-center justify-between gap-2">
                                                <span class="text-[12px] font-heading font-bold uppercase tracking-wider text-ink-secondary">
                                                    Hasil Saya (Asli)
                                                </span>
                                                <span class="font-heading font-bold text-[15px] text-ink tabular-nums">
                                                    {{ number_format($komp['poin_asli'], 2, ',', '.') }} poin
                                                </span>
                                            </div>
                                            <div class="text-[13px] text-ink">
                                                <span class="text-ink-secondary">Masukan tercatat:</span>
                                                <strong>{{ $komp['masukan_asli'] }}</strong>
                                                <span class="text-ink-secondary">({{ $komp['kategori_asli'] }})</span>
                                            </div>
                                            <p class="text-[12px] text-ink-secondary leading-relaxed">
                                                <strong class="text-ink">Alasan Asli:</strong> {{ $komp['alasan_asli'] }}
                                            </p>
                                        </div>

                                        <!-- Sisi Kanan: Komponen Hasil Simulasi What-If -->
                                        <div class="p-4 rounded-[12px] {{ $komp['apakah_diubah'] ? 'bg-highlight-light/55 border border-[#FDE68A]' : 'bg-white border border-border-glass' }} space-y-2">
                                            <div class="flex items-center justify-between gap-2">
                                                <span class="text-[12px] font-heading font-bold uppercase tracking-wider text-sekunder">
                                                    Hasil Simulasi What-If
                                                </span>
                                                <span class="font-heading font-bold text-[15px] text-sekunder tabular-nums">
                                                    {{ number_format($komp['poin_what_if'], 2, ',', '.') }} poin
                                                </span>
                                            </div>
                                            <div class="text-[13px] text-ink">
                                                <span class="text-ink-secondary">Masukan simulasi:</span>
                                                <strong>{{ $komp['masukan_what_if'] }}</strong>
                                                <span class="text-ink-secondary">({{ $komp['kategori_what_if'] }})</span>
                                            </div>
                                            <p class="text-[12px] text-ink-secondary leading-relaxed">
                                                <strong class="text-ink">Langkah Hitung:</strong> {{ $komp['langkah_what_if'] }}
                                            </p>
                                            <p class="text-[13px] text-ink font-medium leading-relaxed">
                                                <strong class="text-sekunder">Alasan Simulasi:</strong> {{ $komp['alasan_what_if'] }}
                                            </p>
                                        </div>
                                    </div>
                                </article>
                            @endforeach
                        </div>
                    </div>

                    <!-- Langkah Lanjut Setelah Melihat Simulasi: Tautan ke Bertanya (KF-08) & Ajukan Keberatan (KF-07) -->
                    <div class="p-5 rounded-[14px] bg-white/85 border border-border-glass flex flex-col lg:flex-row lg:items-center justify-between gap-4">
                        <div class="space-y-1 max-w-2xl">
                            <h3 class="text-[16px] font-heading font-bold text-ink">
                                Langkah Lanjut Setelah Mempelajari Simulasi What-If
                            </h3>
                            <p class="text-[13px] text-ink-secondary leading-relaxed">
                                Simulasi di atas tidak mengubah data Anda dan tidak otomatis dikirim ke petugas. Bila Anda menemukan ketidaksesuaian pencatatan komponen atau mengalami perubahan kondisi ekonomi keluarga, gunakan jalur resmi berikut:
                            </p>
                        </div>
                        <div class="flex flex-wrap items-center gap-3 shrink-0">
                            <x-button :href="route('mahasiswa.pertanyaan.index')" variant="secondary">
                                Bertanya ke Petugas (KF-08)
                            </x-button>
                            <x-button :href="route('mahasiswa.keberatan.create')" variant="primary">
                                Ajukan Keberatan (KF-07)
                            </x-button>
                        </div>
                    </div>
                </div>
            </section>
        @endif

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
