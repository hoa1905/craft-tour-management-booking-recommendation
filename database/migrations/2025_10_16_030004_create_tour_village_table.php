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
        Schema::create('tour_village', function (Blueprint $table) {
            $table->unsignedBigInteger('tour_id');
            $table->unsignedBigInteger('village_id');

            // Khóa chính kép
            $table->primary(['tour_id', 'village_id']);

            // Ràng buộc khóa ngoại
            $table->foreign('tour_id')->references('tour_id')->on('tours');
            $table->foreign('village_id')->references('village_id')->on('villages');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tour_village');
    }
};
