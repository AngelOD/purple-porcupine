<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use SW802F18\Contracts\SensorCluster;

class Room extends Model
{
    /**
     *
     */
    public function sensorClusters()
    {
        return $this->hasMany('App\SensorCluster', 'room_id', 'id');
    }

    /**
     *
     */
    public function getAverageSensorDataAttribute()
    {
        $counters = [];
        $data = [];

        if ($this->sensorClusters()->count() <= 1) {
            return $this->sensorClusters()->first();
        }

        foreach ($this->sensorClusters as $sc) {
            $scd = app()->makeWith(
                SensorCluster::class,
                ['nodeMacAddress' => $sc->node_mac_address]
            );
            $sensors = $scd->getSensors();

            foreach ($sensors as $sensorType => $sensor) {
                if (!array_key_exists($sensorType, $data)) {
                    $data[$sensorType] = $sensor->getValue();
                    $counters[$sensorType] = 1;
                } else {
                    $data[$sensorType] += $sensor->getValue();
                    $counters[$sensorType]++;
                }
            }
        }

        foreach ($data as $key => $value) {
            if ($counters[$key] > 1) {
                $data[$key] /= $counters[$key];

                if (config("sw802f18.sensorInfo.${$key}.dataType") === 'integer') {
                    $data[$key] = round($data[$key], 0);
                }
            }
        }

        return $data;
    }
}
