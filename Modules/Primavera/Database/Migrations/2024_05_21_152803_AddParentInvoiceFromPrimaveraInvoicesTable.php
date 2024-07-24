<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddParentInvoiceFromPrimaveraInvoicesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('primavera_invoices', function (Blueprint $table) {
            $table->unsignedBigInteger('parent')->nullable();
            $table->foreign('parent')->references('id')->on('primavera_invoices')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('primavera_invoices', function (Blueprint $table) {
            // Drop the foreign key constraint
            $table->dropForeign(['parent']);

            // Revert the parent column to string if necessary
            $table->string('parent')->nullable()->change();
        });
    }
}
