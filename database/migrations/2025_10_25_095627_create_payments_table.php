<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->id('payment_id');
            $table->unsignedBigInteger('booking_id');
            $table->string('transaction_id', 255)->nullable();

            $table->enum('payment_method', ['Office', 'MoMo', 'Vnpay']);
            $table->decimal('amount', 10, 2);
            $table->string('payper_name', 100)->nullable();
            $table->string('payper_email', 150)->nullable();
            $table->enum('status', ['pending', 'success', 'failed', 'refunded'])->default('pending');

            $table->timestamps();

            // Khóa ngoại
            $table->foreign('booking_id')->references('booking_id')->on('bookings');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
