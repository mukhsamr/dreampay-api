<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Laravel\Sanctum\Sanctum;

class AppServiceProvider extends ServiceProvider
{
    public function register()
    {
        Sanctum::ignoreMigrations();
    }

    public function boot()
    {
        //
    }
}
