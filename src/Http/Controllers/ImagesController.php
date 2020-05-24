<?php

namespace Gause\ImageableLaravel\Http\Controllers;

use Gause\ImageableLaravel\Facades\Imageable;
use Gause\ImageableLaravel\Models\Image;
use Gause\ImageableLaravel\Requests\ImageableRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ImagesController
{
    /**
     * Creates image or images.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        $request->validate([
            'model_id' => 'required|int',
            'model_type' => 'required|string',
        ]);

        $images = Image::where('model_type', $request->model_type)
            ->where('model_id', $request->model_id)
            ->get();

        $publicImages = [];

        foreach ($images as $image) {
            $publicImages[] = [
                'id' => $image->id,
                'name' => $image->name,
                'url' => $image->url,
                'thumb_url' => $image->thumbUrl,
            ];
        }

        return response()->json([
            'success' => true,
            'data' => $publicImages,
        ]);
    }

    /**
     * Creates image or images.
     *
     * @param ImageableRequest $request
     * @return JsonResponse
     */
    public function store(ImageableRequest $request): JsonResponse
    {
        if ($request->hasImage()) {
            return $this->handleImageCreation($request);
        }

        if ($request->hasImages()) {
            return $this->handleImagesCreation($request);
        }

        return response()->json([
            'success' => false,
        ]);
    }

    /**
     * Deletes image.
     *
     * @param Request $request
     * @param Image $image
     * @return JsonResponse
     */
    public function destroy(Request $request): JsonResponse
    {
        $imageId = $request->route('image');
        $image = Image::findOrFail($imageId);

        $result = Imageable::deleteImage($image);

        return response()->json([
            'success' => $result,
        ]);
    }

    /**
     * Handles Create image request.
     *
     * @param ImageableRequest $request
     * @return JsonResponse
     */
    private function handleImageCreation(ImageableRequest $request): JsonResponse
    {
        $model = $this->getModelFromRequest($request);

        $image = $request->createImage('image', $model);

        return response()->json([
            'data' => $image,
            'success' => true,
        ]);
    }

    /**
     * Handles Create images request.
     *
     * @param ImageableRequest $request
     * @return JsonResponse
     */
    private function handleImagesCreation(ImageableRequest $request): JsonResponse
    {
        $model = $this->getModelFromRequest($request);

        $images = $request->createImages('image', $model);

        return response()->json([
            'data' => $images,
            'success' => true,
        ]);
    }

    /**
     * Get model from request, if model_type & model_id provided.
     *
     * @param ImageableRequest $request
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    private function getModelFromRequest(ImageableRequest $request)
    {
        if ($request->model_id && $request->model_type) {
            try {
                return $request->model_type::find($request->model_id);
            } catch (\Exception $e) { // Not existing model, or not existing model id, or model type is not even model
                return;
            }
        }
    }
}
