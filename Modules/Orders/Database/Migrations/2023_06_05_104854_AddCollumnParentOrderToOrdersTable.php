<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddCollumnParentOrderToOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $tableName = 'orders';
        $columnName = 'parent_order';

        if (!Schema::hasColumn($tableName, $columnName)) {
            Schema::table($tableName, function (Blueprint $table) use ($columnName) {
                $table->bigInteger($columnName)->nullable();
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
            $tableName = 'orders';
            $columnName = 'parent_order';
    
            if (Schema::hasColumn($tableName, $columnName)) {
                Schema::table($tableName, function (Blueprint $table) use ($columnName) {
                    $table->dropColumn($columnName);
                });
            }
    }
}
