<?php

namespace Gause\ImageableLaravel\Tests\Unit;

use Gause\ImageableLaravel\Imageable;
use Gause\ImageableLaravel\Tests\LaravelTestCase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class ImageableTest extends LaravelTestCase
{
    protected $imageable;

    public function setUp(): void
    {
        parent::setUp();

        Storage::fake();

        $this->imageable = new Imageable();
    }

    /** @test */
    public function can_create_image()
    {
        $this->assertDatabaseMissing('images', [
            'name' => 'NewName',
        ]);

        $this->imageable->createImage(
            UploadedFile::fake()->image('avatar.jpg'),
            'NewName',
            'Short description',
            'Description'
        );

        $this->assertDatabaseHas('images', [
            'name' => 'NewName',
        ]);
    }

    /** @test */
    public function can_save_image_to_storage()
    {
        $savedImageData = $this->imageable->saveImage(UploadedFile::fake()->image('avatar.jpg'));

        Storage::assertExists($savedImageData['path']);
    }

    /** @test */
    public function save_image_method_returns_array_with_saved_image_details()
    {
        $savedImageData = $this->imageable->saveImage(UploadedFile::fake()->image('avatar.jpg'));

        $this->assertArrayHasKey('path', $savedImageData);
        $this->assertArrayHasKey('fileName', $savedImageData);
        $this->assertArrayHasKey('extension', $savedImageData);
    }
}
