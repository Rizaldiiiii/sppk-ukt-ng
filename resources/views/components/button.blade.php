@props([
    'variant' => 'primary', // primary | secondary | ghost | danger
    'type' => 'button',
    'href' => null,
])

@php
    $baseClass = 'inline-flex items-center justify-center gap-2 px-5 py-2.5 text-[14px] font-heading font-bold rounded-[10px] min-h-[44px] transition-all duration-150 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-[#550b14] disabled:opacity-60 disabled:cursor-not-allowed';

    $variantClass = match ($variant) {
        'secondary' => 'neu-btn-secondary',
        'ghost' => 'text-[#550b14] hover:bg-[#F5E8E9] active:scale-[0.97]',
        'danger' => 'bg-[#991B1B] text-white shadow-[5px_5px_10px_#D9D3CD,-5px_-5px_10px_#FFFFFF] hover:bg-[#7F1D1D] active:scale-[0.97]',
        default => 'neu-btn-primary',
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

