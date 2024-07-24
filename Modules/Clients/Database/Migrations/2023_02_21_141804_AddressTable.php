<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddressTable extends Migration
{
    public $tableName = 'bms_address';
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create($this->tableName, function (Blueprint $table) {
            $table->id();
            $table->string('address')->nullable();
            $table->string('postal_code')->nullable();
            $table->string('postal_code_address')->nullable();
            $table->boolean('selected_delivery_address')->nullable();
            $table->boolean('selected_invoice_address')->nullable();
            $table->timestamps();
            //FK's
            $table->bigInteger('bms_client')->unsigned();

            $table->index(["bms_client"], 'fk_bms_address_bms_client_idx');

            $table->foreign('bms_client', 'fk_bms_address_bms_client_idx')
            ->references('id')->on('bms_clients')
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
