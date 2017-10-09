<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableShippingCost extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('shipping_costs', function (Blueprint $table) {
            $table->increments("id");
            $table->integer("shipping_package_id");
            $table->integer("shipping_origin_province");
            $table->integer("shipping_origin_city");
            $table->integer("shipping_origin_district");
            $table->integer("shipping_destination_province");
            $table->integer("shipping_destination_city");
            $table->integer("shipping_destination_district");
            $table->integer("shipping_etd");
            $table->decimal("cost", 10);
            $table->boolean("status");
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('shipping_costs');
    }
}
