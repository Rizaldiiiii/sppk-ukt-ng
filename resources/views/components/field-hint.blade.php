@props([
    'id' => null,
    'hint' => [],
])

@if(!empty($hint))
    <aside
        @if($id) id="{{ $id }}" @endif
        class="rounded-[12px] bg-white/80 border border-border-glass p-4 space-y-2 text-[13px] leading-relaxed shadow-glass-xs"
    >
        <div class="flex items-center gap-2 font-heading font-bold text-sekunder text-[13px]">
            <span class="w-5 h-5 rounded-full bg-sekunder-light text-sekunder inline-flex items-center justify-center text-[11px] font-extrabold shrink-0" aria-hidden="true">i</span>
            <span>Petunjuk Pengisian (KF-09): {{ $hint['judul'] ?? 'Panduan Kolom' }}</span>
        </div>

        <dl class="grid grid-cols-1 sm:grid-cols-2 gap-x-4 gap-y-2 text-[12px]">
            <div>
                <dt class="font-heading font-semibold text-ink">Apa yang harus diisi:</dt>
                <dd class="text-ink-secondary mt-0.5">{{ $hint['apa_diisi'] ?? '-' }}</dd>
            </div>
            <div>
                <dt class="font-heading font-semibold text-ink">Format yang diharapkan:</dt>
                <dd class="text-ink-secondary mt-0.5">{{ $hint['format'] ?? '-' }}</dd>
            </div>
            <div>
                <dt class="font-heading font-semibold text-ink">Contoh pengisian:</dt>
                <dd class="text-ink-secondary mt-0.5 italic">"{{ $hint['contoh'] ?? '-' }}"</dd>
            </div>
            <div>
                <dt class="font-heading font-semibold text-warning">Akibat bila salah / kurang lengkap:</dt>
                <dd class="text-ink-secondary mt-0.5">{{ $hint['akibat'] ?? '-' }}</dd>
            </div>
        </dl>

        @if(!empty($hint['catatan_tambahan']))
            <div class="pt-2 border-t border-divider/70 text-[12px] text-ink-secondary">
                <strong class="text-ink">Catatan Petugas:</strong> {{ $hint['catatan_tambahan'] }}
            </div>
        @endif
    </aside>
@endif

