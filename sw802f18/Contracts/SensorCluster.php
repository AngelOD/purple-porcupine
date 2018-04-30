<?php

namespace SW802F18\Contracts;

use Carbon\Carbon;

interface SensorCluster
{
    /**
     * Initialises data as needed for the cluster.
     *
     * @return void
     */
    public function init($nodeMacAddress, $interval = null, $endTime = null);

    /**
     * Retrieve metadata as an associative array.
     * Should have the keys:
     *  - valid [boolean]
     *  - nodeMacAddress [string]
     *  - timestamp [Carbon]
     */
    public function getMetadata();

    /**
     * Retrieve an associated list of sensors in the cluster.
     *
     * @return Sensor[]
     */
    public function getSensors();

    /**
     * Retrieve a specific sensor from the cluster.
     *
     * @param string $key The key of the cluster to retrieve.
     * @return Sensor|null The given sensor if it exists. Otherwise returns null.
     */
    public function getSensor($key);

    /**
     * Retrieve list of sensors in the given cluster as keys for use with getCluster($key).
     *
     * @return string[] The list of sensor keys.
     */
    public function getSensorKeys();

    /**
     * Set the end time for the fetched data. Should default to current time.
     *
     * @param Carbon $time Carbon instance with the time
     */
    public function setEndTime(Carbon $time);

    /**
     * Set the desired interval for the fetched data.
     * Valid keys: days, hours, minutes, seconds
     *
     * @param int[] $interval Assoc. array of data points.
     */
    public function setInterval($interval);

    /**
     * Updates the cluster's sensor data and whatever else is needed.
     *
     * @return void
     */
    public function update();

    /**
     *
     */
    public function getFullDataset($nodeMacAddresses, Carbon $startTime, Carbon $endTime, $interval);
}