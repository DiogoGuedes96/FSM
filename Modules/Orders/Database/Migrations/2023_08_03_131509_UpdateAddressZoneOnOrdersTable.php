<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateAddressZoneOnOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('orders', function (Blueprint $table) {
            if (!Schema::hasColumn('orders', 'zona')) {
                $table->dropColumn('zona');
            }
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
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn('bms_address_zone_id');
            $table->dropColumn('zone');
        });
    }
}
