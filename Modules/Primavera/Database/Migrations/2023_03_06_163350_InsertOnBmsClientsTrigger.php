<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class InsertOnBmsClientsTrigger extends Migration
{ 
    /**
    * Run the migrations.
    *
    * @return void
    */

   public function up()
   {
       DB::unprepared('
            CREATE TRIGGER insert_bms_clients AFTER INSERT ON primavera_clients
            FOR EACH ROW
            BEGIN
                INSERT INTO bms_clients (name, tax_number, country, phone_1, phone_2, phone_3, payment_method, payment_condition, email, total_debt, status, rec_mode, fiscal_name, notes, erp_client_id, primavera_table_id, created_at, updated_at)
                VALUES (new.name, new.tax_number, new.country, new.phone_1, new.phone_2, new.phone_3, new.payment_method, new.payment_condition, new.email, new.total_debt, new.status, new.rec_mode, new.fiscal_name, new.notes, new.primavera_id, new.id, NOW(), NOW());
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
