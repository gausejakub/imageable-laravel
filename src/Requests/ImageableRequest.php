<?php

namespace Gause\ImageableLaravel\Requests;

use Gause\ImageableLaravel\Traits\UsesRequestCreateImages;
use Gause\ImageableLaravel\Traits\UsesRequestSyncImages;
use Illuminate\Foundation\Http\FormRequest;

class ImageableRequest extends FormRequest
{
    use UsesRequestCreateImages;
    use UsesRequestSyncImages;

    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [];
    }
}
