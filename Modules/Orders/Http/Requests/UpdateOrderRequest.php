<?php

namespace Modules\Orders\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Carbon\Carbon;

class UpdateOrderRequest extends FormRequest
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
            'values'                     => 'required|array',
            'values.zona.id'             => 'numeric',
            'values.zona.value'          => 'required|string|max:100',
            'values.request'             => 'required|string',
            'values.notes'               => 'nullable|string',
            'values.delivery_date'       => [
                'required',
                'date',
                'after_or_equal:' . $today,
            ],
            'values.delivery_period'     => 'required|string',
            'values.priority'            => 'required|boolean',
            'values.address'             => 'nullable|string|max:100',
            'values.address.addressId'   => 'nullable|numeric',
            'values.caller_phone'        => 'nullable|numeric',
            'orderData'                  => 'required|array',
            'orderData.products'         => 'required|array',
            'orderData.products.*.id'    => 'required|numeric',
            'orderData.products.*.notes' => 'nullable|string',
        ];
    }

    public function attributes()
    {
        return [
            'values.zona'                         => 'Dados da zona',
            'values.zona.value'                   => 'Zona',
            'values.zona.id'                      => 'Código da zona',
            'values.request'                      => 'N° de Requisição',
            'values.notes'                        => 'Notas para a Encomenda',
            'values.delivery_date'                => 'Entrega',
            'values.delivery_period'              => 'Periodo.',
            'values.priority'                     => 'Encomenda prioritária',
            'values.address'                      => 'Morada',
            'values.addressId'                    => 'Código da Morada',
            'values.caller_phone'                 => 'Telefone do cliente',
            'orderData'                           => 'Dados da encomenda.',
            'orderData.products'                  => 'Lista de produtos',
            'orderData.products.*.id'             => 'Código do produto',
            'orderData.products.*.notes'          => 'Notas do produto',
        ];
    }
}
