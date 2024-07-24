<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class DeleteBmsProductBatchOnDeletePrimaveraProductBatchTrigger extends Migration
{
    public function up()
    {
        DB::unprepared('DROP TRIGGER IF EXISTS delete_from_bms_product_batch');
        DB::unprepared('
            CREATE TRIGGER delete_from_bms_product_batch
            BEFORE DELETE ON primavera_products_batch
            FOR EACH ROW
            BEGIN
                DELETE FROM bms_products_batch
                WHERE erp_product_batch_id = OLD.id;
            END;
        ');
    }

    public function down()
    {
        DB::unprepared('DROP TRIGGER IF EXISTS delete_from_bms_product_batch');
    }
}
