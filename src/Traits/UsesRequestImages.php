<?php

namespace Gause\ImageableLaravel\Traits;

trait UsesRequestImages
{
    use UsesRequestCreateImages;
    use UsesRequestSyncImages;

    /**
     * Determinate if request has image.
     *
     * @param string $prefix
     * @return bool
     */
    public function hasImage($prefix = 'image'): bool
    {
        return $this->{$prefix} !== null;
    }

    /**
     * Determinate if request has images.
     *
     * @param string $prefix
     * @return bool
     */
    public function hasImages($prefix = 'image'): bool
    {
        return $this->{$prefix.'s'} !== null && ! empty($this->{$prefix.'s'});
    }
}
