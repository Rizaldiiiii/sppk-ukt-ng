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
                Teks Alur, Prosedur &amp; Kontak (KF-02 / KF-06)
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
                        <label for="faktor_status" class="block text-[14px] font-medium text-ink mb-1">Status Konfirmasi Sumber Resmi</label>
                        <select id="faktor_status" name="status_konfirmasi" class="glass-input w-full px-4 py-2.5 text-[14px]">
                            <option value="menunggu_konfirmasi">Menunggu konfirmasi resmi</option>
                            <option value="terkonfirmasi">Terkonfirmasi resmi</option>
                        </select>
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
                            <x-badge-status :status="$faktor->status_konfirmasi" :label="$faktor->labelStatusKonfirmasi()" />
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div>
                                <label class="block text-[13px] font-medium text-ink mb-1">Nama Faktor</label>
                                <input name="nama" type="text" value="{{ $faktor->nama }}" required class="glass-input w-full px-3.5 py-2 text-[14px]">
                            </div>
                            <div>
                                <label class="block text-[13px] font-medium text-ink mb-1">Status Konfirmasi</label>
                                <select name="status_konfirmasi" class="glass-input w-full px-3.5 py-2 text-[14px]">
                                    <option value="terkonfirmasi" @selected($faktor->status_konfirmasi === 'terkonfirmasi')>Terkonfirmasi resmi</option>
                                    <option value="menunggu_konfirmasi" @selected($faktor->status_konfirmasi === 'menunggu_konfirmasi')>Menunggu konfirmasi resmi</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-[13px] font-medium text-ink mb-1">Urutan</label>
                                <input name="urutan" type="number" value="{{ $faktor->urutan }}" required class="glass-input w-full px-3.5 py-2 text-[14px]">
                            </div>
                            <div class="md:col-span-3">
                                <label class="block text-[13px] font-medium text-ink mb-1">Penjelasan Singkat</label>
                                <textarea name="penjelasan_singkat" rows="2" required class="glass-input w-full px-3.5 py-2 text-[14px]">{{ $faktor->penjelasan_singkat }}</textarea>
                            </div>
                            <div class="md:col-span-3">
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

        <!-- TAB 3: Halaman Konten Publik (Alur, Prosedur, Kontak) -->
        <section x-cloak x-show="tab === 'halaman'" class="space-y-6">
            @foreach($daftarKonten as $konten)
                <form method="POST" action="{{ route('petugas.konten.update', $konten->slug) }}" class="glass-card p-6 space-y-4" data-prevent-double-submit>
                    @csrf
                    @method('PUT')
                    <div class="flex items-center justify-between">
                        <h3 class="text-[18px] font-heading font-bold text-ink">
                            Halaman: /{{ $konten->slug }}
                        </h3>
                        <span class="text-[12px] text-ink-muted">
                            Diperbarui: {{ $konten->diperbarui_pada?->locale('id')->translatedFormat('d M Y H:i') ?? '-' }}
                        </span>
                    </div>
                    <div>
                        <label class="block text-[13px] font-medium text-ink mb-1">Judul Halaman</label>
                        <input name="judul" type="text" value="{{ $konten->judul }}" required class="glass-input w-full px-4 py-2.5 text-[14px]">
                    </div>
                    <div>
                        <label class="block text-[13px] font-medium text-ink mb-1">Ringkasan</label>
                        <input name="ringkasan" type="text" value="{{ $konten->ringkasan }}" class="glass-input w-full px-4 py-2.5 text-[14px]">
                    </div>
                    <div>
                        <label class="block text-[13px] font-medium text-ink mb-1">Isi Halaman</label>
                        <textarea name="isi" rows="5" required class="glass-input w-full px-4 py-2.5 text-[13px] font-mono">{{ $konten->isi }}</textarea>
                    </div>
                    <div class="flex justify-end">
                        <x-button type="submit" variant="primary">Simpan Perubahan /{{ $konten->slug }}</x-button>
                    </div>
                </form>
            @endforeach
        </section>
    </div>
</x-layouts.app>
