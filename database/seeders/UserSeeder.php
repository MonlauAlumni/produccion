<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::factory()->count(100)->create();

        User::create([
            'name' => 'Admin',
            'last_name_1' => 'Prueba',
            'last_name_2' => 'Laravel',
            'email' => 'admin@example.com',
            'google_id' => null,
            'password' => Hash::make('password123'),
            'training_area' => 'Informatica',
        ]);
    }
}
