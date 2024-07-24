<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class UpdateBmsProductsTrigger extends Migration
{
    /**
    * Run the migrations.
    *
    * @return void
    */

    public function up()
    {
        DB::unprepared('
            CREATE TRIGGER update_bms_products AFTER UPDATE ON primavera_products
            FOR EACH ROW
            BEGIN
                UPDATE bms_products
                    SET
                        name           = new.name,
                        avg_price      = new.avg_price,
                        last_price     = new.last_price,
                        sell_unit      = new.sell_unit,
                        current_stock  = new.current_stock,
                        stock_mov      = new.stock_mov,
                        family         = new.family,
                        sub_family     = new.sub_family,
                        pvp_1          = new.pvp_1,
                        pvp_2          = new.pvp_2,
                        pvp_3          = new.pvp_3,
                        pvp_4          = new.pvp_4,
                        pvp_5          = new.pvp_5,
                        pvp_6          = new.pvp_6,
                        erp_product_id = new.primavera_id,
                        updated_at     = NOW()
                    WHERE primavera_table_id = old.id;
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
        DB::unprepared('DROP TRIGGER IF EXISTS update_bms_products');
    }
}
