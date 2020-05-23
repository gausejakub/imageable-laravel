<?php

namespace Gause\ImageableLaravel\Listeners;

use Gause\ImageableLaravel\Traits\UsesImages;
use Illuminate\Database\Eloquent\Model;

class DeleteModelImages
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param mixed $event
     * @return void
     */
    public function handle($event)
    {
        if (property_exists($event, 'model')) {
            $model = $event->model;
            if ($model instanceof Model) {
                $traits = class_uses($model);
                if (in_array(UsesImages::class, $traits)) {
                    $model->deleteImages();
                }
            }
        }
    }
}
