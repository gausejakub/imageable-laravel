<?php

namespace Gause\ImageableLaravel\Tests\Unit;

use Gause\ImageableLaravel\Events\ImageCreated;
use Gause\ImageableLaravel\Imageable;
use Gause\ImageableLaravel\Tests\Helpers\DummyModel;
use Gause\ImageableLaravel\Tests\LaravelTestCase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Event;
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
    public function image_position_is_automatically_assigned_if_model_is_provided()
    {
        $dummyModel = DummyModel::create();
        $image = $this->imageable->createImage(
            UploadedFile::fake()->image('avatar.jpg'),
            null,
            null,
            null,
            $dummyModel
        );

        $this->assertEquals(1, $image->position);

        $secondImage = $this->imageable->createImage(
            UploadedFile::fake()->image('avatar.jpg'),
            null,
            null,
            null,
            $dummyModel
        );

        $this->assertEquals(2, $secondImage->position);
    }

    /** @test */
    public function image_created_event_is_fired()
    {
        Event::fake();

        $this->imageable->createImage(UploadedFile::fake()->image('avatar.jpg'));

        Event::assertDispatched(ImageCreated::class);
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

    /** @test */
    public function deleting_image_updates_positions()
    {
        $dummyModel = DummyModel::create();
        $firstImage = $this->imageable->createImage(UploadedFile::fake()->image('avatar.jpg'), null, null, null, $dummyModel);
        $secondImage = $this->imageable->createImage(UploadedFile::fake()->image('avatar.jpg'), null, null, null, $dummyModel);
        $thirdImage = $this->imageable->createImage(UploadedFile::fake()->image('avatar.jpg'), null, null, null, $dummyModel);

        $this->assertEquals(1, $firstImage->position);
        $this->assertEquals(2, $secondImage->position);
        $this->assertEquals(3, $thirdImage->position);

        $this->imageable->deleteImage($secondImage);

        $this->assertEquals(1, $firstImage->fresh()->position);
        $this->assertEquals(2, $thirdImage->fresh()->position);
    }

    /** @test */
    public function can_get_next_image_position_for_model()
    {
        $dummyModel = DummyModel::create();
        $firstImage = $this->imageable->createImage(UploadedFile::fake()->image('avatar.jpg'), null, null, null, $dummyModel);
        $secondImage = $this->imageable->createImage(UploadedFile::fake()->image('avatar.jpg'), null, null, null, $dummyModel);
        $thirdImage = $this->imageable->createImage(UploadedFile::fake()->image('avatar.jpg'), null, null, null, $dummyModel);

        $nextPosition = $this->imageable->getNextPosition($dummyModel);

        $this->assertEquals(4, $nextPosition);
    }

    /** @test */
    public function can_get_images_count_for_model()
    {
        $dummyModel = DummyModel::create();
        $firstImage = $this->imageable->createImage(UploadedFile::fake()->image('avatar.jpg'), null, null, null, $dummyModel);
        $secondImage = $this->imageable->createImage(UploadedFile::fake()->image('avatar.jpg'), null, null, null, $dummyModel);
        $thirdImage = $this->imageable->createImage(UploadedFile::fake()->image('avatar.jpg'), null, null, null, $dummyModel);

        $countOfImages = $this->imageable->countOfImages($dummyModel);

        $this->assertEquals(3, $countOfImages);
    }

    /** @test */
    public function can_move_image_position()
    {
        $dummyModel = DummyModel::create();
        $firstImage = $this->imageable->createImage(UploadedFile::fake()->image('avatar.jpg'), null, null, null, $dummyModel);
        $secondImage = $this->imageable->createImage(UploadedFile::fake()->image('avatar.jpg'), null, null, null, $dummyModel);
        $thirdImage = $this->imageable->createImage(UploadedFile::fake()->image('avatar.jpg'), null, null, null, $dummyModel);

        $this->assertEquals(1, $firstImage->position);
        $this->assertEquals(2, $secondImage->position);
        $this->assertEquals(3, $thirdImage->position);

        $this->imageable->moveToPosition($thirdImage, 1);

        $this->assertEquals(2, $firstImage->fresh()->position);
        $this->assertEquals(3, $secondImage->fresh()->position);
        $this->assertEquals(1, $thirdImage->fresh()->position);
    }

    /** @test */
    public function trying_move_image_to_position_out_of_positions_range_moves_image_to_highest_possible_position()
    {
        $dummyModel = DummyModel::create();
        $firstImage = $this->imageable->createImage(UploadedFile::fake()->image('avatar.jpg'), null, null, null, $dummyModel);
        $secondImage = $this->imageable->createImage(UploadedFile::fake()->image('avatar.jpg'), null, null, null, $dummyModel);
        $thirdImage = $this->imageable->createImage(UploadedFile::fake()->image('avatar.jpg'), null, null, null, $dummyModel);

        $this->assertEquals(1, $firstImage->position);
        $this->assertEquals(2, $secondImage->position);
        $this->assertEquals(3, $thirdImage->position);

        $this->imageable->moveToPosition($firstImage, 69);

        $this->assertEquals(3, $firstImage->fresh()->position);
        $this->assertEquals(1, $secondImage->fresh()->position);
        $this->assertEquals(2, $thirdImage->fresh()->position);
    }

    /** @test */
    public function can_move_image_position_up_by_one()
    {
        $dummyModel = DummyModel::create();
        $firstImage = $this->imageable->createImage(UploadedFile::fake()->image('avatar.jpg'), null, null, null, $dummyModel);
        $secondImage = $this->imageable->createImage(UploadedFile::fake()->image('avatar.jpg'), null, null, null, $dummyModel);
        $thirdImage = $this->imageable->createImage(UploadedFile::fake()->image('avatar.jpg'), null, null, null, $dummyModel);

        $this->assertEquals(1, $firstImage->position);
        $this->assertEquals(2, $secondImage->position);
        $this->assertEquals(3, $thirdImage->position);

        $this->imageable->moveUpPosition($thirdImage);

        $this->assertEquals(1, $firstImage->fresh()->position);
        $this->assertEquals(3, $secondImage->fresh()->position);
        $this->assertEquals(2, $thirdImage->fresh()->position);
    }

    /** @test */
    public function can_move_image_position_down_by_one()
    {
        $dummyModel = DummyModel::create();
        $firstImage = $this->imageable->createImage(UploadedFile::fake()->image('avatar.jpg'), null, null, null, $dummyModel);
        $secondImage = $this->imageable->createImage(UploadedFile::fake()->image('avatar.jpg'), null, null, null, $dummyModel);
        $thirdImage = $this->imageable->createImage(UploadedFile::fake()->image('avatar.jpg'), null, null, null, $dummyModel);

        $this->assertEquals(1, $firstImage->position);
        $this->assertEquals(2, $secondImage->position);
        $this->assertEquals(3, $thirdImage->position);

        $this->imageable->moveDownPosition($firstImage);

        $this->assertEquals(2, $firstImage->fresh()->position);
        $this->assertEquals(1, $secondImage->fresh()->position);
        $this->assertEquals(3, $thirdImage->fresh()->position);
    }

    /** @test */
    public function can_move_image_position_to_the_top()
    {
        $dummyModel = DummyModel::create();
        $firstImage = $this->imageable->createImage(UploadedFile::fake()->image('avatar.jpg'), null, null, null, $dummyModel);
        $secondImage = $this->imageable->createImage(UploadedFile::fake()->image('avatar.jpg'), null, null, null, $dummyModel);
        $thirdImage = $this->imageable->createImage(UploadedFile::fake()->image('avatar.jpg'), null, null, null, $dummyModel);

        $this->assertEquals(1, $firstImage->position);
        $this->assertEquals(2, $secondImage->position);
        $this->assertEquals(3, $thirdImage->position);

        $this->imageable->toTopPosition($thirdImage);

        $this->assertEquals(2, $firstImage->fresh()->position);
        $this->assertEquals(3, $secondImage->fresh()->position);
        $this->assertEquals(1, $thirdImage->fresh()->position);
    }

    /** @test */
    public function can_move_image_position_to_the_bottom()
    {
        $dummyModel = DummyModel::create();
        $firstImage = $this->imageable->createImage(UploadedFile::fake()->image('avatar.jpg'), null, null, null, $dummyModel);
        $secondImage = $this->imageable->createImage(UploadedFile::fake()->image('avatar.jpg'), null, null, null, $dummyModel);
        $thirdImage = $this->imageable->createImage(UploadedFile::fake()->image('avatar.jpg'), null, null, null, $dummyModel);

        $this->assertEquals(1, $firstImage->position);
        $this->assertEquals(2, $secondImage->position);
        $this->assertEquals(3, $thirdImage->position);

        $this->imageable->toBottomPosition($firstImage);

        $this->assertEquals(3, $firstImage->fresh()->position);
        $this->assertEquals(1, $secondImage->fresh()->position);
        $this->assertEquals(2, $thirdImage->fresh()->position);
    }
}
