<?php

namespace Modules\Calls\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class AsteriskEvent extends Model
{
    use HasFactory;

    protected $table = 'asterisk_events';
    protected $primaryKey = 'id';

    protected $fillable = [
        'uniqueid',
        'linkedid',
        'type',
        'channel',
        'channel_state',
        'event_json'
    ];
    
    protected static function newFactory()
    {
        return \Modules\Calls\Database\factories\AsteriskEventFactory::new();
    }
}
