<?php

namespace Database\Seeders;

use App\Models\Candidate;
use App\Models\User;
use App\Models\Vacancy;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        User::factory()->create([
            'name' => 'Admin',
            'email' => 'admin@vortex.test',
        ]);

        $openVacancies = Vacancy::factory(40)->create();
        Vacancy::factory(10)->paused()->create();

        Candidate::factory(80)->create()->each(function (Candidate $candidate) use ($openVacancies) {
            $candidate->vacancies()->attach(
                $openVacancies->random(fake()->numberBetween(0, 4))->pluck('id')
            );
        });
    }
}
