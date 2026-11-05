@props([
    'title' => 'Belum Ada Data',
    'description' => 'Data yang Anda cari belum tersedia saat ini.',
])

<div {{ $attributes->merge(['class' => 'glass-card p-8 text-center max-w-2xl mx-auto']) }}>
    <div class="w-12 h-12 mx-auto mb-4 rounded-full bg-sekunder-light text-sekunder border border-border-glass flex items-center justify-center shadow-glass-xs">
        <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true">
            <path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M12 2a10 10 0 100 20 10 10 0 000-20z" />
        </svg>
    </div>
    <h3 class="text-[21px] font-heading font-bold text-ink">{{ $title }}</h3>
    <p class="mt-2 text-[14px] text-ink-secondary leading-relaxed">{{ $description }}</p>
    @if(isset($actions))
        <div class="mt-6 flex flex-wrap items-center justify-center gap-3">
            {{ $actions }}
        </div>
    @endif
</div>
