<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class PhoneBlackListTable extends Migration
{
    /**
     * Schema table name to migrate
     * @var string
     */
    public $tableName = 'phone_blacklist';

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
            Schema::create($this->tableName, function (Blueprint $table) {
            $table->id();
            $table->string('phone', 45);
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
