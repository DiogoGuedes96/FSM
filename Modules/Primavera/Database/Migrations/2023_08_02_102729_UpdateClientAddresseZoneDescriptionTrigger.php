<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateClientAddresseZoneDescriptionTrigger extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::unprepared('DROP TRIGGER IF EXISTS update_bms_address_zone');
        DB::unprepared('
            CREATE TRIGGER update_bms_address_zone AFTER update ON primavera_clients
            FOR EACH ROW
            BEGIN
                DECLARE zone_id int;
                DECLARE bms_client_id int;

                SELECT id INTO zone_id
                FROM bms_address_zone
                WHERE zone = new.zone;

                SELECT id INTO bms_client_id
                FROM bms_client
                WHERE erp_client_id = new.primavera_id;

                IF zone_id IS NOT NULL THEN
                    UPDATE bms_address_zone
                    SET description = new.zone_description
                    WHERE zone = new.zone;
                ELSE
                    INSERT INTO bms_address_zone (zone, description)
                    VALUES (new.zone, new.zone_description);

                    SELECT id INTO zone_id
                    FROM bms_address_zone
                    WHERE zone = new.zone;
                END IF;

                UPDATE bms_address
                SET bms_address_zone_id = zone_id
                WHERE bms_client = bms_client_id;
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
