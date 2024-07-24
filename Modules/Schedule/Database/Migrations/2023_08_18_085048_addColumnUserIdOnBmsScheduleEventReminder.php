<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnUserIdOnBmsScheduleEventReminder extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('bms_schedule_event_reminder', function (Blueprint $table) {
            $table->unsignedBigInteger('user_id')->nullable();

            $table->index(["user_id"], 'fk_bms_schedule_event_reminder_user_id_idx');

            $table->foreign('user_id', 'fk_bms_schedule_event_reminder_user_id_idx')
                ->references('id')->on('users')
                ->onDelete('no action')
                ->onUpdate('no action');
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
