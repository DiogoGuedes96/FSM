<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Query\Expression;


class BmsRemindersTable extends Migration
{
    protected $tableName = 'bms_reminder';

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create($this->tableName, function (Blueprint $table) {
            $table->id();
            $table->json('week_days')->default(new Expression('(JSON_ARRAY())'))->nullable();            
            $table->string('month_day')->nullable();
            $table->string('year_day')->nullable();
            $table->date('start_date')->nullable();
            $table->boolean('active')->nullable();
            $table->timestamps();
            //FK's
            $table->bigInteger('user_id')->unsigned();

            $table->index(["user_id"], 'fk_bms_reminder_user_id_idx');

            $table->foreign('user_id', 'fk_bms_reminder_user_id_idx')
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
        Schema::dropIfExists($this->tableName);
    }
}
