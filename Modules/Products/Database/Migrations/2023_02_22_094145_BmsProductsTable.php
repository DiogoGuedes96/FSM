<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class BmsProductsTable extends Migration
{
    public $tableName = 'bms_products';
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create($this->tableName, function (Blueprint $table) {
            $table->id();
            $table->string('name', 150);
            $table->float('avg_price');
            $table->float('last_price');
            $table->string('sell_unit',45);
            $table->float('current_stock')->nullable();
            $table->string('stock_mov')->nullable();
            $table->string('family')->nullable();
            $table->string('sub_family')->nullable();
            $table->float('pvp_1')->nullable();
            $table->float('pvp_2')->nullable();
            $table->float('pvp_3')->nullable();
            $table->float('pvp_4')->nullable();
            $table->float('pvp_5')->nullable();
            $table->float('pvp_6')->nullable();
            $table->timestamps();
            //FK's
            $table->string('erp_product_id')->nullable();
            $table->bigInteger('primavera_table_id')->nullable();
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
