<?php

namespace Modules\Schedule\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ListRemindersByDatesFromUserRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'dates'   => 'required|array',
            'dates.*' => 'date_format:d-m-Y',
        ];
    }

    public function messages()
    {
        return [
            'dates.required'      => 'Datas',
            'dates.*' => 'Data',
        ];
    }
}
