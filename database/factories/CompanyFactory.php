<?php

namespace Database\Factories;

use App\Models\Company;
use Illuminate\Database\Eloquent\Factories\Factory;

class CompanyFactory extends Factory
{
    protected $model = Company::class;

    public function definition()
    {
        return [
            'user_id' => \App\Models\User::factory(),  // Usamos un factory para generar un usuario también
            'company_name' => $this->faker->company(),
            'slang' => $this->faker->companySuffix(),
            'company_phone' => $this->faker->phoneNumber(),
            'fiscal_id' => $this->faker->unique()->numerify('##########'), // Genera un número único
            'description' => $this->faker->sentence(),
            'profile_picture' => $this->faker->imageUrl(),
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}
