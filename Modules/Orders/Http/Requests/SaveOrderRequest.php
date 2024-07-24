<?php

namespace Modules\Orders\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SaveOrderRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'callerPhone'                     => 'nullable|numeric',
            'isDirectSale'                    => 'nullable|boolean',
            'bmsClient'                       => 'nullable|numeric|exists:bms_clients,id',
            'orderNotes'                      => 'nullable|string|max:1024',
            'orderProducts'                   => 'required|array',
            'orderProducts.*.bms_product'     => 'required|numeric',
            'orderProducts.*.unit'            => 'nullable|string|max:255',
            'orderProducts.*.volume'          => 'nullable|string|max:255',
            'orderProducts.*.conversion'      => 'nullable|numeric|max:255',
            'orderProducts.*.sale_unit'       => 'required|string|max:255',
            'orderProducts.*.quantity'        => 'required|numeric',
            'orderProducts.*.price'           => 'required|numeric',
            'orderProducts.*.correctionPrice' => 'nullable|numeric',
            'orderProducts.*.discount'        => 'nullable|numeric',
        ];
    }

    public function attributes()
    {
        return [
            'orderNotes'                              => 'Notas para a encomenda',
            'isDirectSale'                            => 'Encomenda de venda direta',
            'bmsClient'                               => 'Código do cliente',
            'callerPhone'                             => 'Telefone do cliente',
            'orderProducts'                           => 'Lista de produtos',
            'orderProducts.*.bms_product'             => 'Código do produto.',
            'orderProducts.*.unit'                    => 'Unidade do produto',
            'orderProducts.*.volume'                  => 'Volume do produto',
            'orderProducts.*.quantity'                => 'Quantidade do produto',
            'orderProducts.*.price'                   => 'Valor do produto',
            'orderProducts.*.correctionPrice'         => 'Valor de correção do produto',
            'orderProducts.*.discount'                => 'Valor de desconto',
            'orderProducts.*.conversion'              => 'Ajuste de conversão do produto',
            'orderProducts.*.sale_unit'               => 'Unidade de Venda do produto',
        ];
    }
}
