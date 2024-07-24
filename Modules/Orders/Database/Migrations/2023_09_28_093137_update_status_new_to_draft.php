<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class UpdateStatusNewToDraft extends Migration
{
    public function up()
    {
        DB::table('orders')->where('status', '=', 'new')
            ->update(['status' => 'draft']);
    }

    public function down()
    {
        DB::table('orders')->where('status', '=', 'draft')
            ->update(['status' => 'new']);
    }
}
