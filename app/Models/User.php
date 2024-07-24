<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Modules\Orders\Entities\Order;
use Modules\Schedule\Entities\BmsReminder;
use Nwidart\Modules\Facades\Module;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $table = 'users';
    protected $primaryKey = 'id';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'avatar',
        'token',
        'refresh_token',
        'phone',
        'profile_id',
    ];

    /**
     * > This function returns the user's profile
     * 
     * @return A collection of UserProfile objects.
     */
    public function profile()
    {        
        return $this->belongsTo(UserProfile::class, 'profile_id', 'id');
    }

    /**
     * It returns all the orders that the user has written.
     * 
     * @return A collection of all the orders that were written by the user.
     */
    public function ordersWriten()
    {   
        if (!Module::has('orders') && !Module::isEnabled('orders')){
            return false;
        }

        return $this->hasMany(Order::class, 'writen_by', 'id');
    }

    /**
     * > This function returns all the orders that were requested by the user
     * 
     * @return A collection of all the orders that were requested by the user.
     */
    public function ordersRequested()
    {   
        if (!Module::has('orders') && !Module::isEnabled('orders')){
            return false;
        }

        return $this->hasMany(Order::class, 'requested_by', 'id');
    }

    /**
     * > This function returns all the orders that were prepared by the user
     * 
     * @return A collection of all the orders that have been prepared by the user.
     */
    public function ordersPrepared()
    {   
        if (!Module::has('orders') && !Module::isEnabled('orders')){
            return false;
        }

        return $this->hasMany(Order::class, 'prepared_by', 'id');
    }

    /**
     * > This function returns all the orders that were invoiced by the user
     * 
     * @return A collection of all the orders that have been invoiced by the user.
     */
    public function ordersInvoiced()
    {   
        if (!Module::has('orders') && !Module::isEnabled('orders')){
            return false;
        }

        return $this->hasMany(Order::class, 'invoiced_by', 'id');
    }

    public function bmsReminders()
    {
        if (!Module::has('Schedule') && !Module::isEnabled('Schedule')){
            return false;
        }

        return $this->hasMany(BmsReminder::class, 'user_id', 'id');
    }
}
