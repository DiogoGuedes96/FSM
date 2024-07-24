<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class BmsProductPricesTable extends Migration
{
    public $tableName = 'bms_product_prices';
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create($this->tableName, function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('bms_product_id');
            $table->string('unit');
            $table->float('price');
            $table->boolean('active')->default(true);
            $table->timestamps();
            $table->foreign('bms_product_id')->references('id')->on('bms_products')->onDelete('cascade');
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
