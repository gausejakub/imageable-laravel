<?php

namespace Gause\ImageableLaravel\Tests\Unit\Traits;

use Gause\ImageableLaravel\Models\Image;
use Gause\ImageableLaravel\Tests\Helpers\DummyModel;
use Gause\ImageableLaravel\Tests\LaravelTestCase;
use Illuminate\Database\Eloquent\Relations\Relation;

class UsesImagesTest extends LaravelTestCase
{
    /** @test */
    public function trait_defines_images_relationship()
    {
        $model = new DummyModel();

        $this->assertInstanceOf(Relation::class, $model->images());
    }

    /** @test */
    public function allows_to_delete_all_images()
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

        $image2 = Image::create([
            'name' => 'MyNewImage',
            'file_name' => 'some_name',
            'file_extension' => 'jpg',
            'file_size' => 69,
            'original_file_name' => 'OriginalName',
            'model_id' => $model->id,
            'model_type' => DummyModel::class,
        ]);

        $this->assertDatabaseHas('images', ['id' => $image->id]);
        $this->assertDatabaseHas('images', ['id' => $image2->id]);

        $model->deleteImages();

        $this->assertDatabaseMissing('images', ['id' => $image->id]);
        $this->assertDatabaseMissing('images', ['id' => $image2->id]);
    }

    /** @test */
    public function adds_attribute_to_model_of_public_images_array()
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

        $image2 = Image::create([
            'name' => 'MyNewImage',
            'file_name' => 'some_name',
            'file_extension' => 'jpg',
            'file_size' => 69,
            'original_file_name' => 'OriginalName',
            'model_id' => $model->id,
            'model_type' => DummyModel::class,
        ]);

        $publicImages = $model->publicImages;

        $this->assertEquals(
            [
                0 => [
                    'id' => $image->id,
                    'name' => $image->name,
                    'url' => $image->url,
                    'thumb_url' => $image->url,
                ],
                1 => [
                    'id' => $image2->id,
                    'name' => $image2->name,
                    'url' => $image2->url,
                    'thumb_url' => $image2->url,
                ],
            ],
            $publicImages
        );
    }
}
