<?php

namespace SW802F18\Contracts;

interface SensorCluster
{
    /**
     * Initialises data as needed for the cluster.
     *
     * @return void
     */
    public function init($nodeMacAddress);

    /**
     * Retrieve metadata as an associative array.
     * Should have the keys:
     *  - valid
     *  - radioBusID
     *  - channel
     *  - nodeMacAddress
     *  - packetType
     *  - sequenceNumber
     *  - timestamp
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
     * Updates the cluster's sensor data and whatever else is needed.
     *
     * @return void
     */
    public function update();
}