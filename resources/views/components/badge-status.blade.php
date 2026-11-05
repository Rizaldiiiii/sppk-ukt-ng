@props([
    'status' => 'info',
    'label' => null,
])

@php
    $normalized = mb_strtolower(trim((string) $status));
    $config = match ($normalized) {
        'resmi', 'terkonfirmasi', 'disetujui', 'dijawab', 'aktif', 'berhasil', 'success' => [
            'bg' => 'bg-[#DCFCE7]',
            'text' => 'text-[#166534]',
            'border' => 'border-[#BBF7D0]',
            'icon' => '✓',
        ],
        'perlu perbaikan', 'menunggu_konfirmasi', 'menunggu konfirmasi resmi', 'warning' => [
            'bg' => 'bg-[#FEF3C7]',
            'text' => 'text-[#92400E]',
            'border' => 'border-[#FDE68A]',
            'icon' => '!',
        ],
        'ditolak', 'gagal', 'error' => [
            'bg' => 'bg-[#FEE2E2]',
            'text' => 'text-[#991B1B]',
            'border' => 'border-[#FECACA]',
            'icon' => '✕',
        ],
        'dalam peninjauan', 'simulasi', 'review', 'in_review' => [
            'bg' => 'bg-[#EDE6E3]',
            'text' => 'text-[#6B574F]',
            'border' => 'border-[#DDD9D6]',
            'icon' => '●',
        ],
        default => [
            'bg' => 'bg-[#DBEAFE]',
            'text' => 'text-[#1E40AF]',
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

