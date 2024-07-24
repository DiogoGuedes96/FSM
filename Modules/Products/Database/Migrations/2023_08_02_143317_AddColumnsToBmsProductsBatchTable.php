<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnsToBmsProductsBatchTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('bms_products_batch', function (Blueprint $table) {
            $table->string('description')->nullable();
            $table->float('quantity')->nullable();
            $table->date('expiration_date')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('bms_products_batch', function (Blueprint $table) {
            $table->dropColumn('description');
            $table->dropColumn('quantity');
            $table->dropColumn('expiration_date');
        });
    }
}
