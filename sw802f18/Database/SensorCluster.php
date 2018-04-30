<?php

namespace SW802F18\Database;

use DB;
use Carbon\Carbon;
use SW802F18\Contracts\SensorCluster as SensorClusterContract;
use SW802F18\Helpers\RoomHelper;

class SensorCluster implements SensorClusterContract
{
    private $endTime        = null;
    private $interval       = [];
    private $metadata       = [];
    private $nodeMacAddress = null;
    private $sensors        = [];

    public function init($nodeMacAddress, $interval = null, $endTime = null)
    {
        $this->metadata = [
            'valid' => false,
            'nodeMacAddress' => $nodeMacAddress,
            'sequenceNumbers' => [],
            'timestamps' => [],
        ];
        $this->nodeMacAddress = $nodeMacAddress;
        $this->sensors = [];

        if (empty($endTime)) {
            $endTime = Carbon::now();
        }

        $this->setInterval($interval);
        $this->setEndTime($endTime);
        $this->update();
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

    public function setEndTime(Carbon $time)
    {
        $this->endTime = $time;
    }

    public function setInterval($interval)
    {
        $emptyInterval = [
            'years'     => 0,
            'months'    => 0,
            'days'      => 0,
            'hours'     => 0,
            'minutes'   => 0,
            'seconds'   => 0,
        ];

        if (empty($interval) || !is_array($interval)) {
            $this->interval = array_merge($emptyInterval, ['minutes' => 10]);
            return;
        }

        $this->interval = array_merge($emptyInterval, $interval);
    }

    public function update()
    {
        $times = $this->getNanoTimes();
        $data = DB::table('radio_datas')
                    ->selectRaw(
                        'avg(co2) as co2, '
                        .'avg(humidity) as humidity, '
                        .'avg(light) as light, '
                        .'avg(sound_pressure) as sound_pressure, '
                        .'avg(pressure) as pressure, '
                        .'avg(temperature) as temperature, '
                        .'avg(uv) as uv, '
                        .'avg(tvoc) as tvoc'
                    )
                    ->where('node_mac_address', '=', $this->nodeMacAddress)
                    ->where('timestamp_nano', '>', $times['start'])
                    ->where('timestamp_nano', '<=', $times['end'])
                    ->first();

        if (!empty($data)) {
            $metadata = [
                'valid' => true,
                'nodeMacAddress' => $this->nodeMacAddress,
                'timestamp' => $this->endTime->copy(),
            ];
            $sensorList = [
                'co2'           => 'co2',
                'humidity'      => 'humidity',
                'light'         => 'light',
                'noise'         => 'sound_pressure',
                'pressure'      => 'pressure',
                'temperature'   => 'temperature',
                'uv'            => 'uv',
                'voc'           => 'tvoc',
            ];

            // Zero out data
            foreach ($sensorList as $key => $s) {
                $this->sensors[$key] = new Sensor($key, $data->$s);
            }

            $this->metadata = $metadata;
        } else {
            $this->metadata['valid'] = false;
        }
    }

    private function getNanoTimes()
    {
        $start = $this->endTime->copy();
        $end = $this->endTime->copy();

        $start->subYears($this->interval['years'])
            ->subMonths($this->interval['months'])
            ->subDays($this->interval['days'])
            ->subHours($this->interval['hours'])
            ->subMinutes($this->interval['minutes'])
            ->subSeconds($this->interval['seconds']);

        return [
            'start' => RoomHelper::carbonToNanoTime($start),
            'end' => RoomHelper::carbonToNanoTime($end),
        ];
    }
}