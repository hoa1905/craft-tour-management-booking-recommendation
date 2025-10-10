<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Village>
 */
class VillageFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $regions = ['Tour Miền Bắc', 'Tour Miền Trung', 'Tour Miền Nam'];
        $provinces = ['Hà Nội', 'Hồ Chí Minh', 'Đà Nẵng', 'Hải Phòng', 'Cần Thơ', 'Nghệ An', 'Thừa Thiên Huế', 'Quảng Ninh', 'Bình Định', 'Phú Yên'];
        return [
            'village_name' => $this->faker->words(2, true),
            'region' => $this->faker->randomElement($regions),
            'province' => $this->faker->randomElement($provinces),
        ];
    }
}
