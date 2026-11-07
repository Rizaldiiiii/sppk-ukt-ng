<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Jadwal pemeriksaan harian tenggat SLA pengajuan keberatan UKT (KF-11 Tahap 3)
Schedule::command('sppk:cek-sla-keberatan')->dailyAt('08:00');
