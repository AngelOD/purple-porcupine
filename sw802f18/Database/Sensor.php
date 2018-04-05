<?php

namespace SW802F18\Database;

use SW802F18\Contracts\Sensor as SensorContract;

class Sensor implements SensorContract
{
    protected $data = null;

    public function __construct($name, $unit, $min, $max, $val)
    {
        $this->data = [
            'maxValue'  => $max,
            'minValue'  => $min,
            'name'      => $name,
            'unit'      => $unit,
            'value'     => $value,
        ];
    }

    public function maxValue()
    {
        return $this->getValue('maxValue');
    }

    public function minValue()
    {
        return $this->getValue('minValue');
    }

    public function name()
    {
        return $this->getValue('name');
    }

    public function unit()
    {
        return $this->getValue('unit');
    }

    public function value()
    {
        return $this->getValue('value');
    }

    protected function getValue($key) {
        return $this->data[$key] ?: null;
    }
}