<?php

namespace Modules\Orders\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Contracts\Validation\Validator;

class FilterOrderProductsRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'startDate' => 'date|nullable',
            'endDate'   => 'date|nullable',
        ];
    }
    
    public function attributes()
    {
        return [
            'startDate' => 'Data inicial',
            'endDate'   => 'Data final',
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
