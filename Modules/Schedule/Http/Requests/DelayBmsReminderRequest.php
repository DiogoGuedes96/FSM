<?php

namespace Modules\Schedule\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class DelayBmsReminderRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'reminder_id'                         => 'required|numeric|exists:bms_reminder,id',
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
            'reminder_id'                           => 'ID do lembrete',
            'reminder_delay'                        => 'Avisar-me',
            'reminder_delay.time_delay'             => 'Hora do avisar-me',
            'reminder_delay.remember_time_delay'    => 'The remember time delay is required when reminder delay is present.',
            'reminder_delay.remember_label_delay'   => 'The remember label delay must be a string.',
            'reminder_delay'                        => 'The date is required when reminder delay is present.',
            'reminder_delay.date'                   => 'Data',
        ];
    }
}
