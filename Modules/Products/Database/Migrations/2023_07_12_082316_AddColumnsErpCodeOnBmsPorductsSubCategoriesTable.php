<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnsErpCodeOnBmsPorductsSubCategoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('bms_products_sub_categories', function (Blueprint $table) {
            $table->string('erp_code')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */

    public function down()
    {
        Schema::table('bms_products_sub_categories', function (Blueprint $table) {
            $table->dropColumn('erp_code');
        });
    }
}
