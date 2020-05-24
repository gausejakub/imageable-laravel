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

    /**
     * Sets attribute to model that represents public images array.
     *
     * @return array
     */
    public function getPublicImagesAttribute(): array
    {
        $images = [];

        foreach ($this->images as $image) {
            $images[] = [
                'id' => $image->id,
                'name' => $image->name,
                'url' => $image->url,
                'thumb_url' => $image->thumbUrl,
            ];
        }

        return $images;
    }
}
