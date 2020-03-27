<?php

namespace Gause\ImageableLaravel\Traits;

use Gause\ImageableLaravel\Models\Image;

trait UsesImages
{
    public function images()
    {
        return $this->morphMany(Image::class, 'model');
    }
}
