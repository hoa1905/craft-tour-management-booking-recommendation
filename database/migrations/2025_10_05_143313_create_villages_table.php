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
        Schema::create('villages', function (Blueprint $table) {
            $table->id('village_id'); // BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY
            $table->string('village_name');
            $table->enum('region', ['Tour Miền Bắc', 'Tour Miền Trung', 'Tour Miền Nam']);
            $table->string('province', 100);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('villages');
    }
};
