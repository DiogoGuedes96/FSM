<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class InsertBmsProductsBachTrigger extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::unprepared('
            CREATE TRIGGER insert_bms_product_batch AFTER INSERT ON primavera_products_batch
            FOR EACH ROW
            BEGIN
                DECLARE primaveraProductId int;
                DECLARE primaveraProductBatchId int;
                DECLARE bmsProductId int;
            
                SET primaveraProductId = new.primavera_product_id;
                SET primaveraProductBatchId = new.id;
            
                SET bmsProductId = (
                    SELECT id 
                    FROM bms_products AS bp 
                    WHERE bp.primavera_table_id = primaveraProductId); 
                    
                INSERT INTO bms_products_batch(batch_number, active, erp_product_batch_id, bms_product_id, updated_at, created_at)
                VALUES(new.batch_number, true, primaveraProductBatchId, bmsProductId, NOW(), NOW());
            END;
        ');
    }

    /**
    * Reverse the migrations.
    *
    * @return void
    */
    public function down()
    {
        DB::unprepared('DROP TRIGGER IF EXISTS insert_bms_product_batch');
    }
}
