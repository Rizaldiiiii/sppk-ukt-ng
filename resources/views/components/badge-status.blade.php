@props([
    'status' => 'info',
    'label' => null,
])

@php
    $normalized = mb_strtolower(trim((string) $status));
    $config = match ($normalized) {
        'resmi', 'terkonfirmasi', 'disetujui', 'dijawab', 'aktif', 'berhasil', 'success' => [
            'bg' => 'bg-success-bg',
            'text' => 'text-success',
            'border' => 'border-[#BBF7D0]',
            'icon' => '✓',
        ],
        'perlu perbaikan', 'menunggu_konfirmasi', 'menunggu konfirmasi resmi', 'warning' => [
            'bg' => 'bg-warning-bg',
            'text' => 'text-warning',
            'border' => 'border-[#FDE68A]',
            'icon' => '!',
        ],
        'ditolak', 'gagal', 'error' => [
            'bg' => 'bg-error-bg',
            'text' => 'text-error',
            'border' => 'border-[#FECACA]',
            'icon' => '✕',
        ],
        'dalam peninjauan', 'simulasi', 'review', 'in_review' => [
            'bg' => 'bg-highlight-light',
            'text' => 'text-highlight-dark',
            'border' => 'border-divider',
            'icon' => '●',
        ],
        default => [
            'bg' => 'bg-info-bg',
            'text' => 'text-info',
            'border' => 'border-[#BFDBFE]',
            'icon' => 'ℹ',
        ],
    };

    $displayLabel = $label ?? $status;
@endphp

<span {{ $attributes->merge([
    'class' => "inline-flex items-center gap-1.5 px-3 py-1 rounded-[8px] text-[12px] font-semibold border {$config['bg']} {$config['text']} {$config['border']}"
]) }}>
    <span aria-hidden="true" class="font-bold">{{ $config['icon'] }}</span>
    <span>{{ $displayLabel }}</span>
</span>
