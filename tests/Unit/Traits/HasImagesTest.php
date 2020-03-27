<?php

namespace Gause\ImageableLaravel\Tests\Unit\Traits;

use Gause\ImageableLaravel\Tests\LaravelTestCase;
use Gause\ImageableLaravel\Traits\Imageable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Relation;

class HasImagesTest extends LaravelTestCase
{
    /** @test */
    public function trait_defines_images_relationship()
    {
        $model = new ImageableTraitDummyModel();

        $this->assertInstanceOf(Relation::class, $model->images());
    }
}

class ImageableTraitDummyModel extends Model
{
    use Imageable;
}
