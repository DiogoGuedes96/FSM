<?php

namespace Modules\Orders\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateOrderPending extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'products' => 'required|array',
            'products.*.id' => 'required|numeric',
            'products.*.conversion' => $this->getConversionRule(),
            'products.*.volume' => $this->getVolumeRule(),
            'products.*.unavailability' => 'nullable|boolean',
            'products.*.notes' => 'nullable|string|max:255',
            'products.*.batch' => $this->getBatchRule(),
            'notes' => 'nullable|string',
        ];
    }

    protected function getBatchRule()
    {
        $unavailability = $this->input('products.*.unavailability');

        if ($unavailability === null || $unavailability === false) {
            return 'required|int|exists:bms_products_batch,id';
        }

        return 'nullable|int';
    }

    /**
     * Get the conversion validation rule based on the unavailability value.
     *
     * @return string
     */
    protected function getConversionRule()
    {
        $unavailability = $this->input('products.*.unavailability');

        if ($unavailability === null || $unavailability === false) {
            return 'required|numeric';
        }

        return 'nullable|numeric';
    }

    /**
     * Get the volume validation rule based on the unavailability value.
     *
     * @return string
     */
    protected function getVolumeRule()
    {
        $unavailability = $this->input('products.*.unavailability');

        if ($unavailability === null || $unavailability === false) {
            return 'required|numeric';
        }

        return 'nullable|numeric';
    }

    public function attributes()
    {
        return [
            'products'                      => 'Lista de produtos',
            'products.*.id'                 => 'Código do produto',
            'products.*.conversion'         => 'Unidade de conversão do produto',
            'products.*.volume'             => 'Volume do produto',
            'products.*.unavailability'     => 'Indisponibilidade do produto',
            'products.*.notes'              => 'Notas do produto',
            'products.*.batch'              => 'Lote do produto',
            'notes'                         => 'Notas da encomenda',
        ];
    }
}
