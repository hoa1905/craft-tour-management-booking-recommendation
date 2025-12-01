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
        Schema::create('bookings', function (Blueprint $table) {
            $table->id('booking_id');
            // Khóa ngoại
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('tour_id');
            $table->unsignedBigInteger('schedule_id');
            $table->unsignedBigInteger('request_id');

            $table->string('full_name', 100);
            $table->string('phone_number', 15);
            $table->string('email', 150);
            $table->string('address', 255);
            $table->integer('adult_quantity')->default(1);
            $table->integer('child_quantity')->default(0);
            $table->decimal('price_adult_at_booking', 10, 2);
            $table->decimal('price_child_at_booking', 10, 2);
            $table->decimal('total_price', 10, 2)->default(0);
            $table->enum('status', ['waiting_payment', 'paid', 'active', 'canceled', 'completed'])
                ->default('waiting_payment');
            $table->timestamps();

            // Ràng buộc khóa ngoại
            $table->foreign('user_id')->references('user_id')->on('users');
            $table->foreign('tour_id')->references('tour_id')->on('tours');
            $table->foreign('schedule_id')->references('schedule_id')->on('tour_schedules');
            $table->foreign('request_id')->references('request_id')->on('tour_requests');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bookings');
    }
};
