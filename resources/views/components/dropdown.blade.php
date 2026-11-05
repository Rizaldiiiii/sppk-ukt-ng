@props([
    'label' => 'Menu',
])

<div
    x-data="{ open: false }"
    @click.outside="open = false"
    @keydown.escape.window="open = false"
    class="relative inline-block"
>
    <button
        type="button"
        @click="open = !open"
        :aria-expanded="open"
        class="neu-btn-secondary px-4 py-2.5 inline-flex items-center gap-2 text-[14px]"
    >
        <span>{{ $label }}</span>
        <span
            class="inline-block transition-transform duration-150 text-xs"
            :class="open ? 'rotate-180' : ''"
            aria-hidden="true"
        >
            ▼
        </span>
    </button>

    <div
        x-cloak
        x-show="open"
        x-transition:enter="transition ease-out duration-150"
        x-transition:enter-start="opacity-0 -translate-y-2 scale-95"
        x-transition:enter-end="opacity-100 translate-y-0 scale-100"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100 translate-y-0 scale-100"
        x-transition:leave-end="opacity-0 -translate-y-2 scale-95"
        class="absolute right-0 top-full mt-2 w-56 rounded-[10px] bg-white p-2 shadow-[12px_12px_24px_#D9D3CD,-12px_-12px_24px_#FFFFFF] border border-[#E2DFDC] z-40"
    >
        {{ $slot }}
    </div>
</div>

