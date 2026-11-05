<div {{ $attributes->merge(['class' => 'glass-table-panel overflow-hidden']) }}>
    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            {{ $slot }}
        </table>
    </div>
</div>
