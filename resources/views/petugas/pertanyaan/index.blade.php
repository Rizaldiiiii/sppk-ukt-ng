<x-layouts.app
    title="Daftar Pertanyaan Mahasiswa"
    subtitle="Kelola pertanyaan mahasiswa, berikan jawaban resmi, atau tutup pertanyaan yang telah tuntas (KF-08)"
>
    <!-- Ringkasan Jumlah per Status -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        @foreach($daftarStatus as $statusItem)
            <a
                href="{{ route('petugas.pertanyaan.index', ['status' => $statusItem, 'q' => $kataKunci]) }}"
                class="glass-card glass-card-light p-4 space-y-1 transition-transform hover:-translate-y-0.5 {{ $statusFilter === $statusItem ? 'ring-2 ring-sekunder' : '' }}"
            >
                <div class="text-[12px] font-medium text-ink-secondary">Status: {{ ucfirst($statusItem) }}</div>
                <div class="text-[24px] font-heading font-extrabold text-ink tabular-nums">
                    {{ $ringkasanStatus[$statusItem] ?? 0 }} Pertanyaan
                </div>
            </a>
        @endforeach
    </div>

    <!-- Filter & Pencarian -->
    <section class="glass-card p-6">
        <form method="GET" action="{{ route('petugas.pertanyaan.index') }}" class="grid grid-cols-1 md:grid-cols-12 gap-4 items-end">
            <div class="md:col-span-4">
                <label for="filter_status" class="block text-[13px] font-heading font-semibold text-ink mb-1">
                    Filter Status Pertanyaan
                </label>
                <select id="filter_status" name="status" class="glass-input w-full px-4 py-2.5 text-[14px]">
                    <option value="">Semua Status</option>
                    @foreach($daftarStatus as $statusOption)
                        <option value="{{ $statusOption }}" @selected($statusFilter === $statusOption)>
                            {{ ucfirst($statusOption) }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="md:col-span-5">
                <label for="filter_q" class="block text-[13px] font-heading font-semibold text-ink mb-1">
                    Cari Nama Mahasiswa / NIM / Isi Pertanyaan
                </label>
                <input
                    id="filter_q"
                    type="search"
                    name="q"
                    value="{{ $kataKunci }}"
                    placeholder="Contoh: 531412001, Budi, atau kata kunci pertanyaan..."
                    class="glass-input w-full px-4 py-2.5 text-[14px]"
                >
            </div>

            <div class="md:col-span-3 flex gap-2">
                <x-button type="submit" variant="primary" class="flex-1">
                    Terapkan Filter
                </x-button>
                @if($statusFilter !== '' || $kataKunci !== '')
                    <x-button :href="route('petugas.pertanyaan.index')" variant="secondary">
                        Reset
                    </x-button>
                @endif
            </div>
        </form>
    </section>

    @if($daftarPertanyaan->isEmpty())
        <x-empty-state
            title="Tidak Ada Pertanyaan Mahasiswa yang Cocok"
            description="Belum ada pertanyaan masuk yang sesuai dengan filter pencarian saat ini."
        />
    @else
        <x-table>
            <thead>
                <tr class="border-b border-divider bg-surface-soft/90">
                    <th scope="col" class="px-5 py-4 font-heading font-semibold text-ink">Tanda Terima</th>
                    <th scope="col" class="px-5 py-4 font-heading font-semibold text-ink">Mahasiswa</th>
                    <th scope="col" class="px-5 py-4 font-heading font-semibold text-ink">Isi Pertanyaan</th>
                    <th scope="col" class="px-5 py-4 font-heading font-semibold text-ink">Status</th>
                    <th scope="col" class="px-5 py-4 font-heading font-semibold text-ink">Tanggal Masuk</th>
                    <th scope="col" class="px-5 py-4 font-heading font-semibold text-ink text-right">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-divider">
                @foreach($daftarPertanyaan as $item)
                    <tr class="hover:bg-surface-soft/80">
                        <td class="px-5 py-4 font-heading font-bold text-sekunder tabular-nums">
                            #{{ $item->id }}
                        </td>
                        <td class="px-5 py-4">
                            <div class="font-heading font-bold text-ink">{{ $item->mahasiswa?->user?->nama ?? '-' }}</div>
                            <div class="text-[12px] text-ink-secondary">
                                NIM {{ $item->mahasiswa?->nim ?? '-' }} • {{ $item->mahasiswa?->program_studi ?? '-' }}
                            </div>
                        </td>
                        <td class="px-5 py-4 max-w-md">
                            <p class="text-[13px] text-ink line-clamp-2">{{ \Illuminate\Support\Str::limit($item->isi, 120) }}</p>
                        </td>
                        <td class="px-5 py-4">
                            <x-badge-status :status="$item->status" :label="ucfirst($item->status)" />
                        </td>
                        <td class="px-5 py-4 text-[12px] text-ink-secondary whitespace-nowrap">
                            {{ $item->created_at?->locale('id')->translatedFormat('d M Y H:i') ?? '-' }}
                        </td>
                        <td class="px-5 py-4 text-right whitespace-nowrap">
                            <x-button :href="route('petugas.pertanyaan.show', $item)" variant="secondary" class="text-[12px] px-3.5 py-2 min-h-[38px]">
                                Buka &amp; Jawab
                            </x-button>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </x-table>

        <div class="pt-2">
            {{ $daftarPertanyaan->links() }}
        </div>
    @endif
</x-layouts.app>

