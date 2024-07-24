<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class PrimaveraProductsTable extends Migration
{
    public $tableName = 'primavera_products';
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create($this->tableName, function (Blueprint $table) {
            $table->id();
            $table->string('primavera_id');
            $table->string('name');
            $table->float('avg_price')->nullable();
            $table->float('last_price')->nullable();
            $table->string('sell_unit')->nullable();
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
