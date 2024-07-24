<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AsteriskCallsTable extends Migration
{
    /**
     * Schema table name to migrate
     * @var string
     */
    public $tableName = 'asterisk_calls';

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
            Schema::create($this->tableName, function (Blueprint $table) {
            $table->id();
            $table->string('caller_phone', 45);
            $table->string('linkedid', 45);
            $table->string('status', 255);
            $table->string('client_name', 100);
            $table->string('hangup_status', 100);
            $table->timestamps();
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
