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
        Schema::create('tour_requests', function (Blueprint $table) {
            $table->id('request_id');
            $table->unsignedBigInteger('user_id');
            $table->string('full_name', 100);
            $table->string('phone_number', 15);
            $table->string('email', 100);
            $table->string('departure_point', 100);
            $table->string('desired_destination', 100);
            $table->text('workshop')->nullable();
            $table->json('village_ids'); // Mảng ID làng nghề
            $table->date('start_date');
            $table->date('end_date');
            $table->integer('adult_quantity')->default(1);
            $table->integer('child_quantity')->default(0);
            $table->decimal('price_adult_final', 15, 2)->nullable();
            $table->decimal('price_child_final', 15, 2)->nullable();
            $table->decimal('final_total_price', 15, 2)->nullable();
            $table->decimal('budget_min', 15, 2)->nullable();
            $table->decimal('budget_max', 15, 2)->nullable();
            $table->enum('transportation', ['Ô tô', 'Xe khách', 'Tàu hỏa', 'Máu bay', 'Bất kỳ'])->default('Bất kỳ');

            $table->text('requirements')->nullable();
            $table->enum('status', ['pending', 'offering', 'accepted', 'rejected', 'canceled', 'completed'])
                ->default('pending');
            $table->text('admin_note')->nullable();

            $table->timestamps();

            $table->foreign('user_id')->references('user_id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tour_requests');
    }
};
