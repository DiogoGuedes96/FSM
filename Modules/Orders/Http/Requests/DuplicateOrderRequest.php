<?php

namespace Modules\Orders\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Carbon\Carbon;

class DuplicateOrderRequest extends FormRequest
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
            'request'             => 'required|string',
            'notes'               => 'nullable|string',
            'delivery_date'       => [
                'required',
                'date',
                'after_or_equal:' . $today,
            ],
            'delivery_period'     => 'required|string',
            'priority'            => 'required|boolean'
        ];
    }

    public function attributes()
    {
        return [
            'request' => 'Nº Requisição',
            'notes' => 'Notas para a Encodenda',
            'delivery_date' => 'Entrega',
            'delivery_period' => 'Periodo',
            'priority' => 'Encomenda Prioritária',
        ];
    }
}
