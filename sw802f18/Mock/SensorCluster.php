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

    /**
     * @param void
     * @param array
     * Get all the sensors and data as an associative array
     */
    public function getSensors()
    {
        return $this->sensors;
    }

    public function getSensor($key)
    {
        
    }

    public function getSensorKeys()
    {}

    /**
     * @param void
     * @return void
     * Updates the sensors
     */
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
                'dataType' => 'integer',
                'maxValue' => 8192,
                'minValue' => 400,
                'name' => 'co2',
                'unit' => 'ppm',
                'value' => rand(400, 8192),
            ]),
            new MockSensor([
                'dataType' => 'integer',
                'maxValue' => 1187,
                'minValue' => 0,
                'name' => 'voc',
                'unit' => 'ppb',
                'value' => rand(1, 1187),
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
                'dataType' => 'integer',
                'maxValue' => 1000,
                'minValue' => 0,
                'name' => 'light',
                'unit' => 'lux',
                'value' => rand(0, 1000),
            ]),
            new MockSensor([
                'dataType' => 'integer',
                'maxValue' => 1200,
                'minValue' => 300,
                'name' => 'pressure',
                'unit' => 'hPa',
                'value' => rand(300, 1200),
            ]),
            new MockSensor([
                'dataType' => 'integer',
                'maxValue' => 120,
                'minValue' => 30,
                'name' => 'noise',
                'unit' => 'decibel',
                'value' => rand(30, 120),
            ]),
        ];
    }

    protected function getSensorReadings()
    {}
}