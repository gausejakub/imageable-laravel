<?php


namespace Gause\ImageableLaravel\Tests\Helpers;


use Gause\ImageableLaravel\Traits\UsesImages;
use Illuminate\Database\Eloquent\Model;

class DummyModel extends Model
{
    use UsesImages;
    
    protected $table = 'dummies';
}