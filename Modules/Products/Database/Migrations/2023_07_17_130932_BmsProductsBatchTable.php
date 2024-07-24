<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class BmsProductsBatchTable extends Migration
{
    public $tableName = 'bms_products_batch';
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create($this->tableName, function (Blueprint $table) {
            $table->id();
            $table->string('batch_number');
            $table->boolean('active');
            $table->timestamps();
            //Fk
            $table->BigInteger('bms_product_id')->unsigned();
            $table->BigInteger('erp_product_batch_id')->unsigned();

            $table->index(["bms_product_id"], 'fk_bms_products_batch_bms_product_idx');

            $table->foreign('bms_product_id', 'fk_bms_products_batch_bms_product_idx')
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
