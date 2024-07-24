<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class RenameSubFamilyColumnOnBmsPorductsTable extends Migration
{
    public function up()
    {
        Schema::table('bms_products', function (Blueprint $table) {
            $table->renameColumn('sub_family', 'sub_category_code');
        });
    }

    public function down()
    {
        Schema::table('bms_products', function (Blueprint $table) {
            $table->renameColumn('sub_category_code', 'sub_family');
        });
    }
}
