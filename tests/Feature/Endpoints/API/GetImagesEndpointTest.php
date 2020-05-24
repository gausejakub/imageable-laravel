<?php

namespace Gause\ImageableLaravel\Tests\Feature\Endpoints\API;

use Gause\ImageableLaravel\Models\Image;
use Gause\ImageableLaravel\Tests\Helpers\DummyModel;
use Gause\ImageableLaravel\Tests\LaravelTestCase;

class GetImagesEndpointTest extends LaravelTestCase
{
    /** @test */
    public function can_call_get_images_endpoint()
    {
        $model = DummyModel::create();

        $response = $this->get('/api/images?model_id='.$model->id.'&model_type='.DummyModel::class);

        $response->assertStatus(200);
    }

    /** @test */
    public function returns_public_images_array_in_response()
    {
        $model = DummyModel::create();
        $image = Image::create([
            'name' => 'MyNewImage',
            'file_name' => 'some_name',
            'file_extension' => 'jpg',
            'file_size' => 69,
            'original_file_name' => 'OriginalName',
            'model_id' => $model->id,
            'model_type' => DummyModel::class,
        ]);

        $response = $this->get('/api/images?model_id='.$model->id.'&model_type='.DummyModel::class);

        $data = json_decode($response->getContent(), true)['data'];
        $this->assertEquals(
            [
                0 => [
                    'id' => $image->id,
                    'name' => $image->name,
                    'url' => $image->url,
                    'thumb_url' => $image->thumb_url,
                ],
            ],
            $data
        );
    }
}
