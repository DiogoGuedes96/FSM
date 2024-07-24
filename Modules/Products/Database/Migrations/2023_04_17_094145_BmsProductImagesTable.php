<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class BmsProductImagesTable extends Migration
{
    public $tableName = 'bms_product_images';
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
            $table->string('image_url');
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
