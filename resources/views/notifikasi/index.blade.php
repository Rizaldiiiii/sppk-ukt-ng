@php
    $jumlahNotifikasi = $notifikasi->total();
    $jumlahBelumDibaca = auth()->user()->unreadNotifications()->count();
@endphp
<x-layouts.app
    title="Notifikasi Sistem Saya"
    subtitle="Pemberitahuan perubahan hasil dan status peninjauan UKT (KF-15)"
>
    <x-slot:headerActions>
        @if($jumlahNotifikasi > 0)
            <div class="flex flex-wrap items-center justify-end gap-2.5">
                @if($jumlahBelumDibaca > 0)
                    <form method="POST" action="{{ route('notifikasi.baca-semua') }}" data-prevent-double-submit>
                        @csrf
                        <x-button type="submit" variant="secondary">
                            Tandai Semua Dibaca
                        </x-button>
                    </form>
                @endif

                <div class="flex flex-col items-end gap-1">
                    <form
                        method="POST"
                        action="{{ route('notifikasi.hapus-semua') }}"
                        onsubmit="return confirm('Apakah Anda yakin ingin menghapus semua notifikasi Anda? Tindakan ini tidak dapat dibatalkan.')"
                    >
                        @csrf
                        @method('DELETE')
                        <x-button
                            type="submit"
                            variant="danger"
                            :disabled="$jumlahBelumDibaca > 0"
                        >
                            Hapus Semua Notifikasi
                        </x-button>
                    </form>
                    @if($jumlahBelumDibaca > 0)
                        <span class="text-[11px] text-ink-secondary">
                            Tombol ini aktif setelah semua notifikasi ditandai dibaca.
                        </span>
                    @endif
                </div>
            </div>
        @endif
    </x-slot:headerActions>

    @if($notifikasi->isEmpty())
        <x-empty-state
            title="Belum Ada Pemberitahuan"
            description="Anda belum memiliki pemberitahuan perubahan hasil maupun status pengajuan di sistem."
        />
    @else
        <div class="space-y-4">
            @foreach($notifikasi as $item)
                @php
                    $data = $item->data ?? [];
                    $sudahDibaca = $item->read_at !== null;
                @endphp
                <article class="glass-card p-6 flex flex-col md:flex-row md:items-center justify-between gap-4 {{ !$sudahDibaca ? 'border-l-4 border-l-sekunder' : 'opacity-90' }}">
                    <div class="space-y-1.5">
                        <div class="flex flex-wrap items-center gap-2.5">
                            @if(!$sudahDibaca)
                                <x-badge-status status="info" label="Baru" />
                            @else
                                <span class="px-2.5 py-0.5 rounded-[8px] text-[12px] font-medium bg-surface-soft text-ink-secondary">Sudah dibaca</span>
                            @endif
                            <span class="text-[12px] text-ink-muted">
                                {{ $data['tanggal'] ?? $item->created_at?->locale('id')->translatedFormat('d M Y H:i') }}
                            </span>
                        </div>
                        <h2 class="text-[18px] font-heading font-bold text-ink">
                            {{ $data['judul'] ?? 'Pemberitahuan Sistem' }}
                        </h2>
                        <p class="text-[14px] text-ink-secondary leading-relaxed">
                            {{ $data['pesan'] ?? ('Status baru: ' . ($data['status_baru'] ?? '-')) }}
                        </p>
                    </div>

                    <div class="flex flex-wrap items-center gap-2.5 shrink-0">
                        @if(!empty($data['tautan']))
                            <x-button :href="$data['tautan']" variant="secondary">
                                Buka Halaman
                            </x-button>
                        @endif

                        @if(!$sudahDibaca)
                            <form method="POST" action="{{ route('notifikasi.baca', $item->id) }}" data-prevent-double-submit>
                                @csrf
                                <x-button type="submit" variant="primary">
                                    Tandai Dibaca
                                </x-button>
                            </form>
                        @endif
                    </div>
                </article>
            @endforeach

            <div class="pt-4">
                {{ $notifikasi->links() }}
            </div>
        </div>
    @endif
</x-layouts.app>
