<?php

namespace SW802F18\Mock;

use SW802F18\Contracts\SensorCluster as SensorClusterContract;
use SW802F18\Mock\MockSensor;
use Carbon\Carbon;

class SensorCluster implements SensorClusterContract
{
    private $metadata = [];
    private $sensors = [];

    public function init()
    {
        $this->update();
    }

    public function getSensors()
    {
        return $this->sensors;
    }

    public function getSensor($key)
    {
        if (!array_key_exists($key, $this->sensors) || empty($this->sensors[$key])) {
            return null;
        }

        return $this->sensors[$key];
    }

    public function getSensorKeys()
    {
        if (!is_array($this->sensors)) {
            return [];
        }

        return array_keys($this->sensors);
    }

    /**
     * @param void
     * @return void
     * Updates the sensors
     */
    public function update()
    {
        $this->metadata = [
            'valid' => true,
            'channel' => 0,
            'nodeMacAddress' => '000000FF',
            'packetType' => 1,
            'radioBusID' => 3,
            'sequenceNumber' => rand(0, 10000),
            'timestamp' => Carbon::now(),
        ];

        $this->sensors = [
            'temperature' => new MockSensor([
                'dataType' => 'double',
                'maxValue' => 100.0,
                'minValue' => 0.0,
                'name' => 'temperature',
                'unit' => '°C',
                'value' => round((rand(500, 4000) / 100), 2),
            ]),
            'humidity' => new MockSensor([
                'dataType' => 'double',
                'maxValue' => 100.0,
                'minValue' => 0.0,
                'name' => 'humidity',
                'unit' => '%',
                'value' => round((rand(1, 10000) / 100), 2),
            ]),
            'co2' => new MockSensor([
                'dataType' => 'integer',
                'maxValue' => 8192,
                'minValue' => 400,
                'name' => 'co2',
                'unit' => 'ppm',
                'value' => rand(400, 8192),
            ]),
            'voc' => new MockSensor([
                'dataType' => 'integer',
                'maxValue' => 1187,
                'minValue' => 0,
                'name' => 'voc',
                'unit' => 'ppb',
                'value' => rand(1, 1187),
            ]),
            'uv' => new MockSensor([
                'dataType' => 'integer',
                'maxValue' => 15,
                'minValue' => 0,
                'name' => 'uv',
                'unit' => 'index',
                'value' => rand(0, 15),
            ]),
            'light' => new MockSensor([
                'dataType' => 'integer',
                'maxValue' => 1000,
                'minValue' => 0,
                'name' => 'light',
                'unit' => 'lux',
                'value' => rand(0, 1000),
            ]),
            'pressure' => new MockSensor([
                'dataType' => 'integer',
                'maxValue' => 1200,
                'minValue' => 300,
                'name' => 'pressure',
                'unit' => 'hPa',
                'value' => rand(300, 1200),
            ]),
            'noise' => new MockSensor([
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