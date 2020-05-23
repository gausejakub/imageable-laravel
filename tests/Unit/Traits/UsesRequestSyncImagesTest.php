<?php

namespace Gause\ImageableLaravel\Tests\Unit\Traits;

use Gause\ImageableLaravel\Models\Image;
use Gause\ImageableLaravel\Requests\ImageableRequest;
use Gause\ImageableLaravel\Tests\Helpers\DummyModel;
use Gause\ImageableLaravel\Tests\LaravelTestCase;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class UsesRequestSyncImagesTest extends LaravelTestCase
{
    /**
     * @var Model
     */
    private $model;

    /**
     * @var Image
     */
    private $image;

    /**
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();

        Storage::fake();
        $this->model = DummyModel::create();
        $this->image = Image::create([
            'name' => 'Testing name',
            'short_description' => 'Short testing description',
            'description' => 'Testing description',
            'file_name' => 'xxxxaaakkjjkda',
            'file_extension' => 'jpg',
            'file_size' => 0,
            'original_file_name' => null,
            'position' => 1,
            'model_id' => $this->model->id,
            'model_type' => DummyModel::class,
            'created_by' => null,
        ]);
    }

    /**
     * Get Valid attributes for ImageableRequest.
     *
     * @param array $overrides
     * @return array
     */
    public function getValidAttributes(array $overrides = []): array
    {
        return array_merge([
            'images' => [
                0 => [
                    'name' => 'NewName',
                    'short_description' => 'Short description',
                    'description' => 'Description',
                    'file' => UploadedFile::fake()->image('avatar.jpg'),
                ],
                1 => [
                    'id' => $this->image->id,
                ],
            ],
        ], $overrides);
    }

    /** @test */
    public function can_add_new_image()
    {
        $this->withoutExceptionHandling();
        $this->assertDatabaseHas('images', [
            'id' => $this->image->id,
        ]);
        $this->assertDatabaseMissing('images', [
            'image_name' => 'NewName',
        ]);

        $request = new ImageableRequest();

        $request->merge([
            'images' => [
                0 => [
                    'id' => $this->image->id,
                ],
                1 => [
                    'name' => 'NewName',
                    'short_description' => 'Short description',
                    'description' => 'Description',
                    'file' => UploadedFile::fake()->image('avatar.jpg'),
                ],
            ],
        ]);

        $request->syncImages('image', $this->model);

        $this->assertDatabaseHas('images', [
            'name' => 'NewName',
        ]);
    }

    /** @test */
    public function can_delete_old_image()
    {
        $this->assertDatabaseHas('images', [
            'id' => $this->image->id,
        ]);
        $this->assertDatabaseMissing('images', [
            'image_name' => 'NewName',
        ]);

        $request = new ImageableRequest();

        $request->merge([
            'images' => [
                0 => [
                    'name' => 'NewName',
                    'short_description' => 'Short description',
                    'description' => 'Description',
                    'file' => UploadedFile::fake()->image('avatar.jpg'),
                ],
            ],
        ]);

        $request->syncImages('image', $this->model);

        $this->assertDatabaseMissing('images', [
            'id' => $this->image->id,
        ]);
        $this->assertDatabaseHas('images', [
            'name' => 'NewName',
        ]);
    }

    /** @test */
    public function can_update_positions()
    {
        $this->assertDatabaseHas('images', [
            'id' => $this->image->id,
        ]);
        $this->assertDatabaseMissing('images', [
            'image_name' => 'NewName',
        ]);

        $request = new ImageableRequest();

        $request->merge([
            'images' => [
                0 => [
                    'name' => 'NewName',
                    'short_description' => 'Short description',
                    'description' => 'Description',
                    'file' => UploadedFile::fake()->image('avatar.jpg'),
                ],
                1 => [
                    'id' => $this->image->id,
                ],
            ],
        ]);

        $request->syncImages('image', $this->model);

        $this->assertDatabaseHas('images', [
            'id' => $this->image->id,
            'position' => 2,
        ]);
        $this->assertDatabaseHas('images', [
            'name' => 'NewName',
            'position' => 1,
        ]);
    }

    /** @test */
    public function can_delete_all_images()
    {
        $image = Image::create([
            'name' => 'Testing name',
            'short_description' => 'Short testing description',
            'description' => 'Testing description',
            'file_name' => 'xxxxaaakkjjkda',
            'file_extension' => 'jpg',
            'file_size' => 0,
            'original_file_name' => null,
            'position' => 2,
            'model_id' => $this->model->id,
            'model_type' => DummyModel::class,
            'created_by' => null,
        ]);

        $this->assertDatabaseHas('images', [
            'id' => $this->image->id,
        ]);
        $this->assertDatabaseHas('images', [
            'id' => $image->id,
        ]);

        $request = new ImageableRequest();

        $request->syncImages('image', $this->model);

        $this->assertDatabaseMissing('images', [
            'id' => $this->image->id,
        ]);
        $this->assertDatabaseMissing('images', [
            'id' => $image->id,
        ]);
    }
}
