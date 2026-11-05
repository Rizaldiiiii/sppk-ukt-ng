<x-layouts.app
    title="Riwayat Perubahan (Rekap Auditor)"
    subtitle="Lingkup Fitur KF-13 • Dijadwalkan pada Tahap 2 (Pendukung)"
>
    <x-empty-state
        title="Penelusuran Riwayat Detail Belum Tersedia di Tahap Ini"
        description="Antarmuka penelusuran riwayat akses dan perubahan data (KF-13) dijadwalkan pada pembangunan Tahap 2. Pada Tahap 1 ini, pencatatan audit_logs di tingkat server sudah aktif dan tidak menyertakan data pribadi perorangan bagi peran Pimpinan."
    >
        <x-slot:actions>
            <x-button :href="route('pimpinan.rekap')" variant="primary">
                Kembali ke Rekap Agregat UKT
            </x-button>
        </x-slot:actions>
    </x-empty-state>
</x-layouts.app>

