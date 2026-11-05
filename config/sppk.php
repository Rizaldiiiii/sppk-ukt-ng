<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Keputusan Terbuka Tahap 1 (PRD Bagian 3.3 & Bagian 9 Butir 1–5)
    |--------------------------------------------------------------------------
    |
    | Keputusan ini ditulis eksplisit sebagai konfigurasi sesuai PRD Bagian 3.3
    | dan Blueprint Bagian 14–15, bukan ditebak diam-diam di dalam logika kode.
    |
    */

    // Butir 1: Formula, variabel, bobot, dan ambang resmi kampus belum diketahui.
    // Mesin penilaian berjalan sebagai model penjelasan/simulasi berparameter.
    // Untuk versi prototipe seeder ini, status simulasi dijelaskan di footer halaman hasil.
    'bagian_seeder_simulasi' => [
        'Nilai masukan contoh pada profil mahasiswa simulasi',
        'Parameter aturan, ambang skor, dan bobot komponen pada versi parameter simulasi',
        'Langkah perhitungan skor komponen dan estimasi golongan simulasi',
    ],

    // Butir 2: Kewenangan pimpinan atas rincian perorangan belum dikonfirmasi kampus.
    // Default wajib false: pimpinan hanya melihat rekap agregat tanpa daftar mahasiswa
    // dan tanpa data ekonomi perorangan.
    'pimpinan_lihat_rincian_perorangan' => env('SPPK_PIMPINAN_RINCIAN_PERORANGAN', false),

    // Butir 3: Sumber hasil penetapan resmi kampus.
    // Sampai integrasi otomatis ditetapkan kampus, alurnya adalah entri manual oleh petugas
    // dengan jejak audit.
    'sumber_hasil_resmi' => env('SPPK_SUMBER_HASIL_RESMI', 'entri_manual_petugas'),

    // Butir 4: Apakah kontak petugas dibuka untuk publik atau hanya setelah login.
    // Ditetapkan sebagai konfigurasi eksplisit (default: true pada informasi kanal umum).
    'kontak_petugas_publik' => env('SPPK_KONTAK_PETUGAS_PUBLIK', true),

    // Butir 5: Penyedia email resmi belum ditetapkan.
    // Di Tahap 1 lokal memakai MAIL_MAILER=log dan antrian database; email tidak pernah
    // memuat data ekonomi, dokumen, kata sandi, atau token.
    'email_notifikasi_aktif' => env('SPPK_EMAIL_NOTIFIKASI_AKTIF', true),
];

