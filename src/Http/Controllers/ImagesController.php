<?php

namespace Gause\ImageableLaravel\Http\Controllers;

use Gause\ImageableLaravel\Requests\ImageableRequest;
use Illuminate\Http\JsonResponse;

class ImagesController
{
    public function store(ImageableRequest $request)
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
     * Handles Create image request
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
     * Handles Create images request
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
     * Get model from request, if model_type & model_id provided
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
                return null;
            }
        }
        return null;
    }

}
