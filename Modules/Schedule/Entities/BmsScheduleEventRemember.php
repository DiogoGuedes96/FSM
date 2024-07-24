<?php

namespace Modules\Schedule\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class BmsScheduleEventRemember extends Model
{
    use HasFactory;

    protected $table = 'bms_schedule_event_remember';
    protected $primaryKey = 'id';

    protected $fillable = [
        'date',
        'time',
        'status',
        'bms_schedule_event_id',
    ];

    public function scheduleEvent()
    {
        return $this->belongsTo(BmsScheduleEvent::class, 'bms_schedule_event_id', 'id');
    }
}
