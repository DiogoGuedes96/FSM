<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddTipoPrecoEAnuladoClientToBmsClientTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('bms_clients', function (Blueprint $table) {
            $table->string('tipo_preco')->nullable();
            $table->boolean('cliente_anulado')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('bms_clients', function (Blueprint $table) {
            $table->dropColumn('discount_default');
        });
    }
}
