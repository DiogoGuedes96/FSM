<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class PrimaveraProductsBatchTable extends Migration
{
    public $tableName = 'primavera_products_batch';
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
            $table->BigInteger('primavera_product_id')->unsigned();

            $table->index(["primavera_product_id"], 'fk_primavera_products_batch_primavera_product_idx');

            $table->foreign('primavera_product_id', 'fk_primavera_products_batch_primavera_product_idx')
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
