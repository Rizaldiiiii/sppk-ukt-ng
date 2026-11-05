<?php

namespace App\Providers;

use App\Models\DokumenKeberatan;
use App\Models\HasilUkt;
use App\Models\KontenPublik;
use App\Models\PengajuanKeberatan;
use App\Models\Pertanyaan;
use App\Models\VersiParameter;
use App\Policies\DokumenPolicy;
use App\Policies\HasilPolicy;
use App\Policies\KeberatanPolicy;
use App\Policies\KontenPolicy;
use App\Policies\ParameterPolicy;
use App\Policies\PertanyaanPolicy;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Blade::component('layouts.app', 'layouts.app');
        Blade::component('layouts.guest', 'layouts.guest');

        Gate::policy(HasilUkt::class, HasilPolicy::class);
        Gate::policy(PengajuanKeberatan::class, KeberatanPolicy::class);
        Gate::policy(Pertanyaan::class, PertanyaanPolicy::class);
        Gate::policy(DokumenKeberatan::class, DokumenPolicy::class);
        Gate::policy(KontenPublik::class, KontenPolicy::class);
        Gate::policy(VersiParameter::class, ParameterPolicy::class);

        RateLimiter::for('login', function (Request $request) {
            $email = (string) $request->input('email');

            return Limit::perMinute(5)->by(mb_strtolower($email).'|'.$request->ip());
        });
    }
}
