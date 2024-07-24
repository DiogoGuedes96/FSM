<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class BmsScheduleEventRememberTable extends Migration
{
    protected $tableName = 'bms_schedule_event_remember';

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create($this->tableName, function (Blueprint $table) {
            $table->id();
            $table->date('date')->nullable();
            $table->time('time')->nullable();
            $table->string('status')->nullable();
            $table->timestamps();
            //FK's
            $table->unsignedBigInteger('bms_schedule_event_id')->nullable();

            $table->index(["bms_schedule_event_id"], 'fk_bms_schedule_event_remember_bms_schedule_event_id_idx');

            $table->foreign('bms_schedule_event_id', 'fk_bms_schedule_event_remember_bms_schedule_event_id_idx')
                ->references('id')->on('bms_schedule_event')
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
