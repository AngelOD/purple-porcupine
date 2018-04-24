<?php
namespace SW802F18;
use Carbon\Carbon;
/**
 * This class has functions for calculating Classifications for sensors and a total score.
 * Most functions are actually more like classifiers.
 */
class Scoring
{
    private $co2Classification, $temperatureClassification, 
    $vocClassification, $lightClassification, 
    $noiseClassification, $humidityClassification, 
    $uvClassification;

    public function __construct()
    {

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
        $i = 0;
        $maxClassification = 5;
        while($i <= $maxClassification)
        {
            if(($low[$i] <= $parameters) && ($parameters <= $up[$i]))
            {
                $i++;
                return $i;
            }
            $i++;
        }
        return $maxClassification;
    }

    /**
     * This function makes a noise Classification based on the input
     * 
     * @param sensorValue the value from the sensor...
     */
    private function noiseClassification($sensorValue)
    {
        $lower = [0, 40, 50, 60, 75];
        $upper = [40, 50, 60, 75, 120];
        $this->noiseClassification = $this->classification($sensorValue, $lower, $upper);
    }

    /**
     * This function makes a co2 Classification based on the input
     * 
     * @param sensorValue the value from the sensor
     */
    private function co2Classification($sensorValue)
    {
        $lower = [0, 800, 1000, 1200, 1400];
        $upper = [800, 1000, 1200, 1400, 10000];
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
        $lower = [0, 60, 120, 180, 240];
        $upper = [60, 120, 180, 240, 1000];
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
     * @param sensorValue the value from the sensor
     * @param nanoTime the time in nanoseconds
     */
    private function humidityClassification($sensorValue, $nanoTime)
    {
        $lower;
        $upper;
        $ms = Carbon::createFromTimeStampMs($nanoTime / 1000);
        $month = $ms->month;
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
        $lower = [0, 200, 400, 1000, 5000];
        $upper = [200, 400, 1000, 5000, 10000];
        $this->lightClassification = $this->classification($sensorValue, $lower, $upper);
    }

    /**
     * This function updates all the Classifications, instead of calling all the other functions individually. Convinience, I guess?
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
    public function updateAllClassifications($uv, $light, $voc, $temperature, $co2, $noise, $humidity, $nanoTime)
    {
        $this->lightClassification($light);
        $this->uvClassification($uv);
        $this->co2Classification($co2);
        $this->humidityClassification($humidity, $nanoTime);
        $this->temperatureClassification($temperature);
        $this->vocClassification($voc);
        $this->noiseClassification($noise);
    }

    public function totalScore()
    {
        return 0;
        
    }

    private function soundScore()
    {
        $totalScore = 0;
        if($this->noiseClassification == NULL)
        {
            return $totalScore;
        }
        $sound = $this->noiseScore($this->noiseClassification)*0.8;
        $totalScore = $sound;
        return $totalScore;
    }

    /**
     * Calculates a score for Light
     * 
     * @return lightScore
     */
    private function lightScore()
    {
        $totalScore = 0;
        if($this->uvClassification == NULL || $this->lightClassification == NULL)
        {
            return $totalScore; //If we don't have the classifications, then we can't calculate a totalScore, so 0 is returned
        }
        $uv = $this->uvScore($this->uvClassification)*0.5;
        $lux = $this->luxScore($this->lightClassification)*0.4;
        $totalScore = $uv + $lux;
        return $totalScore;
    }

    /**
     * Rates the Indoor Air Quality with a score.
     * 
     * @param void
     * @return totalScore a total score for the IAQ
     */
    private function IAQScore()
    {
        $totalScore = 0;
        if($this->vocClassification == NULL || $this->temperatureClassification == NULL || $this->co2Classification == NULL || $this->noiseClassification == NULL || $this->humidityClassification == NULL)
        {
            return $totalScore;
        }
        $vocScore = $this->vocScore($this->vocClassification)*0.25;
        $tempScore = $this->temperatureScore($this->temperatureClassification)*0.3;
        $co2Score = $this->co2Score($this->co2Classification)*0.25;
        $humidity = $this->humidityScore($this->humidityClassification)*0.32;
        $totalScore = $vocScore + $tempScore + $co2Score + $humidity;
        return $totalScore;
    }

    /**
     * Calculates a score for VOC
     * 
     * @param classification 
     * @return vocScore
     */
    private function vocScore($classification)
    {
        $score = 0;
        if($classification == 1)
        {
            $score = 100;
        }
        if($classification == 2)
        {
            $score = 80;
        }
        return $score;
    }

    /**
     * Calculates a score for Temperature
     * 
     * @param classification
     * @return tempScore
     */
    private function temperatureScore($classification)
    {
        $score = 0;
        if ($classification == 3)
        {
            $score = 100;
        }
        return $score;
    }

    /**
     * Calculates a score for UV
     * 
     * @param classification
     * @return uvScore
     */
    private function uvScore($classification)
    {
        $score = 0;
        if($classification == 1)
        {
            $score = 100;
        }
        if($classification == 2)
        {
            $score = 80;
        }
        return $score;
    }

    /**
     * Calculates a score for CO2
     * 
     * @param classification 
     * @return co2Score
     */
    private function co2Score($classification)
    {
        $score = 0;
        if($classification == 1)
        {
            $score = 100;
        }
        if($classification == 2)
        {
            $score  = 80;
        }
        return $score;
    }

    /**
     * Calculates a score for humidity
     * 
     * @param classification 
     * @return humidityScore
     */
    private function humidityScore($classification)
    {
        $score = 0;
        if($classification == 3)
        {
            $score = 100;
        }
        if($classification == 2 || $classification == 4)
        {
            $score = 75;
        }
        return $score;
    }

    /**
     * Calculates a score for noise
     * 
     * @param classification 
     * @return noiseScore
     */
    private function noiseScore($classification)
    {
        $score = 0;
        if($classification == 1)
        {
            $score = 100;
        }
        if($classification == 2)
        {
            $score = 80;
        }
        if($classification == 3)
        {
            $score = 60;
        }
        return $score;
    }

    /**
     * Calculates a score for lux
     * 
     * @param classification 
     * @return noiseScore
     */
    private function luxScore($classification)
    {
        $score = 0;
        if($classification == 1)
        {
            $score = 60;
        }
        if($classification == 2)
        {
            $score = 100;
        }
        if($classification == 3)
        {
            $score = 80;
        }
        return $score;
    }
}
?>