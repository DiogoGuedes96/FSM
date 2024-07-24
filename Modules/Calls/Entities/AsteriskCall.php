<?php

namespace Modules\Calls\Entities;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class AsteriskCall extends Model
{
    use HasFactory;

    protected $table = 'asterisk_calls';
    protected $primaryKey = 'id';


    protected $fillable = [
        'caller_phone',
        'linkedid',
        'status',
        'client_name',
        'hangup_status',
        'callee_phone',
        'viewed'
    ];

    public function getCreatedAtAttribute($value)
    {
        return Carbon::parse($value)->subHour();
    }

    public function getUpdatedAtAttribute($value)
    {
        return Carbon::parse($value)->subHour();
    }
    
    protected static function newFactory()
    {
        return \Modules\Calls\Database\factories\AsteriskCallFactory::new();
    }
}
