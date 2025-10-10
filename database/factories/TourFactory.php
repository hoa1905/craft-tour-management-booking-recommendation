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
        $partner = User::where('role', 'partner')->inRandomOrder()->first();
        $village = Village::inRandomOrder()->first();

        return [
            'tour_name' => $this->faker->sentence(3),
            'village_id' => $village->village_id,
            'partner_id' => $partner->user_id,
            'price' => $this->faker->randomFloat(3, 500000, 5000000),
            'duration' => $this->faker->randomElement(['1 ngày', '2 ngày 1 đêm', '3 ngày 2 đêm']),
            'description' => $this->faker->paragraph(4),
            'start_date' => $this->faker->dateTimeBetween('2025-11-01', '2025-12-31')->format('Y-m-d'),
            'end_date' => $this->faker->dateTimeBetween('2026-01-01', '2026-02-01')->format('Y-m-d'),
            'departure_point' => $this->faker->randomElement([
                'Hà Nội',
                'Đà Nẵng',
                'TP. Hồ Chí Minh',
                'Huế',
                'Cần Thơ'
            ]),
            'destination' => $this->faker->randomElement([
                'Sa Pa',
                'Hội An',
                'Phú Quốc',
                'Nha Trang',
                'Đà Lạt'
            ]),
            'transport' => $this->faker->randomElement([
                'Máy bay',
                'Xe du lịch',
            ]),
            'image_url' => $this->faker->randomElement(['tour1.jpg', 'tour2.png', 'tour3.jpeg']),
            'status' => $this->faker->randomElement(['pending', 'approved', 'rejected']),
        ];
    }
}
