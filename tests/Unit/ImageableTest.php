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

    /** @test */
    public function saving_image_can_create_thumbnail()
    {
        config(['imageable-laravel.thumbnails_enabled' => true]);
        $savedImageData = $this->imageable->saveImage(UploadedFile::fake()->image('avatar.jpg', 1920, 1024));

        Storage::assertExists($savedImageData['fileName'].'_thumbnail.'.$savedImageData['extension']);
    }

    /** @test */
    public function saving_image_does_not_create_thumbnail_when_disabled_in_config()
    {
        config(['imageable-laravel.thumbnails_enabled' => false]);
        $savedImageData = $this->imageable->saveImage(UploadedFile::fake()->image('avatar.jpg', 1920, 1024));

        Storage::assertMissing($savedImageData['fileName'].'_thumbnail.'.$savedImageData['extension']);
    }

    /** @test */
    public function can_delete_image()
    {
        $image = $this->imageable->createImage(UploadedFile::fake()->image('avatar.jpg'));

        $this->imageable->deleteImage($image);

        $this->assertDatabaseMissing('images', [
            'id' => $image->id,
        ]);
    }

    /** @test */
    public function deleting_image_also_deletes_image_from_storage()
    {
        $image = $this->imageable->createImage(UploadedFile::fake()->image('avatar.jpg'));

        Storage::assertExists($image->file_name.'.'.$image->file_extension);

        $this->imageable->deleteImage($image);

        $this->assertDatabaseMissing('images', [
            'id' => $image->id,
        ]);
        Storage::assertMissing($image->file_name.'.'.$image->file_extension);
    }
}
