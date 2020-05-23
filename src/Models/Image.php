<?php

namespace Gause\ImageableLaravel\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Image extends Model
{
    /**
     * @var string[]
     */
    protected $fillable = [
        'name',
        'description',
        'short_description',
        'created_by',
        'file_name',
        'file_extension',
        'file_size',
        'original_file_name',
        'position',
        'model_id',
        'model_type',
    ];

    /**
     * @var string
     */
    protected $table = 'images';

    /**
     * Defines relationship with model.
     *
     * @return MorphTo
     */
    public function model()
    {
        return $this->morphTo();
    }

    /**
     * Returns path to image in storage.
     *
     * @return string
     */
    public function getPathAttribute(): string
    {
        return $this->file_name.'.'.$this->file_extension;
    }

    /**
     * Returns url to image file.
     *
     * @return string
     */
    public function getUrlAttribute(): string
    {
        return \Illuminate\Support\Facades\Storage::url($this->path);
    }

    /**
     * Returns temporary url to image file.
     *
     * @return string
     */
    public function getTemporaryUrlAttribute(): string
    {
        return \Illuminate\Support\Facades\Storage::temporaryUrl($this->path);
    }
}
