<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class InsertBmsClientAddressTrigger extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */

    public function up()
    {
        DB::unprepared('DROP TRIGGER IF EXISTS insert_bms_address');
        DB::unprepared('
            CREATE TRIGGER insert_bms_address AFTER INSERT ON bms_clients
            FOR EACH ROW
            BEGIN
                DECLARE idbmsclients int;
                DECLARE address1 nvarchar(100);
                DECLARE postalCode1 nvarchar(100);
                DECLARE postalCodeAddress1 nvarchar(100);
                DECLARE primavera_table_id1 varchar(45);
            
                SET idbmsclients = new.id;
                SET primavera_table_id1 = new.primavera_table_id;
            
                SET address1 = (
                    SELECT address 
                    FROM primavera_clients AS pc 
                    JOIN bms_clients AS bp on bp.primavera_table_id = pc.id 
                    WHERE pc.id = new.primavera_table_id);

                SET postalCode1 = (
                    SELECT postal_code 
                    FROM primavera_clients AS pc 
                    JOIN bms_clients AS bp on bp.primavera_table_id = pc.id 
                    WHERE pc.id = new.primavera_table_id);    

                SET postalCodeAddress1 = (
                    SELECT postal_code_address 
                    FROM primavera_clients AS pc 
                    JOIN bms_clients AS bp on bp.primavera_table_id = pc.id 
                    WHERE pc.id = new.primavera_table_id);  
                    
                INSERT INTO bms_client_address(
                    address, 
                    postalCode, 
                    postalCodeLocation, 
                    bms_client, 
                    primavera_id,
                    updated_at, 
                    created_at)
                VALUES(
                    address1, 
                    postalCode1, 
                    postalCodeAddress1, 
                    idbmsclients, 
                    new.primavera_table_id,
                    NOW(), 
                    NOW()
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
