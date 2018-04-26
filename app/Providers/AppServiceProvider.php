<?php

namespace App\Providers;

use SW802F18\Contracts\SensorCluster as SensorClusterContract;
use SW802F18\Mock\SensorCluster;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind(SensorClusterContract::class, function($app, $vars){
            $sc = new SensorCluster();
            $sc->init($vars['nodeMacAddress']);
            return $sc;
        });
    }
}
