<?php

namespace Gause\ImageableLaravel\Requests;

use Gause\ImageableLaravel\Traits\UsesRequestCreateImages;
use Illuminate\Foundation\Http\FormRequest;

class ImageableRequest extends FormRequest
{
    use UsesRequestCreateImages;

    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [];
    }
}