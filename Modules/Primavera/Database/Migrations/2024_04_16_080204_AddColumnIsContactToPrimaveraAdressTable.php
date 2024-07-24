<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnIsContactToPrimaveraAdressTable extends Migration
{
    public function up()
    {
        Schema::table('primavera_address', function (Blueprint $table) {
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
        Schema::table('primavera_address', function (Blueprint $table) {
            $table->boolean('is_contact');
        });
    }
}
