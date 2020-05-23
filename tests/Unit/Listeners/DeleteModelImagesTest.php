<?php

namespace Gause\ImageableLaravel\Tests\Unit\Listeners;

use Gause\ImageableLaravel\Listeners\DeleteModelImages;
use Gause\ImageableLaravel\Models\Image;
use Gause\ImageableLaravel\Tests\Helpers\DummyModel;
use Gause\ImageableLaravel\Tests\LaravelTestCase;
use Illuminate\Database\Eloquent\Model;

class DeleteModelImagesTest extends LaravelTestCase
{
    /**
     * @var DeleteModelImages
     */
    private $listener;

    public function setUp(): void
    {
        parent::setUp();

        $this->listener = new DeleteModelImages();
    }

    /** @test */
    public function model_images_are_deleted()
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

        $event = new class($model) {
            /**
             * @var Model
             */
            public $model;

            public function __construct(Model $model)
            {
                $this->model = $model;
            }
        };

        $this->listener->handle($event);

        $this->assertDatabaseMissing('images', ['id' => $image->id]);
        $this->assertDatabaseMissing('images', ['id' => $image2->id]);
    }
}
