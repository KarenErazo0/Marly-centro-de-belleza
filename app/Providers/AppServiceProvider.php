<?php

namespace App\Providers;

use Carbon\Carbon;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        setlocale(LC_TIME, 'es_CO.UTF-8', 'es_CO', 'es_ES.UTF-8', 'es_ES', 'Spanish_Spain.1252');
        Carbon::setLocale('es');
    }
}
