<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use SW802F18\Contracts\SensorCluster as SensorClusterContract;
use SW802F18\Contracts\Scoring as ScoringContract;
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
    public function getAccumulatedScoreAttribute()
    {
        return $this->scores()->sum('total_score');
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
                SensorClusterContract::class,
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
        return $this->scores()->today()->get()->toArray();
    }

    /**
     * Sets the scores based on the values from the parameters
     * @param int Pulls per day.
     */
    public function addScore($pullsPerDay)
    {
        $this->setSensorDataIntervalAttribute([
            'days' => 0,
            'hours' => 0,
            'minutes' => 10,
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

        $scoring = app()->make(ScoringContract::class);
        $scoring->updateAllClassifications($uv, $light, $voc, $temperature, $co2, $noise, $humidity, $this->sensorDataEndTimeValue);

        $score = Score::make();
        $score->total_score = $scoring->totalScore($pullsPerDay);
        $score->iaq_score = $scoring->iaqScore();
        $score->visual_score = $scoring->visualScore();
        $score->sound_score = $scoring->soundScore();
        $score->temp_hum_score = $scoring->tempHumScore();
        $score->end_time = TimeHelper::carbonToNanoTime($this->sensorDataEndTimeValue);
        $score->interval = TimeHelper::intervalToSeconds([
            'days' => 0,
            'hours' => 0,
            'minutes' => 10,
            'seconds' => 0,
        ]);

        $this->scores()->save($score);
    }
}
