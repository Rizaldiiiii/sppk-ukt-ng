@php
    $dataIsi = $konten ? json_decode($konten->isi, true) : null;
    $tahapan = $dataIsi['tahapan'] ?? [];
@endphp
<x-layouts.guest title="Alur Penentuan Golongan UKT" :showScrollProgress="true">
    <div class="space-y-10">
        <!-- Header Halaman -->
        <section class="neu-card p-6 md:p-8 relative overflow-hidden">
            <div class="accent-bar absolute top-0 inset-x-0" aria-hidden="true"></div>
            <div class="max-w-3xl space-y-3">
                <span class="inline-block px-3 py-1 rounded-[8px] bg-[#F5E8E9] text-[#550b14] font-heading font-bold text-[12px]">
                    Informasi Publik • Tahapan Proses
                </span>
                <h1 class="text-[28px] md:text-[32px] font-heading font-extrabold text-[#1C1917]">
                    {{ $konten?->judul ?? 'Alur Penentuan Golongan UKT dan Hubungan Data dengan Hasil' }}
                </h1>
                <p class="text-[16px] text-[#57534E] leading-relaxed">
                    {{ $konten?->ringkasan ?? 'Penjelasan tahapan dari pengisian data sampai penetapan hasil serta bagaimana setiap data masukan membentuk komponen penilaian.' }}
                </p>
                @if($konten?->diperbarui_pada)
                    <p class="text-[12px] text-[#78716C] pt-1">
                        Terakhir diperbarui: {{ $konten->diperbarui_pada->locale('id')->translatedFormat('d M Y') }}
                    </p>
                @endif
            </div>
        </section>

        <!-- Tahapan Langkah demi Langkah -->
        <section class="space-y-6" aria-labelledby="heading-tahapan">
            <h2 id="heading-tahapan" class="text-[26px] font-heading font-bold text-[#1C1917]">
                Tahapan Proses dari Pengisian Data hingga Penetapan
            </h2>

            @if($tahapan !== [])
                <div class="space-y-5">
                    @foreach($tahapan as $langkah)
                        <article class="neu-card p-6 md:p-7 flex flex-col md:flex-row gap-5 items-start" data-reveal="up">
                            <div class="w-12 h-12 rounded-[10px] bg-[#550b14] text-[#ffffff] font-heading font-extrabold text-[18px] flex items-center justify-center shrink-0 shadow-[3px_3px_6px_#D9D3CD,-3px_-3px_6px_#FFFFFF]">
                                {{ $langkah['nomor'] ?? $loop->iteration }}
                            </div>
                            <div class="space-y-2.5 flex-1">
                                <h3 class="text-[21px] font-heading font-bold text-[#1C1917]">
                                    {{ $langkah['judul'] }}
                                </h3>
                                <p class="text-[14px] text-[#1C1917] leading-relaxed">
                                    {{ $langkah['deskripsi'] }}
                                </p>
                                @if(!empty($langkah['hubungan_data']))
                                    <div class="p-4 rounded-[10px] bg-[#F5E8E9]/70 border border-[#E2DFDC] text-[13px] text-[#57534E]">
                                        <strong class="text-[#550b14] font-heading">Hubungan Data → Hasil:</strong>
                                        {{ $langkah['hubungan_data'] }}
                                    </div>
                                @endif
                            </div>
                        </article>
                    @endforeach
                </div>
            @else
                <div class="neu-card p-6 text-[14px] text-[#1C1917] whitespace-pre-line leading-relaxed">
                    {{ $konten?->isi }}
                </div>
            @endif
        </section>

        <!-- Pemetaan Hubungan Data Masukan ke Komponen Penilaian -->
        <section class="space-y-4" aria-labelledby="heading-hubungan">
            <div>
                <h2 id="heading-hubungan" class="text-[26px] font-heading font-bold text-[#1C1917]">
                    Bagaimana Data Masukan Membentuk Komponen Penilaian
                </h2>
                <p class="text-[14px] text-[#57534E]">
                    Setiap baris pada tabel hasil mahasiswa berasal dari faktor masukan di bawah ini. Istilah yang dipakai konsisten di seluruh halaman sistem.
                </p>
            </div>

            <!-- Tabel Desktop -->
            <div class="hidden md:block">
                <x-table>
                    <thead>
                        <tr class="border-b border-[#DDD9D6] bg-[#F3F2F0]">
                            <th scope="col" class="px-6 py-4 font-heading font-semibold text-[#1C1917]">Faktor / Data Masukan</th>
                            <th scope="col" class="px-6 py-4 font-heading font-semibold text-[#1C1917]">Apa yang Diukur</th>
                            <th scope="col" class="px-6 py-4 font-heading font-semibold text-[#1C1917]">Hubungan dengan Komponen Hasil</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-[#DDD9D6]">
                        @foreach($faktorAktif as $faktor)
                            <tr class="hover:bg-[#F1EFED] transition-colors">
                                <td class="px-6 py-4 font-heading font-bold text-[#1C1917] align-top">
                                    {{ $faktor->nama }}
                                </td>
                                <td class="px-6 py-4 text-[#57534E] align-top">
                                    {{ $faktor->penjelasan_singkat }}
                                </td>
                                <td class="px-6 py-4 text-[#1C1917] align-top">
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
                    <div class="neu-card p-5 space-y-2">
                        <h3 class="font-heading font-bold text-[16px] text-[#1C1917]">{{ $faktor->nama }}</h3>
                        <p class="text-[13px] text-[#57534E]">{{ $faktor->penjelasan_singkat }}</p>
                        <p class="text-[13px] text-[#1C1917] pt-2 border-t border-[#DDD9D6]">
                            <strong class="text-[#550b14]">Pengaruh ke Hasil:</strong> {{ $faktor->pengaruh_umum }}
                        </p>
                    </div>
                @endforeach
            </div>
        </section>
    </div>
</x-layouts.guest>

