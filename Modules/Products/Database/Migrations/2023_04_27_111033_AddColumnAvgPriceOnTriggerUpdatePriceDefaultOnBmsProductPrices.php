<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class AddColumnAvgPriceOnTriggerUpdatePriceDefaultOnBmsProductPrices extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::unprepared('DROP TRIGGER IF EXISTS update_bms_product_avg_price_trigger');
        DB::unprepared('
            CREATE TRIGGER `update_bms_product_avg_price_trigger` 
            AFTER UPDATE ON `bms_products` 
            FOR EACH ROW 
            BEGIN 
                UPDATE `bms_product_prices` 
                    SET 
                        `price`=NEW.`avg_price`, 
                        `unit`=NEW.`sell_unit`, 
                        `default`=true, 
                        `updated_at`=NOW(),
                        `iva`=NEW.`iva`
                    WHERE `bms_product_id`=NEW.`id` AND `default`=true; 
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
        DB::unprepared('DROP TRIGGER IF EXISTS update_bms_product_avg_price_trigger');
    }
}
