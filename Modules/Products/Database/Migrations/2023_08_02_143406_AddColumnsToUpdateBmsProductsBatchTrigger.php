<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class AddColumnsToUpdateBmsProductsBatchTrigger extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::unprepared('DROP TRIGGER IF EXISTS update_bms_product_batch');
        DB::unprepared('
            CREATE TRIGGER update_bms_product_batch AFTER UPDATE ON primavera_products_batch
            FOR EACH ROW
            BEGIN
                DECLARE primaveraProductId int;
                DECLARE primaveraProductBatchId int;
                DECLARE bmsProductId int;

                SET primaveraProductId = NEW.primavera_product_id;
                SET primaveraProductBatchId = NEW.id; 

                SET bmsProductId = (
                    SELECT id 
                    FROM bms_products AS bp 
                    WHERE bp.primavera_table_id = primaveraProductId); 

                IF bmsProductId IS NOT NULL THEN
                    UPDATE bms_products_batch
                    SET 
                        batch_number         = NEW.batch_number,
                        description          = NEW.batch_number,
                        quantity             = NEW.quantity,
                        active               = NEW.active,
                        erp_product_batch_id = primaveraProductBatchId,
                        bms_product_id       = bmsProductId,
                        expiration_date      = NEW.expiration_date,
                        updated_at           = NOW()
                    WHERE erp_product_batch_id = primaveraProductBatchId;
                ELSE
                    INSERT INTO bms_products_batch(batch_number, description, quantity, active, erp_product_batch_id, bms_product_id, expiration_date, updated_at, created_at)
                    VALUES(new.batch_number, new.description, new.quantity, new.active, primaveraProductBatchId, bmsProductId, new.expiration_date, NOW(), NOW());
                END IF;
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
        DB::unprepared('DROP TRIGGER IF EXISTS update_bms_product_batch');
    }
}
