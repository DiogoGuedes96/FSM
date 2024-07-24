<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnsDeletedAtAndReminderParentFromBMSRemindersTable extends Migration
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
            $table->dateTime('deleted_at')->nullable();
            $table->unsignedBigInteger('reminder_parent')->unsigned()->nullable();
            $table->index(["reminder_parent"], 'fk_bms_reminder_reminder_parent_idx');
            $table->foreign('reminder_parent', 'fk_bms_reminder_reminder_parent_idx')
                ->references('id')->on($this->tableName);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        
    }
}
