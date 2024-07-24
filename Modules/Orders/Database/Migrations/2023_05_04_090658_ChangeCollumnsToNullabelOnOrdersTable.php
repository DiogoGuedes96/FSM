<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ChangeCollumnsToNullabelOnOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Modify column
        Schema::table('orders', function (Blueprint $table) {
            $table->BigInteger('erp_invoice_id')->nullable()->change();
            $table->text('description')->nullable()->change();
            $table->date('writen_date')->nullable()->change();
            $table->date('delivery_date')->nullable()->change();
            $table->string('delivery_address')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
