<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AsteriskCredentialsTable extends Migration
{
   
    /**
     * Schema table name to migrate
     * @var string
     */
    public $tableName = 'asterisk_credentials';

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
            Schema::create($this->tableName, function (Blueprint $table) {
            $table->id();
            $table->string('host', 20);
            $table->string('scheme', 15);
            $table->string('port', 5);
            $table->string('username', 45);
            $table->string('secret', 45);
            $table->bigInteger('connect_timeout')->unsigned();
            $table->bigInteger('read_timeout')->unsigned();
            $table->string('internal_pw', 10);
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