<?php

namespace Gause\ImageableLaravel\Requests;

use Gause\ImageableLaravel\Models\Image;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Storage;

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
            // TODO: save Image to prefered storage
            $fileName = 'NewImageName';
            $originalFileName = 'OriginalFileName';
            $fileExtension = 'jpg';
            $fileSize = 69;

            $image = Image::create([
                'name' => $this->{$prefix.'_name'},
                'short_description' => $this->{$prefix.'_short_description'},
                'description' => $this->{$prefix.'_description'},
                'file_name' => $fileName,
                'file_extension' => $fileExtension,
                'original_file_name' => $originalFileName,
                'file_size' => $fileSize,
                'model_id' => $model ? $model->id : null,
                'model_type' => $model ? get_class($model) : null,
            ]);

            $images[] = $image;
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

        return $this->makeImage(
            $this->{$prefix},
            $this->{$prefix . '_name'},
            $this->{$prefix . '_short_description'},
            $this->{$prefix . '_description'},
            $model
        );
    }

    /**
     * Saves image file to storage and Creates Image model representation of it.
     *
     * @param $imageFile
     * @param string|null $name
     * @param string|null $shortDescription
     * @param string|null $description
     * @param \Illuminate\Database\Eloquent\Model|null $model
     * @return \Gause\ImageableLaravel\Models\Image
     */
    public function makeImage($imageFile, string $name = null, string $shortDescription = null, string $description = null, \Illuminate\Database\Eloquent\Model $model = null): \Gause\ImageableLaravel\Models\Image
    {
        $img = \Intervention\Image\Facades\Image::make($imageFile);//TODO size, extension, original name

        $fileName = uniqid();
        $fileSize = $imageFile->getSize();
        $originalFileName = $imageFile->getClientOriginalName();

        $exploded = explode('.', $imageFile->getClientOriginalName());
        $fileExtension = end($exploded);

        $result = Storage::put(
            $fileName . '.' . $fileExtension,
            $img->encode($fileExtension, 100)
        );

        return Image::create([
            'name' => $name,
            'short_description' => $shortDescription,
            'description' => $description,
            'file_name' => $fileName,
            'file_extension' => $fileExtension,
            'file_size' => $fileSize,
            'original_file_name' => $originalFileName,
            'model_id' => $model ? $model->id : null,
            'model_type' => $model ? get_class($model) : null,
        ]);
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
}
