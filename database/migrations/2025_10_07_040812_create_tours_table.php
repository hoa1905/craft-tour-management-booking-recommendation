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
            $table->decimal('price_adult', 10, 2);
            $table->decimal('price_child', 10, 2);
            $table->string('duration', 20);
            $table->text('description');
            $table->string('departure_point', 100);
            $table->string('destination', 100);
            $table->string('transport', 50);
            $table->string('image_main', 255);
            $table->json('gallery')->nullable(); // Mảng chứa nhiều ảnh mô tả
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->timestamps();
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
