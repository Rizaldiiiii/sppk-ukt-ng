<x-layouts.guest title="Daftar Faktor Penentu UKT" :showScrollProgress="true">
    <div class="space-y-10">
        <!-- Header -->
        <section class="neu-card p-6 md:p-8 relative overflow-hidden">
            <div class="accent-bar absolute top-0 inset-x-0" aria-hidden="true"></div>
            <div class="max-w-3xl space-y-3">
                <span class="inline-block px-3 py-1 rounded-[8px] bg-[#EDE6E3] text-[#6B574F] font-heading font-bold text-[12px]">
                    Informasi Publik • Variabel Penilaian
                </span>
                <h1 class="text-[28px] md:text-[32px] font-heading font-extrabold text-[#1C1917]">
                    {{ $konten?->judul ?? 'Daftar Faktor dan Variabel Penentuan UKT' }}
                </h1>
                <p class="text-[16px] text-[#57534E] leading-relaxed">
                    {{ $konten?->ringkasan ?? 'Daftar lengkap faktor atau variabel yang digunakan dalam model penjelasan penentuan UKT beserta penjelasan dan status konfirmasi resminya.' }}
                </p>
                <p class="text-[14px] text-[#57534E] leading-relaxed">
                    {{ $konten?->isi }}
                </p>
            </div>
        </section>

        <!-- Tabel & Kartu Daftar Faktor -->
        <section class="space-y-4" aria-labelledby="heading-daftar-faktor">
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-3">
                <h2 id="heading-daftar-faktor" class="text-[26px] font-heading font-bold text-[#1C1917]">
                    Komponen Variabel yang Diperhitungkan ({{ $daftarFaktor->count() }} Faktor)
                </h2>
                <div class="flex flex-wrap items-center gap-2 text-[12px]">
                    <x-badge-status status="terkonfirmasi" label="Terkonfirmasi resmi" />
                    <x-badge-status status="menunggu_konfirmasi" label="Menunggu konfirmasi resmi" />
                </div>
            </div>

            <!-- Tabel Desktop (>=768px) -->
            <div class="hidden md:block">
                <x-table>
                    <thead>
                        <tr class="border-b border-[#DDD9D6] bg-[#F3F2F0]">
                            <th scope="col" class="px-6 py-4 font-heading font-semibold text-[#1C1917] w-16">No</th>
                            <th scope="col" class="px-6 py-4 font-heading font-semibold text-[#1C1917]">Nama Faktor / Variabel</th>
                            <th scope="col" class="px-6 py-4 font-heading font-semibold text-[#1C1917]">Penjelasan Singkat</th>
                            <th scope="col" class="px-6 py-4 font-heading font-semibold text-[#1C1917]">Pengaruh Umum terhadap Hasil</th>
                            <th scope="col" class="px-6 py-4 font-heading font-semibold text-[#1C1917]">Status Sumber</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-[#DDD9D6]">
                        @foreach($daftarFaktor as $faktor)
                            <tr class="hover:bg-[#F1EFED] transition-colors">
                                <td class="px-6 py-5 font-heading font-bold text-[#57534E] align-top">
                                    {{ $faktor->urutan }}
                                </td>
                                <td class="px-6 py-5 align-top">
                                    <div class="font-heading font-bold text-[15px] text-[#1C1917]">
                                        {{ $faktor->nama }}
                                    </div>
                                    <div class="text-[12px] text-[#78716C] mt-0.5">
                                        Kode variabel: {{ $faktor->kode }}
                                    </div>
                                </td>
                                <td class="px-6 py-5 text-[14px] text-[#1C1917] leading-relaxed align-top">
                                    {{ $faktor->penjelasan_singkat }}
                                </td>
                                <td class="px-6 py-5 text-[14px] text-[#57534E] leading-relaxed align-top">
                                    {{ $faktor->pengaruh_umum ?? 'Mempengaruhi nilai komponen penilaian sesuai rentang masukan yang tercatat.' }}
                                </td>
                                <td class="px-6 py-5 align-top whitespace-nowrap">
                                    <x-badge-status
                                        :status="$faktor->status_konfirmasi"
                                        :label="$faktor->labelStatusKonfirmasi()"
                                    />
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </x-table>
            </div>

            <!-- Kartu Mobile (<768px) -->
            <div class="md:hidden space-y-4">
                @foreach($daftarFaktor as $faktor)
                    <article class="neu-card p-5 space-y-3">
                        <div class="flex items-start justify-between gap-2">
                            <h3 class="font-heading font-bold text-[16px] text-[#1C1917]">
                                {{ $faktor->urutan }}. {{ $faktor->nama }}
                            </h3>
                            <x-badge-status
                                :status="$faktor->status_konfirmasi"
                                :label="$faktor->labelStatusKonfirmasi()"
                            />
                        </div>
                        <p class="text-[14px] text-[#1C1917] leading-relaxed">
                            {{ $faktor->penjelasan_singkat }}
                        </p>
                        <div class="pt-2 border-t border-[#DDD9D6] text-[13px] text-[#57534E]">
                            <strong class="text-[#1C1917]">Pengaruh umum:</strong>
                            {{ $faktor->pengaruh_umum }}
                        </div>
                    </article>
                @endforeach
            </div>
        </section>
    </div>
</x-layouts.guest>

