<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableOrder extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->increments("id");
            $table->string("order_number", 50);
            $table->integer("user_id");
            $table->string("email", 100);
            $table->string("phone_number");
            $table->decimal("grand_total", 10);
            $table->decimal("shipping_cost", 10);
            $table->integer("shipping_package_id");
            $table->integer("shipping_origin_province");
            $table->integer("shipping_origin_city");
            $table->integer("shipping_origin_district");
            $table->integer("shipping_destination_province");
            $table->integer("shipping_destination_city");
            $table->integer("shipping_destination_district");
            $table->string("shipping_destination_address", 255);
            $table->integer("coupon_id")->nullable();
            $table->decimal("discount_value", 10)->default(0);
            $table->integer("status");
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
        Schema::dropIfExists('orders');
    }
}
