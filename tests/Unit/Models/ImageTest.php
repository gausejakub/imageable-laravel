<?php

namespace Gause\ImageableLaravel\Tests\Unit\Models;

use Gause\ImageableLaravel\Models\Image;
use Gause\ImageableLaravel\Tests\LaravelTestCase;

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
            'original_file_name' => 'OriginalName'
        ]);

        $this->assertDatabaseHas('images', [
            'name' => 'MyNewImage',
        ]);
    }
}
