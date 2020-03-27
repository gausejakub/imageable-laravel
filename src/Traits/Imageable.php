<?php

namespace Gause\ImageableLaravel\Traits;

use Gause\ImageableLaravel\Models\Image;

trait Imageable
{
    public function images()
    {
        return $this->morphMany(Image::class, 'model');
    }
}
