<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;

class UserFactory extends Factory
{
    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->firstName(),
            'last_name_1' => $this->faker->lastName(),
            'last_name_2' => $this->faker->lastName(),
            'email' => $this->faker->unique()->safeEmail(),
            'google_id' => null,
            'password' => Hash::make('password123'), // Contraseña encriptada
            'training_area' => 'Informatica',
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}
