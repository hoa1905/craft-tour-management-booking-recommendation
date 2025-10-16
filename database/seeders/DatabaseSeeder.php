<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

use App\Models\Province;
use App\Models\Region;
use App\Models\Tour;
use App\Models\TourItinerary;
use App\Models\TourVillage;
use App\Models\User;
use Illuminate\Database\Seeder;
use App\Models\Village;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        Province::truncate();
        Region::truncate();
        Tour::truncate();
        TourItinerary::truncate();
        TourVillage::truncate();
        User::truncate();
        Village::truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        Province::factory(34)->create();
        Tour::factory(10)->create();
        Region::factory(3)->create();
        TourItinerary::factory(30)->create();
        TourVillage::factory(10)->create();
        User::factory(10)->create();
        Village::factory(20)->create();

        // \App\Models\User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);
    }
}
