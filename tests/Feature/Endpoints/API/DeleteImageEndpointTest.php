<?php

namespace Gause\ImageableLaravel\Tests\Feature\Endpoints\API;

use Gause\ImageableLaravel\Facades\Imageable;
use Gause\ImageableLaravel\Tests\LaravelTestCase;
use Illuminate\Http\UploadedFile;

class DeleteImageEndpointTest extends LaravelTestCase
{
    /** @test */
    public function endpoint_returns_200()
    {
        $this->withoutExceptionHandling();
        $image = Imageable::createImage(UploadedFile::fake()->image('avatar.jpg'));

        $response = $this->delete('/api/images/'.$image->id);

        $response->assertStatus(200);
    }

    /** @test */
    public function it_deletes_image()
    {
        $image = Imageable::createImage(UploadedFile::fake()->image('avatar.jpg'));

        Imageable::shouldReceive('deleteImage')
            ->with(get_class($image))
            ->once();

        $this->delete('/api/images/'.$image->id);
    }

    /** @test */
    public function it_returns_success_true_if_delete_has_been_successful()
    {
        $image = Imageable::createImage(UploadedFile::fake()->image('avatar.jpg'));
        Imageable::shouldReceive('deleteImage')
            ->once()
            ->andReturn(true);

        $response = $this->delete('/api/images/'.$image->id);

        $this->assertTrue(json_decode($response->getContent())->success);
    }

    /** @test */
    public function it_returns_success_false_if_delete_has_not_been_successful()
    {
        $image = Imageable::createImage(UploadedFile::fake()->image('avatar.jpg'));
        Imageable::shouldReceive('deleteImage')
            ->once()
            ->andReturn(false);

        $response = $this->delete('/api/images/'.$image->id);

        $this->assertFalse(json_decode($response->getContent())->success);
    }

    /** @test */
    public function endpoint_retunrs_404_for_not_existing_image()
    {
        $response = $this->delete('/api/images/'. 1);

        $response->assertStatus(404);
    }
}
