<?php

namespace Gause\ImageableLaravel\Tests;

use Gause\ImageableLaravel\Image;
use PHPUnit\Framework\TestCase;

class ImageTest extends TestCase
{
    /** @test */
    public function hello_world()
    {
        $request = new Image();

        $this->assertEquals('hello-world', $request->test());
    }
}
