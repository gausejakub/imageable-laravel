<?php

namespace Gause\ImageableLaravel\Tests\Console\Commands;

use Gause\ImageableLaravel\Tests\LaravelTestCase;

class ImportImagesTest extends LaravelTestCase
{
    /** @test */
    public function command_exists()
    {
        $this->artisan('image:import')
            ->assertExitCode(true);
    }
}
