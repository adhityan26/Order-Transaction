<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTablePayment extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->increments("id");
            $table->integer("order_id");
            $table->integer("user_id");
            $table->integer("status");
            $table->dateTime("payment_date");
            $table->decimal("total_payment", 10);
            $table->string("reference_no", 50)->nullable();
            $table->string("user_account", 50);
            $table->string("user_bank_account", 20);
            $table->string("notes", 100);
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
        Schema::dropIfExists('payments');
    }
}
