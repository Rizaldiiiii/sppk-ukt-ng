<x-layouts.app
    :title="$judulHalaman ?? 'Pertanyaan kepada Petugas UKT'"
    :subtitle="'Lingkup Fitur ' . ($kodeFitur ?? 'KF-08') . ' • Dijadwalkan pada ' . ($tahapTarget ?? 'Tahap 2')"
>
    <x-empty-state
        :title="($judulHalaman ?? 'Fasilitas Pertanyaan Daring') . ' Belum Tersedia di Tahap Ini'"
        :description="$ringkasanStub ?? 'Fasilitas pengiriman pertanyaan daring dijadwalkan pada pembangunan Tahap 2.'"
    >
        <x-slot:actions>
            <x-button :href="route('publik.bantuan')" variant="primary">
                Lihat Informasi Kanal Bantuan
            </x-button>
            <x-button :href="route('mahasiswa.hasil')" variant="secondary">
                Buka Hasil &amp; Rincian Saya
            </x-button>
        </x-slot:actions>
    </x-empty-state>
</x-layouts.app>

