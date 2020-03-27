<?php

namespace Gause\ImageableLaravel\Facades;

use Illuminate\Support\Facades\Facade;

class Imageable extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'imageable';
    }
}