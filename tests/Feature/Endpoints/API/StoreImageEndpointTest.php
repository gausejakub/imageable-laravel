<?php

namespace Gause\ImageableLaravel\Tests\Feature\Endpoints\API;

use Gause\ImageableLaravel\Tests\LaravelTestCase;

class StoreImageEndpointTest extends LaravelTestCase
{
    /** @test */
    public function can_call_store_image_endpoint()
    {
        $response = $this->post('/api/images', []);

        $response->assertStatus(200);
    }
}
