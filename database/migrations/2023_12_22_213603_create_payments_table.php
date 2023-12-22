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

            $table->string('transaction_id');
            $table->unsignedBigInteger('order_id');
            $table->decimal('amount', 10, 2)->nullable();
            $table->enum('currency', ['usd', 'eur', 'uah'])->default('usd');
            $table->enum('status', ['completed', 'pending', 'refunded', 'failed'])->nullable();
            $table->timestamp('order_created_at')->nullable();
            $table->timestamp('order_completed_at')->nullable();
            $table->decimal('refunded_amount', 10, 2)->nullable();
            $table->decimal('provision_amount', 10, 2)->nullable();
            $table->string('hash')->nullable();
            $table->boolean('is_cash')->nullable();
            $table->boolean('send_push')->nullable();
            $table->integer('processing_time')->nullable();

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
