<x-layouts.app
    :title="$judulHalaman ?? 'Modul Petugas'"
    :subtitle="'Lingkup Fitur ' . ($kodeFitur ?? 'Tahap 2') . ' • Dijadwalkan pada ' . ($tahapTarget ?? 'Tahap 2')"
>
    <x-empty-state
        :title="($judulHalaman ?? 'Fitur Ini') . ' Belum Tersedia di Tahap Ini'"
        :description="$ringkasanStub ?? 'Fitur ini dijadwalkan pada pembangunan Tahap 2.'"
    >
        <x-slot:actions>
            <x-button :href="route('petugas.beranda')" variant="primary">
                Kembali ke Beranda Petugas
            </x-button>
            <x-button :href="route('petugas.hasil.index')" variant="secondary">
                Kelola Hasil &amp; Rincian
            </x-button>
        </x-slot:actions>
    </x-empty-state>
</x-layouts.app>

