<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Tour;
use App\Models\TourSchedule;

class TourSeeder extends Seeder
{
  public function run(): void
  {
    $tours = Tour::factory(10)->create();

    foreach ($tours as $tour) {
      TourSchedule::factory(rand(3, 5))->create([
        'tour_id' => $tour->tour_id,
      ]);
    }
  }
}
