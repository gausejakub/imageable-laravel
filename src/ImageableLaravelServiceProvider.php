<?php

namespace Gause\ImageableLaravel;

use Illuminate\Support\ServiceProvider;

class ImageableLaravelServiceProvider extends ServiceProvider
{
    public function boot()
    {
        if ($this->app->runningInConsole()) {
            $this->commands([
                \Gause\ImageableLaravel\Console\Commands\ImportImages::class,
            ]);
        }

//        if (config('imageable-laravel.routes_enabled')) {
//            \Illuminate\Support\Facades\Route::post('/api/images', '\Gause\ImageableLaravel\Http\Controllers\ImagesController@store');
//        }

        $this->publishes([
            __DIR__ .  '/../config/imageable-laravel.php' => base_path('config/imageable-laravel.php')
        ], 'config');
    }

    public function register()
    {
        $this->app->bind('image', function () {
            return new Image;
        });

        $this->mergeConfigFrom(__DIR__ . '/../config/imageable-laravel.php', 'imageable-laravel');
    }
}
