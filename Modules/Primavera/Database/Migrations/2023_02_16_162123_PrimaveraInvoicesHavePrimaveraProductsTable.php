<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class PrimaveraInvoicesHavePrimaveraProductsTable extends Migration
{
    public $tableName = 'invoices_have_products';

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create($this->tableName, function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            //FK's
            $table->bigInteger('invoice_id')->unsigned();
            $table->bigInteger('product_id')->unsigned();

            $table->index(["invoice_id"], 'fk_invoices_have_products_invoice_id_idx');
            $table->index(["product_id"], 'fk_invoices_have_products_product_id_idx');

            $table->foreign('invoice_id', 'fk_invoices_have_products_invoice_id_idx')
                ->references('id')->on('primavera_invoices')
                ->onDelete('no action')
                ->onUpdate('no action');

            $table->foreign('product_id', 'fk_invoices_have_products_product_id_idx')
                ->references('id')->on('primavera_products')
                ->onDelete('no action')
                ->onUpdate('no action');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists($this->tableName);
    }
}
