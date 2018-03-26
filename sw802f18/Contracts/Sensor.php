<?php

namespace SW802F18\Contracts;

interface Sensor
{
    /**
     * Retrieve the maximum possible value of the given sensor.
     *
     * @return int|double|null Maximum value or null if unknown
     */
    public function maxValue();

    /**
     * Retrieve the minimum possible value of the given sensor.
     *
     * @return int|double|null Minimum value or null if unknown
     */
    public function minValue();

    /**
     * Retrieve the name of the sensor.
     *
     * @return string Sensor name
     */
    public function name();

    /**
     * Retrieve the string representation of the unit for the purpose of printing.
     *
     * @return string Unit used
     */
    public function unit();

    /**
     * Retrieve the current value of the sensor.
     *
     * @return int|double|null Current value or null if unknown
     */
    public function value();
}