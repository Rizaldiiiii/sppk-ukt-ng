<div {{ $attributes->merge(['class' => 'bg-white rounded-[12px] shadow-[8px_8px_16px_#D9D3CD,-8px_-8px_16px_#FFFFFF] border border-[#E2DFDC] overflow-hidden']) }}>
    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            {{ $slot }}
        </table>
    </div>
</div>

