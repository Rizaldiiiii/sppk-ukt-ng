<x-layouts.app
    title="Beranda Petugas UKT"
    subtitle="Ringkasan pengelolaan rincian hasil, parameter penilaian berversi, dan konten informasi publik"
>
    <!-- Statistik Ringkas Petugas -->
    <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-6">
        <div class="glass-card glass-card-light p-6 space-y-2" data-reveal="up" style="--reveal-delay: 0ms">
            <div class="text-[13px] font-medium text-ink-secondary">Mahasiswa Terdaftar</div>
            <div class="text-[28px] font-heading font-extrabold text-ink tabular-nums">{{ $totalMahasiswa }}</div>
            <div class="text-[12px] text-ink-secondary">Profil mahasiswa pada sistem</div>
        </div>

        <div class="glass-card glass-card-light p-6 space-y-2" data-reveal="up" style="--reveal-delay: 80ms">
            <div class="text-[13px] font-medium text-ink-secondary">Hasil Resmi vs Simulasi</div>
            <div class="text-[28px] font-heading font-extrabold text-sekunder tabular-nums">
                {{ $totalHasilResmi }} Resmi <span class="text-[16px] text-highlight">/ {{ $totalHasilSimulasi }} Simulasi</span>
            </div>
            <div class="text-[12px] text-ink-secondary">Dikelola lewat menu Entri &amp; Kelola Hasil</div>
        </div>

        <div class="glass-card glass-card-light p-6 space-y-2" data-reveal="up" style="--reveal-delay: 160ms">
            <div class="text-[13px] font-medium text-ink-secondary">Versi Parameter Aktif (KF-05)</div>
            <div class="text-[21px] font-heading font-extrabold text-ink">
                {{ $versiAktif?->versi ?? 'Belum ada' }}
            </div>
            <div class="text-[12px] text-ink-secondary">
                Berlaku sejak {{ $versiAktif?->berlaku_mulai?->locale('id')->translatedFormat('d M Y') ?? '-' }}
            </div>
        </div>

        <div class="glass-card glass-card-light p-6 space-y-2" data-reveal="up" style="--reveal-delay: 240ms">
            <div class="text-[13px] font-medium text-ink-secondary">Faktor &amp; Golongan Publik</div>
            <div class="text-[28px] font-heading font-extrabold text-ink tabular-nums">
                {{ $totalFaktorAktif }} Faktor • {{ $totalGolonganAktif }} Golongan
            </div>
            <div class="text-[12px] text-ink-secondary">Ditampilkan di halaman terbuka tanpa login</div>
        </div>
    </div>

    <!-- Aksi Utama Tahap 1 -->
    <section class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="glass-card glass-card-glow p-6 flex flex-col justify-between space-y-4">
            <div class="space-y-2">
                <h2 class="text-[21px] font-heading font-bold text-ink">
                    Entri &amp; Kelola Hasil UKT
                </h2>
                <p class="text-[14px] text-ink-secondary leading-relaxed">
                    Masukkan penetapan resmi kampus beserta rincian komponennya atau jalankan perhitungan simulasi untuk mahasiswa. Setiap perubahan memicu pemberitahuan KF-15.
                </p>
            </div>
            <x-button :href="route('petugas.hasil.index')" variant="primary">
                Buka Entri &amp; Kelola Hasil
            </x-button>
        </div>

        <div class="glass-card p-6 flex flex-col justify-between space-y-4">
            <div class="space-y-2">
                <h2 class="text-[21px] font-heading font-bold text-ink">
                    Parameter Penilaian Berversi (KF-05)
                </h2>
                <p class="text-[14px] text-ink-secondary leading-relaxed">
                    Perbarui bobot dan aturan komponen melalui penerbitan versi baru tanpa mengubah kode program dan tanpa mengubah hasil perhitungan lama.
                </p>
            </div>
            <x-button :href="route('petugas.parameter.index')" variant="secondary">
                Kelola Parameter Penilaian
            </x-button>
        </div>

        <div class="glass-card p-6 flex flex-col justify-between space-y-4">
            <div class="space-y-2">
                <h2 class="text-[21px] font-heading font-bold text-ink">
                    Kelola Konten Publik (KF-02/03/06/14)
                </h2>
                <p class="text-[14px] text-ink-secondary leading-relaxed">
                    Perbarui penjelasan alur proses, daftar faktor penentu beserta status konfirmasinya, rentang nominal golongan UKT, dan prosedur keberatan.
                </p>
            </div>
            <x-button :href="route('petugas.konten.index')" variant="secondary">
                Kelola Konten Publik
            </x-button>
        </div>
    </section>

    <!-- Pemantauan Pengiriman Email Notifikasi (KF-15) -->
    @if($kegagalanEmail->isNotEmpty())
        <section class="glass-card p-6 border-l-4 border-l-warning space-y-3">
            <div class="flex items-center justify-between">
                <h2 class="text-[18px] font-heading font-bold text-warning">
                    Catatan Kegagalan Antrean Email Pemberitahuan (KF-15)
                </h2>
                <x-badge-status status="warning" :label="$kegagalanEmail->count() . ' Kegagalan Tercatat'" />
            </div>
            <p class="text-[13px] text-ink-secondary">
                Perubahan hasil atau status tetap tersimpan, namun pengiriman email berikut gagal dan telah dicatat untuk ditindaklanjuti:
            </p>
            <div class="space-y-2">
                @foreach($kegagalanEmail as $log)
                    <div class="p-3 rounded-[8px] bg-warning-bg/75 text-[13px] text-ink flex flex-wrap justify-between gap-2">
                        <span><strong>{{ $log->nilai_sesudah['judul_peristiwa'] ?? 'Notifikasi Email' }}</strong> — Status: {{ $log->nilai_sesudah['status_baru'] ?? '-' }}</span>
                        <span class="text-warning">{{ $log->dibuat_pada?->locale('id')->translatedFormat('d M Y H:i') }}</span>
                    </div>
                @endforeach
            </div>
        </section>
    @endif
</x-layouts.app>
