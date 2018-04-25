<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Room extends Model
{
    public function sensorClusters()
    {
        return $this->hasMany('App\SensorCluster', 'room_id', 'id');
    }
}
