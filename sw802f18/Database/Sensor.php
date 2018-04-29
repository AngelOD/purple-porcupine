<?php

namespace SW802F18\Database;

use SW802F18\Contracts\Sensor as SensorContract;

class Sensor implements SensorContract
{
    protected $data = null;
    protected $sensorType = '';
    protected $value = '';

    public function __construct($sensorType, $value)
    {
        $dataType = config("sw802f18.sensorInfo.${sensorType}.dataType");
        $this->sensorType = $sensorType;

        switch ($sensorType) {
            case 'humidity':
                $value /= 1000;
            break;

            case 'pressure':
            case 'temperature':
                $value /= 100;
            break;
        }

        if ($dataType === 'integer') {
            $value = (int)round($value, 0);
        } elseif ($dataType === 'double') {
            $value = (double)round($value, ($sensorType === 'humidity' ? 3 : 2));
        }

        $this->value = $value;
    }

    public function maxValue()
    {
        return config('sw802f18.sensorInfo.' . $this->sensorType . '.maxValue');
    }

    public function minValue()
    {
        return config('sw802f18.sensorInfo.' . $this->sensorType . '.minValue');
    }

    public function name()
    {
        return config('sw802f18.sensorInfo.' . $this->sensorType . '.name');
    }

    public function unit()
    {
        return config('sw802f18.sensorInfo.' . $this->sensorType . '.unit');
    }

    public function value()
    {
        return $this->value;
    }
}