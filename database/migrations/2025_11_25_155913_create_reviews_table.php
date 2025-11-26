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
        Schema::create('reviews', function (Blueprint $table) {
            $table->id('review_id');
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('tour_id');
            $table->tinyInteger('rating')->checkBetween(1, 5);
            $table->text('comment')->nullable();
            $table->json('images')->nullable(); // Mảng chứa nhiều ảnh mô tả
            $table->timestamps();

            // Một user chỉ được đánh giá một lần cho một tour
            // bảng reviews, không thể có 2 dòng có cùng user_id và tour_id.
            $table->unique(['user_id', 'tour_id'], 'unique_rating');

            $table->foreign('user_id')->references('user_id')->on('users');
            $table->foreign('tour_id')->references('tour_id')->on('tours');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reviews');
    }
};
