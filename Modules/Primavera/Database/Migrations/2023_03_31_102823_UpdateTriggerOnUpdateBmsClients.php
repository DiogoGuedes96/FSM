<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateTriggerOnUpdateBmsClients extends Migration
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
                UPDATE bms_clients
                    SET
                        name                = new.name,
                        tax_number          = new.tax_number,
                        country             = new.country,
                        phone_1             = new.phone_1,
                        phone_2             = new.phone_2,
                        phone_3             = new.phone_3,
                        payment_method      = new.payment_method,
                        payment_condition   = new.payment_condition,
                        email               = new.email,
                        total_debt          = new.total_debt,
                        age_debt            = new.age_debt,
                        status              = new.status,
                        rec_mode            = new.rec_mode,
                        fiscal_name         = new.fiscal_name,
                        notes               = new.notes,
                        erp_client_id       = new.primavera_id,
                        updated_at          = NOW()
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
        DB::unprepared('DROP TRIGGER IF EXISTS update_bms_clients');
    }
}
