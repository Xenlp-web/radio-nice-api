<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class RobokassaServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        return $this->app->bind('Robokassa', function () {
            return new \App\Services\RobokassaService(env('ROBOKASSA_LOGIN'), env('ROBOKASSA_PASSWORD'));
        });
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
