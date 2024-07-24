<?php

namespace Modules\Schedule\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class BmsReminderInfo extends Model
{
    use HasFactory;

    protected $table = 'bms_reminder_info';
    protected $primaryKey = 'id';

    protected $fillable = [
        'name',
        'date',
        'time',
        'remember_time',
        'remember_label',
        'remember_frequency',
        'client_id',
        'client_name',
        'client_phone',
        'notes',

    ];

    public function reminder()
    {
        return $this->belongsTo(BmsReminder::class, 'bms_reminder_info', 'id');
    }

    public function bmsClient()
    {
        return $this->belongsTo(BmsClient::class, 'bms_client_id', 'id');
    }
}
