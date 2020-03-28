<?php

namespace Gause\ImageableLaravel\Tests\Unit\Models;

use Gause\ImageableLaravel\Models\Image;
use Gause\ImageableLaravel\Tests\LaravelTestCase;
use Illuminate\Support\Facades\Storage;

class ImageTest extends LaravelTestCase
{
    /** @test */
    public function can_create_image_model()
    {
        $this->assertDatabaseMissing('images', [
            'name' => 'MyNewImage',
        ]);

        Image::create([
            'name' => 'MyNewImage',
            'file_name' => 'some_name',
            'file_extension' => 'jpg',
            'file_size' => 69,
            'original_file_name' => 'OriginalName',
        ]);

        $this->assertDatabaseHas('images', [
            'name' => 'MyNewImage',
        ]);
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
}
