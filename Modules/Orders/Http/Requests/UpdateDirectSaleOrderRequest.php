<?php

namespace Modules\Orders\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Carbon\Carbon;

class UpdateDirectSaleOrderRequest extends FormRequest
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
            'values.request'             => 'required|string',
            'values.status'              => 'required|string',
            'values.notes'               => 'nullable|string',
            'orderData'                  => 'required|array',
            'orderData.products'         => 'required|array',
            'orderData.products.*.id'    => 'required|numeric',
            'orderData.products.*.notes' => 'nullable|string',
            'orderProducts.products.*.volume'     => 'nullable|string|max:255',
            'orderProducts.products.*.conversion' => 'nullable|numeric|max:255',
            'orderProducts.products.*.quantity'   => 'required|numeric',
        ];
    }

    public function attributes()
    {
        return [
            'values.request'                      => 'N° de Requisição',
            'values.status'                       => 'Estado da Encomenda',
            'values.notes'                        => 'Notas para a Encomenda',
            'orderData'                           => 'Dados da encomenda.',
            'orderData.products'                  => 'Lista de produtos',
            'orderData.products.*.id'             => 'Código do produto',
            'orderData.products.*.notes'          => 'Notas do produto',
            'orderProducts.products.*.volume'     => 'Volume do produto',
            'orderProducts.products.*.quantity'   => 'Quantidade do produto',
            'orderProducts.products.*.conversion' => 'Ajuste de conversão do produto',
        ];
    }
}