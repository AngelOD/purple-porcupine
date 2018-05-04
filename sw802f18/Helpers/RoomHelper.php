<?php

namespace SW802F18\Helpers;

use DB;
use App\Room;
use SW802F18\Contracts\SensorCluster;
use Carbon\Carbon;

class RoomHelper
{
    const CHARACTERS = '23456789ABCDEFGHJKMNPQRSTUVWXYZ';

    /**
     *
     */
    public static function getRandomRoomID()
    {
        $usedRoomIDs = self::getUsedRoomIDs();
        $id = '';

        while (empty($id) || in_array($id, $usedRoomIDs)) {
            $id = '';

            for ($i = 0; $i < 4; $i++) {
                $id .= substr(self::CHARACTERS, rand(0, strlen(self::CHARACTERS)-1), 1);
            }
        }

        return $id;
    }

    /**
     *
     */
    public static function carbonToNanoTime(Carbon $carbon)
    {
        if (empty($carbon)) { return false; }

        return $carbon->timestamp * 1000000000 + $carbon->micro * 1000;
    }

    public static function intervalToNanoInterval($interval)
    {
        if (!is_array($interval)) {
            if (is_numeric($interval)) { return $interval * 1000000000; }

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

        return $result * 1000000000;
    }

    /**
     * Retrieves existing room
     */
    private static function getUsedRoomIDs() {
        $roomIDs = [];
        $results = DB::table('rooms')->select('internal_id as iid')->get();

        foreach ($results as $res) {
            $roomIDs[] = $res->iid;
        }

        return $roomIDs;
    }
}