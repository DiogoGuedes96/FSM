<?php

namespace Modules\Products\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Contracts\Validation\Validator;

class FilterProductsRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'family'            => 'string|nullable',
            'searchInput'       => 'string|nullable',
            'category_code'     => 'string|nullable',
            'sub_category_code' => 'string|nullable',
            'group'             => 'string|nullable',
            'stock'             => 'required|string|in:inStock,outStock',
        ];
    }

    public function messages()
    {
        return [
            'family' => 'Familia',
            'searchInput.string' => 'Entrada de pesquisa',
            'category_code.string' => 'Código da categoria.',
            'sub_category_code.string' => 'Subcódigo da categoria',
            'group.string' => 'Grupo',
            'stock.required' => 'Stock'
        ];
    }

    /**
     * If the validation fails, throw an HTTP response exception with a JSON response
     *
     * @param Validator validator The validator instance.
     */
    public function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json([
            'success'   => false,
            'message'   => 'Validation errors',
            'data'      => $validator->errors()
        ]));
    }
}
