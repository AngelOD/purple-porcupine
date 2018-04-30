<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SensorCluster extends Model
{
    public function room()
    {
        return $this->belongsTo('App\Room', 'room_id', 'id');
    }
}
