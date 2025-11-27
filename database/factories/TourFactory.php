<?php

namespace Database\Factories;

use App\Models\User;
use App\Models\Village;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Tour>
 */
class TourFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'tour_name' => 'Tour ' . $this->faker->city . ' khám phá làng nghề',
            'price_adult' => $this->faker->numberBetween(500000, 3000000),
            'price_child' => $this->faker->numberBetween(200000, 1500000),
            'duration' => $this->faker->randomElement(['1N', '2N1Đ', '3N2Đ', '4N3Đ']),
            'description' => $this->faker->paragraph(4),
            'departure_point' => $this->faker->city,
            'destination' => $this->faker->city,
            'transport' => $this->faker->randomElement(['Xe du lịch', 'Máy bay', 'Tàu hỏa']),
            'image_main' => $this->faker->imageUrl(800, 600, 'tour'),
            'image_1' => $this->faker->imageUrl(800, 600, 'village'),
            'image_2' => $this->faker->imageUrl(800, 600, 'travel'),
            'status' => $this->faker->randomElement(['pending', 'approved', 'rejected']),
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}
