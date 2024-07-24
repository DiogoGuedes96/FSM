<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Query\Expression;

class BmsScheduleEventTable extends Migration
{
    protected $tableName = 'bms_schedule_event';

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
            $table->json('recurrency_week_days')->default(new Expression('(JSON_ARRAY())'))->nullable();            
            $table->integer('recurrency_week')->nullable();            
            $table->string('status', 45)->nullable();
            $table->timestamps();
            //FK's
            $table->unsignedBigInteger('user_id')->nullable();
            $table->unsignedBigInteger('parent_id')->nullable();

            $table->index(["user_id"], 'fk_bms_schedule_event_user_id_idx');
            $table->index(["parent_id"], 'fk_bms_schedule_event_parent_id_idx');

            $table->foreign('user_id', 'fk_bms_schedule_event_user_id_idx')
                ->references('id')->on('users')
                ->onDelete('no action')
                ->onUpdate('no action');

            $table->foreign('parent_id', 'fk_bms_schedule_event_parent_id_idx')
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
