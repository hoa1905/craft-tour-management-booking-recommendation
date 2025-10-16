<?php

namespace Database\Factories;

use App\Models\Tour;
use App\Models\Village;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\TourVillage>
 */
class TourVillageFactory extends Factory
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
            'village_id' => Village::inRandomOrder()->first()?->village_id ?? Village::factory(),
        ];
    }
}
