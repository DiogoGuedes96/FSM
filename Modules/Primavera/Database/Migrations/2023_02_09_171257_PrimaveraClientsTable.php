<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class PrimaveraClientsTable extends Migration
{
    public $tableName = 'primavera_clients';
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create($this->tableName, function (Blueprint $table) {
            $table->id();
            $table->string('primavera_id');
            $table->string('name')->nullable();
            $table->string('address')->nullable();
            $table->string('postal_code')->nullable();
            $table->string('postal_code_address')->nullable();
            $table->string('country')->nullable();
            $table->string('tax_number')->nullable();
            $table->string('phone_1')->nullable();
            $table->string('phone_2')->nullable();
            $table->string('phone_3')->nullable();
            $table->string('payment_method')->nullable();
            $table->string('payment_condition')->nullable();
            $table->string('email')->nullable();
            $table->float('total_debt')->nullable();
            $table->string('status')->nullable();
            $table->string('rec_mode')->nullable();
            $table->string('fiscal_name')->nullable();
            $table->text('notes',500)->nullable();
            $table->timestamps();
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