<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class OrderProductsTable extends Migration
{
    public $tableName = 'order_products';

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create($this->tableName, function (Blueprint $table) {
            $table->id();
            $table->string('name', 100);
            $table->string('quantity', 45);
            $table->string('unit', 45);
            $table->string('description', 255);
            $table->string('volume', 45);
            $table->string('lot', 45);
            $table->float('unit_price');
            $table->float('total_liquid_price');
            $table->float('discount_value');
            $table->timestamps();
            //FK's
            $table->bigInteger('order_id')->unsigned();
            $table->bigInteger('bms_product')->unsigned();


            $table->index(["order_id"], 'fk_order_products_order_id_idx');
            $table->index(["bms_product"], 'fk_order_products_bms_product_idx');

            $table->foreign('order_id', 'fk_order_products_order_id_idx')
                ->references('id')->on('orders')
                ->onDelete('no action')
                ->onUpdate('no action');

            $table->foreign('bms_product', 'fk_order_products_bms_product_idx')
                ->references('id')->on('bms_products')
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
