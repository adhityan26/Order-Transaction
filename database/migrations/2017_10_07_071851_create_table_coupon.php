<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableCoupon extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('coupons', function (Blueprint $table) {
            $table->increments("id");
            $table->string("code", 15);
            $table->string("name", 30);
            $table->string("desc", 255);
            $table->dateTime("valid_from");
            $table->dateTime("valid_to");
            $table->decimal("coupon_value", 10);
            $table->decimal("coupon_percentage", 5,2)->nullable();
            $table->integer("limit");
            $table->integer("limit_terms");
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
        Schema::dropIfExists('coupons');
    }
}
