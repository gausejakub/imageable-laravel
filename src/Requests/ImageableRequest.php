<?php

namespace Gause\ImageableLaravel\Requests;

use Gause\ImageableLaravel\Models\Image;
use Illuminate\Foundation\Http\FormRequest;

// TODO: test me please
class ImageableRequest extends FormRequest
{
    public function authorize()
    {
        return true; // TODO: permissions
    }

    public function rules()
    {
        return [

        ];
    }

    /**
     * Creates and saves Image from Request
     *
     * @param \Illuminate\Database\Eloquent\Model $model
     * @return \Gause\ImageableLaravel\\Models\Image
     */
    public function createImage(\Illuminate\Database\Eloquent\Model $model = null): \Gause\ImageableLaravel\Models\Image
    {
        $this->validateImage();

        // TODO: save Image to prefered storage
        $fileName = 'NewImageName';
        $fileExtension = 'jpg';
        $fileSize = 69;

        $image = Image::create([
            'name' => $this->image_name,
            'short_description' => $this->image_short_description,
            'description' => $this->image_description,
            'file_name' => $fileName,
            'file_extension' => $fileExtension,
            'file_size' => $fileSize,
            'model_id' => $model ? $model->id : null,
            'model_type' => $model ? get_class($model) : null,
        ]);

        return $image;
    }


    /**
     * Validate if Request is suitable for creating Image
     *
     * @return void
     */
    private function validateImage(): void
    {
        $this->validate([
            'image_name' => 'nullable|string|max:255',
            'image_short_description' => 'nullable|string|max:5000',
            'image_description' => 'nullable|string|max:5000',
            'image' => 'required|image',
        ]);
    }
}
