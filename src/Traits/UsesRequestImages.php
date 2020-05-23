<?php

namespace Gause\ImageableLaravel\Traits;

trait UsesRequestImages
{
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
