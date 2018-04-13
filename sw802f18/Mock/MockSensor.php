<?php

namespace SW802F18\Mock;

use SW802F18\Contracts\Sensor as SensorContract;

class MockSensor implements SensorContract
{
    private $dataType = 'double';
    private $maxValue = 100.0;
    private $minValue = 0.0;
    private $name = 'temperature';
    private $unit = '°C';
    private $value = 22.43;

    public function __construct($data)
    {
        $this->updateData($data);
    }

    public function maxValue()
    {
        return $this->maxValue;
    }

    public function minValue()
    {
        return $this->minValue;
    }

    public function name()
    {
        return $this->name;
    }

    public function unit()
    {
        return $this->unit;
    }

    public function value()
    {
        return $this->value;
    }

    public function updateData($newData)
    {
        $keys = [
            'dataType', 'maxValue', 'minValue',
            'name', 'unit', 'value',
        ];

        foreach ($keys as $key) {
            if (isset($newData[$key])) {
                $this->$key = $newData[$key];
            }
        }
    }
}