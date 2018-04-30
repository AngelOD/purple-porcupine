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
                    ->where('node_mac_address', '=', $this->nodeMacAddress)
                    ->where('timestamp_nano', '>', $times['start'])
                    ->where('timestamp_nano', '<=', $times['end'])
                    ->orderBy('id', 'asc')
                    ->get();

        if ($data->count() > 0) {
            $sensorData = [];
            $sensorCount = [];
            $metadata = [
                'valid' => true,
                'nodeMacAddress' => $this->nodeMacAddress,
                'sequenceNumbers' => [],
                'timestamps' => [],
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
                $sensorData[$key] = 0;
                $sensorCount[$key] = 0;
            }

            // Loop through all the returned entries
            foreach ($data as $entry) {
                $metadata['sequenceNumbers'][] = $entry->sequence_number;
                $metadata['timestamps'][] = Carbon::createFromTimestampMs(round($entry->timestamp_nano / 1000000, 4));

                foreach ($sensorList as $key => $value) {
                    $sensorData[$key] += $entry->$value;
                    $sensorCount[$key]++;
                }
            }

            // Set the data based on the returned
            foreach ($sensorList as $key => $sensor) {
                $value = $sensorData[$key];
                if ($sensorCount[$key] > 1) { $value /= $sensorCount[$key]; }

                $this->sensors[$key] = new Sensor($key, $value);
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