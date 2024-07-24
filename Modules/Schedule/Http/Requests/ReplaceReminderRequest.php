<?php

namespace Modules\Schedule\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ReplaceReminderRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'reminder_id' => [
                'required',
                'numeric',
                Rule::exists('bms_reminder', 'id')->where(function ($query) {
                    $query->where('active', 1);
                }),
            ],
            'reminder_info'                => 'nullable|array',
            'reminder_info.name'           => 'required_with:reminder_info|string',
            'reminder_info.date'           => 'required_with:reminder_info|date',
            'reminder_info.time'           => 'required_with:reminder_info|date_format:H:i',
            'reminder_info.remember_time'  => 'nullable|date_format:H:i',
            'reminder_info.remember_label' => 'required_with:reminder_info|string',
            'reminder_info.client_id'      => 'nullable|required_without_all:reminder_info.client_phone,reminder_info.client_name|numeric|exists:bms_clients,id',
            'reminder_info.client_name'    => 'required_without:reminder_info.client_id|string',
            'reminder_info.client_phone'   => 'required_without:reminder_info.client_id|string',
            'reminder_info.notes'          => 'nullable|string',
        ];
    }

    public function attributes()
    {
        return [
            'reminder_id'                   => 'ID do evento',
            'reminder_info'                 => 'Dados do evento',
            'reminder_info.name'            => 'Nome',
            'reminder_info.date'            => 'Data',
            'reminder_info.time'            => 'Hora',
            'reminder_info.remember_time'   => 'The reminder remember time must be in the format H:i.',
            'reminder_info.remember_label'  => 'The reminder remember label is required.',
            'reminder_info.client_id'       => 'ID do cliente',
            'reminder_info.client_name'     => 'Nome do cliente',
            'reminder_info.client_phone'    => 'Telefone do cliente',
            'reminder_info.notes'           => 'Notas do lembrete',
        ];
    }
}
