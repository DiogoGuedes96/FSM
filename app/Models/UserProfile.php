<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserProfile extends Model
{
    use HasFactory;

    protected $table = 'user_profile';
    protected $primaryKey = 'id';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'role',
        'description',
    ];

    public function users()
    {        
        return $this->hasOne(User::class, 'profile_id', 'id');
    }

    public function userProfileModules()
    {        
        return $this->hasMany(UserProfileModules::class, 'profile_id', 'id');
    }
}
