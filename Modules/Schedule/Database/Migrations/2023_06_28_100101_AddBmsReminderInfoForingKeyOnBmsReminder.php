<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddBmsReminderInfoForingKeyOnBmsReminder extends Migration
{
    protected $tableName = 'bms_reminder';

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table($this->tableName, function (Blueprint $table) {
            $table->unsignedBigInteger('bms_reminder_info')->unsigned();

            $table->index(["bms_reminder_info"], 'fk_bms_reminder_bms_reminder_info_idx');

            $table->foreign('bms_reminder_info', 'fk_bms_reminder_bms_reminder_info_idx')
            ->references('id')->on('bms_reminder_info')
            ->onDelete('no action')
            ->onUpdate('no action');

        });
    }

    public function down()
    {
        Schema::table($this->tableName, function (Blueprint $table) {
            $table->dropForeign('bms_reminder_bms_reminder_info_id_foreign');
            $table->dropColumn('bms_reminder_info');
        });
    }
}
