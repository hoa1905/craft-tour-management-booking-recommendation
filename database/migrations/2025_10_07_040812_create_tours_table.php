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
        Schema::create('tours', function (Blueprint $table) {
            $table->id('tour_id');
            $table->string('tour_name', 150);
            $table->unsignedBigInteger('village_id');
            $table->unsignedBigInteger('partner_id');
            $table->decimal('price', 10, 3);
            $table->string('duration', 20);
            $table->text('description');
            $table->date('start_date');
            $table->date('end_date');
            $table->string('departure_point', 100);
            $table->string('destination', 100);
            $table->string('transport', 50);
            $table->string('image_url', 255);
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
            $table->timestamps();

            $table->foreign('village_id')->references('village_id')->on('villages');
            $table->foreign('partner_id')->references('user_id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tours');
    }
};
