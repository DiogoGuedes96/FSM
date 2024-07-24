<?php

namespace Modules\Products\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdatePriceProductsRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'ids'    => 'required|array',
            'ids.*'  => 'integer',
            'prices' => 'array',
            'image'  => 'string',
        ];
    }

    public function messages()
    {
        return [
            'ids.required'    => 'Ids',
            'prices.required' => 'Preços',
        ];
    }
}
