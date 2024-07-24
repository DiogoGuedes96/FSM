<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddNewColumnsInAddressTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('bms_client_address', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('bms_client')->unsigned();
            $table->foreign('bms_client')->references('id')->on('bms_clients');
            $table->string('primavera_id');
            $table->string('iecCode')->nullable();
            $table->string('iecExemptionCode')->nullable();
            $table->string('localCode')->nullable();
            $table->string('postalCode')->nullable();
            $table->string('postalCodeLocation')->nullable();
            $table->string('district')->nullable();
            $table->string('eGAR_APACode')->nullable();
            $table->boolean('eGAR_IsExempt')->nullable();
            $table->string('eGAR_PGLNumber')->nullable();
            $table->string('eGAR_ProducerType')->nullable();
            $table->string('fax')->nullable();
            $table->boolean('iecExempt')->nullable();
            $table->string('location')->nullable();
            $table->string('address')->nullable();
            $table->string('address2')->nullable();
            $table->string('alternativeAddress')->nullable();
            $table->boolean('shippingAddress')->nullable();
            $table->boolean('billingAddress')->nullable();
            $table->boolean('defaultAddress')->nullable();
            $table->string('country')->nullable();
            $table->string('phone')->nullable();
            $table->string('senderType')->nullable();
            $table->string('lastUpdateVersion')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
