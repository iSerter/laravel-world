<?php
use \Illuminate\Support\Facades\Route;

Route::group(['namespace' => '\Iserter\World\Http\Controllers'], function() {
    Route::get('geo/countries','GeoController@countries');
});
