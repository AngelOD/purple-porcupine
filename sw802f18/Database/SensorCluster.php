<?php

namespace SW802F18\Database;

use DB;
use SW802F18\Contracts\SensorCluser as SensorClusterContract;

class SensorCluster implements SensorClusterContract
{
    private $metadata = null;
    private $sensors = null;

    public function init()
    {
        $this->metadata = [
            'valid' => false,
            'channel' => 0,
            'nodeMacAddress' => '00000000',
            'packetType' => 0,
            'radioBusID' => 0,
            'sequenceNumber' => 0,
            'timestamp' => Carbon::now(),
        ];
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