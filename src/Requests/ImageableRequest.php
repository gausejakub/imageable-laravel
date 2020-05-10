<?php

namespace Gause\ImageableLaravel\Requests;

use Gause\ImageableLaravel\Facades\Imageable;
use Gause\ImageableLaravel\Models\Image;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Http\FormRequest;

class ImageableRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [];
    }

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
            $prefix => 'required', //TODO: validate image and image base64 format
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
            $prefix.'s.*.file' => 'required', //TODO: validate image and image base64 format
        ]);
    }

    /**
     * Determinate if request has image.
     *
     * @param string $prefix
     * @return bool
     */
    public function hasImage($prefix = 'image'): bool
    {
        return $this->{$prefix} !== null;
    }

    /**
     * Determinate if request has images.
     *
     * @param string $prefix
     * @return bool
     */
    public function hasImages($prefix = 'image'): bool
    {
        return $this->{$prefix.'s'} !== null && ! empty($this->{$prefix.'s'});
    }
}
