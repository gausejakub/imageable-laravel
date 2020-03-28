<?php

namespace Gause\ImageableLaravel\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ImageableRequest extends FormRequest
{
    /**
     * Creates and saves Images from Request.
     *
     * @param string $prefix
     * @param \Illuminate\Database\Eloquent\Model|null $model
     * @return array
     */
    public function createImages($prefix = 'image', \Illuminate\Database\Eloquent\Model $model = null): array
    {
        $this->validateImages($prefix);

        $images = [];

        foreach ($this->{$prefix.'s'} as $image) {
            $images[] = \Gause\ImageableLaravel\Facades\Imageable::createImage(
                $image[$prefix],
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
     * @param \Illuminate\Database\Eloquent\Model\null $model
     * @return \Gause\ImageableLaravel\\Models\Image
     */
    public function createImage(string $prefix = 'image', \Illuminate\Database\Eloquent\Model $model = null): \Gause\ImageableLaravel\Models\Image
    {
        $this->validateImage($prefix);

        return \Gause\ImageableLaravel\Facades\Imageable::createImage(
            $this->{$prefix},
            $this->{$prefix.'_name'},
            $this->{$prefix.'_short_description'},
            $this->{$prefix.'_description'},
            $model
        );
    }

    /**
     * Validate if Request is suitable for creating Image.
     *
     * @param string $prefix
     * @return void
     */
    private function validateImage($prefix = 'image'): void
    {
        $this->validate([
            $prefix.'_name' => 'nullable|string|max:255',
            $prefix.'_short_description' => 'nullable|string|max:5000',
            $prefix.'_description' => 'nullable|string|max:5000',
            $prefix => 'required|image',
        ]);
    }

    /**
     * Validate if Request is suitable for creating Images.
     *
     * @param string $prefix
     * @return void
     */
    private function validateImages($prefix = 'image'): void
    {
        $this->validate([
            $prefix.'s' => 'required|array',
            $prefix.'s.*.name' => 'nullable|string|max:255',
            $prefix.'s.*.short_description' => 'nullable|string|max:5000',
            $prefix.'s.*.description' => 'nullable|string|max:5000',
            $prefix.'s.*.image' => 'required|image',
        ]);
    }

    /**
     * Determinates if request has image
     *
     * @param string $prefix
     * @return bool
     */
    public function hasImage($prefix = 'image'): bool
    {
        return $this->{$prefix} !== null;
    }

    /**
     * Determinates if request has images
     *
     * @param string $prefix
     * @return bool
     */
    public function hasImages($prefix = 'image'): bool
    {
        return $this->{$prefix . 's'}  !== null && !empty($this->{$prefix . 's'});
    }
}
