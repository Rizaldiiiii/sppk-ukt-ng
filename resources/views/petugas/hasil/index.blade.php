<x-layouts.app
    title="Entri & Kelola Hasil UKT Mahasiswa"
    subtitle="Masukkan hasil penetapan resmi kampus atau hitung simulasi baru dengan versi parameter aktif"
>
    <div x-data="{ mode: 'resmi' }" class="glass-card glass-card-glow p-6 md:p-8 space-y-6">
        <div class="flex flex-wrap items-center justify-between gap-4 border-b border-divider/80 pb-4">
            <div>
                <h2 class="text-[21px] font-heading font-bold text-ink">
                    Formulir Pencatatan Hasil &amp; Rincian Komponen
                </h2>
                <p class="text-[13px] text-ink-secondary">
                    Setiap hasil yang disimpan langsung membuat rincian komponen melalui RuleEngine dan mengirim pemberitahuan.
                </p>
            </div>
            <div class="flex gap-2">
                <button
                    type="button"
                    @click="mode = 'resmi'"
                    :class="mode === 'resmi' ? 'bg-sekunder-light/90 text-sekunder font-bold border border-sekunder shadow-glass-xs' : 'bg-white/75 text-ink-secondary border border-border-glass hover:bg-glass-hover'"
                    class="px-4 py-2.5 min-h-[44px] rounded-[10px] font-heading text-[13px] transition-colors"
                >
                    Entri Hasil Resmi Kampus
                </button>
                <button
                    type="button"
                    @click="mode = 'simulasi'"
                    :class="mode === 'simulasi' ? 'bg-sekunder-light/90 text-sekunder font-bold border border-sekunder shadow-glass-xs' : 'bg-white/75 text-ink-secondary border border-border-glass hover:bg-glass-hover'"
                    class="px-4 py-2.5 min-h-[44px] rounded-[10px] font-heading text-[13px] transition-colors"
                >
                    Hitung Hasil Simulasi Baru ({{ $versiAktif?->versi }})
                </button>
            </div>
        </div>

        <!-- Form Entri Hasil Resmi -->
        <form x-show="mode === 'resmi'" method="POST" action="{{ route('petugas.hasil.store-resmi') }}" class="space-y-5" data-prevent-double-submit>
            @csrf
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label for="resmi_mahasiswa_id" class="block text-[13px] font-medium text-ink mb-1">Pilih Mahasiswa</label>
                    <select id="resmi_mahasiswa_id" name="mahasiswa_id" required class="glass-input w-full px-3.5 py-2.5 text-[14px]">
                        @foreach($daftarMahasiswa as $m)
                            <option
                                value="{{ $m->id }}"
                                @selected((string) old('mahasiswa_id', $selectedMahasiswaId ?? '') === (string) $m->id)
                            >
                                {{ $m->nim }} — {{ $m->user?->nama }} ({{ $m->program_studi }})
                            </option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label for="resmi_golongan_id" class="block text-[13px] font-medium text-ink mb-1">Golongan UKT Resmi</label>
                    <select id="resmi_golongan_id" name="golongan_id" required class="glass-input w-full px-3.5 py-2.5 text-[14px]">
                        @foreach($daftarGolongan as $g)
                            <option value="{{ $g->id }}" @selected((string) old('golongan_id') === (string) $g->id)>{{ $g->nama }} ({{ $g->formatRentang() }})</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label for="resmi_nominal" class="block text-[13px] font-medium text-ink mb-1">Nominal Resmi / Semester (Rp)</label>
                    <input
                        id="resmi_nominal"
                        name="nominal"
                        type="number"
                        value="{{ old('nominal') }}"
                        placeholder="Contoh: 2500000"
                        min="0"
                        required
                        class="glass-input w-full px-3.5 py-2.5 text-[14px] tabular-nums"
                    >
                </div>
                <div>
                    <label for="resmi_tanggal" class="block text-[13px] font-medium text-ink mb-1">Tanggal Penetapan</label>
                    <input id="resmi_tanggal" name="tanggal_penetapan" type="date" value="{{ old('tanggal_penetapan', now()->toDateString()) }}" required class="glass-input w-full px-3.5 py-2.5 text-[14px]">
                </div>
                <div class="md:col-span-2">
                    <label for="resmi_sumber" class="block text-[13px] font-medium text-ink mb-1">Sumber SK / Ketetapan Resmi Kampus</label>
                    <input
                        id="resmi_sumber"
                        name="sumber_penetapan"
                        type="text"
                        value="{{ old('sumber_penetapan') }}"
                        placeholder="Contoh: SK Penetapan UKT Rektor UNG"
                        required
                        class="glass-input w-full px-3.5 py-2.5 text-[14px]"
                    >
                </div>
            </div>

            <div class="pt-2 border-t border-divider/80">
                <h3 class="font-heading font-bold text-[15px] text-ink mb-3">
                    Nilai Masukan Komponen (untuk Pembentukan Tabel Rincian &amp; Kalimat Alasan)
                </h3>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    @foreach($faktorAktif as $f)
                        <div>
                            <label class="block text-[13px] font-medium text-ink mb-1">{{ $f->nama }}</label>
                            <input
                                name="masukan[{{ $f->kode }}]"
                                type="text"
                                required
                                value="{{ old('masukan.' . $f->kode) }}"
                                placeholder="{{ match($f->kode) { 'penghasilan_gabungan' => 'Contoh: 4000000', 'jumlah_tanggungan' => 'Contoh: 3', 'daya_listrik' => 'Contoh: 1300', 'status_tempat_tinggal' => 'Contoh: Milik sendiri sederhana', default => 'Contoh: 1 kendaraan roda dua' } }}"
                                class="glass-input w-full px-3.5 py-2 text-[14px]"
                            >
                        </div>
                    @endforeach
                </div>
            </div>

            <div class="flex justify-end">
                <x-button type="submit" variant="primary">Simpan Hasil Penetapan Resmi</x-button>
            </div>
        </form>

        <!-- Form Hitung Hasil Simulasi -->
        <form x-cloak x-show="mode === 'simulasi'" method="POST" action="{{ route('petugas.hasil.store-simulasi') }}" class="space-y-5" data-prevent-double-submit>
            @csrf
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label for="simulasi_mahasiswa_id" class="block text-[13px] font-medium text-ink mb-1">Pilih Mahasiswa</label>
                    <select id="simulasi_mahasiswa_id" name="mahasiswa_id" required class="glass-input w-full px-3.5 py-2.5 text-[14px]">
                        @foreach($daftarMahasiswa as $m)
                            <option
                                value="{{ $m->id }}"
                                @selected((string) old('mahasiswa_id', $selectedMahasiswaId ?? '') === (string) $m->id)
                            >
                                {{ $m->nim }} — {{ $m->user?->nama }} ({{ $m->program_studi }})
                            </option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label for="simulasi_catatan" class="block text-[13px] font-medium text-ink mb-1">Catatan Simulasi</label>
                    <input
                        id="simulasi_catatan"
                        name="catatan"
                        type="text"
                        value="{{ old('catatan') }}"
                        placeholder="Simulasi menggunakan versi parameter aktif ({{ $versiAktif?->versi }})"
                        class="glass-input w-full px-3.5 py-2.5 text-[14px]"
                    >
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                @foreach($faktorAktif as $f)
                    <div>
                        <label class="block text-[13px] font-medium text-ink mb-1">{{ $f->nama }}</label>
                        <input
                            name="masukan[{{ $f->kode }}]"
                            type="text"
                            required
                            value="{{ old('masukan.' . $f->kode) }}"
                            placeholder="{{ match($f->kode) { 'penghasilan_gabungan' => 'Contoh: 2400000', 'jumlah_tanggungan' => 'Contoh: 4', 'daya_listrik' => 'Contoh: 900', 'status_tempat_tinggal' => 'Contoh: Menumpang / Kontrak', default => 'Contoh: 1 kendaraan roda dua' } }}"
                            class="glass-input w-full px-3.5 py-2 text-[14px]"
                        >
                    </div>
                @endforeach
            </div>

            <div class="flex justify-end">
                <x-button type="submit" variant="primary">Hitung &amp; Simpan Hasil Simulasi</x-button>
            </div>
        </form>
    </div>

    @if(!empty($hasilTerpilih))
        <!-- Panel Rincian Hasil Terpilih (Tampilan Sama Seperti Dilihat Mahasiswa) -->
        <section id="rincian-hasil-terpilih" class="glass-card glass-card-glow p-6 md:p-8 relative overflow-hidden space-y-6">
            <div class="accent-bar absolute top-0 inset-x-0" aria-hidden="true"></div>

            <div class="flex flex-wrap items-start justify-between gap-4 border-b border-divider/80 pb-4">
                <div class="space-y-1">
                    <div class="flex flex-wrap items-center gap-2">
                        <x-badge-status
                            :status="$hasilTerpilih->sumber"
                            :label="$hasilTerpilih->isResmi() ? 'Hasil Penetapan Resmi Kampus' : 'Hasil Perhitungan Simulasi'"
                        />
                        <span class="text-[12px] text-ink-secondary">
                            Ditetapkan {{ $hasilTerpilih->tanggal_penetapan?->locale('id')->translatedFormat('d F Y') ?? '-' }}
                            @if($hasilTerpilih->versiParameter)
                                • Versi Parameter {{ $hasilTerpilih->versiParameter->versi }}
                            @endif
                        </span>
                    </div>
                    <h2 class="text-[21px] font-heading font-bold text-ink">
                        Rincian Hasil UKT #{{ $hasilTerpilih->id }} — {{ $hasilTerpilih->mahasiswa?->user?->nama }} (NIM {{ $hasilTerpilih->mahasiswa?->nim }})
                    </h2>
                    <p class="text-[13px] text-ink-secondary">
                        {{ $hasilTerpilih->golongan?->nama ?? '-' }} • {{ $hasilTerpilih->formatNominal() }} / Semester • Total Poin Komponen: {{ number_format((float) $hasilTerpilih->skor_total, 2, ',', '.') }} / 5,00
                    </p>
                </div>

                <x-button :href="route('petugas.hasil.index')" variant="secondary" class="text-[12px] px-3.5 py-2 min-h-[38px]">
                    Tutup Rincian
                </x-button>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                @foreach($hasilTerpilih->rincianPenilaian as $rincian)
                    <div class="p-4 rounded-[12px] bg-white/85 border border-border-glass space-y-1.5">
                        <div class="flex items-center justify-between gap-2">
                            <span class="font-heading font-bold text-[14px] text-ink">
                                {{ $rincian->faktor?->nama ?? '-' }}
                            </span>
                            <span class="px-2.5 py-0.5 rounded-[6px] bg-sekunder-light text-sekunder font-heading font-bold text-[12px] tabular-nums">
                                Skor {{ number_format((float) $rincian->nilai_komponen, 2, ',', '.') }}
                            </span>
                        </div>
                        <div class="text-[13px] text-ink">
                            <span class="text-ink-secondary">Nilai masukan:</span>
                            <strong>{{ $rincian->nilai_masukan }}</strong>
                        </div>
                        <div class="text-[12px] font-mono text-ink-secondary">
                            {{ $rincian->langkah_perhitungan }}
                        </div>
                        <p class="text-[12px] text-ink-secondary leading-relaxed">
                            {{ $rincian->kalimat_alasan }}
                        </p>
                    </div>
                @endforeach
            </div>
        </section>
    @endif

    <!-- Tabel Daftar Hasil UKT Tercatat -->
    <section class="space-y-4">
        <div class="flex flex-wrap items-center justify-between gap-2">
            <h2 class="text-[21px] font-heading font-bold text-ink">
                Daftar Hasil UKT Mahasiswa Tercatat
            </h2>
            <span class="text-[12px] text-ink-secondary">
                Klik baris atau tombol rincian untuk melihat uraian komponen seperti yang dilihat mahasiswa
            </span>
        </div>

        <div class="hidden md:block">
            <x-table>
                <thead>
                    <tr class="border-b border-divider bg-surface-soft/90">
                        <th scope="col" class="px-6 py-4 font-heading font-semibold text-ink">Mahasiswa</th>
                        <th scope="col" class="px-6 py-4 font-heading font-semibold text-ink">Sumber</th>
                        <th scope="col" class="px-6 py-4 font-heading font-semibold text-ink">Golongan / Referensi</th>
                        <th scope="col" class="px-6 py-4 font-heading font-semibold text-ink text-right">Nominal</th>
                        <th scope="col" class="px-6 py-4 font-heading font-semibold text-ink text-right">Skor Komponen</th>
                        <th scope="col" class="px-6 py-4 font-heading font-semibold text-ink">Versi Parameter</th>
                        <th scope="col" class="px-6 py-4 font-heading font-semibold text-ink text-right">Rincian</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-divider">
                    @foreach($daftarHasil as $h)
                        <tr class="hover:bg-surface-soft/80">
                            <td class="px-6 py-4">
                                <a
                                    href="{{ route('petugas.hasil.index', ['lihat' => $h->id]) }}#rincian-hasil-terpilih"
                                    class="font-heading font-bold text-ink hover:text-sekunder hover:underline"
                                >
                                    {{ $h->mahasiswa?->user?->nama }}
                                </a>
                                <div class="text-[12px] text-ink-secondary">NIM {{ $h->mahasiswa?->nim }} • {{ $h->mahasiswa?->program_studi }}</div>
                            </td>
                            <td class="px-6 py-4">
                                <x-badge-status :status="$h->sumber" :label="$h->isResmi() ? 'Resmi' : 'Simulasi'" />
                            </td>
                            <td class="px-6 py-4 font-medium text-ink">
                                {{ $h->golongan?->nama ?? '-' }}
                            </td>
                            <td class="px-6 py-4 text-right tabular-nums font-bold text-sekunder">
                                {{ $h->formatNominal() }}
                            </td>
                            <td class="px-6 py-4 text-right tabular-nums text-ink">
                                {{ number_format((float) $h->skor_total, 2, ',', '.') }}
                            </td>
                            <td class="px-6 py-4 text-[13px] text-ink-secondary">
                                {{ $h->versiParameter?->versi ?? '-' }}
                            </td>
                            <td class="px-6 py-4 text-right whitespace-nowrap">
                                <a
                                    href="{{ route('petugas.hasil.index', ['lihat' => $h->id]) }}#rincian-hasil-terpilih"
                                    class="inline-flex items-center gap-1 px-3 py-1.5 rounded-[8px] bg-white/85 border border-border-glass text-[12px] font-heading font-semibold text-sekunder hover:bg-sekunder-light/70"
                                >
                                    Lihat Rincian &rarr;
                                </a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </x-table>
        </div>

        <!-- Kartu Ringkas Mobile (<768px) -->
        <div class="md:hidden space-y-3">
            @foreach($daftarHasil as $h)
                <article class="glass-card p-4 space-y-3">
                    <div class="flex items-start justify-between gap-2">
                        <div>
                            <div class="font-heading font-bold text-[14px] text-ink">
                                {{ $h->mahasiswa?->user?->nama }}
                            </div>
                            <div class="text-[12px] text-ink-secondary">
                                NIM {{ $h->mahasiswa?->nim }} • {{ $h->mahasiswa?->program_studi }}
                            </div>
                        </div>
                        <x-badge-status :status="$h->sumber" :label="$h->isResmi() ? 'Resmi' : 'Simulasi'" />
                    </div>
                    <div class="grid grid-cols-2 gap-2 text-[12px] pt-2 border-t border-divider/60">
                        <div>
                            <span class="text-ink-muted">Golongan:</span>
                            <div class="font-heading font-semibold text-ink">{{ $h->golongan?->nama ?? '-' }}</div>
                        </div>
                        <div class="text-right">
                            <span class="text-ink-muted">Nominal:</span>
                            <div class="font-heading font-bold text-sekunder tabular-nums">{{ $h->formatNominal() }}</div>
                        </div>
                        <div>
                            <span class="text-ink-muted">Skor Total:</span>
                            <span class="font-semibold text-ink tabular-nums">{{ number_format((float) $h->skor_total, 2, ',', '.') }}</span>
                        </div>
                        <div class="text-right">
                            <span class="text-ink-muted">Versi:</span>
                            <span class="text-ink-secondary">{{ $h->versiParameter?->versi ?? '-' }}</span>
                        </div>
                    </div>
                    <div class="pt-1">
                        <a
                            href="{{ route('petugas.hasil.index', ['lihat' => $h->id]) }}#rincian-hasil-terpilih"
                            class="inline-flex items-center justify-center w-full px-3.5 py-2 rounded-[10px] bg-white/85 border border-border-glass text-[12px] font-heading font-semibold text-sekunder"
                        >
                            Lihat Rincian Hasil &rarr;
                        </a>
                    </div>
                </article>
            @endforeach
        </div>

        <div class="pt-2">
            {{ $daftarHasil->links() }}
        </div>
    </section>
</x-layouts.app>
