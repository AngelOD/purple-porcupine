<?php

namespace App\Http\Controllers;

use App\Room;
use Illuminate\Http\Request;
use SW802F18\Helpers\RoomHelper;
use Illuminate\Support\Facades\Validator;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Validation\Rule;

class RoomController extends Controller
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
        return view('room');
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
        $room_names = [];
        $rooms = Room::select('name')->get();
        foreach($rooms as $room)
        {
            $room_names[] = $room->name;
        }
        $rules = [
            'room-name' => ['required', 'string', 'max:100', 'min:1', Rule::notIn($room_names)],
            'room-name-alt' => ['required', 'string', 'max:100', 'min:1'], 
        ];
        $validatedData = Validator::make($request->all(), $rules);
        if($validatedData->fails())
        {
            return redirect('room/add')->withErrors($validatedData)->withInput();
        }
        $newRoom = Room::make();
        $newRoom->internal_id = RoomHelper::getRandomRoomID();
        $newRoom->name = $request->input('room-name');
        $newRoom->alt_name = $request->input('room-name-alt');

        $newRoom->save();
        return redirect('room/add')->with('status', 'Room saved');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Room  $room
     * @return \Illuminate\Http\Response
     */
    public function show(Room $room)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Room  $room
     * @return \Illuminate\Http\Response
     */
    public function edit(Room $room)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Room  $room
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Room $room)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Room  $room
     * @return \Illuminate\Http\Response
     */
    public function destroy(Room $room)
    {
        //
    }

    protected function validator(array $data)
    {
        return Validator::make($data, [
            'room-name' => 'required|string|max:100|min:1',
            'room-name-alt' => 'required|string|max:100',
        ]);
    }
}
