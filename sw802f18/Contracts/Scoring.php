<?php

namespace SW802F18\Contracts;

interface Scoring
{


    /**
     * This function updates all the Classifications, instead of calling all the other functions individually. Convinience, I guess?
     * Call this before totalScore() in order to get the most recent score result.
     * 
     * @param uv UV
     * @param light Light level
     * @param voc VOC value
     * @param temperature Temperature
     * @param co2 CO2 value
     * @param noise Noise value
     * @param humidity Humidity value
     * @param nanoTime The time in nano seconds 
     */
    public function updateAllClassifications($uv, $light, $voc, $temperature, $co2, $noise, $humidity, $nanoTime);

    /**
     * Calculates a total score based on classifications.
     * 
     * @return totalScore
     */
    public function totalScore();

    
}