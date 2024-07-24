<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddTimeStampsOnBmsScheduleEventReminders extends Migration
{
    protected $tableName = 'bms_schedule_event_reminder';

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table($this->tableName, function (Blueprint $table) {
            $table->timestamps();
        });
    }

    public function down()
    {

    }
}
