<?php

namespace Gause\ImageableLaravel\Tests\Feature\Endpoints\API;

use Gause\ImageableLaravel\Facades\Imageable;
use Gause\ImageableLaravel\Tests\LaravelTestCase;
use Illuminate\Http\UploadedFile;

class StoreImageEndpointTest extends LaravelTestCase
{
    /** @test */
    public function can_call_store_image_endpoint()
    {
        $response = $this->post('/api/images', []);

        $response->assertStatus(200);
    }

    /** @test */
    public function calling_endpoint_without_image_parameters_returns_unsuccessful_response()
    {
        $response = $this->post('/api/images', []);

        $responseDataObject = json_decode($response->getContent());
        $this->assertFalse($responseDataObject->success);
    }

    /** @test */
    public function calling_endpoint_with_proper_image_attributes_creates_images()
    {
        $file = UploadedFile::fake()->image('avatar.jpg');

        Imageable::shouldReceive('createImage')
            ->with($file, null, null, null, null)
            ->once();

        $this->post('/api/images', [
            'image' => $file,
        ]);
    }

    /** @test */
    public function can_call_endpoint_to_attach_image_to_model()
    {
        $dummyModel = \Gause\ImageableLaravel\Tests\Helpers\DummyModel::create();
        $file = UploadedFile::fake()->image('avatar.jpg');

        Imageable::shouldReceive('createImage')
            ->with($file, null, null, null, get_class($dummyModel))
            ->once();

        $this->post('/api/images', [
            'image' => $file,
            'model_type' => \Gause\ImageableLaravel\Tests\Helpers\DummyModel::class,
            'model_id' => $dummyModel->id,
        ]);
    }

    /** @test */
    public function calling_endpoint_with_proper_images_attributes_creates_images()
    {
        $file = UploadedFile::fake()->image('avatar.jpg');
        $file2 = UploadedFile::fake()->image('avatar.jpg');

        Imageable::shouldReceive('createImage')
            ->with(get_class($file), null, null, null, null)
            ->twice();

        $this->post('/api/images', [
            'images' => [
                0 => [
                    'file' => $file,
                ],
                1 => [
                    'file' => $file2,
                ],
            ],
        ]);
    }

    /** @test */
    public function can_call_endpoint_to_attach_images_to_model()
    {
        $dummyModel = \Gause\ImageableLaravel\Tests\Helpers\DummyModel::create();
        $file = UploadedFile::fake()->image('avatar.jpg');
        $file2 = UploadedFile::fake()->image('avatar.jpg');

        Imageable::shouldReceive('createImage')
            ->with(get_class($file), null, null, null, get_class($dummyModel))
            ->twice();

        $this->post('/api/images', [
            'images' => [
                0 => [
                    'file' => $file,
                ],
                1 => [
                    'file' => $file2,
                ],
            ],
            'model_type' => \Gause\ImageableLaravel\Tests\Helpers\DummyModel::class,
            'model_id' => $dummyModel->id,
        ]);
    }
}
