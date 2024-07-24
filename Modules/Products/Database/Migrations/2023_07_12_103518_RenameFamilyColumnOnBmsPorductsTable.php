<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class RenameFamilyColumnOnBmsPorductsTable extends Migration
{
    public function up()
    {
        Schema::table('bms_products', function (Blueprint $table) {
            $table->renameColumn('family', 'category_code');
        });
    }

    public function down()
    {
        Schema::table('bms_products', function (Blueprint $table) {
            $table->renameColumn('category_code', 'family');
        });
    }
}
