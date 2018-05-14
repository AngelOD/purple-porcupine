<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');
Route::get('/room/add', 'RoomController@index')->name('room.add');
Route::post('/room/add', 'RoomController@store');

Route::get('/test/{roomID}', 'ChartController@test');

Route::get('/sensor/add', 'SensorController@index')->name('sensor.add');
Route::post('/sensor/add', 'SensorController@store');