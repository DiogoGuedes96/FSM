<?php

namespace Modules\Calls\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PhoneBlackList extends Model
{
    use HasFactory;

    protected $table = 'phone_blacklist';
    protected $primaryKey = 'id';

    protected $fillable = [
        'phone',
    ];    
    protected static function newFactory()
    {
        return \Modules\Calls\Database\factories\PhoneBlackListFactory::new();
    }
}
