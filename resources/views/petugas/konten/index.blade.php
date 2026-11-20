<x-layouts.app
    title="Kelola Konten Publik, Faktor, dan Rentang Golongan"
    subtitle="Pembaruan langsung tercermin di halaman publik tanpa login dan tercatat di riwayat (KF-02, KF-03, KF-06, KF-14)"
>
    <div x-data="{ tab: 'faktor' }" class="space-y-6">
        <!-- Navigasi Tab (Glassmorphism.md §5.9) -->
        <div role="tablist" aria-label="Kategori Konten Publik" class="flex flex-wrap gap-2 border-b border-divider/80 pb-3">
            <button
                type="button"
                role="tab"
                :aria-selected="tab === 'faktor'"
                @click="tab = 'faktor'"
                :class="tab === 'faktor' ? 'bg-sekunder-light/90 text-sekunder border-b-2 border-sekunder font-bold shadow-glass-xs' : 'text-ink-secondary hover:bg-glass-hover font-semibold'"
                class="px-5 py-2.5 min-h-[44px] rounded-[10px] font-heading text-[14px] transition-colors"
            >
                Daftar Faktor Penilaian (KF-03)
            </button>
            <button
                type="button"
                role="tab"
                :aria-selected="tab === 'golongan'"
                @click="tab = 'golongan'"
                :class="tab === 'golongan' ? 'bg-sekunder-light/90 text-sekunder border-b-2 border-sekunder font-bold shadow-glass-xs' : 'text-ink-secondary hover:bg-glass-hover font-semibold'"
                class="px-5 py-2.5 min-h-[44px] rounded-[10px] font-heading text-[14px] transition-colors"
            >
                Rentang Nominal Golongan (KF-14)
            </button>
            <button
                type="button"
                role="tab"
                :aria-selected="tab === 'halaman'"
                @click="tab = 'halaman'"
                :class="tab === 'halaman' ? 'bg-sekunder-light/90 text-sekunder border-b-2 border-sekunder font-bold shadow-glass-xs' : 'text-ink-secondary hover:bg-glass-hover font-semibold'"
                class="px-5 py-2.5 min-h-[44px] rounded-[10px] font-heading text-[14px] transition-colors"
            >
                Teks Alur, Prosedur, Kontak &amp; Petunjuk (KF-02 / KF-06 / KF-09)
            </button>
        </div>

        <!-- TAB 1: Daftar Faktor Penilaian (KF-03) -->
        <section x-show="tab === 'faktor'" class="space-y-6">
            <div class="glass-card p-6 space-y-4">
                <h2 class="text-[21px] font-heading font-bold text-ink">
                    Tambah Faktor Penilaian Baru
                </h2>
                <form method="POST" action="{{ route('petugas.faktor.store') }}" class="grid grid-cols-1 md:grid-cols-2 gap-4" data-prevent-double-submit>
                    @csrf
                    <div>
                        <label for="faktor_kode" class="block text-[14px] font-medium text-ink mb-1">Kode Variabel</label>
                        <input id="faktor_kode" name="kode" type="text" required placeholder="contoh: tagihan_air" class="glass-input w-full px-4 py-2.5 text-[14px]">
                    </div>
                    <div>
                        <label for="faktor_nama" class="block text-[14px] font-medium text-ink mb-1">Nama Faktor Penilaian</label>
                        <input id="faktor_nama" name="nama" type="text" required placeholder="Nama faktor yang mudah dipahami" class="glass-input w-full px-4 py-2.5 text-[14px]">
                    </div>
                    <div class="md:col-span-2">
                        <label for="faktor_penjelasan" class="block text-[14px] font-medium text-ink mb-1">Penjelasan Singkat</label>
                        <textarea id="faktor_penjelasan" name="penjelasan_singkat" rows="2" required class="glass-input w-full px-4 py-2.5 text-[14px]"></textarea>
                    </div>
                    <div class="md:col-span-2">
                        <label for="faktor_pengaruh" class="block text-[14px] font-medium text-ink mb-1">Pengaruh Umum terhadap Hasil</label>
                        <textarea id="faktor_pengaruh" name="pengaruh_umum" rows="2" class="glass-input w-full px-4 py-2.5 text-[14px]"></textarea>
                    </div>
                    <div>
                        <label for="faktor_urutan" class="block text-[14px] font-medium text-ink mb-1">Urutan Tampil</label>
                        <input id="faktor_urutan" name="urutan" type="number" value="6" min="1" required class="glass-input w-full px-4 py-2.5 text-[14px]">
                    </div>
                    <div class="md:col-span-2 pt-2">
                        <x-button type="submit" variant="primary">Simpan Faktor Baru</x-button>
                    </div>
                </form>
            </div>

            <div class="space-y-4">
                <h2 class="text-[21px] font-heading font-bold text-ink">
                    Faktor Penilaian Terdaftar
                </h2>
                @foreach($daftarFaktor as $faktor)
                    <form method="POST" action="{{ route('petugas.faktor.update', $faktor) }}" class="glass-card p-6 space-y-4" data-prevent-double-submit>
                        @csrf
                        @method('PUT')
                        <div class="flex flex-wrap items-center justify-between gap-2">
                            <span class="font-heading font-bold text-[16px] text-ink">
                                #{{ $faktor->urutan }} — {{ $faktor->nama }} ({{ $faktor->kode }})
                            </span>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-[13px] font-medium text-ink mb-1">Nama Faktor</label>
                                <input name="nama" type="text" value="{{ $faktor->nama }}" required class="glass-input w-full px-3.5 py-2 text-[14px]">
                            </div>
                            <div>
                                <label class="block text-[13px] font-medium text-ink mb-1">Urutan</label>
                                <input name="urutan" type="number" value="{{ $faktor->urutan }}" required class="glass-input w-full px-3.5 py-2 text-[14px]">
                            </div>
                            <div class="md:col-span-2">
                                <label class="block text-[13px] font-medium text-ink mb-1">Penjelasan Singkat</label>
                                <textarea name="penjelasan_singkat" rows="2" required class="glass-input w-full px-3.5 py-2 text-[14px]">{{ $faktor->penjelasan_singkat }}</textarea>
                            </div>
                            <div class="md:col-span-2">
                                <label class="block text-[13px] font-medium text-ink mb-1">Pengaruh Umum</label>
                                <textarea name="pengaruh_umum" rows="2" class="glass-input w-full px-3.5 py-2 text-[14px]">{{ $faktor->pengaruh_umum }}</textarea>
                            </div>
                        </div>
                        <div class="flex justify-end">
                            <x-button type="submit" variant="secondary">Perbarui Faktor</x-button>
                        </div>
                    </form>
                @endforeach

                <div class="pt-2">
                    {{ $daftarFaktor->links() }}
                </div>
            </div>
        </section>

        <!-- TAB 2: Rentang Nominal Golongan UKT (KF-14) -->
        <section x-cloak x-show="tab === 'golongan'" class="space-y-6">
            <div class="glass-card p-6 space-y-4">
                <h2 class="text-[21px] font-heading font-bold text-ink">
                    Tambah Golongan UKT Baru
                </h2>
                <form method="POST" action="{{ route('petugas.golongan.store') }}" class="grid grid-cols-1 md:grid-cols-3 gap-4" data-prevent-double-submit>
                    @csrf
                    <div>
                        <label class="block text-[13px] font-medium text-ink mb-1">Kode Golongan</label>
                        <input name="kode" type="text" required placeholder="GOL-IX" class="glass-input w-full px-3.5 py-2.5 text-[14px]">
                    </div>
                    <div>
                        <label class="block text-[13px] font-medium text-ink mb-1">Nama Golongan</label>
                        <input name="nama" type="text" required placeholder="Golongan IX" class="glass-input w-full px-3.5 py-2.5 text-[14px]">
                    </div>
                    <div>
                        <label class="block text-[13px] font-medium text-ink mb-1">Mulai Berlaku</label>
                        <input name="berlaku_mulai" type="date" value="{{ now()->toDateString() }}" required class="glass-input w-full px-3.5 py-2.5 text-[14px]">
                    </div>
                    <div>
                        <label class="block text-[13px] font-medium text-ink mb-1">Nominal Minimum (Rp)</label>
                        <input name="nominal_min" type="number" min="0" required class="glass-input w-full px-3.5 py-2.5 text-[14px]">
                    </div>
                    <div>
                        <label class="block text-[13px] font-medium text-ink mb-1">Nominal Maksimum (Rp)</label>
                        <input name="nominal_max" type="number" min="0" required class="glass-input w-full px-3.5 py-2.5 text-[14px]">
                    </div>
                    <div>
                        <label class="block text-[13px] font-medium text-ink mb-1">Sumber Ketetapan</label>
                        <input name="sumber" type="text" required placeholder="SK Rektor UNG..." class="glass-input w-full px-3.5 py-2.5 text-[14px]">
                    </div>
                    <div class="md:col-span-3">
                        <x-button type="submit" variant="primary">Simpan Golongan UKT</x-button>
                    </div>
                </form>
            </div>

            <div class="space-y-4">
                @foreach($daftarGolongan as $golongan)
                    <form method="POST" action="{{ route('petugas.golongan.update', $golongan) }}" class="glass-card p-6 grid grid-cols-1 md:grid-cols-5 gap-4 items-end" data-prevent-double-submit>
                        @csrf
                        @method('PUT')
                        <div>
                            <label class="block text-[13px] font-medium text-ink mb-1">Nama ({{ $golongan->kode }})</label>
                            <input name="nama" type="text" value="{{ $golongan->nama }}" required class="glass-input w-full px-3.5 py-2 text-[14px]">
                        </div>
                        <div>
                            <label class="block text-[13px] font-medium text-ink mb-1">Nominal Min (Rp)</label>
                            <input name="nominal_min" type="number" value="{{ $golongan->nominal_min }}" required class="glass-input w-full px-3.5 py-2 text-[14px] tabular-nums">
                        </div>
                        <div>
                            <label class="block text-[13px] font-medium text-ink mb-1">Nominal Max (Rp)</label>
                            <input name="nominal_max" type="number" value="{{ $golongan->nominal_max }}" required class="glass-input w-full px-3.5 py-2 text-[14px] tabular-nums">
                        </div>
                        <div>
                            <label class="block text-[13px] font-medium text-ink mb-1">Sumber &amp; Tanggal</label>
                            <input name="sumber" type="text" value="{{ $golongan->sumber }}" required class="glass-input w-full px-3.5 py-2 text-[14px]">
                            <input name="berlaku_mulai" type="hidden" value="{{ $golongan->berlaku_mulai?->format('Y-m-d') }}">
                        </div>
                        <div class="flex justify-end">
                            <x-button type="submit" variant="secondary">Simpan</x-button>
                        </div>
                    </form>
                @endforeach
            </div>
        </section>

        <!-- TAB 3: Halaman Konten Publik (Alur, Prosedur, Kontak, Petunjuk, Panduan Visual) -->
        <section x-cloak x-show="tab === 'halaman'" class="space-y-6">
            @foreach($daftarKonten as $konten)
                @if(in_array($konten->slug, ['faktor', 'panduan'], true))
                    @continue
                @elseif($konten->slug === 'alur')
                    @php
                        $decodedAlur = is_string($konten->isi) ? json_decode($konten->isi, true) : null;
                        $jsonValidAlur = is_array($decodedAlur)
                            && isset($decodedAlur['tahapan'])
                            && is_array($decodedAlur['tahapan'])
                            && count($decodedAlur['tahapan']) > 0;
                        $tahapanAwal = $jsonValidAlur
                            ? array_values(array_map(static fn ($item, $i) => [
                                'nomor' => $i + 1,
                                'judul' => (string) ($item['judul'] ?? ''),
                                'deskripsi' => (string) ($item['deskripsi'] ?? ''),
                                'hubungan_data' => (string) ($item['hubungan_data'] ?? ''),
                            ], $decodedAlur['tahapan'], array_keys($decodedAlur['tahapan'])))
                            : [];
                        $stateAwalAlur = [
                            'judul' => (string) $konten->judul,
                            'ringkasan' => (string) ($konten->ringkasan ?? ''),
                            'isi' => (string) ($konten->isi ?? ''),
                            'tahapan' => $tahapanAwal,
                        ];
                    @endphp
                    <div
                        x-data="{
                            editing: false,
                            pesanError: '',
                            initial: @js($stateAwalAlur),
                            form: @js($stateAwalAlur),
                            mulaiEdit() {
                                this.form = JSON.parse(JSON.stringify(this.initial));
                                this.pesanError = '';
                                this.editing = true;
                            },
                            batal() {
                                this.form = JSON.parse(JSON.stringify(this.initial));
                                this.pesanError = '';
                                this.editing = false;
                            },
                            tambahTahapan() {
                                this.pesanError = '';
                                this.form.tahapan.push({
                                    nomor: this.form.tahapan.length + 1,
                                    judul: '',
                                    deskripsi: '',
                                    hubungan_data: ''
                                });
                            },
                            hapusTahapan(idx) {
                                if (this.form.tahapan.length <= 1) {
                                    this.pesanError = 'Minimal harus ada 1 tahapan pada alur penentuan UKT. Tahapan terakhir tidak dapat dihapus.';
                                    return;
                                }
                                this.pesanError = '';
                                this.form.tahapan.splice(idx, 1);
                            }
                        }"
                        class="glass-card p-6 space-y-5"
                    >
                        <div class="flex flex-wrap items-center justify-between gap-3 border-b border-divider/80 pb-4">
                            <div>
                                <h3 class="text-[18px] font-heading font-bold text-ink">
                                    Halaman: /{{ $konten->slug }}
                                </h3>
                                <span class="text-[12px] text-ink-muted">
                                    Diperbarui: {{ $konten->diperbarui_pada?->locale('id')->translatedFormat('d M Y H:i') ?? '-' }}
                                </span>
                            </div>
                            <div x-show="!editing">
                                <x-button type="button" variant="secondary" @click="mulaiEdit()">
                                    Mulai Mengedit
                                </x-button>
                            </div>
                        </div>

                        <!-- Mode Lihat (Read-Only) -->
                        <div x-show="!editing" class="space-y-4">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div class="p-3.5 rounded-[10px] bg-white/75 border border-border-glass">
                                    <div class="text-[11px] font-heading font-semibold uppercase tracking-wider text-ink-muted">Judul Halaman</div>
                                    <div class="text-[14px] font-heading font-bold text-ink mt-0.5">{{ $konten->judul }}</div>
                                </div>
                                <div class="p-3.5 rounded-[10px] bg-white/75 border border-border-glass">
                                    <div class="text-[11px] font-heading font-semibold uppercase tracking-wider text-ink-muted">Ringkasan</div>
                                    <div class="text-[13px] text-ink-secondary mt-0.5">{{ $konten->ringkasan ?: '-' }}</div>
                                </div>
                            </div>

                            @if($jsonValidAlur)
                                <div class="space-y-3">
                                    <div class="text-[13px] font-heading font-bold text-ink">
                                        Daftar Tahapan Alur ({{ count($tahapanAwal) }} Tahapan)
                                    </div>
                                    <div class="grid grid-cols-1 gap-3">
                                        @foreach($tahapanAwal as $tahapItem)
                                            <div class="p-4 rounded-[12px] bg-white/80 border border-border-glass flex items-start gap-3.5">
                                                <span class="w-8 h-8 rounded-[8px] bg-sekunder text-white font-heading font-bold text-xs flex items-center justify-center shrink-0">
                                                    {{ $tahapItem['nomor'] }}
                                                </span>
                                                <div class="space-y-1 flex-1 min-w-0">
                                                    <div class="font-heading font-bold text-[14px] text-ink">
                                                        {{ $tahapItem['judul'] }}
                                                    </div>
                                                    <p class="text-[13px] text-ink-secondary leading-relaxed">
                                                        {{ $tahapItem['deskripsi'] }}
                                                    </p>
                                                    @if($tahapItem['hubungan_data'] !== '')
                                                        <p class="text-[12px] text-sekunder">
                                                            <strong>Hubungan Data:</strong> {{ $tahapItem['hubungan_data'] }}
                                                        </p>
                                                    @endif
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @else
                                <div class="p-4 rounded-[12px] bg-warning-bg border border-[#FDE68A] text-[13px] text-warning">
                                    Isi tersimpan belum berformat JSON tahapan terstruktur. Klik <strong>Mulai Mengedit</strong> untuk menyunting teksnya.
                                </div>
                                <div class="p-4 rounded-[12px] bg-white/80 border border-border-glass text-[13px] text-ink whitespace-pre-line">{{ $konten->isi }}</div>
                            @endif
                        </div>

                        <!-- Mode Edit -->
                        <form
                            x-cloak
                            x-show="editing"
                            method="POST"
                            action="{{ route('petugas.konten.update', $konten->slug) }}"
                            class="space-y-5"
                        >
                            @csrf
                            @method('PUT')
                            <div>
                                <label class="block text-[13px] font-medium text-ink mb-1">Judul Halaman</label>
                                <input name="judul" type="text" x-model="form.judul" required class="glass-input w-full px-4 py-2.5 text-[14px]">
                            </div>
                            <div>
                                <label class="block text-[13px] font-medium text-ink mb-1">Ringkasan</label>
                                <input name="ringkasan" type="text" x-model="form.ringkasan" class="glass-input w-full px-4 py-2.5 text-[14px]">
                            </div>

                            @if($jsonValidAlur)
                                <div class="space-y-4">
                                    <div class="flex flex-wrap items-center justify-between gap-2">
                                        <span class="text-[14px] font-heading font-bold text-ink">
                                            Tahapan Alur Penentuan UKT (<span x-text="form.tahapan.length"></span> Tahapan)
                                        </span>
                                        <x-button type="button" variant="secondary" @click="tambahTahapan()">
                                            + Tambah Tahapan
                                        </x-button>
                                    </div>

                                    <div
                                        x-show="pesanError !== ''"
                                        role="alert"
                                        class="p-3.5 rounded-[10px] bg-error-bg border border-[#FECACA] text-[13px] font-medium text-error"
                                        x-text="pesanError"
                                    ></div>

                                    <div class="space-y-4">
                                        <template x-for="(tahap, idx) in form.tahapan" :key="idx">
                                            <div class="p-4 rounded-[12px] bg-white/85 border border-border-glass space-y-3">
                                                <div class="flex items-center justify-between gap-2 border-b border-divider/70 pb-2.5">
                                                    <span class="inline-flex items-center gap-2 font-heading font-bold text-[14px] text-sekunder">
                                                        <span class="w-7 h-7 rounded-[8px] bg-sekunder text-white text-xs inline-flex items-center justify-center" x-text="idx + 1"></span>
                                                        <span>Tahapan #<span x-text="idx + 1"></span></span>
                                                    </span>
                                                    <x-button
                                                        type="button"
                                                        variant="danger"
                                                        class="text-[12px] px-3 py-1.5 min-h-[36px]"
                                                        @click="hapusTahapan(idx)"
                                                    >
                                                        Hapus
                                                    </x-button>
                                                </div>

                                                <div>
                                                    <label class="block text-[13px] font-medium text-ink mb-1">Judul Tahapan</label>
                                                    <input
                                                        type="text"
                                                        :name="'tahapan[' + idx + '][judul]'"
                                                        x-model="tahap.judul"
                                                        required
                                                        class="glass-input w-full px-3.5 py-2 text-[14px]"
                                                    >
                                                </div>

                                                <div>
                                                    <label class="block text-[13px] font-medium text-ink mb-1">Deskripsi</label>
                                                    <textarea
                                                        rows="2"
                                                        :name="'tahapan[' + idx + '][deskripsi]'"
                                                        x-model="tahap.deskripsi"
                                                        required
                                                        class="glass-input w-full px-3.5 py-2 text-[13px]"
                                                    ></textarea>
                                                </div>

                                                <div>
                                                    <label class="block text-[13px] font-medium text-ink mb-1">Hubungan Data</label>
                                                    <textarea
                                                        rows="2"
                                                        :name="'tahapan[' + idx + '][hubungan_data]'"
                                                        x-model="tahap.hubungan_data"
                                                        class="glass-input w-full px-3.5 py-2 text-[13px]"
                                                    ></textarea>
                                                </div>
                                            </div>
                                        </template>
                                    </div>
                                </div>
                            @else
                                <div role="alert" class="p-3.5 rounded-[10px] bg-warning-bg border border-[#FDE68A] text-[13px] text-warning">
                                    <strong>Peringatan:</strong> Isi tersimpan ternyata bukan JSON yang valid (data lama). Anda tetap dapat mengeditnya melalui kotak teks biasa di bawah ini.
                                </div>
                                <div>
                                    <label class="block text-[13px] font-medium text-ink mb-1">Isi Halaman (Teks Data Lama)</label>
                                    <textarea name="isi" rows="5" x-model="form.isi" required class="glass-input w-full px-4 py-2.5 text-[13px]">{{ $konten->isi }}</textarea>
                                </div>
                            @endif

                            <div class="flex flex-wrap items-center justify-end gap-2.5 pt-2 border-t border-divider/80">
                                <x-button type="button" variant="secondary" @click="batal()">
                                    Batal
                                </x-button>
                                <x-button type="submit" variant="primary">
                                    Simpan
                                </x-button>
                            </div>
                        </form>
                    </div>

                @elseif($konten->slug === 'prosedur-keberatan')
                    @php
                        $decodedProsedur = is_string($konten->isi) ? json_decode($konten->isi, true) : null;
                        $jsonValidProsedur = is_array($decodedProsedur)
                            && isset($decodedProsedur['tujuan_pengajuan'], $decodedProsedur['siapa_boleh_mengajukan'], $decodedProsedur['dokumen_diperlukan'], $decodedProsedur['tahapan_peninjauan'])
                            && is_array($decodedProsedur['siapa_boleh_mengajukan'])
                            && is_array($decodedProsedur['dokumen_diperlukan'])
                            && is_array($decodedProsedur['tahapan_peninjauan']);
                        $stateAwalProsedur = [
                            'judul' => (string) $konten->judul,
                            'ringkasan' => (string) ($konten->ringkasan ?? ''),
                            'isi' => (string) ($konten->isi ?? ''),
                            'tujuan_pengajuan' => $jsonValidProsedur ? (string) $decodedProsedur['tujuan_pengajuan'] : '',
                            'siapa_boleh_mengajukan' => $jsonValidProsedur ? array_values(array_map('strval', $decodedProsedur['siapa_boleh_mengajukan'])) : [],
                            'dokumen_diperlukan' => $jsonValidProsedur ? array_values(array_map('strval', $decodedProsedur['dokumen_diperlukan'])) : [],
                            'tahapan_peninjauan' => $jsonValidProsedur ? array_values(array_map('strval', $decodedProsedur['tahapan_peninjauan'])) : [],
                        ];
                    @endphp
                    <div
                        x-data="{
                            editing: false,
                            pesanError: '',
                            initial: @js($stateAwalProsedur),
                            form: @js($stateAwalProsedur),
                            mulaiEdit() {
                                this.form = JSON.parse(JSON.stringify(this.initial));
                                this.pesanError = '';
                                this.editing = true;
                            },
                            batal() {
                                this.form = JSON.parse(JSON.stringify(this.initial));
                                this.pesanError = '';
                                this.editing = false;
                            },
                            tambahButir(bagian) {
                                this.pesanError = '';
                                this.form[bagian].push('');
                            },
                            hapusButir(bagian, idx) {
                                if (this.form[bagian].length <= 1) {
                                    this.pesanError = 'Setiap daftar pada prosedur keberatan wajib memiliki minimal 1 butir.';
                                    return;
                                }
                                this.pesanError = '';
                                this.form[bagian].splice(idx, 1);
                            }
                        }"
                        class="glass-card p-6 space-y-5"
                    >
                        <div class="flex flex-wrap items-center justify-between gap-3 border-b border-divider/80 pb-4">
                            <div>
                                <h3 class="text-[18px] font-heading font-bold text-ink">
                                    Halaman: /{{ $konten->slug }}
                                </h3>
                                <span class="text-[12px] text-ink-muted">
                                    Diperbarui: {{ $konten->diperbarui_pada?->locale('id')->translatedFormat('d M Y H:i') ?? '-' }}
                                </span>
                            </div>
                            <div x-show="!editing">
                                <x-button type="button" variant="secondary" @click="mulaiEdit()">
                                    Mulai Mengedit
                                </x-button>
                            </div>
                        </div>

                        <!-- Mode Lihat (Read-Only) -->
                        <div x-show="!editing" class="space-y-4">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div class="p-3.5 rounded-[10px] bg-white/75 border border-border-glass">
                                    <div class="text-[11px] font-heading font-semibold uppercase tracking-wider text-ink-muted">Judul Halaman</div>
                                    <div class="text-[14px] font-heading font-bold text-ink mt-0.5">{{ $konten->judul }}</div>
                                </div>
                                <div class="p-3.5 rounded-[10px] bg-white/75 border border-border-glass">
                                    <div class="text-[11px] font-heading font-semibold uppercase tracking-wider text-ink-muted">Ringkasan</div>
                                    <div class="text-[13px] text-ink-secondary mt-0.5">{{ $konten->ringkasan ?: '-' }}</div>
                                </div>
                            </div>

                            @if($jsonValidProsedur)
                                <div class="p-4 rounded-[12px] bg-white/80 border border-border-glass space-y-1">
                                    <div class="text-[13px] font-heading font-bold text-ink">Tujuan Pengajuan</div>
                                    <p class="text-[13px] text-ink-secondary leading-relaxed">{{ $stateAwalProsedur['tujuan_pengajuan'] }}</p>
                                </div>

                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div class="p-4 rounded-[12px] bg-white/80 border border-border-glass space-y-2">
                                        <div class="text-[13px] font-heading font-bold text-ink">
                                            Siapa Boleh Mengajukan ({{ count($stateAwalProsedur['siapa_boleh_mengajukan']) }} Butir)
                                        </div>
                                        <ul class="space-y-1.5 text-[13px] text-ink-secondary list-disc pl-5">
                                            @foreach($stateAwalProsedur['siapa_boleh_mengajukan'] as $butir)
                                                <li>{{ $butir }}</li>
                                            @endforeach
                                        </ul>
                                    </div>

                                    <div class="p-4 rounded-[12px] bg-white/80 border border-border-glass space-y-2">
                                        <div class="text-[13px] font-heading font-bold text-ink">
                                            Dokumen Diperlukan ({{ count($stateAwalProsedur['dokumen_diperlukan']) }} Butir)
                                        </div>
                                        <ul class="space-y-1.5 text-[13px] text-ink-secondary list-disc pl-5">
                                            @foreach($stateAwalProsedur['dokumen_diperlukan'] as $butir)
                                                <li>{{ $butir }}</li>
                                            @endforeach
                                        </ul>
                                    </div>
                                </div>

                                <div class="p-4 rounded-[12px] bg-white/80 border border-border-glass space-y-2">
                                    <div class="text-[13px] font-heading font-bold text-ink">
                                        Tahapan Peninjauan ({{ count($stateAwalProsedur['tahapan_peninjauan']) }} Tahap)
                                    </div>
                                    <ol class="space-y-1.5 text-[13px] text-ink-secondary list-decimal pl-5">
                                        @foreach($stateAwalProsedur['tahapan_peninjauan'] as $butir)
                                            <li>{{ $butir }}</li>
                                        @endforeach
                                    </ol>
                                </div>
                            @else
                                <div class="p-4 rounded-[12px] bg-warning-bg border border-[#FDE68A] text-[13px] text-warning">
                                    Isi tersimpan belum berformat JSON terstruktur. Klik <strong>Mulai Mengedit</strong> untuk menyunting teksnya.
                                </div>
                                <div class="p-4 rounded-[12px] bg-white/80 border border-border-glass text-[13px] text-ink whitespace-pre-line">{{ $konten->isi }}</div>
                            @endif
                        </div>

                        <!-- Mode Edit -->
                        <form
                            x-cloak
                            x-show="editing"
                            method="POST"
                            action="{{ route('petugas.konten.update', $konten->slug) }}"
                            class="space-y-5"
                        >
                            @csrf
                            @method('PUT')
                            <div>
                                <label class="block text-[13px] font-medium text-ink mb-1">Judul Halaman</label>
                                <input name="judul" type="text" x-model="form.judul" required class="glass-input w-full px-4 py-2.5 text-[14px]">
                            </div>
                            <div>
                                <label class="block text-[13px] font-medium text-ink mb-1">Ringkasan</label>
                                <input name="ringkasan" type="text" x-model="form.ringkasan" class="glass-input w-full px-4 py-2.5 text-[14px]">
                            </div>

                            @if($jsonValidProsedur)
                                <div
                                    x-show="pesanError !== ''"
                                    role="alert"
                                    class="p-3.5 rounded-[10px] bg-error-bg border border-[#FECACA] text-[13px] font-medium text-error"
                                    x-text="pesanError"
                                ></div>

                                <!-- Bagian 1: Tujuan Pengajuan -->
                                <div class="p-4 rounded-[12px] bg-white/85 border border-border-glass space-y-2">
                                    <label class="block text-[14px] font-heading font-bold text-ink">1. Tujuan Pengajuan</label>
                                    <textarea
                                        name="tujuan_pengajuan"
                                        rows="2"
                                        x-model="form.tujuan_pengajuan"
                                        required
                                        class="glass-input w-full px-3.5 py-2 text-[13px]"
                                    ></textarea>
                                </div>

                                <!-- Bagian 2: Siapa Boleh Mengajukan -->
                                <div class="p-4 rounded-[12px] bg-white/85 border border-border-glass space-y-3">
                                    <div class="flex flex-wrap items-center justify-between gap-2">
                                        <span class="text-[14px] font-heading font-bold text-ink">
                                            2. Siapa Boleh Mengajukan (<span x-text="form.siapa_boleh_mengajukan.length"></span> Butir)
                                        </span>
                                        <x-button type="button" variant="secondary" class="text-[12px] px-3 py-1.5 min-h-[36px]" @click="tambahButir('siapa_boleh_mengajukan')">
                                            + Tambah Butir
                                        </x-button>
                                    </div>
                                    <div class="space-y-2.5">
                                        <template x-for="(butir, idx) in form.siapa_boleh_mengajukan" :key="idx">
                                            <div class="flex items-start gap-2">
                                                <span class="w-7 h-9 rounded-[8px] bg-sekunder-light text-sekunder font-heading font-bold text-xs inline-flex items-center justify-center shrink-0" x-text="idx + 1"></span>
                                                <input
                                                    type="text"
                                                    :name="'siapa_boleh_mengajukan[' + idx + ']'"
                                                    x-model="form.siapa_boleh_mengajukan[idx]"
                                                    required
                                                    class="glass-input flex-1 px-3.5 py-2 text-[13px]"
                                                >
                                                <x-button type="button" variant="danger" class="text-[12px] px-3 py-1.5 min-h-[36px] shrink-0" @click="hapusButir('siapa_boleh_mengajukan', idx)">
                                                    Hapus
                                                </x-button>
                                            </div>
                                        </template>
                                    </div>
                                </div>

                                <!-- Bagian 3: Dokumen Diperlukan -->
                                <div class="p-4 rounded-[12px] bg-white/85 border border-border-glass space-y-3">
                                    <div class="flex flex-wrap items-center justify-between gap-2">
                                        <span class="text-[14px] font-heading font-bold text-ink">
                                            3. Dokumen Diperlukan (<span x-text="form.dokumen_diperlukan.length"></span> Butir)
                                        </span>
                                        <x-button type="button" variant="secondary" class="text-[12px] px-3 py-1.5 min-h-[36px]" @click="tambahButir('dokumen_diperlukan')">
                                            + Tambah Butir
                                        </x-button>
                                    </div>
                                    <div class="space-y-2.5">
                                        <template x-for="(butir, idx) in form.dokumen_diperlukan" :key="idx">
                                            <div class="flex items-start gap-2">
                                                <span class="w-7 h-9 rounded-[8px] bg-highlight-light text-highlight-dark font-heading font-bold text-xs inline-flex items-center justify-center shrink-0" x-text="idx + 1"></span>
                                                <input
                                                    type="text"
                                                    :name="'dokumen_diperlukan[' + idx + ']'"
                                                    x-model="form.dokumen_diperlukan[idx]"
                                                    required
                                                    class="glass-input flex-1 px-3.5 py-2 text-[13px]"
                                                >
                                                <x-button type="button" variant="danger" class="text-[12px] px-3 py-1.5 min-h-[36px] shrink-0" @click="hapusButir('dokumen_diperlukan', idx)">
                                                    Hapus
                                                </x-button>
                                            </div>
                                        </template>
                                    </div>
                                </div>

                                <!-- Bagian 4: Tahapan Peninjauan -->
                                <div class="p-4 rounded-[12px] bg-white/85 border border-border-glass space-y-3">
                                    <div class="flex flex-wrap items-center justify-between gap-2">
                                        <span class="text-[14px] font-heading font-bold text-ink">
                                            4. Tahapan Peninjauan (<span x-text="form.tahapan_peninjauan.length"></span> Tahap)
                                        </span>
                                        <x-button type="button" variant="secondary" class="text-[12px] px-3 py-1.5 min-h-[36px]" @click="tambahButir('tahapan_peninjauan')">
                                            + Tambah Butir
                                        </x-button>
                                    </div>
                                    <div class="space-y-2.5">
                                        <template x-for="(butir, idx) in form.tahapan_peninjauan" :key="idx">
                                            <div class="flex items-start gap-2">
                                                <span class="w-7 h-9 rounded-[8px] bg-sekunder text-white font-heading font-bold text-xs inline-flex items-center justify-center shrink-0" x-text="idx + 1"></span>
                                                <textarea
                                                    rows="2"
                                                    :name="'tahapan_peninjauan[' + idx + ']'"
                                                    x-model="form.tahapan_peninjauan[idx]"
                                                    required
                                                    class="glass-input flex-1 px-3.5 py-2 text-[13px]"
                                                ></textarea>
                                                <x-button type="button" variant="danger" class="text-[12px] px-3 py-1.5 min-h-[36px] shrink-0" @click="hapusButir('tahapan_peninjauan', idx)">
                                                    Hapus
                                                </x-button>
                                            </div>
                                        </template>
                                    </div>
                                </div>
                            @else
                                <div role="alert" class="p-3.5 rounded-[10px] bg-warning-bg border border-[#FDE68A] text-[13px] text-warning">
                                    <strong>Peringatan:</strong> Isi tersimpan ternyata bukan JSON yang valid (data lama). Anda tetap dapat mengeditnya melalui kotak teks biasa di bawah ini.
                                </div>
                                <div>
                                    <label class="block text-[13px] font-medium text-ink mb-1">Isi Halaman (Teks Data Lama)</label>
                                    <textarea name="isi" rows="5" x-model="form.isi" required class="glass-input w-full px-4 py-2.5 text-[13px]">{{ $konten->isi }}</textarea>
                                </div>
                            @endif

                            <div class="flex flex-wrap items-center justify-end gap-2.5 pt-2 border-t border-divider/80">
                                <x-button type="button" variant="secondary" @click="batal()">
                                    Batal
                                </x-button>
                                <x-button type="submit" variant="primary">
                                    Simpan
                                </x-button>
                            </div>
                        </form>
                    </div>

                @elseif($konten->slug === 'petunjuk-pengisian')
                    @php
                        $decodedPetunjuk = is_string($konten->isi) ? json_decode($konten->isi, true) : null;
                        $defaultsPetunjuk = \App\Models\KontenPublik::defaultPetunjukPengisian();
                        $jsonValidPetunjuk = is_array($decodedPetunjuk);
                        $petunjukAwal = [];
                        if ($jsonValidPetunjuk) {
                            foreach ($defaultsPetunjuk as $kodeKolom => $defItem) {
                                $src = (isset($decodedPetunjuk[$kodeKolom]) && is_array($decodedPetunjuk[$kodeKolom]))
                                    ? $decodedPetunjuk[$kodeKolom]
                                    : [];
                                $petunjukAwal[$kodeKolom] = [
                                    'judul' => (string) ($src['judul'] ?? $defItem['judul']),
                                    'apa_diisi' => (string) ($src['apa_diisi'] ?? $defItem['apa_diisi']),
                                    'format' => (string) ($src['format'] ?? $defItem['format']),
                                    'contoh' => (string) ($src['contoh'] ?? $defItem['contoh']),
                                    'akibat' => (string) ($src['akibat'] ?? $defItem['akibat']),
                                ];
                            }
                        }
                        $stateAwalPetunjuk = [
                            'judul' => (string) $konten->judul,
                            'ringkasan' => (string) ($konten->ringkasan ?? ''),
                            'isi' => (string) ($konten->isi ?? ''),
                            'petunjuk' => $petunjukAwal,
                        ];
                    @endphp
                    <div
                        x-data="{
                            editing: false,
                            initial: @js($stateAwalPetunjuk),
                            form: @js($stateAwalPetunjuk),
                            mulaiEdit() {
                                this.form = JSON.parse(JSON.stringify(this.initial));
                                this.editing = true;
                            },
                            batal() {
                                this.form = JSON.parse(JSON.stringify(this.initial));
                                this.editing = false;
                            }
                        }"
                        class="glass-card p-6 space-y-5"
                    >
                        <div class="flex flex-wrap items-center justify-between gap-3 border-b border-divider/80 pb-4">
                            <div>
                                <h3 class="text-[18px] font-heading font-bold text-ink">
                                    Halaman: /{{ $konten->slug }}
                                </h3>
                                <span class="text-[12px] text-ink-muted">
                                    Diperbarui: {{ $konten->diperbarui_pada?->locale('id')->translatedFormat('d M Y H:i') ?? '-' }}
                                </span>
                            </div>
                            <div x-show="!editing">
                                <x-button type="button" variant="secondary" @click="mulaiEdit()">
                                    Mulai Mengedit
                                </x-button>
                            </div>
                        </div>

                        <!-- Mode Lihat (Read-Only) -->
                        <div x-show="!editing" class="space-y-4">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div class="p-3.5 rounded-[10px] bg-white/75 border border-border-glass">
                                    <div class="text-[11px] font-heading font-semibold uppercase tracking-wider text-ink-muted">Judul Halaman</div>
                                    <div class="text-[14px] font-heading font-bold text-ink mt-0.5">{{ $konten->judul }}</div>
                                </div>
                                <div class="p-3.5 rounded-[10px] bg-white/75 border border-border-glass">
                                    <div class="text-[11px] font-heading font-semibold uppercase tracking-wider text-ink-muted">Ringkasan</div>
                                    <div class="text-[13px] text-ink-secondary mt-0.5">{{ $konten->ringkasan ?: '-' }}</div>
                                </div>
                            </div>

                            @if($jsonValidPetunjuk)
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    @foreach($petunjukAwal as $kodeKolom => $itemKolom)
                                        <div class="p-4 rounded-[12px] bg-white/80 border border-border-glass space-y-2">
                                            <div class="flex items-center justify-between gap-2">
                                                <span class="font-heading font-bold text-[14px] text-ink">{{ $itemKolom['judul'] }}</span>
                                                <span class="px-2 py-0.5 rounded-[6px] bg-sekunder-light text-sekunder font-mono text-[11px]">{{ $kodeKolom }}</span>
                                            </div>
                                            <dl class="space-y-1 text-[12px] text-ink-secondary">
                                                <div><strong class="text-ink">Apa Diisi:</strong> {{ $itemKolom['apa_diisi'] }}</div>
                                                <div><strong class="text-ink">Format:</strong> {{ $itemKolom['format'] }}</div>
                                                <div><strong class="text-ink">Contoh:</strong> {{ $itemKolom['contoh'] }}</div>
                                                <div><strong class="text-ink">Akibat:</strong> {{ $itemKolom['akibat'] }}</div>
                                            </dl>
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <div class="p-4 rounded-[12px] bg-warning-bg border border-[#FDE68A] text-[13px] text-warning">
                                    Isi tersimpan belum berformat JSON petunjuk pengisian terstruktur. Klik <strong>Mulai Mengedit</strong> untuk menyunting teksnya.
                                </div>
                                <div class="p-4 rounded-[12px] bg-white/80 border border-border-glass text-[13px] text-ink whitespace-pre-line">{{ $konten->isi }}</div>
                            @endif
                        </div>

                        <!-- Mode Edit -->
                        <form
                            x-cloak
                            x-show="editing"
                            method="POST"
                            action="{{ route('petugas.konten.update', $konten->slug) }}"
                            class="space-y-5"
                        >
                            @csrf
                            @method('PUT')
                            <div>
                                <label class="block text-[13px] font-medium text-ink mb-1">Judul Halaman</label>
                                <input name="judul" type="text" x-model="form.judul" required class="glass-input w-full px-4 py-2.5 text-[14px]">
                            </div>
                            <div>
                                <label class="block text-[13px] font-medium text-ink mb-1">Ringkasan</label>
                                <input name="ringkasan" type="text" x-model="form.ringkasan" class="glass-input w-full px-4 py-2.5 text-[14px]">
                            </div>

                            @if($jsonValidPetunjuk)
                                <div class="space-y-4">
                                    <div class="text-[13px] text-ink-secondary">
                                        Kelima kunci kolom di bawah ini terikat langsung ke formulir keberatan dan pertanyaan mahasiswa (KF-09).
                                    </div>
                                    @foreach($petunjukAwal as $kodeKolom => $itemKolom)
                                        <div class="p-4 rounded-[12px] bg-white/85 border border-border-glass space-y-3">
                                            <div class="flex items-center justify-between gap-2 border-b border-divider/70 pb-2">
                                                <span class="font-heading font-bold text-[14px] text-sekunder">
                                                    Kolom Formulir: {{ $kodeKolom }}
                                                </span>
                                                <span class="px-2.5 py-0.5 rounded-[6px] bg-surface-soft text-ink-secondary text-[11px] font-medium">
                                                    Kolom Tetap
                                                </span>
                                            </div>
                                            <div>
                                                <label class="block text-[13px] font-medium text-ink mb-1">Judul</label>
                                                <input
                                                    type="text"
                                                    name="petunjuk[{{ $kodeKolom }}][judul]"
                                                    x-model="form.petunjuk['{{ $kodeKolom }}'].judul"
                                                    required
                                                    class="glass-input w-full px-3.5 py-2 text-[13px]"
                                                >
                                            </div>
                                            <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                                                <div>
                                                    <label class="block text-[13px] font-medium text-ink mb-1">Apa Diisi</label>
                                                    <textarea
                                                        rows="2"
                                                        name="petunjuk[{{ $kodeKolom }}][apa_diisi]"
                                                        x-model="form.petunjuk['{{ $kodeKolom }}'].apa_diisi"
                                                        required
                                                        class="glass-input w-full px-3.5 py-2 text-[13px]"
                                                    ></textarea>
                                                </div>
                                                <div>
                                                    <label class="block text-[13px] font-medium text-ink mb-1">Format</label>
                                                    <textarea
                                                        rows="2"
                                                        name="petunjuk[{{ $kodeKolom }}][format]"
                                                        x-model="form.petunjuk['{{ $kodeKolom }}'].format"
                                                        required
                                                        class="glass-input w-full px-3.5 py-2 text-[13px]"
                                                    ></textarea>
                                                </div>
                                                <div>
                                                    <label class="block text-[13px] font-medium text-ink mb-1">Contoh</label>
                                                    <textarea
                                                        rows="2"
                                                        name="petunjuk[{{ $kodeKolom }}][contoh]"
                                                        x-model="form.petunjuk['{{ $kodeKolom }}'].contoh"
                                                        required
                                                        class="glass-input w-full px-3.5 py-2 text-[13px]"
                                                    ></textarea>
                                                </div>
                                                <div>
                                                    <label class="block text-[13px] font-medium text-ink mb-1">Akibat</label>
                                                    <textarea
                                                        rows="2"
                                                        name="petunjuk[{{ $kodeKolom }}][akibat]"
                                                        x-model="form.petunjuk['{{ $kodeKolom }}'].akibat"
                                                        required
                                                        class="glass-input w-full px-3.5 py-2 text-[13px]"
                                                    ></textarea>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <div role="alert" class="p-3.5 rounded-[10px] bg-warning-bg border border-[#FDE68A] text-[13px] text-warning">
                                    <strong>Peringatan:</strong> Isi tersimpan ternyata bukan JSON yang valid (data lama). Anda tetap dapat mengeditnya melalui kotak teks biasa di bawah ini.
                                </div>
                                <div>
                                    <label class="block text-[13px] font-medium text-ink mb-1">Isi Halaman (Teks Data Lama)</label>
                                    <textarea name="isi" rows="5" x-model="form.isi" required class="glass-input w-full px-4 py-2.5 text-[13px]">{{ $konten->isi }}</textarea>
                                </div>
                            @endif

                            <div class="flex flex-wrap items-center justify-end gap-2.5 pt-2 border-t border-divider/80">
                                <x-button type="button" variant="secondary" @click="batal()">
                                    Batal
                                </x-button>
                                <x-button type="submit" variant="primary">
                                    Simpan
                                </x-button>
                            </div>
                        </form>
                    </div>

                @elseif($konten->slug === 'kontak')
                    @php
                        $stateAwalKontak = [
                            'judul' => (string) $konten->judul,
                            'ringkasan' => (string) ($konten->ringkasan ?? ''),
                            'isi' => (string) ($konten->isi ?? ''),
                        ];
                    @endphp
                    <div
                        x-data="{
                            editing: false,
                            initial: @js($stateAwalKontak),
                            form: @js($stateAwalKontak),
                            mulaiEdit() {
                                this.form = JSON.parse(JSON.stringify(this.initial));
                                this.editing = true;
                            },
                            batal() {
                                this.form = JSON.parse(JSON.stringify(this.initial));
                                this.editing = false;
                            }
                        }"
                        class="glass-card p-6 space-y-5"
                    >
                        <div class="flex flex-wrap items-center justify-between gap-3 border-b border-divider/80 pb-4">
                            <div>
                                <h3 class="text-[18px] font-heading font-bold text-ink">
                                    Halaman: /{{ $konten->slug }}
                                </h3>
                                <span class="text-[12px] text-ink-muted">
                                    Diperbarui: {{ $konten->diperbarui_pada?->locale('id')->translatedFormat('d M Y H:i') ?? '-' }}
                                </span>
                            </div>
                            <div x-show="!editing">
                                <x-button type="button" variant="secondary" @click="mulaiEdit()">
                                    Mulai Mengedit
                                </x-button>
                            </div>
                        </div>

                        <!-- Mode Lihat (Read-Only) -->
                        <div x-show="!editing" class="space-y-3">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div class="p-3.5 rounded-[10px] bg-white/75 border border-border-glass">
                                    <div class="text-[11px] font-heading font-semibold uppercase tracking-wider text-ink-muted">Judul Halaman</div>
                                    <div class="text-[14px] font-heading font-bold text-ink mt-0.5">{{ $konten->judul }}</div>
                                </div>
                                <div class="p-3.5 rounded-[10px] bg-white/75 border border-border-glass">
                                    <div class="text-[11px] font-heading font-semibold uppercase tracking-wider text-ink-muted">Ringkasan</div>
                                    <div class="text-[13px] text-ink-secondary mt-0.5">{{ $konten->ringkasan ?: '-' }}</div>
                                </div>
                            </div>
                            <div class="p-4 rounded-[12px] bg-white/80 border border-border-glass space-y-1">
                                <div class="text-[11px] font-heading font-semibold uppercase tracking-wider text-ink-muted">Isi Kontak &amp; Jam Layanan</div>
                                <p class="text-[14px] text-ink leading-relaxed whitespace-pre-line">{{ $konten->isi }}</p>
                            </div>
                        </div>

                        <!-- Mode Edit -->
                        <form
                            x-cloak
                            x-show="editing"
                            method="POST"
                            action="{{ route('petugas.konten.update', $konten->slug) }}"
                            class="space-y-4"
                        >
                            @csrf
                            @method('PUT')
                            <div>
                                <label class="block text-[13px] font-medium text-ink mb-1">Judul Halaman</label>
                                <input name="judul" type="text" x-model="form.judul" required class="glass-input w-full px-4 py-2.5 text-[14px]">
                            </div>
                            <div>
                                <label class="block text-[13px] font-medium text-ink mb-1">Ringkasan</label>
                                <input name="ringkasan" type="text" x-model="form.ringkasan" class="glass-input w-full px-4 py-2.5 text-[14px]">
                            </div>
                            <div>
                                <label class="block text-[13px] font-medium text-ink mb-1">Isi Halaman</label>
                                <textarea name="isi" rows="4" x-model="form.isi" required class="glass-input w-full px-4 py-2.5 text-[14px]"></textarea>
                            </div>
                            <div class="flex flex-wrap items-center justify-end gap-2.5 pt-2 border-t border-divider/80">
                                <x-button type="button" variant="secondary" @click="batal()">
                                    Batal
                                </x-button>
                                <x-button type="submit" variant="primary">
                                    Simpan
                                </x-button>
                            </div>
                        </form>
                    </div>
                @endif
            @endforeach

            @php
                $kontenPanduan = $daftarKonten->firstWhere('slug', 'panduan');
            @endphp
            @if($kontenPanduan)
                <div class="glass-card p-6 space-y-6">
                    <div class="flex flex-wrap items-center justify-between gap-3 border-b border-divider/80 pb-4">
                        <div>
                            <h3 class="text-[18px] font-heading font-bold text-ink">
                                Halaman: /{{ $kontenPanduan->slug }} (Panduan Visual KF-10)
                            </h3>
                            <span class="text-[12px] text-ink-muted">
                                Diperbarui: {{ $kontenPanduan->diperbarui_pada?->locale('id')->translatedFormat('d M Y H:i') ?? '-' }}
                            </span>
                        </div>
                        <span class="px-2.5 py-1 rounded-[8px] text-[12px] font-heading font-bold {{ $kontenPanduan->visual_path ? 'bg-sekunder-light text-sekunder' : 'bg-surface-soft text-ink-secondary' }}">
                            {{ $kontenPanduan->visual_path ? 'Visual Kustom Aktif' : 'Diagram Bawaan Aktif' }}
                        </span>
                    </div>

                    <!-- Form Judul & Ringkasan Halaman Panduan (Route PUT yang sudah ada) -->
                    <form method="POST" action="{{ route('petugas.konten.update', $kontenPanduan->slug) }}" class="space-y-4" data-prevent-double-submit>
                        @csrf
                        @method('PUT')
                        <div>
                            <label class="block text-[13px] font-medium text-ink mb-1">Judul Halaman</label>
                            <input name="judul" type="text" value="{{ $kontenPanduan->judul }}" required class="glass-input w-full px-4 py-2.5 text-[14px]">
                        </div>
                        <div>
                            <label class="block text-[13px] font-medium text-ink mb-1">Ringkasan</label>
                            <input name="ringkasan" type="text" value="{{ $kontenPanduan->ringkasan }}" class="glass-input w-full px-4 py-2.5 text-[14px]">
                        </div>
                        <div class="flex justify-end">
                            <x-button type="submit" variant="primary">Simpan Perubahan /{{ $kontenPanduan->slug }}</x-button>
                        </div>
                    </form>

                    <!-- Editor Visual Panduan (KF-10) -->
                    <div class="pt-4 border-t border-divider/80 space-y-4">
                        <div class="space-y-1">
                            <h4 class="text-[15px] font-heading font-bold text-ink">
                                Visual Diagram Tahapan Proses (KF-10)
                            </h4>
                            <p class="text-[13px] text-ink-secondary">
                                Catatan: Visual yang diunggah harus menggambarkan 5 tahapan proses penentuan dan keberatan UKT yang sama dengan penjelasan tertulis pada halaman Alur (KF-02) dan Prosedur Keberatan (KF-06).
                            </p>
                        </div>

                        <!-- (a) Pratinjau Visual Saat Ini -->
                        <div class="p-4 rounded-[12px] bg-white/80 border border-border-glass space-y-3">
                            <div class="text-[12px] font-heading font-semibold uppercase tracking-wider text-ink-muted">
                                Pratinjau Visual Saat Ini
                            </div>
                            @if($kontenPanduan->visual_path)
                                <div class="space-y-2">
                                    <img
                                        src="{{ asset('storage/'.$kontenPanduan->visual_path) }}"
                                        alt="Pratinjau diagram tahapan proses penentuan dan keberatan UKT"
                                        class="max-h-80 w-auto rounded-[10px] border border-border-glass bg-white p-2"
                                    >
                                    <div class="text-[12px] text-ink-secondary">
                                        Berkas aktif: <code>{{ $kontenPanduan->visual_path }}</code>
                                    </div>
                                </div>
                            @else
                                <div class="text-[13px] text-ink-secondary">
                                    Saat ini halaman publik <code>/panduan</code> <strong>menggunakan diagram bawaan</strong> (5 tahapan ilustrasi SVG bawaan sistem).
                                </div>
                            @endif
                        </div>

                        <!-- (b) Form Unggah Visual Baru (.png / .svg, maks 2 MB) & (c) Kembalikan ke Diagram Bawaan -->
                        <div class="flex flex-col md:flex-row md:items-end justify-between gap-4">
                            <form
                                method="POST"
                                action="{{ route('petugas.konten.panduan.visual.upload') }}"
                                enctype="multipart/form-data"
                                class="flex-1 flex flex-col sm:flex-row sm:items-end gap-3"
                                data-prevent-double-submit
                            >
                                @csrf
                                <div class="flex-1">
                                    <label for="input_visual_panduan" class="block text-[13px] font-medium text-ink mb-1">
                                        Pilih Berkas Visual (.png atau .svg, maks. 2 MB)
                                    </label>
                                    <input
                                        id="input_visual_panduan"
                                        name="visual"
                                        type="file"
                                        accept=".png,.svg,image/png,image/svg+xml"
                                        required
                                        class="glass-input w-full px-3.5 py-2 text-[13px]"
                                    >
                                </div>
                                <x-button type="submit" variant="primary" class="shrink-0">
                                    Unggah Visual
                                </x-button>
                            </form>

                            @if($kontenPanduan->visual_path)
                                <form
                                    method="POST"
                                    action="{{ route('petugas.konten.panduan.visual.destroy') }}"
                                    onsubmit="return confirm('Kembalikan halaman panduan ke diagram bawaan dan hapus berkas visual kustom saat ini?')"
                                >
                                    @csrf
                                    @method('DELETE')
                                    <x-button type="submit" variant="secondary" class="shrink-0">
                                        Kembalikan ke Diagram Bawaan
                                    </x-button>
                                </form>
                            @endif
                        </div>
                    </div>
                </div>
            @endif
        </section>
    </div>
</x-layouts.app>
