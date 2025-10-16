<?php

namespace Database\Factories;

use App\Models\Tour;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\TourItinerary>
 */
class TourItineraryFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'tour_id' => Tour::inRandomOrder()->first()?->tour_id ?? Tour::factory(),
            'day_number' => $this->faker->numberBetween(1, 5),
            'activities' => $this->faker->paragraph(3),
            'meals' => $this->faker->randomElement(['Sáng', 'Trưa', 'Tối', 'Sáng + Trưa + Tối']),
            'accommodation' => $this->faker->randomElement(['Khách sạn 3 sao', 'Homestay', 'Resort']),
        ];
    }
}
