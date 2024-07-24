<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class AddColumnLastPriceOnTriggerInsertPriceDefaultOnBmsProductPrices extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::unprepared('DROP TRIGGER IF EXISTS bms_product_avg_price_trigger');
        DB::unprepared('
        CREATE TRIGGER `bms_product_last_price_trigger` 
        AFTER INSERT ON `bms_products` 
        FOR EACH ROW 
        BEGIN 
            INSERT INTO `bms_product_prices` (
                `bms_product_id`, 
                `unit`, 
                `price`, 
                `default`, 
                `active`,
                `created_at`, 
                `updated_at`,
                `iva`
            ) 
            VALUES (
                NEW.`id`, 
                NEW.`sell_unit`, 
                NEW.`last_price`, 
                true, 
                true,
                NOW(), 
                NOW(),
                NEW.`iva`
            ) 
            ON DUPLICATE KEY UPDATE `price`=VALUES(`price`), `unit`=VALUES(`unit`), `default`=true, `updated_at`=NOW(); 
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
        DB::unprepared('DROP TRIGGER IF EXISTS bms_product_last_price_trigger');
    }
}
