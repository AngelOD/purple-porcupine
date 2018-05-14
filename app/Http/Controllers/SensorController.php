<?php

namespace App\Http\Controllers;

use App\SensorCluster;
use Illuminate\Http\Request;
use App\Room;
use Illuminate\Support\Facades\Validator;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Validation\Rule;

class SensorController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $rooms = Room::orderby('name')->get();
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
        $sensors = SensorCluster::select('node_mac_address')->get();
       
        $macs = [];
        foreach($sensors as $sensor)
        {
            $macs[] = $sensor->node_mac_address;
        }

        $rooms = Room::select('id')->get();
        $room_ids = [];
        foreach($rooms as $room)
        {
            $room_ids[] = $room->id;
        }
        $rules = [
            'mac-address' => ['required', 'min:8', 'max:10', 'string', Rule::notIn($macs)],
            'room-id' => ['required', 'min:1', 'integer', Rule::in($room_ids)],
        ];
        $validatedData = Validator::make($request->all(), $rules);
        if($validatedData->fails())
        {
            return redirect('sensor/add')->withErrors($validatedData)->withInput();
        }
        $sensor = SensorCluster::make();
        $sensor->node_mac_address = $request->input('mac-address');
        $sensor->room_id = $request->input('room-id');
        
        $sensor->save();
        
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
