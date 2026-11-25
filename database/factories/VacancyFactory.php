<?php

namespace Database\Factories;

use App\Enums\VacancyType;
use App\Models\Vacancy;
use Illuminate\Database\Eloquent\Factories\Factory;

class VacancyFactory extends Factory
{
    protected $model = Vacancy::class;

    public function definition(): array
    {
        return [
            'title' => fake()->randomElement([
                'Desenvolvedor Backend PHP',
                'Desenvolvedor Frontend',
                'Desenvolvedor Full Stack',
                'Analista de Dados',
                'Analista de Suporte',
                'Engenheiro DevOps',
                'Designer UX/UI',
                'Analista de QA',
                'Product Owner',
                'Professor de Programação',
            ]).' '.fake()->randomElement(['Júnior', 'Pleno', 'Sênior']),
            'description' => fake()->paragraphs(3, true),
            'type' => fake()->randomElement(VacancyType::cases()),
            'location' => fake()->city().' - '.fake()->stateAbbr(),
            'salary' => fake()->optional(0.8)->randomFloat(2, 1500, 20000),
            'is_paused' => false,
        ];
    }

    public function paused(): static
    {
        return $this->state(fn () => ['is_paused' => true]);
    }
}
