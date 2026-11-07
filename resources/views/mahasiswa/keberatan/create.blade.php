<x-layouts.app
    title="Formulir Pengajuan Keberatan UKT"
    subtitle="Isi alasan keberatan secara faktual dengan mengikuti petunjuk pengisian di samping setiap kolom (KF-07 & KF-09)"
>
    <x-slot:headerActions>
        <x-button :href="route('mahasiswa.keberatan.index')" variant="secondary">
            ← Kembali ke Daftar Keberatan
        </x-button>
    </x-slot:headerActions>

    @if($errors->any())
        <section role="alert" class="glass-card p-6 border-l-4 border-l-error bg-error-bg/90 space-y-2">
            <h2 class="text-[16px] font-heading font-bold text-error">
                Pengajuan Belum Dapat Dikirim — Periksa Kolom yang Ditandai
            </h2>
            <p class="text-[13px] text-ink">
                Isian teks yang sudah Anda tulis tetap disimpan sebagai draf di bawah ini. Silakan perbaiki kolom berikut sesuai petunjuk:
            </p>
            <ul class="list-disc pl-5 text-[13px] text-error font-medium space-y-1">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </section>
    @endif

    <form
        method="POST"
        action="{{ route('mahasiswa.keberatan.store') }}"
        enctype="multipart/form-data"
        data-prevent-double-submit
        class="glass-card glass-card-glow p-6 md:p-8 relative overflow-hidden space-y-8"
    >
        <div class="accent-bar absolute top-0 inset-x-0" aria-hidden="true"></div>
        @csrf
        <input type="hidden" name="form_token" value="{{ $formToken }}">

        <div class="space-y-2 border-b border-divider/80 pb-4">
            <span class="inline-block px-3 py-1 rounded-[8px] bg-sekunder-light text-sekunder font-heading font-bold text-[12px]">
                KF-07 &amp; KF-09 • Formulir Terpandu
            </span>
            <h2 class="text-[21px] font-heading font-bold text-ink">
                Data Pengajuan Peninjauan Ulang UKT — {{ $mahasiswa->user->nama }} (NIM {{ $mahasiswa->nim }})
            </h2>
            <p class="text-[14px] text-ink-secondary leading-relaxed">
                Setiap kolom dilengkapi panduan pengisian di sebelah kanan/bawah kolom agar pengajuan Anda langsung memenuhi syarat verifikasi petugas.
            </p>
        </div>

        <!-- Kolom 1: Pilihan Hasil UKT -->
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">
            <div class="lg:col-span-7 space-y-2">
                <label for="hasil_id" class="block font-heading font-bold text-[14px] text-ink">
                    1. Hasil UKT yang Diajukan Keberatan
                </label>
                <select
                    id="hasil_id"
                    name="hasil_id"
                    aria-describedby="hint-keberatan-hasil @error('hasil_id') error-hasil-id @enderror"
                    class="glass-input w-full px-4 py-3 text-[14px]"
                >
                    @if($daftarHasil->isEmpty())
                        <option value="">Belum ada hasil UKT tercatat pada akun saya</option>
                    @else
                        @foreach($daftarHasil as $hasil)
                            <option
                                value="{{ $hasil->id }}"
                                @selected((string) old('hasil_id', $hasilTerbaru?->id) === (string) $hasil->id)
                            >
                                {{ $hasil->golongan?->nama ?? 'Golongan' }} — {{ $hasil->formatNominal() }} / Semester
                                ({{ $hasil->isResmi() ? 'Penetapan Resmi' : 'Simulasi' }}, {{ $hasil->tanggal_penetapan?->locale('id')->translatedFormat('d M Y') ?? '-' }})
                            </option>
                        @endforeach
                    @endif
                </select>

                @error('hasil_id')
                    <p id="error-hasil-id" class="text-[13px] font-medium text-error mt-1">
                        {{ $message }}
                    </p>
                @enderror
            </div>

            <div class="lg:col-span-5">
                <x-field-hint id="hint-keberatan-hasil" :hint="$petunjuk['keberatan_hasil'] ?? []" />
            </div>
        </div>

        <!-- Kolom 2: Alasan Keberatan (Wajib) -->
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start border-t border-divider/70 pt-6">
            <div class="lg:col-span-7 space-y-2">
                <label for="alasan" class="block font-heading font-bold text-[14px] text-ink">
                    2. Alasan Keberatan &amp; Rincian Kondisi Ekonomi Sebenarnya <span class="text-error">*</span>
                </label>
                <textarea
                    id="alasan"
                    name="alasan"
                    rows="6"
                    required
                    aria-describedby="hint-keberatan-alasan @error('alasan') error-alasan @enderror"
                    placeholder="Sebutkan komponen penilaian yang dinilai tidak sesuai beserta kondisi ekonomi keluarga yang sebenarnya..."
                    class="glass-input w-full px-4 py-3 text-[14px] leading-relaxed"
                >{{ old('alasan') }}</textarea>

                @error('alasan')
                    <p id="error-alasan" class="text-[13px] font-medium text-error mt-1">
                        {{ $message }}
                    </p>
                @enderror
            </div>

            <div class="lg:col-span-5">
                <x-field-hint id="hint-keberatan-alasan" :hint="$petunjuk['keberatan_alasan'] ?? []" />
            </div>
        </div>

        <!-- Kolom 3: Dokumen Bukti Pendukung (Penyimpanan Privat) -->
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start border-t border-divider/70 pt-6">
            <div class="lg:col-span-7 space-y-2">
                <label for="dokumen" class="block font-heading font-bold text-[14px] text-ink">
                    3. Unggah Dokumen Bukti Pendukung (Opsional / Sangat Disarankan)
                </label>
                <input
                    id="dokumen"
                    name="dokumen[]"
                    type="file"
                    multiple
                    accept=".pdf,.jpg,.jpeg,.png,application/pdf,image/jpeg,image/png"
                    aria-describedby="hint-keberatan-dokumen @error('dokumen') error-dokumen @enderror"
                    class="glass-input w-full px-4 py-3 text-[14px]"
                >
                <p class="text-[12px] text-ink-secondary">
                    <strong>Batas konservatif sementara (PRD §9 butir 8):</strong> Maksimal {{ $maxFiles }} berkas per pengajuan, format <strong>PDF, JPG, atau PNG</strong>, ukuran maksimal <strong>2 MB ({{ number_format($maxFileSizeKb, 0, ',', '.') }} KB)</strong> per berkas. Berkas disimpan di ruang privat terenkripsi nama acak dan hanya dapat diakses oleh Anda serta Petugas UKT berwenang.
                </p>

                @error('dokumen')
                    <p id="error-dokumen" class="text-[13px] font-medium text-error mt-1">
                        {{ $message }}
                    </p>
                @enderror
                @foreach($errors->get('dokumen.*') as $fileErrors)
                    @foreach($fileErrors as $fileError)
                        <p class="text-[13px] font-medium text-error mt-1">
                            {{ $fileError }}
                        </p>
                    @endforeach
                @endforeach
            </div>

            <div class="lg:col-span-5">
                <x-field-hint id="hint-keberatan-dokumen" :hint="$petunjuk['keberatan_dokumen'] ?? []" />
            </div>
        </div>

        <!-- Tombol Kirim -->
        <div class="border-t border-divider/80 pt-6 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
            <p class="text-[12px] text-ink-secondary max-w-xl">
                Dengan menekan tombol kirim, pengajuan Anda akan dicatat pada buku riwayat sistem dan tidak dapat dikirim ganda secara tidak sengaja.
            </p>
            <div class="flex flex-wrap gap-3">
                <x-button :href="route('mahasiswa.keberatan.index')" variant="secondary">
                    Batal
                </x-button>
                <x-button type="submit" variant="primary">
                    Kirim Pengajuan Keberatan
                </x-button>
            </div>
        </div>
    </form>
</x-layouts.app>

