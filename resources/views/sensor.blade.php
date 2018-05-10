<!doctype html>
<html lang="{{ app()->getLocale() }}">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>Purple Porcupine</title>

        <!-- Fonts -->
        <link href="https://fonts.googleapis.com/css?family=Raleway:100,600" rel="stylesheet" type="text/css">

        <!-- Styles -->
        <style>
                html, body {
                    background-color: #fff;
                    color: #636b6f;
                    font-family: 'Raleway', sans-serif;
                    font-weight: 100;
                    height: 100vh;
                    margin: 0;
                }
    
                .full-height {
                    height: 100vh;
                }
    
                .flex-center {
                    align-items: center;
                    display: flex;
                    justify-content: center;
                }
    
                .position-ref {
                    position: relative;
                }
    
                .top-right {
                    position: absolute;
                    right: 10px;
                    top: 18px;
                }
    
                .content {
                    text-align: center;
                }
    
                .title {
                    font-size: 84px;
                }
    
                .links > a {
                    color: #636b6f;
                    padding: 0 25px;
                    font-size: 12px;
                    font-weight: 600;
                    letter-spacing: .1rem;
                    text-decoration: none;
                    text-transform: uppercase;
                }
    
                .m-b-md {
                    margin-bottom: 30px;
                }
            </style>
            <link href="{{ asset('css/app.css') }}" rel="stylesheet">
    </head>
    <body>
        <div class="content">
            <div class="title m-b-md">
                Add Sensor
            </div>
                <p class="lead">Fill out the form to add a new sensor to a room</p>
            <form class="form-horizontal" method="POST" action="{{ action('SensorController@store') }}">
                <div class="form-group row">
                    <label class="col-sm-2 col-form-label" for="mac-address">Name of room</label>
                    <div class="col-sm-10">
                        <input name="mac-address" type="text" class="form-control" id="mac-address" maxlength="10" placeholder="000000000A" required>
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-sm-2 col-form-label" for="room-id">Room</label>
                    <div class="col-sm-10">
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
                <label class="col-form-label col-sm-2">{{session('status')}}</label>
                    @endif
                </div>
                {{ csrf_field() }}
            </form>
        </div>
    </body>
</html>
