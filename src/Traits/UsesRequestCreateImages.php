<?php

namespace Gause\ImageableLaravel\Traits;

use Gause\ImageableLaravel\Facades\Imageable;
use Gause\ImageableLaravel\Models\Image;
use Illuminate\Database\Eloquent\Model;

trait UsesRequestCreateImages
{
    use UsesRequestImages;

    /**
     * Creates and saves Images from Request.
     *
     * @param string $prefix
     * @param Model|null $model
     * @return array
     */
    public function createImages($prefix = 'image', Model $model = null): array
    {
        $this->validateImages($prefix);

        $images = [];

        foreach ($this->{$prefix.'s'} as $image) {
            $images[] = Imageable::createImage(
                $image['file'],
                array_key_exists($prefix.'_name', $image) ? $image[$prefix.'_name'] : null,
                array_key_exists($prefix.'_short_description', $image) ? $image[$prefix.'_short_description'] : null,
                array_key_exists($prefix.'_description', $image) ? $image[$prefix.'_description'] : null,
                $model
            );
        }

        return $images;
    }

    /**
     * Creates and saves Image from Request.
     *
     * @param string $prefix
     * @param Model $model
     * @return Image
     */
    public function createImage(string $prefix = 'image', Model $model = null): Image
    {
        $this->validateImage($prefix);

        return Imageable::createImage(
            $this->{$prefix},
            $this->{$prefix.'_name'},
            $this->{$prefix.'_short_description'},
            $this->{$prefix.'_description'},
            $model
        );
    }
}
