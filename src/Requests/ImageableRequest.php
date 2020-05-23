<?php

namespace Gause\ImageableLaravel\Requests;

use Gause\ImageableLaravel\Traits\UsesRequestImages;
use Illuminate\Foundation\Http\FormRequest;

class ImageableRequest extends FormRequest
{
    use UsesRequestImages;

    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [];
    }
}
