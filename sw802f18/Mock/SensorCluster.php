<?php

namespace SW802F18\Mock;

use SW802F18\Contracts\SensorCluster as SensorClusterContract;
use SW802F18\Mock\MockSensor;

class SensorCluster implements SensorClusterContract
{
    private $sensors = null;

    public function init()
    {}

    public function getSensors()
    {}

    public function getSensor($key)
    {}

    public function getSensorKeys()
    {}

    public function update()
    {}

    protected function getSensorReadings()
    {}
}