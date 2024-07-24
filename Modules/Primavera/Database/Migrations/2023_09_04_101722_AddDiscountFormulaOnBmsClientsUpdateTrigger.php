<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class AddDiscountFormulaOnBmsClientsUpdateTrigger extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::unprepared('DROP TRIGGER IF EXISTS update_bms_clients');
        DB::unprepared('
            CREATE TRIGGER update_bms_clients AFTER UPDATE ON primavera_clients
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

                UPDATE bms_clients
                SET
                    name = NEW.name,
                    tax_number = NEW.tax_number,
                    country = NEW.country,
                    phone_1 = NEW.phone_1,
                    phone_2 = NEW.phone_2,
                    phone_3 = NEW.phone_3,
                    payment_method = NEW.payment_method,
                    payment_condition = NEW.payment_condition,
                    email = NEW.email,
                    total_debt = NEW.total_debt,
                    status = NEW.status,
                    rec_mode = NEW.rec_mode,
                    fiscal_name = NEW.fiscal_name,
                    notes = NEW.notes,
                    erp_client_id = NEW.primavera_id,
                    primavera_table_id = NEW.id,
                    age_debt = NEW.age_debt,
                    discount_default = discount,
                    updated_at = NOW()
                WHERE
                    primavera_table_id = NEW.id;
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
        DB::unprepared('DROP TRIGGER IF EXISTS update_bms_clients');
    }
}
