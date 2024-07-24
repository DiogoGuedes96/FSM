<?php

namespace Modules\Orders\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RemoveOrdersFromCacheRequest extends FormRequest
{
    public function rules()
    {
        return [
            'browser_token' => 'required|string'
        ];
    }

    public function attributes()
    {
        return [
            'browser_token' => 'Browser Token'
        ];
    }
}
