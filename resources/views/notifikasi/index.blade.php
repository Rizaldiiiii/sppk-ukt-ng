<x-layouts.app
    title="Notifikasi Sistem Saya"
    subtitle="Pemberitahuan perubahan hasil dan status peninjauan UKT (KF-15)"
>
    <x-slot:headerActions>
        @if(auth()->user()->unreadNotifications()->count() > 0)
            <form method="POST" action="{{ route('notifikasi.baca-semua') }}" data-prevent-double-submit>
                @csrf
                <x-button type="submit" variant="secondary">
                    Tandai Semua Dibaca
                </x-button>
            </form>
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
                <article class="neu-card p-6 flex flex-col md:flex-row md:items-center justify-between gap-4 {{ !$sudahDibaca ? 'border-l-4 border-l-[#550b14]' : 'opacity-90' }}">
                    <div class="space-y-1.5">
                        <div class="flex flex-wrap items-center gap-2.5">
                            @if(!$sudahDibaca)
                                <x-badge-status status="info" label="Baru" />
                            @else
                                <span class="px-2.5 py-0.5 rounded-[8px] text-[12px] font-medium bg-[#F3F2F0] text-[#57534E]">Sudah dibaca</span>
                            @endif
                            <span class="text-[12px] text-[#78716C]">
                                {{ $data['tanggal'] ?? $item->created_at?->locale('id')->translatedFormat('d M Y H:i') }}
                            </span>
                        </div>
                        <h2 class="text-[18px] font-heading font-bold text-[#1C1917]">
                            {{ $data['judul'] ?? 'Pemberitahuan Sistem' }}
                        </h2>
                        <p class="text-[14px] text-[#57534E] leading-relaxed">
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

