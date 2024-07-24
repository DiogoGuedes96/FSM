<?php

namespace Modules\Orders\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ForkOrderRequest extends FormRequest
{
    public function rules()
    {
        return [
            'orderId' => 'required|numeric',
            'orderProducts' => 'required|array|min:1',
            'orderProducts.*' => 'required|numeric',
        ];
    }
    
    public function attributes()
    {
        return [
            'orderId' => 'Código da encomenda',
            'orderProducts' => 'Lista de produtos',
        ];
    }
}
