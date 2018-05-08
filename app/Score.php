<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Score extends Model
{

    protected $fillable = [
        'end_time', 'interval', 'room_id', 'total_score'
    ];

    protected $hidden = [
        'id'
    ];
    //
    public function room()
    {
        return $this->belongsTo('App\Room', 'room_id', 'id');
    }

    
}
