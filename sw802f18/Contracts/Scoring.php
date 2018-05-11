<?php

namespace SW802F18\Contracts;

use Carbon\Carbon;

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
    public function updateAllClassifications($uv, $light, $voc, $temperature, $co2, $noise, $humidity, Carbon $nanoTime);

    /**
     * Calculates a total score based on classifications.
     *
     * @param scorePulls the number of scorepulls per day.
     * @return double
     */
    public function totalScore($scorePulls);

    /**
     * Calculates a score for temperature and humidity
     *
     * @return double
     */
    public function tempHumScore();

    /**
     * Calculates a score for sound
     *
     * @return double
     */
    public function soundScore();

    /**
     * Calculates a score for Visual
     *
     * @return double
     */
    public function visualScore();

    /**
     * Rates the Indoor Air Quality with a score.
     *
     * @param void
     * @return double a total score for the IAQ
     */
    public function iaqScore();

    /**
     * Calculates a score for VOC
     *
     * @return double
     */
    public function vocScore();

    /**
     * Calculates a score for Temperature
     *
     * @return double
     */
    public function temperatureScore();

    /**
     * Calculates a score for UV
     *
     * @return double
     */
    public function uvScore();

    /**
     * Calculates a score for CO2
     *
     * @return double
     */
    public function co2Score();

    /**
     * Calculates a score for humidity
     *
     * @return double
     */
    public function humidityScore();

    /**
     * Calculates a score for noise
     *
     * @return double
     */
    public function noiseScore();

    /**
     * Calculates a score for lux
     *
     * @return double
     */
    public function luxScore();
}