<x-layouts.app
    :title="$judulHalaman ?? 'Pengajuan Keberatan UKT'"
    :subtitle="'Lingkup Fitur ' . ($kodeFitur ?? 'KF-07') . ' • Dijadwalkan pada ' . ($tahapTarget ?? 'Tahap 2')"
>
    <x-empty-state
        :title="($judulHalaman ?? 'Fitur Ini') . ' Belum Tersedia di Tahap Ini'"
        :description="$ringkasanStub ?? 'Fitur ini dijadwalkan pada pembangunan Tahap 2.'"
    >
        <x-slot:actions>
            <x-button :href="route('publik.prosedur-keberatan')" variant="primary">
                Baca Prosedur Pengajuan Keberatan (KF-06)
            </x-button>
            <x-button :href="route('mahasiswa.hasil')" variant="secondary">
                Kembali ke Hasil &amp; Rincian Saya
            </x-button>
        </x-slot:actions>
    </x-empty-state>
</x-layouts.app>

