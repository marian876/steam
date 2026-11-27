<?php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
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
        RateLimiter::for('acceso-cuenta', function (Request $request) {
            $correo = strtolower(trim((string) $request->input('email')));

            return Limit::perMinute(10)->by(hash('sha256', $correo.'|'.$request->ip()));
        });
    }
}
