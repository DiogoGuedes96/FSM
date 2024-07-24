<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateColumnTitleInBmsScheduleEventReminderTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('bms_schedule_event_reminder', function (Blueprint $table) {
            $table->string('title', 255)->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('bms_schedule_event_reminder', function (Blueprint $table) {
            $table->string('title', 45)->nullable()->change();
        });
    }
}
