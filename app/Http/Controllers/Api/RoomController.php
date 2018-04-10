<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class RoomController extends Controller
{
    /**
     * List with all rooms.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return response()->json([
                [
                    "id" => "F6H7", 
                    "name" => "0.2.12", 
                    "altName" => "", 
                    "score" => 243,
                ],
                [
                    "id" => "YZZE", 
                    "name" => "0.2.13", 
                    "altName" => "", 
                    "score" => 284,
                ],
                [
                    "id" => "LAZC", 
                    "name" => "0.2.90", 
                    "altName" => "", 
                    "score" => 634,
                ],
                [
                    "id" => "2U2C", 
                    "name" => "0.1.95", 
                    "altName" => "Auditorie", 
                    "score" => 1438,
                ],
        ], 200);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return response()->json([
            "id" => $id, 
            "name" => "0.2.12", 
            "altName" => "", 
            "score" => 243,
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
        // Check if $sensor is 'all' then show all sensors.
        //mock data
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
                    "uv"=> 0,
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
                    "value"=>0,
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