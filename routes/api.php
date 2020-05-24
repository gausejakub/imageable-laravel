<?php

Route::group(['prefix' => '/api'], function () {
    Route::get('/images', '\Gause\ImageableLaravel\Http\Controllers\ImagesController@index');
    Route::post('/images', '\Gause\ImageableLaravel\Http\Controllers\ImagesController@store');
    Route::delete('/images/{image}', '\Gause\ImageableLaravel\Http\Controllers\ImagesController@destroy');
});
