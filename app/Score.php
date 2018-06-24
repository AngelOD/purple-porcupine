<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
use SW802F18\Helpers\TimeHelper;

class Score extends Model
{

    protected $fillable = [
        'end_time', 'interval', 'room_id', 'total_score'
    ];

    protected $hidden = [
        'id'
    ];

    public function room()
    {
        return $this->belongsTo('App\Room', 'room_id', 'id');
    }

    public function scopeToday($query)
    {
        $start = TimeHelper::carbonToNanoTime(Carbon::now()->setTimezone('Europe/Copenhagen')->startOfDay());
        $end = TimeHelper::carbonToNanoTime(Carbon::now()->setTimezone('Europe/Copenhagen'));

        return $query->whereBetween('end_time', [$start, $end]);
    }
}
