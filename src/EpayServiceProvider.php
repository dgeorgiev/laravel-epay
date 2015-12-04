<?php

namespace Dgeorgiev\Epay;

use Illuminate\Support\ServiceProvider;

class EpayServiceProvider extends ServiceProvider
{
    /**
     * Perform post-registration booting of services.
     *
     * @return void
     */
    public function boot()
    {

        $this->publishes([
                __DIR__.'/config/config.php' => config_path('epay.php'),
        ]);

    }

    /**
     * Register any package services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind('Epay',function($app){
            return new Epay($app);
        });

        config([
                'config/epay.php',
        ]);
    }
}
