<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddZoneColumnsInClientAddressTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('bms_client_address', function (Blueprint $table) {
            $table->unsignedBigInteger('bms_address_zone_id')->nullable();
            $table->foreign('bms_address_zone_id')->references('id')->on('bms_address_zone');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
