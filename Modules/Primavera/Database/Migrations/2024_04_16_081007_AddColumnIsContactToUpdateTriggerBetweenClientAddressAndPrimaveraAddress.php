<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnIsContactToUpdateTriggerBetweenClientAddressAndPrimaveraAddress extends Migration
{
    public function up()
    {
        DB::unprepared('DROP TRIGGER IF EXISTS client_address_primavera_address_after_update_trigger');
        DB::unprepared('
            CREATE TRIGGER client_address_primavera_address_after_update_trigger
            AFTER UPDATE ON primavera_address 
            FOR EACH ROW
            BEGIN
                DECLARE bms_client_id INT;
                DECLARE address_id INT;
        
                SET bms_client_id = (SELECT id FROM bms_clients WHERE erp_client_id = NEW.primavera_id);
        
                IF bms_client_id IS NOT NULL THEN
                    SELECT id INTO address_id
                    FROM bms_client_address
                    WHERE alternativeAddress = NEW.alternativeAddress AND bms_client = bms_client_id;
        
                    IF address_id IS NULL THEN
                        INSERT INTO bms_client_address (
                            bms_client, 
                            primavera_id,
                            iecCode,
                            iecExemptionCode,
                            localCode,
                            postalCode,
                            postalCodeLocation,
                            district,
                            eGAR_APACode,
                            eGAR_IsExempt,
                            eGAR_PGLNumber,
                            eGAR_ProducerType,
                            fax,
                            iecExempt,
                            location,
                            address,
                            address2,
                            alternativeAddress,
                            shippingAddress,
                            billingAddress,
                            defaultAddress,
                            country,
                            phone,
                            phone_2,
                            senderType,
                            lastUpdateVersion, 
                            updated_at,
                            is_contact
                        ) VALUES (  
                            bms_client_id, 
                            NEW.primavera_id,
                            NEW.iecCode,
                            NEW.iecExemptionCode,
                            NEW.localCode,
                            NEW.postalCode,
                            NEW.postalCodeLocation,
                            NEW.district,
                            NEW.eGAR_APACode,
                            NEW.eGAR_IsExempt,
                            NEW.eGAR_PGLNumber,
                            NEW.eGAR_ProducerType,
                            NEW.fax,
                            NEW.iecExempt,
                            NEW.location,
                            NEW.address,
                            NEW.address2,
                            NEW.alternativeAddress,
                            NEW.shippingAddress,
                            NEW.billingAddress,
                            NEW.defaultAddress,
                            NEW.country,
                            NEW.phone,
                            NEW.phone_2,
                            NEW.senderType,
                            NEW.lastUpdateVersion, 
                            NOW(),
                            NEW.is_contact
                        );
                    ELSE 
                        -- Update existing record
                        UPDATE bms_client_address
                        SET iecCode = NEW.iecCode,
                            iecExemptionCode = NEW.iecExemptionCode,
                            localCode = NEW.localCode,
                            postalCode = NEW.postalCode,
                            postalCodeLocation = NEW.postalCodeLocation,
                            district = NEW.district,
                            eGAR_APACode = NEW.eGAR_APACode,
                            eGAR_IsExempt = NEW.eGAR_IsExempt,
                            eGAR_PGLNumber = NEW.eGAR_PGLNumber,
                            eGAR_ProducerType = NEW.eGAR_ProducerType,
                            fax = NEW.fax,
                            iecExempt = NEW.iecExempt,
                            location = NEW.location,
                            address = NEW.address,
                            address2 = NEW.address2,
                            alternativeAddress = NEW.alternativeAddress,
                            shippingAddress = NEW.shippingAddress,
                            billingAddress = NEW.billingAddress,
                            defaultAddress = NEW.defaultAddress,
                            country = NEW.country,
                            phone = NEW.phone,
                            phone_2 = NEW.phone_2,
                            senderType = NEW.senderType,
                            lastUpdateVersion = NEW.lastUpdateVersion,
                            updated_at = NOW(),
                            is_contact = NEW.is_contact 
                        WHERE id = address_id;
                    END IF;
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
        DB::unprepared('DROP TRIGGER IF EXISTS client_address_primavera_address_after_update_trigger');    
    }
}
