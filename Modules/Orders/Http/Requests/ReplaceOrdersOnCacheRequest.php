<?php

namespace Modules\Orders\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ReplaceOrdersOnCacheRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'order_id' => 'required|integer|min:1|exists:orders,id',
            'browser_token' => 'required|string'
        ];
    }

    public function attributes()
    {
        return [
            'order_id' => 'ID da Encomenda',
            'browser_token' => 'Browser Token'
        ];
    }
}
