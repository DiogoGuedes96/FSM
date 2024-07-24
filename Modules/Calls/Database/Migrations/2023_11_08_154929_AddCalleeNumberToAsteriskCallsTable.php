<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddCalleeNumberToAsteriskCallsTable extends Migration
{
    public function up()
    {
        Schema::table('asterisk_calls', function (Blueprint $table) {
            $table->string('callee_phone')->nullable();
        });
    }

    public function down()
    {
        Schema::table('asterisk_calls', function (Blueprint $table) {
            $table->dropColumn('callee_phone');
        });
    }
}
