<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::get('torrents', 'TransmissionController@index');
Route::post('torrents/{hash}/start', 'TransmissionController@start');
Route::post('torrents/{hash}/stop', 'TransmissionController@stop');
Route::delete('torrents/{hash}', 'TransmissionController@destroy');

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:api');
