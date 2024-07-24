<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTriggerClientContactAddresseZoneDescriptionTrigger extends Migration
{
    public function up()
    {
        DB::unprepared('DROP TRIGGER IF EXISTS insert_bms_address_zone_from_contacts');
        DB::unprepared('
            CREATE TRIGGER insert_bms_address_zone_from_contacts AFTER INSERT ON primavera_clients_contacts
            FOR EACH ROW
            BEGIN
                DECLARE zone_id INT;
                DECLARE bms_client_id INT;
                DECLARE address_id INT;

                SET zone_id = (SELECT id FROM bms_address_zone WHERE zone = NEW.zona);

                IF zone_id IS NOT NULL THEN
                    UPDATE bms_address_zone
                    SET description = NEW.descricao_zona
                    WHERE zone = NEW.zona;
                ELSE
                    INSERT INTO bms_address_zone (zone, description)
                    VALUES (NEW.zona, NEW.descricao_zona);

                    SELECT id INTO zone_id
                    FROM bms_address_zone
                    WHERE zone = NEW.zona;
                END IF;

                SET bms_client_id = (SELECT id FROM bms_clients WHERE erp_client_id = NEW.primavera_id);

                IF bms_client_id IS NOT NULL THEN
                    SELECT id INTO address_id
                    FROM bms_address
                    WHERE address = NEW.morada AND bms_client = bms_client_id;

                    IF address_id IS NULL THEN
                        INSERT INTO bms_address (address, postal_code, postal_code_address, bms_address_zone_id, bms_client, updated_at, created_at)
                        VALUES (NEW.morada, NEW.cod_postal, NEW.cod_postal_local, zone_id, bms_client_id, NOW(), NOW());
                    END IF;
                END IF;
            END;
        ');
    }

    public function down()
    {
        DB::unprepared('DROP TRIGGER IF EXISTS insert_bms_address_zone_from_contacts');
    }
}
