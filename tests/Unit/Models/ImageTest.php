<?php

namespace Gause\ImageableLaravel\Tests\Unit\Models;

use Gause\ImageableLaravel\Events\ImageCreated;
use Gause\ImageableLaravel\Events\ImageDeleted;
use Gause\ImageableLaravel\Models\Image;
use Gause\ImageableLaravel\Tests\LaravelTestCase;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Storage;

class ImageTest extends LaravelTestCase
{
    /** @test */
    public function can_create_image_model()
    {
        $refClass = new \ReflectionClass(Image::class);
        $this->assertTrue($refClass->isSubclassOf(\Illuminate\Database\Eloquent\Model::class));
    }

    /** @test */
    public function has_model_relationship()
    {
        $image = Image::create([
            'name' => 'MyNewImage',
            'file_name' => 'some_name',
            'file_extension' => 'jpg',
            'file_size' => 69,
            'original_file_name' => 'OriginalName',
        ]);

        $this->assertInstanceOf(Relation::class, $image->model());
    }

    /** @test */
    public function can_get_path()
    {
        $image = Image::create([
            'name' => 'MyNewImage',
            'file_name' => 'some_name',
            'file_extension' => 'jpg',
            'file_size' => 69,
            'original_file_name' => 'OriginalName',
        ]);

        $this->assertEquals('some_name.jpg', $image->path);
    }

    /** @test */
    public function can_get_image_file_url()
    {
        Storage::shouldReceive('url')
            ->andReturn('example.com/some_name.jpg');

        $image = Image::create([
            'name' => 'MyNewImage',
            'file_name' => 'some_name',
            'file_extension' => 'jpg',
            'file_size' => 69,
            'original_file_name' => 'OriginalName',
        ]);

        $this->assertEquals('example.com/some_name.jpg', $image->url);
    }

    /** @test */
    public function can_get_image_file_temp_url()
    {
        Storage::shouldReceive('temporaryUrl')
            ->andReturn('example.com/some_name.jpg');

        $image = Image::create([
            'name' => 'MyNewImage',
            'file_name' => 'some_name',
            'file_extension' => 'jpg',
            'file_size' => 69,
            'original_file_name' => 'OriginalName',
        ]);

        $this->assertEquals('example.com/some_name.jpg', $image->temporaryUrl);
    }

    /** @test */
    public function image_created_is_called_after_model_is_created()
    {
        Event::fake();

        $image = Image::create([
            'name' => 'MyNewImage',
            'file_name' => 'some_name',
            'file_extension' => 'jpg',
            'file_size' => 69,
            'original_file_name' => 'OriginalName',
        ]);

        Event::assertDispatched(ImageCreated::class);
    }

    /** @test */
    public function image_deleted_is_called_after_model_is_deleted()
    {
        Event::fake();
        $image = Image::create([
            'name' => 'MyNewImage',
            'file_name' => 'some_name',
            'file_extension' => 'jpg',
            'file_size' => 69,
            'original_file_name' => 'OriginalName',
        ]);

        $image->delete();

        Event::assertDispatched(ImageDeleted::class);
    }
}
