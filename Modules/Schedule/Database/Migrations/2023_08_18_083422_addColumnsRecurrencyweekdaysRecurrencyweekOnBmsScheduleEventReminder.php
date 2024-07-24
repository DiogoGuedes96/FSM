<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnsRecurrencyweekdaysRecurrencyweekOnBmsScheduleEventReminder extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('bms_schedule_event_reminder', function (Blueprint $table) {
            $table->json('recurrency_week_days')->nullable();
            $table->integer('recurrency_week')->nullable();
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
