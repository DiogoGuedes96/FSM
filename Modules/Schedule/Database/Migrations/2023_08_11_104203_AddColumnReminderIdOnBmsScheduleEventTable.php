<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnReminderIdOnBmsScheduleEventTable extends Migration
{
    protected $tableName = 'bms_schedule_event';

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table($this->tableName, function (Blueprint $table) {
            $table->unsignedBigInteger('bms_schedule_event_reminder_id')->unsigned();

            $table->index(["bms_schedule_event_reminder_id"], 'fk_bms_schedule_event_bms_schedule_event_reminder_id_idx');

            $table->foreign('bms_schedule_event_reminder_id', 'fk_bms_schedule_event_bms_schedule_event_reminder_id_idx')
                ->references('id')->on('bms_schedule_event_reminder')
                ->onDelete('no action')
                ->onUpdate('no action');

        });
    }

    public function down()
    {
        Schema::table($this->tableName, function (Blueprint $table) {
            $table->dropForeign('fk_bms_schedule_event_bms_schedule_event_reminder_id_idx');
            $table->dropColumn('bms_schedule_event_reminder_id');
        });
    }
}
