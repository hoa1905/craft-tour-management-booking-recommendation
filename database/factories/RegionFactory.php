<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Region>
 */
class RegionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $regions = ['Miền Bắc', 'Miền Trung', 'Miền Nam'];

        return [
            'region_name' => $this->faker->randomElement($regions),
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}
