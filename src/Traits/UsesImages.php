<?php

namespace Gause\ImageableLaravel\Traits;

use Gause\ImageableLaravel\Models\Image;
use Illuminate\Database\Eloquent\Relations\MorphMany;

trait UsesImages
{
    /**
     * @return MorphMany
     */
    public function images(): MorphMany
    {
        return $this->morphMany(Image::class, 'model');
    }

    /**
     * Deletes all model images.
     *
     * @return void
     */
    public function deleteImages(): void
    {
        $this->images()->delete();
    }
}
