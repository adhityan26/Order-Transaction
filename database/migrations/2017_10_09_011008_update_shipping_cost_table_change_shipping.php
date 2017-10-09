<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateShippingCostTableChangeShipping extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('shipping_costs', function (Blueprint $table) {
            $table->string("shipping_origin_province", 50)->change();
            $table->string("shipping_origin_city", 50)->change();
            $table->string("shipping_origin_district", 50)->change();
            $table->string("shipping_destination_province", 50)->change();
            $table->string("shipping_destination_city", 50)->change();
            $table->string("shipping_destination_district", 50)->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('shipping_costs', function (Blueprint $table) {
            $table->integer("shipping_origin_province")->change();
            $table->integer("shipping_origin_city")->change();
            $table->integer("shipping_origin_district")->change();
            $table->integer("shipping_destination_province")->change();
            $table->integer("shipping_destination_city")->change();
            $table->integer("shipping_destination_district")->change();
        });
    }
}
