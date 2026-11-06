@props([
    'variant' => 'primary', // primary | secondary | ghost | danger
    'type' => 'button',
    'href' => null,
])

@php
    // Transisi tidak memakai transition-all: setiap varian hanya mentransisikan background-color + transform (150ms)
    // agar tidak ada kedip warna/opacity/backdrop-filter saat hover.
    $baseClass = 'inline-flex items-center justify-center gap-2 px-5 py-2.5 text-[14px] font-heading font-bold rounded-[10px] min-h-[44px] focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-sekunder disabled:opacity-60 disabled:cursor-not-allowed';

    $variantClass = match ($variant) {
        'secondary' => 'glass-btn-secondary',
        'ghost' => 'text-sekunder transition-[background-color,transform] duration-150 hover:bg-sekunder-light/85 active:scale-[0.97]',
        'danger' => 'bg-error text-white shadow-glass-sm transition-[background-color,transform] duration-150 hover:bg-[#7F1D1D] active:scale-[0.97]',
        default => 'glass-btn-primary',
    };
@endphp

@if($href)
    <a href="{{ $href }}" {{ $attributes->merge(['class' => $baseClass.' '.$variantClass]) }}>
        {{ $slot }}
    </a>
@else
    <button type="{{ $type }}" {{ $attributes->merge(['class' => $baseClass.' '.$variantClass]) }}>
        <span data-label-default class="inline-flex items-center gap-2">{{ $slot }}</span>
        <span data-label-loading class="hidden items-center gap-2">
            <span class="loading-spinner" aria-hidden="true"></span>
            <span>Memproses...</span>
        </span>
    </button>
@endif
