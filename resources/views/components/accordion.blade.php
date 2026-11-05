@props([
    'title' => 'Detail Informasi',
    'defaultOpen' => false,
])

<div x-data="{ open: @js($defaultOpen) }" class="neu-card overflow-hidden">
    <button
        type="button"
        @click="open = !open"
        :aria-expanded="open"
        class="flex w-full items-center justify-between px-6 py-4 text-left font-heading font-bold text-[#1C1917] hover:bg-[#F1EFED] transition-colors duration-150 min-h-[48px]"
    >
        <span>{{ $title }}</span>
        <span
            class="inline-block text-[#550b14] transition-transform duration-200"
            :class="open ? 'rotate-180' : ''"
            aria-hidden="true"
        >
            ▼
        </span>
    </button>

    <div
        x-cloak
        x-show="open"
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0 -translate-y-2"
        x-transition:enter-end="opacity-100 translate-y-0"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100 translate-y-0"
        x-transition:leave-end="opacity-0 -translate-y-2"
        class="px-6 pb-6 pt-2 border-t border-[#DDD9D6] text-[#57534E]"
    >
        {{ $slot }}
    </div>
</div>

