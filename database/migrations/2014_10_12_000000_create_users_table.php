<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsersTable extends Migration
{
    public $tableName = 'users';
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create($this->tableName, function (Blueprint $table) {
            $table->id();
            $table->string('name', 45);
            $table->string('email', 45)->unique();
            $table->string('password', 100)->nullable();
            $table->text('token')->nullable();
            $table->string('refresh_token', 255)->nullable();
            $table->string('avatar', 255)->nullable();
            $table->integer('phone')->nullable();
            $table->timestamps();
            //FK's
            $table->bigInteger('profile_id')->unsigned();

            $table->index(["profile_id"], 'fk_users_profile_id_idx');

            $table->foreign('profile_id', 'fk_users_profile_id_idx')
                ->references('id')->on('user_profile')
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
