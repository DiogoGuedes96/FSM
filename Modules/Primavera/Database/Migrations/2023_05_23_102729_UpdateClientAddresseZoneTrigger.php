<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateClientAddresseZoneTrigger extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::unprepared('
            CREATE TRIGGER update_bms_address_zone AFTER update ON bms_clients
            FOR EACH ROW
            BEGIN

                DECLARE id_bms_client int;
                DECLARE address_zone varchar(45);
            
                SET id_bms_client = NEW.id;
            
                SET address_zone = (
                    SELECT zone 
                    FROM primavera_clients AS pc 
                    JOIN bms_clients AS bc on bc.primavera_table_id = pc.id 
                    WHERE pc.id = new.primavera_table_id);      
                    
                IF EXISTS (

                    SELECT 1 
                    FROM bms_address 
                    WHERE bms_client = id_bms_client

                ) THEN

                    UPDATE bms_address
                    SET zone = address_zone
                    WHERE bms_client = id_bms_client;
                    
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
        DB::unprepared('DROP TRIGGER IF EXISTS update_bms_address_zone');
    }
}
