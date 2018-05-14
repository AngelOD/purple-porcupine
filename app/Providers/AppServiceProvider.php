<?php

namespace App\Providers;

use SW802F18\Contracts\Scoring as ScoringContract;
use SW802F18\Contracts\SensorCluster as SensorClusterContract;
use SW802F18\Database\SensorCluster as MySqlSensorCluster;
use SW802F18\Influx\SensorCluster as InfluxSensorCluster;
use SW802F18\Helpers\Scoring;
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
        $this->app->bind(ScoringContract::class, function($app) {
            $sc = new Scoring();
            return $sc;
        });

        $this->app->bind(SensorClusterContract::class, function($app, $vars) {
            if (config('influxdb.use')) {
                $sc = new InfluxSensorCluster();
            } else {
                $sc = new MySqlSensorCluster();
            }

            if (!array_key_exists('skipInit', $vars) || $vars['skipInit'] !== true) {
                if (!array_key_exists('endTime', $vars)) { $vars['endTime'] = null; }
                if (!array_key_exists('interval', $vars)) { $vars['interval'] = null; }

                $sc->init($vars['nodeMacAddress'], $vars['interval'], $vars['endTime']);
            }

            return $sc;
        });
    }
}
