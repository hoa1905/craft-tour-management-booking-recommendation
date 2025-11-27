<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
class UserFactory extends Factory
{
  /**
   * Define the model's default state.
   *
   * @return array<string, mixed>
   */
  public function definition(): array
  {
    $role = $this->faker->randomElement(['client', 'admin']);
    $status = 'approved';

    return [
      'user_name' => $this->faker->name(),
      'email' => $this->faker->unique()->safeEmail(),
      'password' => Hash::make('123456'), // mật khẩu mặc định
      'phone' => $this->faker->phoneNumber(),
      'role' => $role,
      'status' => $status,
    ];
  }
}
