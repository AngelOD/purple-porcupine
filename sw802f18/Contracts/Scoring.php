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
     * @param scorePulls the number of scorepulls per day.
     * @return totalScore
     */
    public function totalScore($scorePulls);

    /**
     * Calculates a score for temperature and humidity
     * 
     * @return tempHumScore
     */
    public function tempHumScore();

    /**
     * Calculates a score for sound
     * 
     * @return soundScore
     */
    public function soundScore();

    /**
     * Calculates a score for Visual
     * 
     * @return visualScore
     */
    public function visualScore();

    /**
     * Rates the Indoor Air Quality with a score.
     * 
     * @param void
     * @return totalScore a total score for the IAQ
     */
    public function IAQScore();

    /**
     * Calculates a score for VOC
     * 
     * @return vocScore
     */
    public function vocScore();

    /**
     * Calculates a score for Temperature
     *
     * @return tempScore
     */
    public function temperatureScore();

    /**
     * Calculates a score for UV
     *
     * @return uvScore
     */
    public function uvScore();

    /**
     * Calculates a score for CO2
     * 
     * @return co2Score
     */
    public function co2Score();

    /**
     * Calculates a score for humidity
     * 
     * @return humidityScore
     */
    public function humidityScore();

    /**
     * Calculates a score for noise
     * 
     * @return noiseScore
     */
    public function noiseScore();

    /**
     * Calculates a score for lux
     * 
     * @return noiseScore
     */
    public function luxScore();
}