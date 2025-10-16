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
        Schema::create('tour_villages', function (Blueprint $table) {
            $table->id('tour_village_id');
            $table->unsignedBigInteger('tour_id');
            $table->unsignedBigInteger('village_id');

            $table->foreign('tour_id')->references('tour_id')->on('tours');
            $table->foreign('village_id')->references('village_id')->on('villages');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tour_villages');
    }
};
