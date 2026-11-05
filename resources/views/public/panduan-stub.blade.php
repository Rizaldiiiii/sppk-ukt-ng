<x-layouts.guest title="Panduan Visual Tahapan Proses">
    <div class="py-6">
        <x-empty-state
            title="Panduan Visual Tahapan Proses Belum Tersedia di Tahap Ini"
            description="Fitur panduan visual bergambar/video tahapan proses (KF-10) dijadwalkan pada pembangunan Tahap 3 (Lanjutan). Pada Tahap 1 ini, seluruh penjelasan tahapan penentuan UKT dan prosedur pengajuan keberatan sudah tersedia lengkap dalam bentuk penjelasan tertulis dan tabel."
        >
            <x-slot:actions>
                <x-button :href="route('publik.alur')" variant="primary">
                    Baca Alur Penentuan UKT (KF-02)
                </x-button>
                <x-button :href="route('publik.prosedur-keberatan')" variant="secondary">
                    Baca Prosedur Keberatan (KF-06)
                </x-button>
            </x-slot:actions>
        </x-empty-state>
    </div>
</x-layouts.guest>

