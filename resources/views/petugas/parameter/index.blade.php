<x-layouts.app
    title="Kelola Parameter Penilaian Berversi (KF-05)"
    subtitle="Perubahan parameter selalu menerbitkan versi baru agar hasil perhitungan lama tetap dapat ditelusuri (KN-04 & KN-06)"
>
    <!-- Versi Parameter yang Sedang Aktif -->
    @if($versiAktif)
        <section class="glass-card p-6 md:p-8 space-y-6 relative overflow-hidden">
            <div class="accent-bar absolute top-0 inset-x-0" aria-hidden="true"></div>

            <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                <div>
                    <x-badge-status status="aktif" label="Versi Parameter Aktif Saat Ini" />
                    <h2 class="text-[26px] font-heading font-extrabold text-ink mt-2">
                        Versi {{ $versiAktif->versi }}
                    </h2>
                    <p class="text-[14px] text-ink-secondary">
                        Berlaku mulai {{ $versiAktif->berlaku_mulai?->locale('id')->translatedFormat('d M Y') }} • {{ $versiAktif->catatan }}
                    </p>
                </div>
            </div>

            <!-- Form Terbitkan Versi Parameter Baru -->
            <form method="POST" action="{{ route('petugas.parameter.store') }}" class="space-y-6 pt-4 border-t border-divider/80" data-prevent-double-submit>
                @csrf

                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label for="versi" class="block text-[14px] font-medium text-ink mb-1.5">
                            Kode Versi Baru
                        </label>
                        <input
                            id="versi"
                            name="versi"
                            type="text"
                            value="{{ old('versi', 'v1.1-simulasi') }}"
                            required
                            class="glass-input w-full px-4 py-2.5 text-[14px]"
                        >
                        @error('versi')
                            <p class="mt-1 text-[13px] text-error" role="alert">! {{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="berlaku_mulai" class="block text-[14px] font-medium text-ink mb-1.5">
                            Tanggal Mulai Berlaku
                        </label>
                        <input
                            id="berlaku_mulai"
                            name="berlaku_mulai"
                            type="date"
                            value="{{ old('berlaku_mulai', now()->toDateString()) }}"
                            required
                            class="glass-input w-full px-4 py-2.5 text-[14px]"
                        >
                    </div>

                    <div>
                        <label for="catatan" class="block text-[14px] font-medium text-ink mb-1.5">
                            Catatan Perubahan Versi
                        </label>
                        <input
                            id="catatan"
                            name="catatan"
                            type="text"
                            value="{{ old('catatan', 'Penyesuaian porsi bobot komponen simulasi') }}"
                            required
                            class="glass-input w-full px-4 py-2.5 text-[14px]"
                        >
                    </div>
                </div>

                <!-- Tabel Bobot & Aturan per Faktor -->
                <x-table>
                    <thead>
                        <tr class="border-b border-divider bg-surface-soft/90">
                            <th scope="col" class="px-6 py-4 font-heading font-semibold text-ink">Faktor Penilaian</th>
                            <th scope="col" class="px-6 py-4 font-heading font-semibold text-ink">Tipe &amp; Ringkasan Ambang/Kategori</th>
                            <th scope="col" class="px-6 py-4 font-heading font-semibold text-ink w-44 text-right">Bobot Versi Baru (%)</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-divider">
                        @foreach($versiAktif->parameterPenilaian as $param)
                            <tr class="hover:bg-surface-soft/80 align-top">
                                <td class="px-6 py-4">
                                    <div class="font-heading font-bold text-ink">{{ $param->faktor?->nama }}</div>
                                    <div class="text-[12px] text-ink-muted">Kode: {{ $param->faktor?->kode }}</div>
                                </td>
                                <td class="px-6 py-4 text-[13px] text-ink-secondary">
                                    <div class="font-medium text-ink mb-1">{{ $param->aturan['penjelasan_aturan'] ?? '-' }}</div>
                                    <ul class="space-y-1 text-[12px]">
                                        @foreach(($param->aturan['rentang'] ?? []) as $r)
                                            <li>• {{ $r['label'] ?? ($r['nilai'] ?? '-') }} → Skor {{ $r['skor'] ?? '-' }}</li>
                                        @endforeach
                                    </ul>
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <label for="bobot_{{ $param->faktor_id }}" class="sr-only">Bobot {{ $param->faktor?->nama }}</label>
                                    <input
                                        id="bobot_{{ $param->faktor_id }}"
                                        name="bobot[{{ $param->faktor_id }}]"
                                        type="number"
                                        step="1"
                                        min="1"
                                        max="100"
                                        value="{{ $param->aturan['bobot'] ?? 20 }}"
                                        required
                                        class="glass-input w-28 px-3 py-2 text-right font-heading font-bold tabular-nums text-[14px]"
                                    >
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </x-table>

                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                    <p class="text-[13px] text-ink-secondary">
                        <strong>Jaminan Keterlacakan:</strong> Menerbitkan versi baru tidak akan mengubah nilai pada hasil mahasiswa yang sudah dihitung dengan versi <strong>{{ $versiAktif->versi }}</strong>.
                    </p>
                    <x-button type="submit" variant="primary">
                        Terbitkan Versi Parameter Baru
                    </x-button>
                </div>
            </form>
        </section>
    @endif

    <!-- Riwayat Versi Parameter -->
    <section class="space-y-4">
        <h2 class="text-[21px] font-heading font-bold text-ink">
            Riwayat Versi Parameter Penilaian
        </h2>

        <x-table>
            <thead>
                <tr class="border-b border-divider bg-surface-soft/90">
                    <th scope="col" class="px-6 py-4 font-heading font-semibold text-ink">Versi</th>
                    <th scope="col" class="px-6 py-4 font-heading font-semibold text-ink">Mulai Berlaku</th>
                    <th scope="col" class="px-6 py-4 font-heading font-semibold text-ink">Catatan</th>
                    <th scope="col" class="px-6 py-4 font-heading font-semibold text-ink text-right">Hasil Terkait</th>
                    <th scope="col" class="px-6 py-4 font-heading font-semibold text-ink">Status</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-divider">
                @foreach($riwayatVersi as $v)
                    <tr class="hover:bg-surface-soft/80">
                        <td class="px-6 py-4 font-heading font-bold text-ink">{{ $v->versi }}</td>
                        <td class="px-6 py-4 text-ink-secondary">{{ $v->berlaku_mulai?->locale('id')->translatedFormat('d M Y') }}</td>
                        <td class="px-6 py-4 text-ink">{{ $v->catatan }}</td>
                        <td class="px-6 py-4 text-right tabular-nums font-bold text-sekunder">{{ $v->hasil_ukt_count }} hasil</td>
                        <td class="px-6 py-4">
                            @if($v->aktif)
                                <x-badge-status status="aktif" label="Aktif" />
                            @else
                                <span class="px-2.5 py-1 rounded-[8px] bg-surface-soft text-ink-secondary text-xs font-semibold">Arsip Versi Lama</span>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </x-table>

        <div class="pt-2">
            {{ $riwayatVersi->links() }}
        </div>
    </section>
</x-layouts.app>
