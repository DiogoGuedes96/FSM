<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class PrimaveraClientsContactsTable extends Migration
{
    public $tableName = 'primavera_clients_contacts';

    public function up()
    {
        Schema::create($this->tableName, function (Blueprint $table) {
            $table->id();
            $table->string('primavera_id');
            $table->string('cod_postal')->nullable();
            $table->string('cod_postal_local')->nullable();
            $table->string('contacto')->nullable();
            $table->string('localidade')->nullable();
            $table->string('morada')->nullable();
            $table->string('pais')->nullable();
            $table->string('telefone')->nullable();
            $table->string('telefone_2')->nullable();
            $table->string('telemovel')->nullable();
            $table->string('email')->nullable();
            $table->string('primeiro_nome')->nullable();
            $table->string('ultimo_nome')->nullable();
            $table->string('zona')->nullable();
            $table->string('descricao_zona')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists($this->tableName);
    }
}
