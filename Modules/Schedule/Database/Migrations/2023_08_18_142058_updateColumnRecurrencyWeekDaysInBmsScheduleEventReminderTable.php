<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Query\Expression;

class UpdateColumnRecurrencyWeekDaysInBmsScheduleEventReminderTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('bms_schedule_event_reminder', function (Blueprint $table) {
            $table->json('recurrency_week_days')->default(new Expression('(JSON_ARRAY())'))->nullable()->change();
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
