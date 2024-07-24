<?php

namespace Modules\Schedule\Entities;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class BmsReminder extends Model
{
    use HasFactory;

    protected $table = 'bms_reminder';
    protected $primaryKey = 'id';

    protected $fillable = [
        'week_days',
        'month_day',
        'year_day',
        'start_date',
        'active',
        'user_id',
        'bms_reminder_info',
        'deleted_at',
        'reminder_parent',
        'remember_frequency'
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function reminderInfo()
    {
        return $this->hasOne(BmsReminderInfo::class, 'id', 'bms_reminder_info');
    }

    public function delays()
    {
        return $this->hasMany(BmsReminderDelay::class, 'bms_reminder', 'id');
    }
}
