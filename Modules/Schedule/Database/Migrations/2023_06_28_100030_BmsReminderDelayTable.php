<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class BmsReminderDelayTable extends Migration
{
    protected $tableName = 'bms_reminder_delay';

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create($this->tableName, function (Blueprint $table) {
            $table->id();
            $table->time('time_delay')->nullable();            
            $table->time('remember_time_delay')->nullable();
            $table->string('remember_label_delay')->nullable();
            $table->date('date')->nullable();
            $table->timestamps();
            //FK's
            $table->bigInteger('bms_reminder')->unsigned();

            $table->index(["bms_reminder"], 'fk_bms_reminder_delay_bms_reminder_idx');

            $table->foreign('bms_reminder', 'fk_bms_reminder_delay_bms_reminder_idx')
            ->references('id')->on('bms_reminder')
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
        Schema::dropIfExists($this->tableName);
    }
}
