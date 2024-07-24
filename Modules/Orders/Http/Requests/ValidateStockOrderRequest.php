<?php

namespace Modules\Orders\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ValidateStockOrderRequest extends FormRequest
{
    public function rules()
    {
        return [
            'status' => 'required|string',
            'products' => 'required|array',
            'products.*' => 'integer',
        ];
    }

    public function attributes()
    {
        return [
            'status' => 'Status',
            'products' => 'Lista de produtos',
            'products.*' => 'Produto',
        ];
    }
}