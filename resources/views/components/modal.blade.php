@props([
    'id' => 'modal-dialog',
    'title' => 'Konfirmasi',
    'triggerLabel' => 'Buka Modal',
])

<div x-data="{ open: false }" class="inline-block">
    <button
        type="button"
        @click="open = true"
        class="glass-btn-secondary px-4 py-2 text-[14px]"
    >
        {{ $triggerLabel }}
    </button>

    <div
        x-cloak
        x-show="open"
        @keydown.escape.window="open = false"
        role="dialog"
        aria-modal="true"
        aria-labelledby="{{ $id }}-title"
        class="fixed inset-0 z-50 flex items-center justify-center p-4"
    >
        <div
            class="absolute inset-0 bg-ink/35 backdrop-blur-[4px]"
            x-show="open"
            x-transition.opacity.duration.200ms
            @click="open = false"
        ></div>

        <div
            class="relative w-full max-w-lg rounded-[16px] bg-glass-raised backdrop-blur-[24px] p-6 shadow-glass-lg border border-border-glass"
            x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0 scale-95 translate-y-3"
            x-transition:enter-end="opacity-100 scale-100 translate-y-0"
            x-transition:leave="transition ease-in duration-150"
            x-transition:leave-start="opacity-100 scale-100 translate-y-0"
            x-transition:leave-end="opacity-0 scale-95 translate-y-3"
        >
            <div class="flex items-center justify-between pb-3 border-b border-divider/80">
                <h2 id="{{ $id }}-title" class="text-[21px] font-heading font-bold text-ink">
                    {{ $title }}
                </h2>
                <button
                    type="button"
                    @click="open = false"
                    aria-label="Tutup dialog"
                    class="w-11 h-11 rounded-[10px] inline-flex items-center justify-center text-ink-secondary hover:bg-glass-hover"
                >
                    ✕
                </button>
            </div>

            <div class="mt-4 text-ink">
                {{ $slot }}
            </div>
        </div>
    </div>
</div>
