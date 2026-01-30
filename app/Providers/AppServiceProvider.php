<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\URL; // <--- JANGAN LUPA IMPORT INI

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        // Jika di Production (Hosting), Paksa HTTPS
        if($this->app->environment('production') || env('APP_ENV') === 'production') {
            URL::forceScheme('https');
        }
    }
}