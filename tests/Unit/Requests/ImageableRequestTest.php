<?php

namespace Gause\ImageableLaravel\Tests;

use Gause\ImageableLaravel\Requests\ImageableRequest;
use PHPUnit\Framework\TestCase;

class ImageableRequestTest extends TestCase
{
    /** @test */
    public function hello_world()
    {
        $request = new ImageableRequest();

        $this->assertEquals('hello-world', $request->test());
    }
}
