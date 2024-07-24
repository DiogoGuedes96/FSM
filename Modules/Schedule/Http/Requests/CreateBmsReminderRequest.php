<?php

namespace Modules\Schedule\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateBmsReminderRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'week_days'                           => 'required_without_all:month_day,start_date,year_day|array',
            'week_days.*'                         => 'numeric|between:1,7',
            'month_day'                           => 'required_without_all:week_days,start_date,year_day|numeric|max:31',
            'year_day'                            => 'required_without_all:week_days,start_date,week_days|numeric|max:366',
            'start_date'                          => 'required_without_all:week_days,month_day,year_day|date',
            'reminder_info'                       => 'required|array',
            'reminder_info.name'                  => 'required_with:reminder_info|string',
            'reminder_info.date'                  => 'required_with:reminder_info|date',
            'reminder_info.time'                  => 'required_with:reminder_info|date_format:H:i',
            'reminder_info.remember_time'         => 'nullable|date_format:H:i',
            'reminder_info.remember_label'        => 'required_with:reminder_info|numeric',
            'reminder_info.client_id'             => 'nullable|required_without_all:reminder_info.client_phone,reminder_info.client_name|numeric|exists:bms_clients,id',
            'reminder_info.client_name'           => 'required_without:reminder_info.client_id|string',
            'reminder_info.client_phone'          => 'required_without:reminder_info.client_id|string',
            'reminder_info.notes'                 => 'nullable|string',
            'reminder_delay'                      => 'nullable|array',
            'reminder_delay.time_delay'           => 'required_with:reminder_delay|date_format:H:i',
            'reminder_delay.remember_time_delay'  => 'required_with:reminder_delay|date_format:H:i',
            'reminder_delay.remember_label_delay' => 'required_with:reminder_delay|string',
            'reminder_delay.date'                 => 'required_with:reminder_delay|date',
        ];
    }

    public function messages()
    {
        return [
            'week_days'                             => 'Dias da semana',
            'week_days.*'                           => 'Dia da semana',
            'month_day'                             => 'Dia do mês',
            'year_day'                              => 'Dia do ano',
            'start_date'                            => 'Data início',
            'reminder_info'                         => 'Informações do lembrete',
            'reminder_info.name'                    => 'Nome do lembrete',
            'reminder_info.date'                    => 'Data do lembrete',
            'reminder_info.time'                    => 'Hora do lembrete',
            'reminder_info.remember_time'           => 'The reminder remember time must be in the H:i format.',
            'reminder_info.remember_label'          => 'The reminder remember label must be a numeric.',
            'reminder_info.client_id'               => 'ID do cliente',
            'reminder_info.client_name'             => 'Nome do cliente',
            'reminder_info.client_phone'            => 'Telefone do cliente',
            'reminder_info.notes'                   => 'Notas do lembrete',
            'reminder_delay'                        => 'Avisar-me',
            'reminder_delay.remember_time_delay'    => 'Hora do avisar-me',
            'reminder_delay.remember_label_delay'   => 'The reminder delay remember label is required.',
            'reminder_delay.date'                   => 'Data do avisar-me',
        ];
    }
}
