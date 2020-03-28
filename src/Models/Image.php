<?php

namespace Gause\ImageableLaravel\Models;

use Illuminate\Database\Eloquent\Model;

class Image extends Model
{
    protected $fillable = [
        'name',
        'description',
        'short_description',
        'created_by',
        'file_name',
        'file_extension',
        'file_size',
        'original_file_name',
        'model_id',
        'model_type',
    ];

    protected $table = 'images';

    /**
     * Defines relationship with model.
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphTo
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
