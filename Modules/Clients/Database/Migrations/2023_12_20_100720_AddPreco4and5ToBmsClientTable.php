<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddPreco4and5ToBmsClientTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('bms_clients', function (Blueprint $table) {
            $table->string('phone_4')->nullable();
            $table->string('phone_5')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('bms_clients', function (Blueprint $table) {
            $table->dropColumn('discount_default');
        });
    }
}
