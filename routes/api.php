<?php

Route::group(['prefix' => '/api'], function () {
    Route::post('/images', '\Gause\ImageableLaravel\Http\Controllers\ImagesController@store');
    Route::delete('/images/{image}', '\Gause\ImageableLaravel\Http\Controllers\ImagesController@destroy');
});
