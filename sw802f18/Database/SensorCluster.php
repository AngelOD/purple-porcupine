<?php

namespace SW802F18\Database;

use DB;
use SW802F18\Contracts\SensorCluser as SensorClusterContract;

class SensorCluster implements SensorClusterContract
{
    private $sensors = null;

    public function init()
    {
        $this->sensors = [];

        update();
    }

    public function getSensors()
    {}

    public function getSensor($key)
    {}

    public function getSensorKeys()
    {}

    public function update()
    {
        $data = DB::table('radio_datas')
                    ->orderBy('id', 'desc')
                    ->first();
    }

    protected function getSensorReadings()
    {}
}