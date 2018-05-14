<?php

namespace SW802F18\Influx;

use DB;
use Carbon\Carbon;
use SW802F18\Contracts\SensorCluster as SensorClusterContract;
use SW802F18\Helpers\TimeHelper;

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
        $fields = [
            'co2', 'humidity', 'light', 'sound_pressure', 'pressure', 'temperature', 'uv', 'tvoc',
        ];

        $times = $this->getNanoTimes();
        $resultSet = InfluxDB::query(
            'SELECT '
            .implode(', ', array_map(function ($e) { return sprintf('MEAN("%1$s") AS "%1$s"', $e); }, $fields))
            .' FROM radio_datas '
            .' WHERE "node_mac_address" = ' . $this->nodeMacAddress
            .' AND time > ' . $times['start']
            .' AND time <=' . $times['end']
        );
        $data = $resultSet->getPoints();

        if (!empty($data)) {
            $data = $data[0];

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
                $this->sensors[$key] = new Sensor($key, $data[$s]);
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

        $start->subDays($this->interval['days'])
            ->subHours($this->interval['hours'])
            ->subMinutes($this->interval['minutes'])
            ->subSeconds($this->interval['seconds']);

        return [
            'start' => TimeHelper::carbonToNanoTime($start),
            'end' => TimeHelper::carbonToNanoTime($end),
        ];
    }

    public function getFullDataset($nodeMacAddresses, Carbon $startTime, Carbon $endTime, $interval, $tz = 'Europe/Copenhagen')
    {
        // Ensure that we're dealing with an array
        if (!is_array($nodeMacAddresses)) {
            $nodeMacAddresses = [$nodeMacAddresses];
        }

        // Prepare list of sensor types with both types of names (API and DB)
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

        $result = [];
        $startTimeNano = TimeHelper::carbonToNanoTime($startTime);
        $endTimeNano = TimeHelper::carbonToNanoTime($endTime);
        $intervalNano = TimeHelper::intervalToNanoInterval($interval);
        $resultSet = InfluxDB::query(
            'SELECT *'
            .' FROM radio_datas'
            .' WHERE (' . implode(' OR ', array_map(function ($e) { return sprintf('"node_mac_address" = %s', $e); }, $nodeMacAddresses)) . ')'
            .' AND time > ' . $startTimeNano
            .' AND time <= ' . $endTimeNano
            .' ORDER BY time ASC'
        );
        $dataset = $resultSet->getPoints();

        if (count($dataset) < 1) { return $result; }

        foreach ($dataset as $entry) {
            $time = TimeHelper::carbonToNanoTime(Carbon::parse($entry['time']));
            $index = intdiv($time - $startTimeNano, $intervalNano);

            if (!isset($result[$index])) {
                $timestamp = $startTimeNano + $index * $intervalNano;
                $result[$index] = [
                    'count'         => 0,
                    'timestamp'     => Carbon::createFromTimestampMs($timestamp / 1000000, $tz),
                    'co2'           => 0,
                    'humidity'      => 0,
                    'light'         => 0,
                    'noise'         => 0,
                    'pressure'      => 0,
                    'temperature'   => 0,
                    'uv'            => 0,
                    'voc'           => 0,
                ];
            }

            $result[$index]['count']++;
            $result[$index]['co2'] += $entry['co2'];
            $result[$index]['humidity'] += $entry['humidity'] / 1000;
            $result[$index]['light'] += $entry['light'];
            $result[$index]['noise'] += $entry['sound_pressure'];
            $result[$index]['pressure'] += $entry['pressure'] / 100;
            $result[$index]['temperature'] += $entry['temperature'] / 100;
            $result[$index]['uv'] += $entry['uv'];
            $result[$index]['voc'] += $entry['tvoc'];
        }

        foreach ($result as $index => $entry) {
            $count = $entry['count'];

            $result[$index]['co2'] /= $count;
            $result[$index]['humidity'] /= $count;
            $result[$index]['light'] /= $count;
            $result[$index]['noise'] /= $count;
            $result[$index]['pressure'] /= $count;
            $result[$index]['temperature'] /= $count;
            $result[$index]['uv'] /= $count;
            $result[$index]['voc'] /= $count;
        }

        return $result;
    }
}