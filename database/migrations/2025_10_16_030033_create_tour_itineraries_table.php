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
        Schema::create('tour_itineraries', function (Blueprint $table) {
            $table->id('itinerary_id');
            $table->unsignedBigInteger('tour_id');
            $table->integer('day_number');
            $table->text('activities');
            $table->string('meals', 100);
            $table->string('accommodation', 100);

            $table->foreign('tour_id')->references('tour_id')->on('tours');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tour_itineraries');
    }
};
