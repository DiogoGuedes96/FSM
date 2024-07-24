<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ModifyDiscountTypeBmsClientTable extends Migration
{
    public function up()
    {
        Schema::table('bms_clients', function (Blueprint $table) {
            $table->float('discount_default')->nullable()->change();
        });
    }

    public function down()
    {
        Schema::table('bms_clients', function (Blueprint $table) {
            // Revert the column data type to INT
            $table->integer('discount_default')->nullable()->change();
        });
    }
}
