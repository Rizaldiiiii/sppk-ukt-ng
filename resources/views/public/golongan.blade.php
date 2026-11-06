<x-layouts.guest title="Rentang Nominal Tiap Golongan UKT" :showScrollProgress="true">
    <div class="space-y-10">
        <!-- Header -->
        <section class="glass-card glass-card-glow p-6 md:p-8 relative overflow-hidden">
            <div class="accent-bar absolute top-0 inset-x-0" aria-hidden="true"></div>
            <div class="max-w-3xl space-y-3">
                <span class="inline-block px-3 py-1 rounded-[8px] bg-sekunder-light text-sekunder font-heading font-bold text-[12px]">
                    Informasi Publik • Rentang Tarif per Semester
                </span>
                <h1 class="text-[28px] md:text-[32px] font-heading font-extrabold text-ink">
                    Rentang Nominal Tiap Golongan UKT
                </h1>
                <p class="text-[16px] text-ink-secondary leading-relaxed">
                    Daftar seluruh kelompok golongan Uang Kuliah Tunggal (UKT) yang berlaku beserta batas nominal minimum dan maksimum per semester, sumber ketetapan, dan tanggal mulai berlakunya.
                </p>
                @if($terakhirDiperbarui)
                    <p class="text-[12px] text-ink-muted pt-1">
                        Tanggal pembaruan terakhir daftar golongan: {{ \Illuminate\Support\Carbon::parse($terakhirDiperbarui)->locale('id')->translatedFormat('d M Y') }}
                    </p>
                @endif
            </div>
        </section>

        <!-- Tabel Desktop (>=768px) -->
        <section class="space-y-4" aria-labelledby="heading-tabel-golongan">
            <h2 id="heading-tabel-golongan" class="text-[26px] font-heading font-bold text-ink">
                Tabel Golongan UKT yang Berlaku ({{ $daftarGolongan->count() }} Golongan)
            </h2>

            <div class="hidden md:block">
                <x-table>
                    <thead>
                        <tr class="border-b border-divider bg-surface-soft/90">
                            <th scope="col" class="px-6 py-4 font-heading font-semibold text-ink">Kode</th>
                            <th scope="col" class="px-6 py-4 font-heading font-semibold text-ink">Golongan UKT</th>
                            <th scope="col" class="px-6 py-4 font-heading font-semibold text-ink text-right">Batas Bawah / Semester</th>
                            <th scope="col" class="px-6 py-4 font-heading font-semibold text-ink text-right">Batas Atas / Semester</th>
                            <th scope="col" class="px-6 py-4 font-heading font-semibold text-ink">Sumber &amp; Berlaku Mulai</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-divider">
                        @foreach($daftarGolongan as $golongan)
                            <tr class="hover:bg-surface-soft/80 transition-colors h-[60px]">
                                <td class="px-6 py-4 font-heading font-bold text-sekunder">
                                    {{ $golongan->kode }}
                                </td>
                                <td class="px-6 py-4 font-heading font-bold text-ink">
                                    {{ $golongan->nama }}
                                </td>
                                <td class="px-6 py-4 text-right tabular-nums font-medium text-ink">
                                    Rp {{ number_format($golongan->nominal_min, 0, ',', '.') }}
                                </td>
                                <td class="px-6 py-4 text-right tabular-nums font-bold text-sekunder">
                                    Rp {{ number_format($golongan->nominal_max, 0, ',', '.') }}
                                </td>
                                <td class="px-6 py-4 text-[13px] text-ink-secondary">
                                    <div class="font-medium text-ink">{{ $golongan->sumber }}</div>
                                    <div class="text-[12px] text-ink-muted">
                                        Berlaku mulai {{ $golongan->berlaku_mulai?->locale('id')->translatedFormat('d M Y') }}
                                        @if($golongan->berlaku_sampai)
                                            s.d. {{ $golongan->berlaku_sampai->locale('id')->translatedFormat('d M Y') }}
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </x-table>
            </div>

            <!-- Kartu Mobile (<768px) -->
            <div class="md:hidden space-y-4">
                @foreach($daftarGolongan as $golongan)
                    <article class="glass-card glass-card-light p-5 space-y-3">
                        <div class="flex items-center justify-between">
                            <span class="font-heading font-bold text-[16px] text-ink">{{ $golongan->nama }}</span>
                            <span class="px-2.5 py-1 rounded-[8px] bg-sekunder-light text-sekunder font-heading font-bold text-xs">{{ $golongan->kode }}</span>
                        </div>
                        <div class="p-3.5 rounded-[10px] bg-white/80 border border-border-glass flex items-center justify-between">
                            <span class="text-[13px] text-ink-secondary">Rentang / Semester</span>
                            <span class="font-heading font-bold text-sekunder tabular-nums">{{ $golongan->formatRentang() }}</span>
                        </div>
                        <div class="text-[12px] text-ink-secondary">
                            <div><strong>Sumber:</strong> {{ $golongan->sumber }}</div>
                            <div><strong>Berlaku mulai:</strong> {{ $golongan->berlaku_mulai?->locale('id')->translatedFormat('d M Y') }}</div>
                        </div>
                    </article>
                @endforeach
            </div>
        </section>
    </div>
</x-layouts.guest>
