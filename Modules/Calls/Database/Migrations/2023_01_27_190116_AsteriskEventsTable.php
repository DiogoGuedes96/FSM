<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AsteriskEventsTable extends Migration
{
    /**
     * Schema table name to migrate
     * @var string
     */
    public $tableName = 'asterisk_events';

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
            Schema::create($this->tableName, function (Blueprint $table) {
            $table->id();
            $table->string('uniqueid', 45);
            $table->string('linkedid', 45);
            $table->string('type', 45);
            $table->longText('event_json', 45);
            $table->string('channel', 45);
            $table->string('channel_state', 45);
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
