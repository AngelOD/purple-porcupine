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
        ];
    }

    protected function getSensorReadings()
    {}
}