<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnIsContactToBmsClientAddressTable extends Migration
{
    public function up()
    {
        Schema::table('bms_client_address', function (Blueprint $table) {
            $table->boolean('is_contact')->default(false);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('bms_client_address', function (Blueprint $table) {
            $table->boolean('is_contact');
        });
    }
}
