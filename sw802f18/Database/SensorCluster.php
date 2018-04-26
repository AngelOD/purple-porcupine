<?php

namespace SW802F18\Database;

use DB;
use Carbon\Carbon;
use SW802F18\Contracts\SensorCluser as SensorClusterContract;

class SensorCluster implements SensorClusterContract
{
    private $metadata = null;
    private $nodeMacAddress = null;
    private $sensors = null;

    public function init($nodeMacAddress)
    {
        $this->metadata = [
            'valid' => false,
            'channel' => 0,
            'nodeMacAddress' => $nodeMacAddress,
            'packetType' => 0,
            'radioBusID' => 0,
            'sequenceNumber' => 0,
            'timestamp' => Carbon::now(),
        ];
        $this->nodeMacAddress = $nodeMacAddress;
        $this->sensors = [];

        update();
    }

    public function getMetadata()
    {
        return $this->metadata;
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

    public function update()
    {
        $data = DB::table('radio_datas')
                    ->where('node_mac_address', '=', $this->node_mac_address)
                    ->orderBy('id', 'desc')
                    ->first();

        if (!empty($data)) {
            $this->metadata = array_merge(
                $this->metadata,
                [
                    'valid' => true,
                    'channel' => $data->channel,
                    'packetType' => $data->packet_type,
                    'radioBusID' => $data->radio_bus_id,
                    'sequenceNumber' => $data->sequence_number,
                    'timestamp' => Carbon::createFromTimestampMs(round($data->timestamp_nano / 1000000, 4)),
                ]
            );

            $sensorList = [
                'co2' => 'co2',
                'humidity' => 'humidity',
                'light' => 'light',
                'noise' => 'sound_pressure',
                'pressure' => 'pressure',
                'temperature' => 'temperature',
                'uv' => 'uv',
                'voc' => 'tvoc',
            ];

            foreach ($sensorList as $key => $value) {
                $this->sensors[$key] = array_merge(
                    config("sw802f18.sensorInfo.${$key}"),
                    ['value' => $data->$value]
                );
            }
        } else {
            $this->metadata['valid'] = false;
        }
    }
}