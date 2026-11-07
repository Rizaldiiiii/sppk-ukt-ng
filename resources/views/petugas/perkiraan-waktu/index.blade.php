<x-layouts.app
    title="Kelola Perkiraan Waktu Tunggu &amp; SLA (KF-11)"
    subtitle="Tetapkan perkiraan waktu tunggu resmi menggunakan pilihan hari kerja terstandar untuk perhitungan tenggat otomatis"
>
    <!-- Penjelasan Aturan SLA KF-11 -->
    <section class="glass-card glass-card-glow p-6 relative overflow-hidden space-y-2">
        <div class="accent-bar absolute top-0 inset-x-0" aria-hidden="true"></div>
        <div class="flex flex-wrap items-center gap-2 pt-1">
            <span class="px-3 py-1 rounded-[8px] bg-sekunder-light text-sekunder font-heading font-bold text-[12px]">
                KF-11 • Standar Waktu Layanan (SLA)
            </span>
        </div>
        <h2 class="text-[20px] font-heading font-bold text-ink">
            Aturan Penetapan Waktu Tunggu &amp; Penguncian Tenggat
        </h2>
        <p class="text-[14px] text-ink-secondary leading-relaxed">
            Pilihan waktu tunggu menggunakan satuan <strong>hari kerja (Senin–Jumat, di luar hari libur nasional)</strong> agar sistem dapat menghitung tanggal perkiraan selesai secara otomatis. Teks perkiraan, sumber ketetapan, dan tanggal pembaruan dibentuk otomatis oleh sistem. Bila dipilih <strong>"Belum ditetapkan"</strong>, sistem menampilkan status jujur tanpa mengarang angka. Perubahan SLA di halaman ini <strong>hanya berlaku bagi pengajuan keberatan baru</strong> dan tidak mengubah tenggat pengajuan yang sudah berjalan.
        </p>
    </section>

    @if($errors->any())
        <div role="alert" class="p-4 rounded-[12px] bg-error-bg border border-[#FECACA] text-[13px] text-error space-y-1">
            <div class="font-heading font-bold">Periksa kembali pilihan Anda:</div>
            <ul class="list-disc pl-5">
                @foreach($errors->all() as $err)
                    <li>{{ $err }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- Dua Panel Pengaturan: Peninjauan Keberatan & Penetapan Hasil -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- 1. Perkiraan Waktu Peninjauan Keberatan UKT -->
        <section class="glass-card p-6 md:p-7 flex flex-col justify-between space-y-6">
            <div class="space-y-4">
                <div class="flex items-start justify-between gap-3">
                    <div>
                        <span class="text-[12px] font-heading font-bold uppercase tracking-wider text-sekunder">
                            Jenis: Keberatan UKT
                        </span>
                        <h3 class="text-[20px] font-heading font-bold text-ink mt-0.5">
                            Waktu Tunggu Peninjauan Keberatan
                        </h3>
                    </div>
                    @if($perkiraanKeberatan)
                        <x-badge-status status="aktif" :label="($daftarPilihanSla[$pilihanKeberatanAktif] ?? 'Aktif')" />
                    @else
                        <x-badge-status status="warning" label="Belum ditetapkan" />
                    @endif
                </div>

                <div class="p-4 rounded-[12px] bg-white/80 border border-border-glass space-y-1.5 text-[13px]">
                    <div class="font-heading font-bold text-ink">Tampilan Saat Ini kepada Mahasiswa &amp; Publik:</div>
                    @if($perkiraanKeberatan)
                        <p class="text-[14px] font-semibold text-sekunder">{{ $perkiraanKeberatan->teks_perkiraan }}</p>
                        <p class="text-[12px] text-ink-secondary">
                            Sumber: {{ $perkiraanKeberatan->sumber }} • Diperbarui: {{ $perkiraanKeberatan->tanggal?->locale('id')->translatedFormat('d M Y') }}
                        </p>
                    @else
                        <p class="text-[14px] font-semibold text-ink">Waktu tunggu belum ditetapkan</p>
                        <p class="text-[12px] text-ink-secondary">
                            Sistem menampilkan status pengajuan saat ini tanpa mencantumkan angka perkiraan hari.
                        </p>
                    @endif
                </div>
            </div>

            <form
                method="POST"
                action="{{ route('petugas.perkiraan-waktu.update', 'keberatan') }}"
                data-prevent-double-submit
                class="space-y-4 pt-2 border-t border-divider/80"
            >
                @csrf
                @method('PUT')

                <div class="space-y-1.5">
                    <label for="pilihan_sla_keberatan" class="block text-[14px] font-heading font-bold text-ink">
                        Pilih Standar Waktu Peninjauan Keberatan <span class="text-error">*</span>
                    </label>
                    <select
                        id="pilihan_sla_keberatan"
                        name="pilihan_sla"
                        required
                        class="glass-input w-full px-4 py-3 text-[14px]"
                    >
                        @foreach($daftarPilihanSla as $nilai => $label)
                            <option value="{{ $nilai }}" @selected($pilihanKeberatanAktif === (string) $nilai)>
                                {{ $label }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="flex justify-end">
                    <x-button type="submit" variant="primary">
                        Simpan SLA Peninjauan Keberatan
                    </x-button>
                </div>
            </form>
        </section>

        <!-- 2. Perkiraan Waktu Penetapan Hasil UKT -->
        <section class="glass-card p-6 md:p-7 flex flex-col justify-between space-y-6">
            <div class="space-y-4">
                <div class="flex items-start justify-between gap-3">
                    <div>
                        <span class="text-[12px] font-heading font-bold uppercase tracking-wider text-sekunder">
                            Jenis: Penetapan Hasil UKT
                        </span>
                        <h3 class="text-[20px] font-heading font-bold text-ink mt-0.5">
                            Waktu Tunggu Penetapan Hasil Awal
                        </h3>
                    </div>
                    @if($perkiraanPenetapan)
                        <x-badge-status status="aktif" :label="($daftarPilihanSla[$pilihanPenetapanAktif] ?? 'Aktif')" />
                    @else
                        <x-badge-status status="warning" label="Belum ditetapkan" />
                    @endif
                </div>

                <div class="p-4 rounded-[12px] bg-white/80 border border-border-glass space-y-1.5 text-[13px]">
                    <div class="font-heading font-bold text-ink">Tampilan Saat Ini pada Halaman Hasil Mahasiswa:</div>
                    @if($perkiraanPenetapan)
                        <p class="text-[14px] font-semibold text-sekunder">{{ $perkiraanPenetapan->teks_perkiraan }}</p>
                        <p class="text-[12px] text-ink-secondary">
                            Sumber: {{ $perkiraanPenetapan->sumber }} • Diperbarui: {{ $perkiraanPenetapan->tanggal?->locale('id')->translatedFormat('d M Y') }}
                        </p>
                    @else
                        <p class="text-[14px] font-semibold text-ink">Waktu tunggu belum ditetapkan</p>
                        <p class="text-[12px] text-ink-secondary">
                            Ditampilkan pada halaman hasil bagi mahasiswa yang penetapan resminya sedang diproses.
                        </p>
                    @endif
                </div>
            </div>

            <form
                method="POST"
                action="{{ route('petugas.perkiraan-waktu.update', 'penetapan') }}"
                data-prevent-double-submit
                class="space-y-4 pt-2 border-t border-divider/80"
            >
                @csrf
                @method('PUT')

                <div class="space-y-1.5">
                    <label for="pilihan_sla_penetapan" class="block text-[14px] font-heading font-bold text-ink">
                        Pilih Standar Waktu Penetapan Hasil <span class="text-error">*</span>
                    </label>
                    <select
                        id="pilihan_sla_penetapan"
                        name="pilihan_sla"
                        required
                        class="glass-input w-full px-4 py-3 text-[14px]"
                    >
                        @foreach($daftarPilihanSla as $nilai => $label)
                            <option value="{{ $nilai }}" @selected($pilihanPenetapanAktif === (string) $nilai)>
                                {{ $label }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="flex justify-end">
                    <x-button type="submit" variant="primary">
                        Simpan SLA Penetapan Hasil
                    </x-button>
                </div>
            </form>
        </section>
    </div>

    <!-- Riwayat Ketetapan Perkiraan Waktu -->
    <section class="glass-card p-6 space-y-4">
        <h3 class="text-[18px] font-heading font-bold text-ink">
            Riwayat Ketetapan Perkiraan Waktu (Hanya 1 Baris Aktif per Jenis)
        </h3>

        @if($riwayatPerkiraan->isEmpty())
            <p class="text-[13px] text-ink-secondary">
                Belum ada ketetapan perkiraan waktu yang disimpan pada tabel <code>perkiraan_waktu</code>.
            </p>
        @else
            <x-table>
                <thead>
                    <tr class="border-b border-divider bg-surface-soft/90">
                        <th scope="col" class="px-5 py-3.5 font-heading font-semibold text-ink">ID</th>
                        <th scope="col" class="px-5 py-3.5 font-heading font-semibold text-ink">Jenis</th>
                        <th scope="col" class="px-5 py-3.5 font-heading font-semibold text-ink">Teks Perkiraan Otomatis</th>
                        <th scope="col" class="px-5 py-3.5 font-heading font-semibold text-ink">Sumber &amp; Tanggal</th>
                        <th scope="col" class="px-5 py-3.5 font-heading font-semibold text-ink">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-divider">
                    @foreach($riwayatPerkiraan as $row)
                        <tr class="hover:bg-surface-soft/80">
                            <td class="px-5 py-3.5 font-heading font-bold text-sekunder tabular-nums">#{{ $row->id }}</td>
                            <td class="px-5 py-3.5 font-heading font-semibold text-ink capitalize">{{ $row->jenis }}</td>
                            <td class="px-5 py-3.5 text-[13px] text-ink">{{ $row->teks_perkiraan }}</td>
                            <td class="px-5 py-3.5 text-[12px] text-ink-secondary">
                                <div>{{ $row->sumber }}</div>
                                <div class="text-ink-muted">{{ $row->tanggal?->locale('id')->translatedFormat('d M Y') }}</div>
                            </td>
                            <td class="px-5 py-3.5">
                                @if($row->aktif)
                                    <x-badge-status status="aktif" label="Aktif" />
                                @else
                                    <span class="px-2.5 py-1 rounded-[8px] bg-surface-soft text-ink-muted text-[12px] font-semibold border border-divider">
                                        Nonaktif
                                    </span>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </x-table>
        @endif
    </section>
</x-layouts.app>

