<?php

namespace Modules\Schedule\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ListEventsByDatesFromUserRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'dates'   => 'array',
            'dates.*' => 'date_format:Y-m-d',
        ];
    }

    public function messages()
    {
        return [
            'dates.required'    => 'Datas',
            'dates.*'           => 'Data',
        ];
    }
}
