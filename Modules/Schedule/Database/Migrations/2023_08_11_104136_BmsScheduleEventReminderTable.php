<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Query\Expression;

class BmsScheduleEventReminderTable extends Migration
{
    protected $tableName = 'bms_schedule_event_reminder';

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create($this->tableName, function (Blueprint $table) {
            $table->id();
            $table->string('title', 45)->nullable();
            $table->string('description', 255)->nullable();
            $table->string('client_phone')->nullable();
            $table->string('client_name', 45)->nullable();
            $table->text('notes')->nullable();
            $table->date('date')->nullable();
            $table->time('time')->nullable();
            $table->json('delay')->default(new Expression('(JSON_ARRAY())'))->nullable();       
            $table->string('recurrency_type', 45)->nullable();
            $table->string('status')->nullable();
            //FK's
            $table->unsignedBigInteger('client_id')->nullable();

            $table->index(["client_id"], 'fk_bms_schedule_event_reminder_client_id_idx');

            $table->foreign('client_id', 'fk_bms_schedule_event_reminder_client_id_idx')
                ->references('id')->on('bms_clients')
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
