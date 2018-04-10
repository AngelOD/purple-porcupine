<?php

use Illuminate\Http\Request;

/*
| Temperature: -40 to 125 (Acc: 0.015)
| Movement: Boolean
| Gas - CO2: 400ppm to 8192ppm (clipped)
| Gas - TVOC: 0ppb 1187ppb (clipped)
| Humidity: 0 to 100 %RH (Acc: 0.008)
| Pressure:
|   Absolute: 300 hPa to 1100 hPa (Acc: 1.0 at 0 to 65 °C)
|   Relative: 700 hPa to 900 hPa (Acc: 0.12 at 24 to 40 °C)
| UV: ???
| Ambient Light: ??? (Lux?)
*/

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

// Unprotected routes
Route::namespace('Api')->group(function() {
    Route::get('/rooms', 'RoomController@index');
    Route::get('/rooms/{roomId}', 'RoomController@show');
    Route::get('/rooms/{roomId}/{sensorType}', 'RoomController@showSensor');
});

// Protected routes
Route::middleware('auth:api')->namespace('Api')->group(function() {
    //
});