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
            __DIR__.'/../config/imageable-laravel.php' => base_path('config/imageable-laravel.php'),
        ], 'config');

        if (! class_exists('CreateImagesTable')) {
            $this->publishes([
                __DIR__.'/../database/migrations/create_images_table.php.stub' => database_path('migrations/'.date('Y_m_d_His', time()).'_create_images_table.php'),
            ], 'migrations');
        }
    }

    public function register()
    {
        $this->mergeConfigFrom(__DIR__.'/../config/imageable-laravel.php', 'imageable-laravel');

        /*
        * Register the service provider for the dependency.
        */
        $this->app->register(\Intervention\Image\ImageServiceProvider::class);
        /*
         * Create aliases for the dependency.
         */
        $loader = \Illuminate\Foundation\AliasLoader::getInstance();
        $loader->alias('Image', \Intervention\Image\Facades\Image::class);
    }
}
