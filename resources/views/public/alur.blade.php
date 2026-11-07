@php
    $dataIsi = $konten ? json_decode($konten->isi, true) : null;
    $tahapan = $dataIsi['tahapan'] ?? [];
@endphp
<x-layouts.guest title="Alur Penentuan Golongan UKT" :showScrollProgress="true">
    <div class="space-y-10">
        <!-- Header Halaman -->
        <section class="glass-card glass-card-glow p-6 md:p-8 relative overflow-hidden">
            <div class="accent-bar absolute top-0 inset-x-0" aria-hidden="true"></div>
            <div class="max-w-3xl space-y-3">
                <span class="inline-block px-3 py-1 rounded-[8px] bg-sekunder-light text-sekunder font-heading font-bold text-[12px]">
                    Informasi Publik • Tahapan Proses
                </span>
                <h1 class="text-[28px] md:text-[32px] font-heading font-extrabold text-ink">
                    {{ $konten?->judul ?? 'Alur Penentuan Golongan UKT dan Hubungan Data dengan Hasil' }}
                </h1>
                <p class="text-[16px] text-ink-secondary leading-relaxed">
                    {{ $konten?->ringkasan ?? 'Penjelasan tahapan dari pengisian data sampai penetapan hasil serta bagaimana setiap data masukan membentuk komponen penilaian.' }}
                </p>
                @if($konten?->diperbarui_pada)
                    <p class="text-[12px] text-ink-muted pt-1">
                        Terakhir diperbarui: {{ $konten->diperbarui_pada->locale('id')->translatedFormat('d M Y') }}
                    </p>
                @endif
                <div class="pt-2">
                    <x-button :href="route('publik.panduan')" variant="secondary">
                        Lihat Panduan Visual Tahapan (KF-10) →
                    </x-button>
                </div>
            </div>
        </section>

        <!-- Tahapan Langkah demi Langkah -->
        <section class="space-y-6" aria-labelledby="heading-tahapan">
            <h2 id="heading-tahapan" class="text-[26px] font-heading font-bold text-ink">
                Tahapan Proses dari Pengisian Data hingga Penetapan
            </h2>

            @if($tahapan !== [])
                <div class="space-y-5">
                    @foreach($tahapan as $langkah)
                        <article class="glass-card p-6 md:p-7 flex flex-col md:flex-row gap-5 items-start" data-reveal="up">
                            <div class="w-12 h-12 rounded-[10px] bg-sekunder text-[#ffffff] font-heading font-extrabold text-[18px] flex items-center justify-center shrink-0 shadow-glass-xs">
                                {{ $langkah['nomor'] ?? $loop->iteration }}
                            </div>
                            <div class="space-y-2.5 flex-1">
                                <h3 class="text-[21px] font-heading font-bold text-ink">
                                    {{ $langkah['judul'] }}
                                </h3>
                                <p class="text-[14px] text-ink leading-relaxed">
                                    {{ $langkah['deskripsi'] }}
                                </p>
                                @if(!empty($langkah['hubungan_data']))
                                    <div class="p-4 rounded-[10px] bg-sekunder-light/70 border border-border-glass text-[13px] text-ink-secondary">
                                        <strong class="text-sekunder font-heading">Hubungan Data → Hasil:</strong>
                                        {{ $langkah['hubungan_data'] }}
                                    </div>
                                @endif
                            </div>
                        </article>
                    @endforeach
                </div>
            @else
                <div class="glass-card p-6 text-[14px] text-ink whitespace-pre-line leading-relaxed">
                    {{ $konten?->isi }}
                </div>
            @endif
        </section>

        <!-- Pemetaan Hubungan Data Masukan ke Komponen Penilaian -->
        <section class="space-y-4" aria-labelledby="heading-hubungan">
            <div>
                <h2 id="heading-hubungan" class="text-[26px] font-heading font-bold text-ink">
                    Bagaimana Data Masukan Membentuk Komponen Penilaian
                </h2>
                <p class="text-[14px] text-ink-secondary">
                    Setiap baris pada tabel hasil mahasiswa berasal dari faktor masukan di bawah ini. Istilah yang dipakai konsisten di seluruh halaman sistem.
                </p>
            </div>

            <!-- Tabel Desktop -->
            <div class="hidden md:block">
                <x-table>
                    <thead>
                        <tr class="border-b border-divider bg-surface-soft/90">
                            <th scope="col" class="px-6 py-4 font-heading font-semibold text-ink">Faktor / Data Masukan</th>
                            <th scope="col" class="px-6 py-4 font-heading font-semibold text-ink">Apa yang Diukur</th>
                            <th scope="col" class="px-6 py-4 font-heading font-semibold text-ink">Hubungan dengan Komponen Hasil</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-divider">
                        @foreach($faktorAktif as $faktor)
                            <tr class="hover:bg-surface-soft/80 transition-colors">
                                <td class="px-6 py-4 font-heading font-bold text-ink align-top">
                                    {{ $faktor->nama }}
                                </td>
                                <td class="px-6 py-4 text-ink-secondary align-top">
                                    {{ $faktor->penjelasan_singkat }}
                                </td>
                                <td class="px-6 py-4 text-ink align-top">
                                    {{ $faktor->pengaruh_umum ?? 'Menghasilkan nilai kontribusi komponen sesuai kategori masukan yang tercatat.' }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </x-table>
            </div>

            <!-- Kartu Mobile -->
            <div class="md:hidden space-y-4">
                @foreach($faktorAktif as $faktor)
                    <div class="glass-card glass-card-light p-5 space-y-2">
                        <h3 class="font-heading font-bold text-[16px] text-ink">{{ $faktor->nama }}</h3>
                        <p class="text-[13px] text-ink-secondary">{{ $faktor->penjelasan_singkat }}</p>
                        <p class="text-[13px] text-ink pt-2 border-t border-divider/80">
                            <strong class="text-sekunder">Pengaruh ke Hasil:</strong> {{ $faktor->pengaruh_umum }}
                        </p>
                    </div>
                @endforeach
            </div>
        </section>
    </div>
</x-layouts.guest>
