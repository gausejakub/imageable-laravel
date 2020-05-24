<?php

namespace Gause\ImageableLaravel;

use Gause\ImageableLaravel\Events\ImageCreated;
use Gause\ImageableLaravel\Models\Image;
use Illuminate\Database\Eloquent\Model;
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

        if (is_string($imageFile)) {
            $fileExtension = explode('/', mime_content_type($imageFile))[1];
        } else {
            $exploded = explode('.', $imageFile->getClientOriginalName());
            $fileExtension = end($exploded);
        }

        $fileSize = $img->filesize();

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
            'fileSize' => $fileSize,
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
     * @param Model|null $model
     * @param int|null $position
     * @return Image
     */
    public function createImage($imageFile, string $name = null, string $shortDescription = null, string $description = null, Model $model = null, int $position = null): Image
    {
        if (is_file($imageFile)) {
            $originalFileName = $imageFile->getClientOriginalName();
        }

        $savedImageDetails = $this->saveImage($imageFile);

        if ($model) {
            $position = $position ?: $this->getNextPosition($model);
        } else {
            $position = null;
        }

        $image = Image::create([
            'name' => $name,
            'short_description' => $shortDescription,
            'description' => $description,
            'file_name' => $savedImageDetails['fileName'],
            'file_extension' => $savedImageDetails['extension'],
            'file_size' => $savedImageDetails['fileSize'],
            'original_file_name' => isset($originalFileName) ? $originalFileName : null,
            'position' => $position,
            'model_id' => $model ? $model->id : null,
            'model_type' => $model ? get_class($model) : null,
        ]);

        return $image;
    }

    /**
     * Delete Image model and also delete image from storage.
     *
     * @param Image $image
     * @return bool
     * @throws \Exception
     */
    public function deleteImage(Image $image): bool
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

        return $image->delete(false);
    }

    /**
     * Return next available position of model images.
     *
     * @param Model $model
     * @return int
     */
    public function getNextPosition(Model $model): int
    {
        return $this->countOfImages($model) + 1;
    }

    /**
     * Return currently highest position of model images.
     *
     * @param Model $model
     * @return int
     */
    public function getHighestPosition(Model $model): int // TODO test me
    {
        return $this->countOfImages($model);
    }

    /**
     * Return count of model images.
     *
     * @param Model $model
     * @return int
     */
    public function countOfImages(Model $model): int
    {
        return Image::where('model_id', $model->id)
                ->where('model_type', get_class($model))
                ->count();
    }

    /**
     * Moves image to given position.
     *
     * @param Image $image
     * @param int $position
     * @return Image
     * @throws \Exception
     */
    public function moveToPosition(Image $image, int $position): Image
    {
        $model = $image->model;
        if ($model == null) {
            throw new \Exception('Cannot move Image that does not belong to any model scope.');
        }

        if ($position > $this->getHighestPosition($model)) {
            $position = $this->getHighestPosition($model);
        }

        $imagesAbove = Image::where('model_id', $model->id)
            ->where('model_type', get_class($model))
            ->where('position', '>', $image->position)
            ->get();

        foreach ($imagesAbove as $imageAbove) {
            $imageAbove->update(['position' => $imageAbove->position - 1]);
        }

        $imagesAboveNewPosition = Image::where('model_id', $model->id)
            ->where('model_type', get_class($model))
            ->where('position', '>=', $position)
            ->where('id', '!=', $image->id)
            ->get();

        foreach ($imagesAboveNewPosition as $imageAboveNewPosition) {
            $imageAboveNewPosition->update(['position' => $imageAboveNewPosition->position + 1]);
        }

        $image->update([
            'position' => $position,
        ]);

        return $image->fresh();
    }

    /**
     * Move Image one position up.
     *
     * @param Image $image
     * @return Image
     * @throws \Exception
     */
    public function moveUpPosition(Image $image): Image
    {
        if ($image->position == 1) { // TODO: test this
            return $image;
        }

        return $this->moveToPosition($image, $image->position - 1);
    }

    /**
     * Move Image one position down.
     *
     * @param Image $image
     * @return Image
     * @throws \Exception
     */
    public function moveDownPosition(Image $image): Image
    {
        $model = $image->model;
        if ($model == null) {
            throw new \Exception('Cannot move Image that does not belong to any model scope.');
        }

        if ($image->position == $this->getHighestPosition($image->model)) { // TODO: test this
            return $image;
        }

        return $this->moveToPosition($image, $image->position + 1);
    }

    /**
     * Moves image to top position of its model scope.
     *
     * @param Image $image
     * @return Image
     * @throws \Exception
     */
    public function toTopPosition(Image $image): Image
    {
        if ($image->position == 1) {
            return $image;
        }

        return $this->moveToPosition($image, 1);
    }

    /**
     * Moves image to bottom position of its model scope.
     *
     * @param Image $image
     * @return Image
     * @throws \Exception
     */
    public function toBottomPosition(Image $image): Image
    {
        $model = $image->model;
        if ($model == null) {
            throw new \Exception('Cannot move Image that does not belong to any model scope.');
        }

        if ($image->position == $this->getHighestPosition($model)) {
            return $image;
        }

        return $this->moveToPosition($image, $this->getHighestPosition($model));
    }
}
