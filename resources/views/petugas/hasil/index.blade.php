<x-layouts.app
    title="Entri & Kelola Hasil UKT Mahasiswa"
    subtitle="Masukkan hasil penetapan resmi kampus atau hitung simulasi baru dengan versi parameter aktif"
>
    <div x-data="{ mode: 'resmi' }" class="neu-card p-6 md:p-8 space-y-6">
        <div class="flex flex-wrap items-center justify-between gap-4 border-b border-[#DDD9D6] pb-4">
            <div>
                <h2 class="text-[21px] font-heading font-bold text-[#1C1917]">
                    Formulir Pencatatan Hasil &amp; Rincian Komponen
                </h2>
                <p class="text-[13px] text-[#57534E]">
                    Setiap hasil yang disimpan langsung membuat rincian komponen melalui RuleEngine dan mengirim pemberitahuan (KF-15).
                </p>
            </div>
            <div class="flex gap-2">
                <button
                    type="button"
                    @click="mode = 'resmi'"
                    :class="mode === 'resmi' ? 'bg-[#F5E8E9] text-[#550b14] font-bold border border-[#550b14]' : 'bg-white text-[#57534E] border border-[#E2DFDC]'"
                    class="px-4 py-2.5 min-h-[44px] rounded-[10px] font-heading text-[13px]"
                >
                    Entri Hasil Resmi Kampus
                </button>
                <button
                    type="button"
                    @click="mode = 'simulasi'"
                    :class="mode === 'simulasi' ? 'bg-[#F5E8E9] text-[#550b14] font-bold border border-[#550b14]' : 'bg-white text-[#57534E] border border-[#E2DFDC]'"
                    class="px-4 py-2.5 min-h-[44px] rounded-[10px] font-heading text-[13px]"
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
                    <label for="resmi_mahasiswa_id" class="block text-[13px] font-medium text-[#1C1917] mb-1">Pilih Mahasiswa</label>
                    <select id="resmi_mahasiswa_id" name="mahasiswa_id" required class="neu-input w-full px-3.5 py-2.5 text-[14px]">
                        @foreach($daftarMahasiswa as $m)
                            <option value="{{ $m->id }}">{{ $m->nim }} — {{ $m->user?->nama }} ({{ $m->program_studi }})</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label for="resmi_golongan_id" class="block text-[13px] font-medium text-[#1C1917] mb-1">Golongan UKT Resmi</label>
                    <select id="resmi_golongan_id" name="golongan_id" required class="neu-input w-full px-3.5 py-2.5 text-[14px]">
                        @foreach($daftarGolongan as $g)
                            <option value="{{ $g->id }}">{{ $g->nama }} ({{ $g->formatRentang() }})</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label for="resmi_nominal" class="block text-[13px] font-medium text-[#1C1917] mb-1">Nominal Resmi / Semester (Rp)</label>
                    <input id="resmi_nominal" name="nominal" type="number" value="2500000" min="0" required class="neu-input w-full px-3.5 py-2.5 text-[14px] tabular-nums">
                </div>
                <div>
                    <label for="resmi_tanggal" class="block text-[13px] font-medium text-[#1C1917] mb-1">Tanggal Penetapan</label>
                    <input id="resmi_tanggal" name="tanggal_penetapan" type="date" value="{{ now()->toDateString() }}" required class="neu-input w-full px-3.5 py-2.5 text-[14px]">
                </div>
                <div class="md:col-span-2">
                    <label for="resmi_sumber" class="block text-[13px] font-medium text-[#1C1917] mb-1">Sumber SK / Ketetapan Resmi Kampus</label>
                    <input id="resmi_sumber" name="sumber_penetapan" type="text" value="SK Penetapan UKT Rektor UNG" required class="neu-input w-full px-3.5 py-2.5 text-[14px]">
                </div>
            </div>

            <div class="pt-2 border-t border-[#DDD9D6]">
                <h3 class="font-heading font-bold text-[15px] text-[#1C1917] mb-3">
                    Nilai Masukan Komponen (untuk Pembentukan Tabel Rincian &amp; Kalimat Alasan)
                </h3>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    @foreach($faktorAktif as $f)
                        <div>
                            <label class="block text-[13px] font-medium text-[#1C1917] mb-1">{{ $f->nama }}</label>
                            <input
                                name="masukan[{{ $f->kode }}]"
                                type="text"
                                required
                                placeholder="Masukkan nilai {{ $f->kode }}"
                                value="{{ match($f->kode) { 'penghasilan_gabungan' => '4000000', 'jumlah_tanggungan' => '3', 'daya_listrik' => '1300', 'status_tempat_tinggal' => 'Milik sendiri sederhana', default => '1 kendaraan roda dua' } }}"
                                class="neu-input w-full px-3.5 py-2 text-[14px]"
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
                    <label for="simulasi_mahasiswa_id" class="block text-[13px] font-medium text-[#1C1917] mb-1">Pilih Mahasiswa</label>
                    <select id="simulasi_mahasiswa_id" name="mahasiswa_id" required class="neu-input w-full px-3.5 py-2.5 text-[14px]">
                        @foreach($daftarMahasiswa as $m)
                            <option value="{{ $m->id }}">{{ $m->nim }} — {{ $m->user?->nama }} ({{ $m->program_studi }})</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label for="simulasi_catatan" class="block text-[13px] font-medium text-[#1C1917] mb-1">Catatan Simulasi</label>
                    <input id="simulasi_catatan" name="catatan" type="text" value="Simulasi menggunakan versi parameter aktif ({{ $versiAktif?->versi }})" class="neu-input w-full px-3.5 py-2.5 text-[14px]">
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                @foreach($faktorAktif as $f)
                    <div>
                        <label class="block text-[13px] font-medium text-[#1C1917] mb-1">{{ $f->nama }}</label>
                        <input
                            name="masukan[{{ $f->kode }}]"
                            type="text"
                            required
                            value="{{ match($f->kode) { 'penghasilan_gabungan' => '2400000', 'jumlah_tanggungan' => '4', 'daya_listrik' => '900', 'status_tempat_tinggal' => 'Menumpang / Kontrak', default => '1 kendaraan roda dua' } }}"
                            class="neu-input w-full px-3.5 py-2 text-[14px]"
                        >
                    </div>
                @endforeach
            </div>

            <div class="flex justify-end">
                <x-button type="submit" variant="primary">Hitung &amp; Simpan Hasil Simulasi</x-button>
            </div>
        </form>
    </div>

    <!-- Tabel Daftar Hasil UKT Tercatat -->
    <section class="space-y-4">
        <h2 class="text-[21px] font-heading font-bold text-[#1C1917]">
            Daftar Hasil UKT Mahasiswa Tercatat
        </h2>

        <x-table>
            <thead>
                <tr class="border-b border-[#DDD9D6] bg-[#F3F2F0]">
                    <th scope="col" class="px-6 py-4 font-heading font-semibold text-[#1C1917]">Mahasiswa</th>
                    <th scope="col" class="px-6 py-4 font-heading font-semibold text-[#1C1917]">Sumber</th>
                    <th scope="col" class="px-6 py-4 font-heading font-semibold text-[#1C1917]">Golongan / Referensi</th>
                    <th scope="col" class="px-6 py-4 font-heading font-semibold text-[#1C1917] text-right">Nominal</th>
                    <th scope="col" class="px-6 py-4 font-heading font-semibold text-[#1C1917] text-right">Skor Komponen</th>
                    <th scope="col" class="px-6 py-4 font-heading font-semibold text-[#1C1917]">Versi Parameter</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-[#DDD9D6]">
                @foreach($daftarHasil as $h)
                    <tr class="hover:bg-[#F1EFED]">
                        <td class="px-6 py-4">
                            <div class="font-heading font-bold text-[#1C1917]">{{ $h->mahasiswa?->user?->nama }}</div>
                            <div class="text-[12px] text-[#57534E]">NIM {{ $h->mahasiswa?->nim }} • {{ $h->mahasiswa?->program_studi }}</div>
                        </td>
                        <td class="px-6 py-4">
                            <x-badge-status :status="$h->sumber" :label="$h->isResmi() ? 'Resmi' : 'Simulasi'" />
                        </td>
                        <td class="px-6 py-4 font-medium text-[#1C1917]">
                            {{ $h->golongan?->nama ?? '-' }}
                        </td>
                        <td class="px-6 py-4 text-right tabular-nums font-bold text-[#550b14]">
                            {{ $h->formatNominal() }}
                        </td>
                        <td class="px-6 py-4 text-right tabular-nums text-[#1C1917]">
                            {{ number_format((float) $h->skor_total, 2, ',', '.') }}
                        </td>
                        <td class="px-6 py-4 text-[13px] text-[#57534E]">
                            {{ $h->versiParameter?->versi ?? '-' }}
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </x-table>

        <div class="pt-2">
            {{ $daftarHasil->links() }}
        </div>
    </section>
</x-layouts.app>

