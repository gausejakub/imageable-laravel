<?php

namespace Gause\ImageableLaravel\Traits;

use Gause\ImageableLaravel\Facades\Imageable;
use Illuminate\Database\Eloquent\Model;

trait UsesRequestSyncImages
{
    /**
     * Creates and saves Images from Request.
     *
     * @param string $prefix
     * @param Model|null $model
     * @return array
     */
    public function syncImages($prefix = 'image', Model $model = null): array
    {
        $this->validateSyncingImages($prefix);

        $images = [];

        if ($model) {
            $missingImages = $model->images->pluck('id')->toArray();
        }

        foreach ($this->{$prefix.'s'} as $key => $image) {
            if (array_key_exists('id', $image) && $image['id'] !== null) {
                if ($model) {
                    unset($missingImages[array_search($image['id'], $missingImages)]);

                    $image = $model->images()->find($image['id']);

                    if ($image && $key + 1 !== $image->position) {
                        $image->update(['position' => $key + 1]);
                    }
                }
            } else {
                $images[] = Imageable::createImage(
                    $image['file'],
                    array_key_exists('name', $image) ? $image['name'] : null,
                    array_key_exists('short_description', $image) ? $image['short_description'] : null,
                    array_key_exists('description', $image) ? $image['description'] : null,
                    $model,
                    $key + 1
                );
            }
        }

        if ($model) {
            foreach ($missingImages as $imageId) {
                $image = $model->images()->find($imageId);
                if ($image) {
                    $image->delete();
                }
            }
        }

        return $images;
    }

    /**
     * Validate if Request is suitable for syncing Images.
     *
     * @param string $prefix
     * @return void
     */
    private function validateSyncingImages($prefix = 'image'): void
    {
        $this->validate([
            $prefix.'s' => 'required|array',
            $prefix.'s.*.name' => 'nullable|string|max:255',
            $prefix.'s.*.short_description' => 'nullable|string|max:5000',
            $prefix.'s.*.description' => 'nullable|string|max:5000',
            $prefix.'s.*.file' => 'required_without:'.$prefix.'s.*.id|nullable', //TODO: validate image and image base64 format
            $prefix.'s.*.id' => 'required_without:'.$prefix.'s.*.file|nullable|int|exists:images,id', //TODO: validate image and image base64 format
        ]);
    }
}
