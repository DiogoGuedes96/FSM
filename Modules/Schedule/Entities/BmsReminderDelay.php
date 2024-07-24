<?php

namespace Modules\Schedule\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class BmsReminderDelay extends Model
{
    use HasFactory;

    protected $table = 'bms_reminder_delay';
    protected $primaryKey = 'id';

    protected $fillable = [
        'time_delay',
        'remember_time_delay',
        'remember_label_delay',
        'date',
        'bms_reminder',
    ];

    public function reminder()
    {
        return $this->belongsTo(BmsReminder::class, 'bms_reminder', 'id');
    }
}
