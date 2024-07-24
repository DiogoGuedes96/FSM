<?php

namespace Modules\Schedule\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class BmsScheduleEventReminder extends Model
{
    use HasFactory;


    protected $table = 'bms_schedule_event_reminder';
    protected $primaryKey = 'id';

    protected $fillable = [
        'title',
        'description',
        'client_phone',
        'client_name',
        'notes',
        'date',
        'time',
        'delay',
        'recurrency_type',
        'status',
        'client_id',
        'recurrency_week_days',
        'recurrency_week',
        'user_id',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function scheduleEvent()
    {
        return $this->hasMany(BmsScheduleEvent::class, 'bms_schedule_event_reminder_id', 'id');
    }
}
