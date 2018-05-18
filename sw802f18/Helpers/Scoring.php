<?php

namespace SW802F18\Helpers;

use Carbon\Carbon;
use SW802F18\Contracts\Scoring as ScoringContract;

/**
 * This class has functions for calculating Classifications for sensors and a total score.
 * Most functions are actually more like classifiers.  
 */
class Scoring implements ScoringContract
{
    private $co2Classification, $temperatureClassification,
            $vocClassification, $lightClassification,
            $noiseClassification, $humidityClassification,
            $uvClassification;

    public function __construct()
    {
        $co2Classification          = 0;
        $temperatureClassification  = 0;
        $vocClassification          = 0;
        $lightClassification        = 0;
        $noiseClassification        = 0;
        $humidityClassification     = 0;
        $uvClassification           = 0;
    }

    /**
     * This function returns a Classification no matter what type of sensor the data comes from.
     *
     * @param parameters is the output from the sensor
     * @param low low is the an array of the lower bound values for each comparision.
     * @param up up is the an array of the upper bound values for each comparision.
     * @return classification Returns an int value
     */
    private function classification($parameter, array $low, array $up)
    {
        $maxClassification = 4;
        for($i = 0; $i <= $maxClassification; $i++)
        {
            if(($low[$i] <= $parameter) && ($parameter <= $up[$i]))
            {
                $i++;
                return $i;
            }
        }
        $maxClassification++;
        return $maxClassification;
    }

    /**
     * This function makes a noise Classification based on the input
     *
     * @param sensorValue the value from the sensor...
     */
    private function noiseClassification($sensorValue)
    {
        $lower = [0, 45, 60, 67, 75];
        $upper = [45, 60, 67, 75, 120];
        $this->noiseClassification = $this->classification($sensorValue, $lower, $upper);
    }

    /**
     * This function makes a co2 Classification based on the input
     *
     * @param sensorValue the value from the sensor
     */
    private function co2Classification($sensorValue)
    {
        $lower = [0, 1000, 1200, 1400, 2000];
        $upper = [1000, 1200, 1400, 2000, 10000];
        $this->co2Classification = $this->classification($sensorValue, $lower, $upper);
    }

    /**
     * TODO: upper og lower values skal muligvis rettes
     * This function makes a VOC Classification based on the input
     *
     * @param sensorValue the value from the sensor
     */
    private function vocClassification($sensorValue)
    {
        $lower = [0, 60, 90, 120, 180];
        $upper = [60, 90, 120, 180, 1000];
        $this->vocClassification = $this->classification($sensorValue, $lower, $upper);
    }

    /**
     * This function makes a temperature Classification based on the input
     *
     * @param sensorValue the value from the sensor
     */
    private function temperatureClassification($sensorValue)
    {
        $lower = [0, 18, 19.5, 23.5, 25];
        $upper = [18, 19.5, 23.5, 25, 100];
        $this->temperatureClassification = $this->classification($sensorValue, $lower, $upper);
    }

    /**
     * This function makes a humidity Classification based on the input and  time
     *
     * @param double|int $sensorValue the value from the sensor
     * @param Carbon $nanoTime the time in nanoseconds
     */
    private function humidityClassification($sensorValue, Carbon $time)
    {
        $lower;
        $upper;
        $month = $time->month;
        if($month == 1 || $month == 2 || $month == 3 || $month == 4 || $month == 10 || $month == 11 || $month == 12) //Winter months
        {
            $lower = [0, 25, 35, 40, 45];
            $upper = [25, 35, 40, 45, 100];
        }
        else //If it isn't winter, then it must be summer.
        {
            $lower = [0, 25, 35, 50, 60];
            $upper = [25, 35, 50, 60, 100];
        }
        $this->humidityClassification = $this->classification($sensorValue, $lower, $upper);
    }

    /**
     * This function makes a humidity Classification based on the input
     *
     * @param sensorValue the value from the sensor
     */
    private function uvClassification($sensorValue)
    {
        $lower = [0, 1, 2, 3, 5];
        $upper = [1, 2, 3, 5, 15];
        $this->uvClassification = $this->classification($sensorValue, $lower, $upper);
    }

    /**
     * This function makes light Classification based on the input
     *
     * @param sensorValue the value from the sensor
     */
    private function lightClassification($sensorValue)
    {
        $lower = [0, 200, 400, 1000, 2000];
        $upper = [200, 400, 1000, 2000, 10000];
        $this->lightClassification = $this->classification($sensorValue, $lower, $upper);
    }

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
    public function updateAllClassifications($uv, $light, $voc, $temperature, $co2, $noise, $humidity, Carbon $time)
    {
        $this->lightClassification($light);
        $this->uvClassification($uv);
        $this->co2Classification($co2);
        $this->humidityClassification($humidity, $time);
        $this->temperatureClassification($temperature);
        $this->vocClassification($voc);
        $this->noiseClassification($noise);
    }

    /**
     * Calculates a total score based on classifications.
     *
     * @param scorePulls the number of scorepulls per day
     * @return totalScore
     */
    public function totalScore($scorePulls = 1)
    {
        $sound = $this->soundScore()*0.25;
        $visual = $this->visualScore()*0.22;
        $iaq = $this->iaqScore()*0.3;
        $tempHum = $this->tempHumScore()*0.23;
        return ($iaq + $visual + $sound + $tempHum) / $scorePulls;
    }

    /**
     * Calculates a score for temperature and humidity
     *
     * @return tempHumScore
     */
    public function tempHumScore()
    {
        $totalScore = 0;
        if($this->temperatureClassification == NULL || $this->humidityClassification == NULL)
        {
            return $totalScore;
        }
        $tempScore = $this->temperatureScore()*0.5;
        $humidity = $this->humidityScore()*0.5;
        $totalScore = $tempScore + $humidity;
        return $totalScore;
    }

    /**
     * Calculates a score for sound
     *
     * @return totalScore
     */
    public function soundScore()
    {
        $totalScore = 0;
        if($this->noiseClassification == NULL)
        {
            return $totalScore;
        }
        $sound = $this->noiseScore();
        $totalScore = $sound;
        return $totalScore;
    }

    /**
     * Calculates a score for visual
     *
     * @return visualScore
     */
    public function visualScore()
    {
        $totalScore = 0;
        if($this->uvClassification == NULL || $this->lightClassification == NULL)
        {
            return $totalScore; //If we don't have the classifications, then we can't calculate a totalScore, so 0 is returned
        }
        $uv = $this->uvScore()*0.5;
        $lux = $this->luxScore()*0.5;
        $totalScore = $uv + $lux;
        return $totalScore;
    }

    /**
     * Rates the Indoor Air Quality with a score.
     *
     * @param void
     * @return double a total score for the IAQ
     */
    public function iaqScore()
    {
        $totalScore = 0;
        if($this->vocClassification == NULL || $this->temperatureClassification == NULL || $this->co2Classification == NULL || $this->noiseClassification == NULL || $this->humidityClassification == NULL)
        {
            return $totalScore;
        }
        $vocScore = $this->vocScore()*0.5;
        $co2Score = $this->co2Score()*0.5;
        $totalScore = $vocScore + $co2Score;
        return $totalScore;
    }

    /**
     * Calculates a score for VOC
     *
     * @return vocScore
     */
    public function vocScore()
    {
        $score = 0;
        if($this->vocClassification == 1)
        {
            $score = 100;
        }
        if($this->vocClassification == 2)
        {
            $score = 75;
        }
        if($this->vocClassification == 3)
        {
            $score = 50;
        }
        if($this->vocClassification == 4)
        {
            $score = 25;
        }
        return $score;
    }

    /**
     * Calculates a score for Temperature
     *
     * @return tempScore
     */
    public function temperatureScore()
    {
        $score = 0;
        if ($this->temperatureClassification == 3)
        {
            $score = 100;
        }
        if($this->temperatureClassification == 2 || $this->temperatureClassification == 4)
        {
            $score = 50;
        }
        return $score;
    }

    /**
     * Calculates a score for UV
     *
     * @return uvScore
     */
    public function uvScore()
    {
        $score = 0;
        if($this->uvClassification == 1)
        {
            $score = 100;
        }
        if($this->uvClassification == 2)
        {
            $score = 75;
        }
        return $score;
    }

    /**
     * Calculates a score for CO2
     *
     * @return co2Score
     */
    public function co2Score()
    {
        $score = 0;
        if($this->co2Classification == 1)
        {
            $score = 100;
        }
        if($this->co2Classification == 2)
        {
            $score  = 75;
        }
        if($this->co2Classification == 3)
        {
            $score  = 50;
        }
        if($this->co2Classification == 4)
        {
            $score  = 25;
        }

        return $score;
    }

    /**
     * Calculates a score for humidity
     *
     * @return humidityScore
     */
    public function humidityScore()
    {
        $score = 0;
        if($this->humidityClassification == 3)
        {
            $score = 100;
        }
        if($this->humidityClassification == 2 || $this->humidityClassification == 4)
        {
            $score = 50;
        }
        return $score;
    }

    /**
     * Calculates a score for noise
     *
     * @return noiseScore
     */
    public function noiseScore()
    {
        $score = 0;
        if($this->noiseClassification == 1)
        {
            $score = 100;
        }
        if($this->noiseClassification == 2)
        {
            $score = 75;
        }
        if($this->noiseClassification == 3)
        {
            $score = 50;
        }
        if($this->noiseClassification == 4)
        {
            $score = 25;
        }
        return $score;
    }

    /**
     * Calculates a score for lux
     *
     * @return lightScore
     */
    public function luxScore()
    {
        $score = 0;
        if($this->lightClassification == 3)
        {
            $score = 100;
        }
        if($this->lightClassification == 2 || $this->lightClassification == 4)
        {
            $score = 50;
        }
        return $score;
    }
}
?>