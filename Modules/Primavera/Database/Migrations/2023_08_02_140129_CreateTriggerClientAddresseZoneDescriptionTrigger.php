<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTriggerClientAddresseZoneDescriptionTrigger extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::unprepared('DROP TRIGGER IF EXISTS insert_bms_address_zone');
        DB::unprepared('
            CREATE TRIGGER insert_bms_address_zone AFTER INSERT ON primavera_clients
            FOR EACH ROW
            BEGIN
                DECLARE zone_id INT;
                DECLARE bms_client_id INT;

                SELECT id INTO zone_id
                FROM bms_address_zone
                WHERE zone = NEW.zone;

                IF zone_id IS NOT NULL THEN
                    UPDATE bms_address_zone
                    SET description = NEW.zone_description
                    WHERE zone = NEW.zone;
                ELSE
                    INSERT INTO bms_address_zone (zone, description)
                    VALUES (NEW.zone, NEW.zone_description);

                    SELECT id INTO zone_id
                    FROM bms_address_zone
                    WHERE zone = NEW.zone;
                END IF;

                SET bms_client_id = (SELECT id FROM bms_clients WHERE erp_client_id = NEW.primavera_id);

                IF bms_client_id IS NOT NULL THEN
                    UPDATE bms_address
                    SET bms_address_zone_id = zone_id
                    WHERE bms_client = bms_client_id;
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
        DB::unprepared('DROP TRIGGER IF EXISTS insert_bms_address_zone');
    }
}
