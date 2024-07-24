<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnProductBatchOnOrderProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('order_products', function (Blueprint $table) {
            $table->bigInteger('bms_product_batch')->unsigned()->nullable();

            $table->index(["bms_product_batch"], 'fk_order_products_bms_product_batch_idx');

            $table->foreign('bms_product_batch', 'fk_order_products_bms_product_batch_idx')
                ->references('id')->on('bms_products_batch')
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
        Schema::table('order_products', function (Blueprint $table) {
            $table->dropColumn('bms_product_batch');
        });
    }
}