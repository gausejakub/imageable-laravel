<?php

namespace Gause\ImageableLaravel;

use Gause\ImageableLaravel\Events\ImageCreated;
use Gause\ImageableLaravel\Models\Image;
use Illuminate\Support\Facades\Storage;

class Imageable
{
    /**
     * Save image in storage.
     *
     * @param $imageFile
     * @return array
     */
    public function saveImage($imageFile): array
    {
        $img = \Intervention\Image\Facades\Image::make($imageFile); //TODO size, extension, original name

        $fileName = uniqid();

        $exploded = explode('.', $imageFile->getClientOriginalName());
        $fileExtension = end($exploded);

        $filePath = $fileName.'.'.$fileExtension;

        $result = Storage::put(
            $filePath,
            $img->encode($fileExtension, 100)
        );

        if (config('imageable-laravel.thumbnails_enabled')) {
            $img->resize(320, null);
            $thumbnailPath = $fileName.'_thumbnail.'.$fileExtension;

            $result = Storage::put(
                $thumbnailPath,
                $img->encode($fileExtension, 100)
            );
        }

        return [
            'path' => $filePath,
            'fileName' => $fileName,
            'extension' => $fileExtension,
        ];
    }

    /**
     * Deletes Image from storage & also deletes thumbnails of image.
     *
     * @param $path
     * @return void
     */
    public function deleteImageFromStorage($path): void
    {
        Storage::delete($path);

        $explodedPath = explode('.', $path);
        $explodedPath[count($explodedPath) - 2] = $explodedPath[count($explodedPath) - 2].'_thumbnail';
        $thumbnailPath = implode('.', $explodedPath);

        Storage::exists($path) ? Storage::delete($path) : null;
        Storage::exists($thumbnailPath) ? Storage::delete($thumbnailPath) : null;
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
    public function createImage($imageFile, string $name = null, string $shortDescription = null, string $description = null, \Illuminate\Database\Eloquent\Model $model = null): \Gause\ImageableLaravel\Models\Image
    {
        $fileSize = $imageFile->getSize();
        $originalFileName = $imageFile->getClientOriginalName();

        $savedImageDetails = $this->saveImage($imageFile);

        $image = Image::create([
            'name' => $name,
            'short_description' => $shortDescription,
            'description' => $description,
            'file_name' => $savedImageDetails['fileName'],
            'file_extension' => $savedImageDetails['extension'],
            'file_size' => $fileSize,
            'original_file_name' => $originalFileName,
            'position' => $model ? $this->getNextPosition($model) : null,
            'model_id' => $model ? $model->id : null,
            'model_type' => $model ? get_class($model) : null,
        ]);

        event(new ImageCreated($image));

        return $image;
    }

    /**
     * Delete Image model and also delete image from storage.
     *
     * @param Image $image
     * @return bool
     * @throws \Exception
     */
    public function deleteImage(\Gause\ImageableLaravel\Models\Image $image): bool
    {
        $this->deleteImageFromStorage($image->path);

        if ($image->model) { // Move all related Images above down by one
            $relatedImagesAbove = Image::where('model_id', $image->model_id)
                ->where('model_type', $image->model_type)
                ->where('position', '>', $image->position)
                ->get();

            foreach ($relatedImagesAbove as $relatedImage) {
                $relatedImage->update(['position' => $relatedImage->position - 1]);
            }
        }

        return $image->delete();
    }

    /**
     * Return next available position of model images.
     *
     * @param \Illuminate\Database\Eloquent\Model $model
     * @return int
     */
    public function getNextPosition(\Illuminate\Database\Eloquent\Model $model): int
    {
        return $this->countOfImages($model) + 1;
    }

    /**
     * Return count of model images.
     *
     * @param \Illuminate\Database\Eloquent\Model $model
     * @return int
     */
    public function countOfImages(\Illuminate\Database\Eloquent\Model $model): int
    {
        return Image::where('model_id', $model->id)
                ->where('model_type', get_class($model))
                ->count();
    }
}
