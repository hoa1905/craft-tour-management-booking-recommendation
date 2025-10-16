<?php

namespace Database\Factories;

use App\Models\Tour;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\TourSchedule>
 */
class TourScheduleFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $tour = Tour::inRandomOrder()->first() ?? Tour::factory()->create();

        // Chuyển duration từ "3N2Đ" => số ngày
        preg_match('/(\d+)N/', $tour->duration, $matches);
        $durationDays = isset($matches[1]) ? (int)$matches[1] : 1;

        $startDate = $this->faker->dateTimeBetween('+1 days', '+2 months');
        $endDate = (clone $startDate)->modify('+' . ($durationDays - 1) . ' days');

        return [
            'tour_id' => $tour->tour_id,
            'start_date' => $startDate,
            'end_date' => $endDate,
            'seats_available' => $this->faker->numberBetween(10, 30),
        ];
    }
}
