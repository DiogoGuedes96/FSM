<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddZoneDescriptionAndDescontsColumnsInPrimaveraClientsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('primavera_clients', function (Blueprint $table) {
            $table->string('zone_description')->nullable();
            $table->integer('discount_1')->nullable();
            $table->integer('discount_2')->nullable();
            $table->integer('discount_3')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('primavera_clients', function (Blueprint $table) {
            $table->dropColumn('zone_description');
            $table->dropColumn('discount_1');
            $table->dropColumn('discount_2');
            $table->dropColumn('discount_3');
        });
    }
}
