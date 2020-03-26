<?php

namespace Gause\ImageableLaravel\Tests;

use Gause\ImageableLaravel\Requests\ImageableRequest;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class ImageableRequestTest extends LaravelTestCase
{
    /** @test */
    public function can_create_image()
    {
        $this->assertDatabaseMissing('images', [
            'image_name' => 'NewName',
            'image_short_description' => 'Short description',
            'image_description' => 'Description',
        ]);

        $request = new ImageableRequest();

        $request->merge([
            'image_name' => 'NewName',
            'image_short_description' => 'Short description',
            'image_description' => 'Description',
            'image' => UploadedFile::fake()->image('avatar.jpg'),
        ]);

        $request->createImage();

        $this->assertDatabaseHas('images', [
            'name' => 'NewName',
            'short_description' => 'Short description',
            'description' => 'Description',
        ]);
    }

    /** @test */
    public function name_is_nullable()
    {
        $exceptionThrown = false;
        $request = new ImageableRequest();

        $request->merge([
            'image_short_description' => 'Short description',
            'image_description' => 'Description',
            'image' => UploadedFile::fake()->image('avatar.jpg'),
        ]);

        try {
            $request->createImage();
        } catch (\Exception $e) {
            $exceptionThrown = true;
        }

        $this->assertFalse($exceptionThrown);
    }

    /** @test */
    public function name_has_to_be_string()
    {
        $exceptionThrown = false;
        $request = new ImageableRequest();

        $request->merge([
            'image_name' => 69,
            'image_short_description' => 'Short description',
            'image_description' => 'Description',
            'image' => UploadedFile::fake()->image('avatar.jpg'),
        ]);

        try {
            $request->createImage();
        } catch (\Exception $e) {
            $exceptionThrown = true;
        }

        $this->assertTrue($exceptionThrown);
    }

    /** @test */
    public function name_can_be_255_chars_long()
    {
        $exceptionThrown = false;
        $request = new ImageableRequest();

        $request->merge([
            'image_name' => \Illuminate\Support\Str::random(256),
            'image_short_description' => 'Short description',
            'image_description' => 'Description',
            'image' => UploadedFile::fake()->image('avatar.jpg'),
        ]);

        try {
            $request->createImage();
        } catch (\Exception $e) {
            $exceptionThrown = true;
        }

        $this->assertTrue($exceptionThrown);
    }

    /** @test */
    public function short_description_is_nullable()
    {
        $exceptionThrown = false;
        $request = new ImageableRequest();

        $request->merge([
            'image_name' => 'Name',
            'image_description' => 'Description',
            'image' => UploadedFile::fake()->image('avatar.jpg'),
        ]);

        try {
            $request->createImage();
        } catch (\Exception $e) {
            $exceptionThrown = true;
        }

        $this->assertFalse($exceptionThrown);
    }

    /** @test */
    public function short_description_has_to_be_string()
    {
        $exceptionThrown = false;
        $request = new ImageableRequest();

        $request->merge([
            'image_name' => 'Name',
            'image_short_description' => 69,
            'image_description' => 'Description',
            'image' => UploadedFile::fake()->image('avatar.jpg'),
        ]);

        try {
            $request->createImage();
        } catch (\Exception $e) {
            $exceptionThrown = true;
        }

        $this->assertTrue($exceptionThrown);
    }

    /** @test */
    public function short_description_can_be_5000_chars_long()
    {
        $exceptionThrown = false;
        $request = new ImageableRequest();

        $request->merge([
            'image_name' => 'Name',
            'image_short_description' => \Illuminate\Support\Str::random(5001),
            'image_description' => 'Description',
            'image' => UploadedFile::fake()->image('avatar.jpg'),
        ]);

        try {
            $request->createImage();
        } catch (\Exception $e) {
            $exceptionThrown = true;
        }

        $this->assertTrue($exceptionThrown);
    }

    /** @test */
    public function description_is_nullable()
    {
        $exceptionThrown = false;
        $request = new ImageableRequest();

        $request->merge([
            'image_name' => 'Name',
            'image_short_description' => 'Short description',
            'image' => UploadedFile::fake()->image('avatar.jpg'),
        ]);

        try {
            $request->createImage();
        } catch (\Exception $e) {
            $exceptionThrown = true;
        }

        $this->assertFalse($exceptionThrown);
    }

    /** @test */
    public function description_has_to_be_string()
    {
        $exceptionThrown = false;
        $request = new ImageableRequest();

        $request->merge([
            'image_name' => 'Name',
            'image_short_description' => 'Short description',
            'image_description' => 69,
            'image' => UploadedFile::fake()->image('avatar.jpg'),
        ]);

        try {
            $request->createImage();
        } catch (\Exception $e) {
            $exceptionThrown = true;
        }

        $this->assertTrue($exceptionThrown);
    }

    /** @test */
    public function description_can_be_5000_chars_long()
    {
        $exceptionThrown = false;
        $request = new ImageableRequest();

        $request->merge([
            'image_name' => 'Name',
            'image_short_description' => 'Description',
            'image_description' => \Illuminate\Support\Str::random(5001),
            'image' => UploadedFile::fake()->image('avatar.jpg'),
        ]);

        try {
            $request->createImage();
        } catch (\Exception $e) {
            $exceptionThrown = true;
        }

        $this->assertTrue($exceptionThrown);
    }

    /** @test */
    public function image_is_required()
    {
        $exceptionThrown = false;
        $request = new ImageableRequest();

        $request->merge([
            'image_name' => 'Name',
            'image_short_description' => 'Description',
            'image_description' => 'Description',
        ]);

        try {
            $request->createImage();
        } catch (\Exception $e) {
            $exceptionThrown = true;
        }

        $this->assertTrue($exceptionThrown);
    }

    /** @test */
    public function image_has_to_be_valid_image()
    {
        $exceptionThrown = false;
        $request = new ImageableRequest();

        $request->merge([
            'image_name' => 'Name',
            'image_short_description' => 'Description',
            'image_description' => 'Description',
            'image' => UploadedFile::fake()->create('some.mp3'),
        ]);

        try {
            $request->createImage();
        } catch (\Exception $e) {
            $exceptionThrown = true;
        }

        $this->assertTrue($exceptionThrown);
    }
}
