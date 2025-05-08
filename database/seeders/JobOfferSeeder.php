<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\JobOffer;
use Faker\Factory as Faker;
use Carbon\Carbon;

class JobOfferSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $faker = Faker::create();

        for ($i = 0; $i < 50; $i++) {
            JobOffer::create([
                'company_id' => 113,
                'title' => $faker->jobTitle,
                'description' => $faker->paragraph(3),
                'requirements' => $faker->sentence(10),
                'experience' => $faker->randomElement(['1-2 años', '3-5 años', 'Más de 5 años']),
                'responsibilities' => $faker->sentence(15),
                'benefits' => $faker->sentence(10),
                'min_experience' => $faker->randomElement([1, 2, 3, 4, 5]),
                'min_studies' => $faker->randomElement(['Bachillerato', 'Diplomado', 'Licenciatura', 'Máster', 'Doctorado']),
                'vacancies' => $faker->numberBetween(1, 5),
                'minimum_salary' => $faker->randomFloat(2, 15000, 30000),
                'maximum_salary' => $faker->randomFloat(2, 30001, 60000),
                'location' => $faker->city,
                'work_mode' => $faker->randomElement(['remoto', 'presencial', 'híbrido']),
                'category' => $faker->randomElement(['tecnología', 'marketing', 'ventas', 'automoción', 'finanzas']),
                'expires_at' => Carbon::now()->addDays($faker->numberBetween(10, 30)),
                'deadline' => Carbon::now()->addDays($faker->numberBetween(1, 10)),
                'status' => $faker->randomElement(['activo', 'cerrado', 'pendiente']),
                'file' => null, // O puedes agregar una ruta a un archivo si es necesario
            ]);
        }
    }
}
