<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class PrimaveraInvoicesTable extends Migration
{
    public $tableName = 'primavera_invoices';

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create($this->tableName, function (Blueprint $table) {
            $table->id();
            $table->string('number', 45);
            $table->string('invoice_address', 100);
            $table->string('doc_type', 45);
            $table->string('doc_series', 45);
            $table->string('payment_conditions', 45);
            $table->text('description', 500);
            $table->date('invoice_date');
            $table->date('invoice_expires');
            $table->float('total_value');
            $table->float('liquid_value');
            $table->float('total_discounts');
            $table->float('iva_value');
            $table->timestamps();
            //FK's
            $table->bigInteger('primavera_client')->unsigned();

            $table->index(["primavera_client"], 'fk_primavera_invoices_primavera_client_idx');

            $table->foreign('primavera_client', 'fk_primavera_invoices_primavera_client_idx')
            ->references('id')->on('primavera_clients')
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
