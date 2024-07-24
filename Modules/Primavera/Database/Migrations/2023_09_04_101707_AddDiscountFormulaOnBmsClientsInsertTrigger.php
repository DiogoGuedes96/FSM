<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class AddDiscountFormulaOnBmsClientsInsertTrigger extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::unprepared('DROP TRIGGER IF EXISTS insert_bms_clients');
        DB::unprepared('
            CREATE TRIGGER insert_bms_clients AFTER INSERT ON primavera_clients
            FOR EACH ROW
            BEGIN
                DECLARE stepOne DECIMAL(10, 2);
                DECLARE stepTwo DECIMAL(10, 2);
                DECLARE stepThree DECIMAL(10, 2);
                DECLARE discount DECIMAL(10, 2);

                SET stepOne = 100 - ((NEW.discount_1 / 100) * 100);
                SET stepTwo = stepOne - ((NEW.discount_2 / 100) * stepOne);
                SET stepThree = stepTwo - ((NEW.discount_3 / 100) * stepTwo);

                SET discount = 100 - stepThree;

                INSERT INTO bms_clients (
                    name, tax_number, country, phone_1, phone_2, phone_3, payment_method, payment_condition, email, total_debt, status, rec_mode, fiscal_name, notes, erp_client_id, primavera_table_id, age_debt, discount_default, created_at, updated_at
                )
                VALUES (
                    NEW.name, NEW.tax_number, NEW.country, NEW.phone_1, NEW.phone_2, NEW.phone_3, NEW.payment_method, NEW.payment_condition, NEW.email, NEW.total_debt, NEW.status, NEW.rec_mode, NEW.fiscal_name, NEW.notes, NEW.primavera_id, NEW.id, NEW.age_debt, discount, NOW(), NOW()
                );
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
        DB::unprepared('DROP TRIGGER IF EXISTS insert_bms_clients');
    }
}
