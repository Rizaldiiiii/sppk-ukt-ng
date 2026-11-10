<x-layouts.guest title="Daftar Faktor Penentu UKT" :showScrollProgress="true">
    <div class="space-y-10">
        <!-- Header -->
        <section class="glass-card glass-card-glow p-6 md:p-8 relative overflow-hidden">
            <div class="accent-bar absolute top-0 inset-x-0" aria-hidden="true"></div>
            <div class="max-w-3xl space-y-3">
                <span class="inline-block px-3 py-1 rounded-[8px] bg-highlight-light text-highlight-dark font-heading font-bold text-[12px]">
                    Informasi Publik • Variabel Penilaian
                </span>
                <h1 class="text-[28px] md:text-[32px] font-heading font-extrabold text-ink">
                    {{ $konten?->judul ?? 'Daftar Faktor dan Variabel Penentuan UKT' }}
                </h1>
                <p class="text-[16px] text-ink-secondary leading-relaxed">
                    {{ $konten?->ringkasan ?? 'Daftar lengkap faktor atau variabel yang digunakan dalam model penjelasan penentuan UKT beserta penjelasan dan status konfirmasi resminya.' }}
                </p>
                <p class="text-[14px] text-ink-secondary leading-relaxed">
                    {{ $konten?->isi }}
                </p>
            </div>
        </section>

        <!-- Tabel & Kartu Daftar Faktor -->
        <section class="space-y-4" aria-labelledby="heading-daftar-faktor">
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-3">
                <h2 id="heading-daftar-faktor" class="text-[26px] font-heading font-bold text-ink">
                    Komponen Variabel yang Diperhitungkan ({{ $daftarFaktor->count() }} Faktor)
                </h2>
            </div>

            <!-- Tabel Desktop (>=768px) -->
            <div class="hidden md:block">
                <x-table>
                    <thead>
                        <tr class="border-b border-divider bg-surface-soft/90">
                            <th scope="col" class="px-6 py-4 font-heading font-semibold text-ink w-16">No</th>
                            <th scope="col" class="px-6 py-4 font-heading font-semibold text-ink">Nama Faktor / Variabel</th>
                            <th scope="col" class="px-6 py-4 font-heading font-semibold text-ink">Penjelasan Singkat</th>
                            <th scope="col" class="px-6 py-4 font-heading font-semibold text-ink">Pengaruh Umum terhadap Hasil</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-divider">
                        @foreach($daftarFaktor as $faktor)
                            <tr class="hover:bg-surface-soft/80 transition-colors">
                                <td class="px-6 py-5 font-heading font-bold text-ink-secondary align-top">
                                    {{ $faktor->urutan }}
                                </td>
                                <td class="px-6 py-5 align-top">
                                    <div class="font-heading font-bold text-[15px] text-ink">
                                        {{ $faktor->nama }}
                                    </div>
                                    <div class="text-[12px] text-ink-muted mt-0.5">
                                        Kode variabel: {{ $faktor->kode }}
                                    </div>
                                </td>
                                <td class="px-6 py-5 text-[14px] text-ink leading-relaxed align-top">
                                    {{ $faktor->penjelasan_singkat }}
                                </td>
                                <td class="px-6 py-5 text-[14px] text-ink-secondary leading-relaxed align-top">
                                    {{ $faktor->pengaruh_umum ?? 'Mempengaruhi nilai komponen penilaian sesuai rentang masukan yang tercatat.' }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </x-table>
            </div>

            <!-- Kartu Mobile (<768px) -->
            <div class="md:hidden space-y-4">
                @foreach($daftarFaktor as $faktor)
                    <article class="glass-card glass-card-light p-5 space-y-3">
                        <div class="flex items-start justify-between gap-2">
                            <h3 class="font-heading font-bold text-[16px] text-ink">
                                {{ $faktor->urutan }}. {{ $faktor->nama }}
                            </h3>
                        </div>
                        <p class="text-[14px] text-ink leading-relaxed">
                            {{ $faktor->penjelasan_singkat }}
                        </p>
                        <div class="pt-2 border-t border-divider/80 text-[13px] text-ink-secondary">
                            <strong class="text-ink">Pengaruh umum:</strong>
                            {{ $faktor->pengaruh_umum }}
                        </div>
                    </article>
                @endforeach
            </div>
        </section>
    </div>
</x-layouts.guest>
