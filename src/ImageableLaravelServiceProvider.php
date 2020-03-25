<?php

namespace Gause\ImageableLaravel;

use Illuminate\Support\ServiceProvider;

class ImageableLaravelServiceProvider extends ServiceProvider
{
    public function boot()
    {

    }

    public function register()
    {
        $this->app->bind('image', function () {
            return new Image;
        });
    }
}