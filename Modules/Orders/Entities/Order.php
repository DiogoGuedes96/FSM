<?php

namespace Modules\Orders\Entities;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\Clients\Entities\AddressZone;
use Modules\Clients\Entities\ClientAddress;
use Modules\Clients\Entities\Clients;
use Modules\Orders\Services\OrdersService;
use Modules\Primavera\Entities\PrimaveraInvoices;
use Nwidart\Modules\Facades\Module;

class Order extends Model
{
    use HasFactory;

    protected $table = 'orders';
    protected $primaryKey = 'id';
    const STATUS_CANCELED = 'canceled';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'status',
        'description',
        'writen_date',
        'delivery_date',
        'delivery_period',
        'delivery_address',
        'writen_by',
        'requested_by',
        'prepared_by',
        'invoiced_by',
        'erp_invoice_id',
        'bms_client',
        'total_iva_value',
        'total_value',
        'total_liquid_value',
        'priority',
        'caller_phone',
        'bms_address_zone_id',
        'request_number',
        'parent_order',
        'bms_client_address_id',
        'isDirectSale',
    ];

    protected $casts = [
        'isDirectSale' => 'boolean',
    ];

    protected static function newFactory()
    {
        return \Modules\Orders\Database\factories\OrderFactory::new();
    }

    /**
     * > This function returns the client associated with the current order
     *
     * @return A Modules\Clients\Entities\Client;
     */
    public function client()
    {
        if (!Module::has('clients') && !Module::isEnabled('clients')) {
            return false;
        }

        return $this->belongsTo(Clients::class, 'bms_client', 'id');
    }

    /**
     * > This function returns all the invoices associated with the current order
     *
     * @return A Modules\Primavera\Entities\PrimaveraInvoices;
     */
    public function primaveraInvoices()
    {
        if (!Module::has('primavera') && !Module::isEnabled('primavera')) {
            return false;
        }

        return $this->belongsTo(PrimaveraInvoices::class, 'erp_invoice_id', 'id');
    }

    /**
     * This function returns all the orderProducts that are in the current order
     *
     * @return A collection of OrderProducts.
     */
    public function orderProducts()
    {
        return $this->hasMany(OrderProducts::class, 'order_id', 'id');
    }

    /**
     * It returns the user who wrote the current order.
     *
     * @return The user who wrote the current order.
     */
    public function orderWriter()
    {
        return $this->belongsTo(User::class, 'writen_by');
    }

    /**
     * It returns the user who requested the current order.
     *
     * @return The user who requested the current order.
     */
    public function orderRequester()
    {
        return $this->belongsTo(User::class, 'requested_by');
    }

    /**
     * It returns the user who prepared the current order.
     *
     * @return The user who prepared the current order.
     */
    public function orderPreparer()
    {
        return $this->belongsTo(User::class, 'prepared_by');
    }

    /**
     * It returns the user who invoiced the current order.
     *
     * @return The user who invoiced the current order.
     */
    public function orderInvoicer()
    {
        return $this->belongsTo(User::class, 'id', 'invoiced_by');
    }

    public function parentOrder()
    {
        return $this->belongsTo(Order::class, 'parent_order', 'id');
    }

    public function getOrdersByStatusAndPriority($status, $zoneId, $deliveryDate = null, $deliveryPeriod = null)
    {
        $query = $this
            ->with('client')
            ->with('zone')
            ->with('primaveraInvoices')
            ->has('orderProducts')
            ->with('orderWriter')
            ->with('orderPreparer')
            ->where('status', $status)
            ->orderBy('delivery_date', 'asc')
            ->orderByRaw("CASE
                WHEN delivery_period = 'morning' THEN 1
                WHEN delivery_period = 'evening' THEN 2
                ELSE 3
                END")
            ->orderByRaw("CASE
                WHEN priority IS NULL THEN 1
                ELSE 0
                END")
            ->orderBy('priority', 'asc')
            ->orderBy('created_at', 'asc');

        if ($status === OrdersService::STATUS_IN_PREPARATION) {
            $query->with(['orderProducts' => function ($query) {
                $query->with('bmsProduct')
                    ->with('bmsProduct.batches');
            }]);
        } else {
            $query->with(['orderProducts' => function ($query) {
                $query->whereNull('unavailability')
                    ->with('bmsProduct')
                    ->with('bmsProduct.batches')
                    ->orWhere('unavailability', '=', false);
            }]);
        }

        if ($zoneId) {
            $query->whereHas('zone', function ($subQuery) use ($zoneId) {
                $subQuery->where('id', $zoneId);
            });
        }

        if ($deliveryDate) {
            $query->whereDate('delivery_date', \Carbon\Carbon::createFromFormat('d/m/Y', $deliveryDate)->format('Y-m-d'));
        }

        if ($deliveryPeriod) {
            $query->where('delivery_period', $deliveryPeriod);
        }

        return $query->paginate(50);
    }

    public function getOrdersByStatus($status, int $zoneId = null, $deliveryDate = null, $deliveryPeriod = null)
    {
        $query = $this
            ->with(['orderProducts' => function ($query) {
                $query->with('bmsProduct')
                    ->with('bmsProduct.batches');
            }])
            ->with('zone')
            ->with('client')
            ->with('orderWriter')
            ->with('orderPreparer')
            ->with('client.addresses')
            ->with('primaveraInvoices')
            ->with('primaveraInvoices.childrens', function ($q) {
                $q->orderBy('created_at', 'desc');
            })
            ->where('status', $status)
            ->orderBy('id', 'desc');

        if ($zoneId) {
            if (method_exists($this, 'zone')) {
                $query->whereHas('zone', function ($subQuery) use ($zoneId) {
                    $subQuery->where('id', $zoneId);
                });
            } else {
                return [];
            }
        }

        if ($deliveryDate) {
            $query->whereDate('delivery_date', \Carbon\Carbon::createFromFormat('d/m/Y', $deliveryDate)->format('Y-m-d'));
        }

        if ($deliveryPeriod) {
            $query->where('delivery_period', $deliveryPeriod);
        }

        return $query->paginate(50);
    }

    public function zone()
    {
        return $this->hasOne(AddressZone::class, 'id', 'bms_address_zone_id');
    }

    public function clientAddress()
    {
        return $this->hasOne(ClientAddress::class, 'id', 'bms_client_address_id');
    }
}
