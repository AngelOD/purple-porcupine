<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use SW802F18\Helpers\RoomHelper;
use App\Room;
use App\Score;
use DB;

class RoomController extends Controller
{
    /**
     * List with all rooms.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $data = [];

        $rooms = Room::get();

        foreach ($rooms as $room) {
            $score = DB::table('scores')->where('room_id', '=', $room->internal_id)->latest()->first();
            $data[] = [
                'id' => $room->internal_id,
                'name' => $room->name,
                'altName' => $room->alt_name,
                'score' => $score->total_score,
            ];
        }

        return response()->json($data, 200);
    }

    /**
     * Display the specified resource.
     *
     * @param  string  $id
     * @return \Illuminate\Http\Response
     */
    public function show($roomID)
    {
        $room = Room::where('internal_id', '=', strtoupper($roomID))->first();

        if (empty($room)) { return response()->json('Invalid parameter data', 400); }
        $score = DB::table('scores')->where('room_id', '=', $room->internal_id)->latest()->first();
        return response()->json([
            "id" => $room->internal_id,
            "name" => $room->name,
            "altName" => $room->alt_name,
            "score" => $score->total_score,
        ], 200);
    }

    /**
     * Display the one or all sensors for a specific room. If $sensor = 'all' then all sensor data for a room will be returned.
     *
     * @param int $id
     * @param string $sensor
     * @return \Illuminiate\Http\Response
     */
    public function showSensor($roomID, $sensor = 'all')
    {
        $room = Room::where('internal_id', '=', strtoupper($roomID))->first();

        if (empty($room)) { return response()->json('Invalid parameter data', 400); }

        $data = $room->averageSensorData;

        switch($sensor)
        {
            case 'all':
                return response()->json(array_merge($data, ['id' => $roomID]), 200);

            case 'co2':
            case 'humidity':
            case 'light':
            case 'noise':
            case 'pressure':
            case 'temperature':
            case 'uv':
            case 'voc':
                return response()->json([
                    'id' => $roomID,
                    'type' => $sensor,
                    'value' => $data[$sensor],
                ], 200);
                
            default:
                return response()->json("No sensor found", 400);
        }
    }

    /**
     * Gets the socre for today.
     * @return array 
     */
    public function getScoresForToday($roomID)
    {
        $room = Room::where('internal_id', '=', strtoupper($roomID))->first();

        if(empty($room))
        {
            return response()->json('Invalid parameter data', 400);
        }

        return $room->scoresForThisDay;
    }
}