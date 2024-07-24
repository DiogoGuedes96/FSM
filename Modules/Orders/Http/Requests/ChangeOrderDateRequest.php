<?php

namespace Modules\Orders\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Carbon\Carbon;

class ChangeOrderDateRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $today = Carbon::now()->format('DD/MM/YYYY');

        return [
            'delivery_date' => [
                'required',
                'date',
                'after_or_equal:' . $today,
            ],
        ];
    }

    public function attributes()
    {
        return [
            'delivery_date' => 'Data de entrega',
        ];
    }
}
