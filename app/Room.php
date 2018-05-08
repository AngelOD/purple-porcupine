<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use SW802F18\Contracts\SensorCluster;
use SW802F18\Helpers\Scoring;
use Carbon\Carbon;
use SW802F18\Helpers\TimeHelper;

class Room extends Model
{
    private $sensorDataIntervalValue = null;
    private $sensorDataEndTimeValue = null;

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
    public function scores()
    {
        return $this->hasMany('App\Score', 'room_id', 'id');
    }

    /**
     *
     */
    public function getAverageSensorDataAttribute()
    {
        $counters = [];
        $data = [];

        if ($this->sensorClusters()->count() < 1) {
            return null;
        }

        foreach ($this->sensorClusters as $sc) {
            $scd = app()->makeWith(
                SensorCluster::class,
                [
                    'nodeMacAddress' => $sc->node_mac_address,
                    'endTime' => $this->sensorDataEndTimeValue,
                    'interval' => $this->sensorDataIntervalValue,
                ]
            );
            $sensors = $scd->getSensors();

            foreach ($sensors as $sensorType => $sensor) {
                if (!array_key_exists($sensorType, $data)) {
                    $data[$sensorType] = $sensor->value();
                    $counters[$sensorType] = 1;
                } else {
                    $data[$sensorType] += $sensor->value();
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

    /**
     * 
     */
    public function setSensorDataIntervalAttribute($interval)
    {
        $this->sensorDataIntervalValue = $interval;
    }

    /**
     * 
     */
    public function setSensorDataEndTimeAttribute($endTime)
    {
        $this->sensorDataEndTimeValue = $endTime;
    }

    /**
     * Returns the score for current day-
     * @return array Array empty if no scores are recorded.
     */
    public function getScoresForThisDayAttribute()
    {
        $data = [];
        $currentDay = Carbon::now();
        $startNanoTime = TimeHelper::carbonToNanoTime($currentDay->copy()->startOfDay());
        $endNanoTime = TimeHelper::carbonToNanoTime($currentDay->copy()->endOfDay());
        $scores = $this->scores()->whereBetween('end_time', [$startNanoTime, $endNanoTime])->get();

        if($scores->count() < 1)
        {
            return $data;
        }

        foreach($scores as $score)
        {
            $data[] = [
                'room_id' => $score->room_id,
                'id' => $score->id,
                'end_time' => $score->end_time,
                'interval' => $score->interval,
                'total_score' => $score->total_score,
                'IAQ_score' => $score->IAQ_score,
                'sound_score' => $score->sound_score,
                'temp_hum_score' => $score->temp_hum_score,
                'visual_score' => $score->visual_score,
            ];
        }

        return $data;
    }

    /**
     * Sets the scores based on the values from the parameters
     * @param int Pulls per day. 
     */
    public function addScore($pullsPerDay)
    {
        $this->setSensorDataIntervalAttribute([
            'days' => 0,
            'hours' => 1,
            'minutes' => 0,
            'seconds' => 0,
        ]);
        $this->setSensorDataEndTimeAttribute(Carbon::now());
        
        $data = $this->averageSensorData;
        $voc = $data['voc']; 
        $co2 = $data['co2']; 
        $noise = $data['noise']; 
        $light = $data['light']; 
        $temperature = $data['temperature']; 
        $humidity  = $data['humidity']; ; 
        $uv = $data['uv']; ;
        
        $scoring = new Scoring();
        $scoring->updateAllClassifications($uv, $light, $voc, $temperature, $co2, $noise, $humidity, $this->sensorDataEndTimeValue);

        $score = Score::make(); 
        $score->total_score = $scoring->totalScore($pullsPerDay);
        $score->IAQ_score = $scoring->IAQScore();
        $score->visual_score = $scoring->visualScore();
        $score->sound_score = $scoring->soundScore();
        $score->temp_hum_score = $scoring->tempHumScore();
        $score->end_time = TimeHelper::carbonToNanoTime($this->sensorDataEndTimeValue);
        $score->interval = TimeHelper::intervalToSeconds([
            'days' => 0,
            'hours' => 1,
            'minutes' => 0,
            'seconds' => 0,
        ]);

        $this->scores()->save($score);
    }
}
