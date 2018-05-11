@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">
                        Add Sensor
                    </div>
                    <div class="card-body">
            <p class="lead">Fill out the form to add a new sensor to a room</p>
        <form class="form-horizontal" method="POST" action="{{ action('SensorController@store') }}">
            <div class="form-group row">
                <label class="col-sm-4 col-form-label" for="mac-address">MAC Address</label>
                <div class="col-sm-6">
                    <input name="mac-address" type="text" class="form-control" id="mac-address" maxlength="10" placeholder="000000000A" required>
                </div>
            </div>
            <div class="form-group row">
                <label class="col-sm-4 col-form-label" for="room-id">Room</label>
                <div class="col-sm-6">
                    <select name="room-id" class="form-control" required>
                        @foreach($rooms as $room)
                            <option value="{{$room->id}}">{{$room->name . ' - ' . $room->alt_name}}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="form-group row-center">
                <button name="add-sensor-btn" type="submit" class="btn btn-primary">Add Sensor</button>
            </div>
            <div class="form-group row-center">
                @if (session('status'))
            <label class="col-form-label">{{session('status')}}</label>
                @endif
            </div>
            {{ csrf_field() }}
        </form>
            </div>
                </div>
        </div>
        </div>
    </div>
    @endsection