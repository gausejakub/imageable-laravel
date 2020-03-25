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
    }

    public function register()
    {
        $this->app->bind('image', function () {
            return new Image;
        });
    }
}