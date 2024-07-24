<?php

namespace Modules\Clients\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\Orders\Entities\Order;
use Nwidart\Modules\Facades\Module;

class Clients extends Model
{
    use HasFactory;

    protected $table = 'bms_clients';
    protected $primaryKey = 'id';

    protected $fillable = [
        'name',
        'country',
        'tax_number',
        'phone_1',
        'phone_2',
        'phone_3',
        'phone_4',
        'phone_5',
        'payment_method',
        'payment_condition',
        'email',
        'total_debt',
        'age_debt',
        'status',
        'rec_mode',
        'fiscal_name',
        'notes',
        "erp_client_id",
        "primavera_table_id",
        "discount_default",
        "cliente_anulado",
        "tipo_preco"
    ];

    protected static function boot()
    {
        parent::boot();

        static::addGlobalScope('cliente_anulado', function ($query) {
            $query->where('cliente_anulado', false);
        });
    }

    protected static function newFactory()
    {
        return \Modules\Primavera\Database\factories\PrimaveraFactory::new();
    }

    /**
     * > This function returns all the addresses that belong to this client
     *
     * @return A collection of Address objects.
     */
    public function addresses()
    {
        return $this->hasMany(ClientAddress::class, 'bms_client', 'id');
    }

    public function originalAddresses() {
        return $this->hasMany(Address::class, 'bms_client');
    }

    /**
     * It returns all the orders that belong to the client.
     *
     * @return A collection of orders
     */
    public function orders()
    {
        if (!Module::has('orders') && Module::isDisabled('orders')) {
            return false;
        }

        return $this->hasMany(Order::class, 'bms_client', 'id');
    }

    public function reminderInfo()
    {
        if (!Module::has('Schedule') && Module::isDisabled('Schedule')) {
            return false;
        }

        return $this->hasMany(BmsReminderInfo::class, 'bms_client_id', 'id');
    }
}
