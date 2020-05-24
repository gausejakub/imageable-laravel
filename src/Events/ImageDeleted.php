<?php

namespace Gause\ImageableLaravel\Events;

use Gause\ImageableLaravel\Models\Image;
use Illuminate\Queue\SerializesModels;

class ImageDeleted
{
    use SerializesModels;

    public $image;

    /**
     * Create a new event instance.
     *
     * @param Image $image
     */
    public function __construct(Image $image)
    {
        $this->image = $image;
    }
}
