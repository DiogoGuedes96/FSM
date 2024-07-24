<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class BmsReminderInfoTable extends Migration
{
    protected $tableName = 'bms_reminder_info';

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create($this->tableName, function (Blueprint $table) {
            $table->id();
            $table->string('name')->nullable();
            $table->date('date')->nullable();
            $table->time('time')->nullable();            
            $table->time('remember_time')->nullable();
            $table->time('remember_label')->nullable();
            $table->string('client_name')->nullable();
            $table->string('client_phone')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
            //FK's
            $table->unsignedBigInteger('client_id')->nullable();

            $table->index(["client_id"], 'fk_bms_reminder_info_client_id_idx');

            $table->foreign('client_id', 'fk_bms_reminder_info_client_id_idx')
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
