<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Doctor>
 */
class DoctorFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory()->create(['role' => 'doctor'])->id,
            'specialization' => $this->faker->randomElement(['Umum', 'Umum', 'Umum', 'THT', 'Gigi', 'Kulit']),
            'profile' => '',
        ];
    }
}
