<?php

namespace SW802F18\Mock;

use SW802F18\Contracts\SensorCluster as SensorClusterContract;
use SW802F18\Mock\MockSensor;
use Carbon\Carbon;

class SensorCluster implements SensorClusterContract
{
    private $metadata = [];
    private $nodeMacAddress = null;
    private $sensors = [];

    public function init($nodeMacAddress)
    {
        $this->nodeMacAddress = $nodeMacAddress;
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

    /**
     * @param void
     * @return void
     * Updates the sensors
     */
    public function update()
    {
        $this->metadata = [
            'valid' => true,
            'channel' => 0,
            'nodeMacAddress' => $this->nodeMacAddress,
            'packetType' => 1,
            'radioBusID' => 3,
            'sequenceNumber' => rand(0, 10000),
            'timestamp' => Carbon::now(),
        ];

        $this->sensors = [
            'co2' => new MockSensor(
                array_merge(
                    config('sw802f18.sensorInfo.co2'),
                    [
                        'value' => rand(
                            config('sw802f18.sensorInfo.co2.minValue'),
                            config('sw802f18.sensorInfo.co2.maxValue')
                        )
                    ]
                )
            ),
            'humidity' => new MockSensor(
                array_merge(
                    config('sw802f18.sensorInfo.humidity'),
                    [
                        'value' => round(
                            rand(
                                config('sw802f18.sensorInfo.humidity.minValue') * 100,
                                config('sw802f18.sensorInfo.humidity.minValue') * 100
                            ) / 100,
                            2
                        )
                    ]
                )
            ),
            'light' => new MockSensor(
                array_merge(
                    config('sw802f18.sensorInfo.light'),
                    [
                        'value' => rand(
                            config('sw802f18.sensorInfo.light.minValue'),
                            config('sw802f18.sensorInfo.light.maxValue')
                        )
                    ]
                )
            ),
            'noise' => new MockSensor(
                array_merge(
                    config('sw802f18.sensorInfo.noise'),
                    [
                        'value' => rand(
                            config('sw802f18.sensorInfo.noise.minValue'),
                            config('sw802f18.sensorInfo.noise.maxValue')
                        )
                    ]
                )
            ),
            'pressure' => new MockSensor(
                array_merge(
                    config('sw802f18.sensorInfo.pressure'),
                    [
                        'value' => rand(
                            config('sw802f18.sensorInfo.pressure.minValue'),
                            config('sw802f18.sensorInfo.pressure.maxValue')
                        )
                    ]
                )
            ),
            'temperature' => new MockSensor(
                array_merge(
                    config('sw802f18.sensorInfo.temperature'),
                    [
                        'value' => round(
                            rand(
                                config('sw802f18.sensorInfo.temperature.minValue') * 100,
                                config('sw802f18.sensorInfo.temperature.minValue') * 100
                            ) / 100,
                            2
                        )
                    ]
                )
            ),
            'uv' => new MockSensor(
                array_merge(
                    config('sw802f18.sensorInfo.uv'),
                    [
                        'value' => rand(
                            config('sw802f18.sensorInfo.uv.minValue'),
                            config('sw802f18.sensorInfo.uv.maxValue')
                        )
                    ]
                )
            ),
            'voc' => new MockSensor(
                array_merge(
                    config('sw802f18.sensorInfo.voc'),
                    [
                        'value' => rand(
                            config('sw802f18.sensorInfo.voc.minValue'),
                            config('sw802f18.sensorInfo.voc.maxValue')
                        )
                    ]
                )
            ),
        ];
    }
}