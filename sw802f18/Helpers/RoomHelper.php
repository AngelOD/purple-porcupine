<?php

namespace SW802F18\Helpers;

use DB;
use App\Room;
use SW802F18\Contracts\SensorCluster;

class RoomHelper
{
    const CHARACTERS = '23456789ABCDEFGHJKMNPQRSTUVWXYZ';

    /**
     *
     */
    public static function getAveragedRoom($roomID)
    {
        $room = DB::where('internal_id', '=', $roomID)->first();

        if (empty($room)) { return null; }

        $retRoom = [
            'id' => $room->internal_id,
            'name' => $room->name,
            'altName' => $room->alt_name,
            'score' => 0,
            'co2' => 0,
            'humidity' => 0.0,
            'light' => 0,
            'noise' => 0,
            'pressure' => 0,
            'temperature' => 0.0,
            'uv' => 0,
            'voc' => 0,
        ];

        $scs = $room->sensorClusters;

        if (empty($scs)) { return null; }

        foreach ($scs as $sc) {

        }
    }

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
                $id .= substr(self::CHARACTERS, rand(0, strlen(self::CHARACTERS)), 1);
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