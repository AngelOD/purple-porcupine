<?php

namespace SW802F18\Helpers;

use DB;
use App\Room;
use SW802F18\Contracts\SensorCluster;
use Carbon\Carbon;

class RoomHelper
{
    const CALCULATIONS_PER_DAY = 8;
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