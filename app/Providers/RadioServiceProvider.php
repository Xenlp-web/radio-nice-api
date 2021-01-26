<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class RadioServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        return $this->app->bind('Radio', function () {
            return new \App\Services\RadioService();
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
