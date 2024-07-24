<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class AddIvaColumnToTriggerInsertPrimaveraProducts extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::unprepared('DROP TRIGGER IF EXISTS insert_bms_products');
        DB::unprepared('
            CREATE TRIGGER insert_bms_products AFTER INSERT ON primavera_products
            FOR EACH ROW
            BEGIN
                INSERT INTO bms_products (
                    name, avg_price, last_price, sell_unit, current_stock, stock_mov, family, sub_family, pvp_1, pvp_2, pvp_3, pvp_4, pvp_5, pvp_6, erp_product_id, primavera_table_id, iva, created_at, updated_at)
                VALUES (new.name, new.avg_price, new.last_price, new.sell_unit, new.current_stock, new.stock_mov, new.family, new.sub_family, new.pvp_1, new.pvp_2, new.pvp_3, new.pvp_4, new.pvp_5, new.pvp_6, new.primavera_id, new.id, new.iva, NOW(), NOW());
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
        DB::unprepared('DROP TRIGGER IF EXISTS insert_bms_products');
    }
}
