<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class OrdersTable extends Migration
{
    public $tableName = 'orders';

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create($this->tableName, function (Blueprint $table) {
            $table->id();
            $table->string('status', 45);
            $table->text('description', 500);
            $table->date('writen_date');
            $table->date('delivery_date');
            $table->string('delivery_address', 100);
            $table->timestamps();
            //FK's
            $table->BigInteger('writen_by')->unsigned();
            $table->BigInteger('requested_by')->unsigned();
            $table->BigInteger('prepared_by')->unsigned();
            $table->BigInteger('invoiced_by')->unsigned();
            $table->BigInteger('erp_invoice_id')->unsigned();
            $table->BigInteger('bms_client')->unsigned();

            $table->index(["writen_by"], 'fk_orders_writen_by_idx');
            $table->index(["requested_by"], 'fk_orders_requested_by_idx');
            $table->index(["prepared_by"], 'fk_orders_prepared_by_idx');
            $table->index(["invoiced_by"], 'fk_orders_invoiced_by_idx');
            $table->index(["erp_invoice_id"], 'fk_orders_erp_invoice_id_idx');
            $table->index(["bms_client"], 'fk_orders_bms_client_idx');


            $table->foreign('writen_by', 'fk_orders_writen_by_idx')
                ->references('id')->on('users')
                ->onDelete('no action')
                ->onUpdate('no action');

            $table->foreign('requested_by', 'fk_orders_requested_by_idx')
                ->references('id')->on('users')
                ->onDelete('no action')
                ->onUpdate('no action');

            $table->foreign('prepared_by', 'fk_orders_prepared_by_idx')
                ->references('id')->on('users')
                ->onUpdate('no action');

            $table->foreign('invoiced_by', 'fk_orders_invoiced_by_idx')
                ->references('id')->on('users')
                ->onDelete('no action')
                ->onUpdate('no action');

            $table->foreign('erp_invoice_id', 'fk_orders_erp_invoice_id_idx')
                ->references('id')->on('primavera_invoices')
                ->onDelete('no action')
                ->onUpdate('no action');

            $table->foreign('bms_client', 'fk_orders_bms_client_idx')
                ->references('id')->on('bms_clients')
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
