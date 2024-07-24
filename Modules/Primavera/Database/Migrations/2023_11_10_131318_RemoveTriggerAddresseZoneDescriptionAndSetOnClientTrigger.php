<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class RemoveTriggerAddresseZoneDescriptionAndSetOnClientTrigger extends Migration
{
    public function up()
    {
        DB::unprepared('DROP TRIGGER IF EXISTS insert_bms_address_zone_from_clients');
    }

    public function down()
    {
        DB::unprepared('DROP TRIGGER IF EXISTS insert_bms_address_zone_from_clients');
    }
}
