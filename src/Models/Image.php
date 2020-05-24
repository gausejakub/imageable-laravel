<?php

namespace Gause\ImageableLaravel\Models;

use Gause\ImageableLaravel\Events\ImageCreated;
use Gause\ImageableLaravel\Events\ImageDeleted;
use Gause\ImageableLaravel\Facades\Imageable;
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
     * @var string[]
     */
    protected $dispatchesEvents = [
        'created' => ImageCreated::class,
        'deleted' => ImageDeleted::class,
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
        return 'public/'.$this->file_name.'.'.$this->file_extension;
    }

    /**
     * Returns path to thumb image in storage.
     *
     * @return string
     */
    public function getThumbPathAttribute(): string
    {
        if (config('imageable-laravel.thumbnails_enabled')) {
            return 'public/'.$this->file_name.'_thumbnail.'.$this->file_extension;
        } else {
            return $this->path;
        }

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
     * Returns url to thumbnail image file.
     *
     * @return string
     */
    public function getThumbUrlAttribute(): string
    {
        return \Illuminate\Support\Facades\Storage::url($this->thumbPath);
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

    /**
     * Returns temporary url to image file.
     *
     * @return string
     */
    public function getTemporaryThumbUrlAttribute(): string
    {
        return \Illuminate\Support\Facades\Storage::temporaryUrl($this->thumbPath);
    }

    /**
     * Delete Image.
     * @return bool|null
     * @throws \Exception
     */
    public function delete(bool $deleteWithFile = true): bool
    {
        if ($deleteWithFile === true) {
            return Imageable::deleteImage($this);
        }

        return parent::delete();
    }
}
