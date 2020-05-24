<?php

namespace Gause\ImageableLaravel\Tests\Unit\Models;

use Gause\ImageableLaravel\Events\ImageCreated;
use Gause\ImageableLaravel\Facades\Imageable;
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

        $this->assertEquals('public/some_name.jpg', $image->path);
    }

    /** @test */
    public function can_get_thumbnail_path()
    {
        $image = Image::create([
            'name' => 'MyNewImage',
            'file_name' => 'some_name',
            'file_extension' => 'jpg',
            'file_size' => 69,
            'original_file_name' => 'OriginalName',
        ]);

        $this->assertEquals('public/some_name_thumbnail.jpg', $image->thumbPath);
    }

    /** @test */
    public function can_get_image_file_url()
    {
        $image = Image::create([
            'name' => 'MyNewImage',
            'file_name' => 'some_name',
            'file_extension' => 'jpg',
            'file_size' => 69,
            'original_file_name' => 'OriginalName',
        ]);


        Storage::shouldReceive('url')
            ->with($image->path)
            ->andReturn('test')
            ->once();

        $image->url;
    }

    /** @test */
    public function can_get_image_thumb_file_url()
    {
        $image = Image::create([
            'name' => 'MyNewImage',
            'file_name' => 'some_name',
            'file_extension' => 'jpg',
            'file_size' => 69,
            'original_file_name' => 'OriginalName',
        ]);


        Storage::shouldReceive('url')
            ->with($image->thumbPath)
            ->andReturn('test')
            ->once();

        $image->thumbUrl;
    }

    /** @test */
    public function can_get_image_file_temp_url()
    {
        $image = Image::create([
            'name' => 'MyNewImage',
            'file_name' => 'some_name',
            'file_extension' => 'jpg',
            'file_size' => 69,
            'original_file_name' => 'OriginalName',
        ]);


        Storage::shouldReceive('temporaryUrl')
            ->with($image->path)
            ->andReturn('test')
            ->once();

        $image->temporaryUrl;
    }

    /** @test */
    public function can_get_image_file_thumb_temp_url()
    {
        $image = Image::create([
            'name' => 'MyNewImage',
            'file_name' => 'some_name',
            'file_extension' => 'jpg',
            'file_size' => 69,
            'original_file_name' => 'OriginalName',
        ]);


        Storage::shouldReceive('temporaryUrl')
            ->with($image->thumbPath)
            ->andReturn('test')
            ->once();

        $image->temporaryThumbUrl;
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
    public function imageable_delete_method_is_called_when_deleting_image()
    {
        Imageable::shouldReceive('deleteImage');

        $image = Image::create([
            'name' => 'MyNewImage',
            'file_name' => 'some_name',
            'file_extension' => 'jpg',
            'file_size' => 69,
            'original_file_name' => 'OriginalName',
        ]);

        $image->delete();
    }
}
