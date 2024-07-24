<?php

namespace Modules\Primavera\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\Orders\Entities\Order;
use Nwidart\Modules\Facades\Module;

class PrimaveraInvoices extends Model
{
    use HasFactory;

    protected $table = 'primavera_invoices';
    protected $primaryKey = 'id';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'number',
        'invoice_address',
        'doc_type',
        'doc_series',
        'description',
        'payment_conditions',
        'invoice_date',
        'invoice_expires',
        'total_value',
        'liquid_value',
        'total_discounts',
        'iva_value',
        'primavera_client',
        'parent',
        'pendent_value'
    ];

    /**
     * It returns the client that is associated with the currne invoice
     * 
     * @return The client that is associated with the invoice.
     */
    public function client()
    {
        return $this->BelongsTo(PrimaveraClients::class, 'id', 'primavera_client');
    }

    /**
     * It returns the orders that are associated with the invoice.
     * 
     * @return A collection of orders
     */
    public function orders()
    {
        if (!Module::has('orders') && Module::isDisabled('orders')) {
            return false;
        }

        return $this->hasMany(Order::class, 'erp_invoice_id', 'id');
    }

    /**
     * > This function returns a collection of products that belong to this invoice
     */
    public function products()
    {
        return $this->belongsToMany(PrimaveraProducts::class, 'invoices_have_products', 'invoice_id', 'product_id')->withTimestamps();
    }

    public function parent()
    {
        return $this->belongsTo(PrimaveraInvoices::class, 'parent');
    }

    public function childrens()
    {
        return $this->hasMany(PrimaveraInvoices::class, 'parent', 'id');
    }
}
