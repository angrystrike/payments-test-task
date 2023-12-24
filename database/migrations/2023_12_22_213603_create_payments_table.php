<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePaymentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('currency_id');
            $table->string('transaction_id');
            $table->unsignedBigInteger('order_id');
            $table->decimal('amount', 10, 2)->nullable();
            $table->enum('status', ['completed', 'pending', 'refunded', 'failed'])->nullable();
            $table->timestamp('order_created_at')->nullable();
            $table->timestamp('order_completed_at')->nullable();
            $table->decimal('refunded_amount', 10, 2)->nullable();
            $table->decimal('provision_amount', 10, 2)->nullable();
            $table->string('hash')->nullable();
            $table->boolean('is_cash')->nullable();
            $table->boolean('send_push')->nullable();
            $table->integer('processing_time')->nullable();
            
            $table->foreign('currency_id')->references('id')->on('currencies');
            $table->foreign('user_id')->references('id')->on('users');
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
