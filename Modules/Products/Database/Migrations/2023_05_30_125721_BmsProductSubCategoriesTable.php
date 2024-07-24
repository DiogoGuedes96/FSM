<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class BmsProductSubCategoriesTable extends Migration
{
    public $tableName = 'bms_products_sub_categories';
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create($this->tableName, function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name')->nullable();
            //FK's
            $table->BigInteger('category')->unsigned();

            $table->index(["category"], 'fk_sub_categories_category_idx');


            $table->foreign('category', 'fk_sub_categories_category_idx')
            ->references('id')->on('bms_products_categories')
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
