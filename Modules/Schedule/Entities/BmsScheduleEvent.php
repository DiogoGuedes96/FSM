<?php

namespace Modules\Schedule\Entities;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class BmsScheduleEvent extends Model
{
    use HasFactory;

    protected $table = 'bms_schedule_event';
    protected $primaryKey = 'id';

    protected $fillable = [
        'date',
        'time',
        'recurrency_week_days',
        'recurrency_week',
        'status',
        'user_id',
        'parent_id',
        'bms_schedule_event_reminder_id'
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function parent()
    {
        return $this->belongsTo(BmsScheduleEvent::class, 'parent_id', 'id');
    }

    public function children()
    {
        return $this->hasMany(BmsScheduleEvent::class, 'parent_id', 'id');
    }

    public function remembers()
    {
        return $this->hasMany(BmsScheduleEventRemember::class, 'bms_schedule_event_id', 'id');
    }

    public function reminder()
    {
        return $this->belongsTo(BmsScheduleEventReminder::class, 'bms_schedule_event_reminder_id', 'id');
    }
}
