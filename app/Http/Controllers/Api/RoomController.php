<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Room;

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
            $data[] = [
                'id' => $room->internal_id,
                'name' => $room->name,
                'altName' => $room->alt_name,
                'score' => rand(0, 1500),
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

        return response()->json([
            "id" => $room->internal_id,
            "name" => $room->name,
            "altName" => $room->alt_name,
            "score" => rand(0, 1500),
        ], 200);
    }

    /**
     * Display the one or all sensors for a specific room. If $sensor = 'all' then all sensor data for a room will be returned.
     *
     * @param int $id
     * @param string $sensor
     * @return \Illuminiate\Http\Response
     */
    public function showSensor($id, $sensor = 'all')
    {
        $room = Room::where('internal_id', '=', strtoupper($roomID))->first();

        if (empty($room)) { return response()->json('Invalid parameter data', 400); }

        switch($sensor)
        {
            case 'all':
                return response()->json([
                    "id"=> $id,
                    "co2"=> 414,
                    "humidity"=> 18.93,
                    "light"=> 8,
                    "noise"=> 66,
                    "pressure"=> 101304,
                    "temperature"=> 23.22,
                    "uv"=> 3,
                    "voc"=> 2,
                ], 200);
                break;
            case 'co2':
                return response()->json([
                    "id"=>$id,
                    "type"=>"co2",
                    "value"=>414,
                ], 200);
                break;
            case 'humidity':
                return response()->json([
                    "id"=>$id,
                    "type"=>"humidity",
                    "value"=>18.93,
                ], 200);
                break;
            case 'light':
                return response()->json([
                    "id"=>$id,
                    "type"=>"light",
                    "value"=>8,
                ], 200);
                break;
            case 'noise':
                return response()->json([
                    "id"=>$id,
                    "type"=>"noise",
                    "value"=>66,
                ], 200);
                break;
            case 'pressure':
                return response()->json([
                    "id"=>$id,
                    "type"=>"pressure",
                    "value"=>101304,
                ], 200);
                break;
            case 'temperature':
                return response()->json([
                    "id"=>$id,
                    "type"=>"temperature",
                    "value"=>23.49,
                ], 200);
                break;
            case 'uv':
                return response()->json([
                    "id"=>$id,
                    "type"=>"uv",
                    "value"=>3,
                ], 200);
                break;
            case 'voc':
                return response()->json([
                    "id"=>$id,
                    "type"=>"voc",
                    "value"=>2,
                ], 200);
                break;
            default:
                return response()->json("No sensor found", 400);
        }
    }
}