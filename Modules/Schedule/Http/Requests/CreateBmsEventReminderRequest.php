<?php

namespace Modules\Schedule\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateBmsEventReminderRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'reminder'                          => 'required|array',
            'reminder.id'                       => 'nullable|exists:bms_schedule_event,id',
            'reminder.title'                    => 'required|string|max:255',
            'reminder.description'              => 'nullable|string|max:255',
            'reminder.client_phone'             => 'required|string|max:20',
            'reminder.client_name'              => 'required|string|max:45',
            'reminder.notes'                    => 'nullable|string',
            'reminder.date'                     => 'required|date',
            'reminder.time'                     => 'required|date_format:H:i',
            'reminder.delay'                    => 'required|array',
            'reminder.delay.*'                  => 'required|integer',
            'reminder.recurrency_type'          => 'required|string|max:45',
            'reminder.status'                   => 'required|string',
            'reminder.client_id'                => 'exists:bms_clients,id',
            'reminder.recurrency_week_days'     => 'nullable|array',
            'reminder.recurrency_week_days.*'   => 'integer|digits_between:1,7',
            'reminder.recurrency_week'          => 'nullable|integer',
        ];
    }

    public function attributes()
    {
        return [
            'reminder'                           => 'Detalhes do evento',
            'reminder.title'                     => 'Título',
            'reminder.description'               => 'Descrição',
            'reminder.client_phone'              => 'Contacto do cliente',
            'reminder.client_name'               => 'Nome do cliente',
            'reminder.notes'                     => 'Notas',
            'reminder.date'                      => 'Data',
            'reminder.time'                      => 'Hora',
            'reminder.delay'                     => 'Avisar-me',
            'reminder.delay.*'                   => 'Frequência do avisar-me',
            'reminder.recurrency_type'           => 'Frequência do avisar-me',
            'reminder.status'                    => 'Status',
            'reminder.client_id'                 => 'ID do Cliente',
            'reminder.recurrency_week_days'      => 'Frequência de repetições',
            'reminder.recurrency_week_days.*'    => 'Frequência de repetição',
            'reminder.recurrency_week'           => 'Frequência semanal'
        ];
    }
}
