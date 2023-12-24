<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCardsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cards', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->unsignedBigInteger('bin')->nullable();
            $table->unsignedBigInteger('last_digits')->nullable();
            $table->string('payment_system', 50)->nullable();
            $table->string('payment_group', 10)->nullable();
            $table->string('country', 10)->nullable();
            $table->enum('currency', ['usd', 'eur', 'uah'])->default('usd');
            $table->foreign('user_id')->references('id')->on('users');
            $table->decimal('balance', 10, 2)->default(0.00);

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
        Schema::dropIfExists('cards');
    }
}
