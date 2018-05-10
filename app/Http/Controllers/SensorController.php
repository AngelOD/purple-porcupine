<?php

namespace App\Http\Controllers;

use App\SensorCluster;
use Illuminate\Http\Request;
use App\Room;

class SensorController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $rooms = Room::get();
        return view('sensor', ['rooms' => $rooms]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if($request->has('room-id') && $request->has('mac-address'))
        {
            $sensor = SensorCluster::make();
            $sensor->node_mac_address = $request->input('mac-address');
            $sensor->room_id = $request->input('room-id');
            
            $sensor->save();
        }
        return redirect('sensor/add')->with('status', 'Sensor saved');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\SensorCluster  $sensorCluster
     * @return \Illuminate\Http\Response
     */
    public function show(SensorCluster $sensorCluster)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\SensorCluster  $sensorCluster
     * @return \Illuminate\Http\Response
     */
    public function edit(SensorCluster $sensorCluster)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\SensorCluster  $sensorCluster
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, SensorCluster $sensorCluster)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\SensorCluster  $sensorCluster
     * @return \Illuminate\Http\Response
     */
    public function destroy(SensorCluster $sensorCluster)
    {
        //
    }
}
