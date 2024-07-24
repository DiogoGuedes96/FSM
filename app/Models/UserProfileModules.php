<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserProfileModules extends Model
{
    use HasFactory;

    protected $table = 'user_profile_modules';
    protected $primaryKey = 'id';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'profile_id',
        'module',
    ];

    /**
     * > This function returns all the users that have the current profile
     * 
     * @return A collection of users that have the current profile.
     */
    public function userProfile()
    {        
        return $this->belongsTo(UserProfile::class, 'profile_id', 'id');
    }
}
