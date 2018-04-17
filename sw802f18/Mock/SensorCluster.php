<?php

namespace SW802F18\Mock;

use SW802F18\Contracts\SensorCluster as SensorClusterContract;
use SW802F18\Mock\MockSensor;

class SensorCluster implements SensorClusterContract
{
    private $sensors = null;

    public function init()
    {
        $this->update();
    }

    public function getSensors()
    {}

    public function getSensor($key)
    {}

    public function getSensorKeys()
    {}

    public function update()
    {
        $this->sensors = [
            new MockSensor([
                'dataType' => 'double',
                'maxValue' => 100.0,
                'minValue' => 0.0,
                'name' => 'temperature',
                'unit' => '°C',
                'value' => round((rand(500, 4000) / 100), 2),
            ]),
            new MockSensor([
                'dataType' => 'double',
                'maxValue' => 100.0,
                'minValue' => 0.0,
                'name' => 'humidity',
                'unit' => '%',
                'value' => round((rand(1, 10000) / 100), 2),
            ]),
            new MockSensor([
                'dataType' => 'double',
                'maxValue' => 8192.0,
                'minValue' => 400.0,
                'name' => 'co2',
                'unit' => 'ppm',
                'value' => round((rand(40000, 819200) / 100), 2),
            ]),
            new MockSensor([
                'dataType' => 'double',
                'maxValue' => 1187.0,
                'minValue' => 0.0,
                'name' => 'voc',
                'unit' => 'ppb',
                'value' => round((rand(1, 118700) / 100), 2),
            ]),
            new MockSensor([
                'dataType' => 'integer',
                'maxValue' => 15,
                'minValue' => 0,
                'name' => 'uv',
                'unit' => 'index',
                'value' => rand(0, 15),
            ]),
            new MockSensor([
                'dataType' => 'double',
                'maxValue' => 1000,
                'minValue' => 0.008,
                'name' => 'light',
                'unit' => 'lux',
                'value' => round((rand(8, 100000) / 100), 2),
            ]),
            new MockSensor([
                'dataType' => 'double',
                'maxValue' => 1200,
                'minValue' => 300,
                'name' => 'pressure',
                'unit' => 'hPa',
                'value' => round((rand(30000, 120000) / 100), 2),
            ]),
            new MockSensor([
                'dataType' => 'double',
                'maxValue' => 120,
                'minValue' => 30,
                'name' => 'noise',
                'unit' => 'decibel',
                'value' => round((rand(3000, 12000) / 100), 2),
            ]),
        ];
    }

    protected function getSensorReadings()
    {}
}