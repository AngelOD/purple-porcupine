<?php

namespace SW802F18\Helpers;

use Carbon\Carbon;

class TimeHelper
{
    /**
     *
     */
    public static function carbonToNanoTime(Carbon $carbon)
    {
        if (empty($carbon)) { return false; }

        return $carbon->timestamp * 1000000000 + $carbon->micro * 1000;
    }

    /**
     *
     */
    public static function intervalToSeconds($interval)
    {
        if (!is_array($interval)) {
            if (is_numeric($interval)) { return $interval; }

            return 0;
        }

        $result = 0;
        $inSeconds = [
            'days' => 24 * 60 * 60,
            'hours' => 60 * 60,
            'minutes' => 60,
            'seconds' => 1,
        ];

        foreach ($interval as $key => $value) {
            if (array_key_exists($key, $inSeconds)) {
                $result += $inSeconds[$key] * $value;
            }
        }

        return $result;
    }

    /**
     *
     */
    public static function intervalToNanoInterval($interval)
    {
        return self::intervalToSeconds($interval) * 1000000000;
    }
}