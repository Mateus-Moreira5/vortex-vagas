<?php

namespace Tests\Unit;

use App\Models\Vacancy;
use Tests\TestCase;

class VacancyTest extends TestCase
{
    public function test_new_vacancy_starts_open(): void
    {
        $vacancy = new Vacancy;

        $this->assertFalse($vacancy->is_paused);
        $this->assertTrue($vacancy->isOpen());
    }

    public function test_paused_vacancy_is_not_open(): void
    {
        $vacancy = new Vacancy(['is_paused' => true]);

        $this->assertFalse($vacancy->isOpen());
    }

    public function test_formatted_salary_uses_brazilian_currency_format(): void
    {
        $vacancy = new Vacancy(['salary' => 5000]);

        $this->assertSame('R$ 5.000,00', $vacancy->formatted_salary);
    }

    public function test_formatted_salary_shows_a_combinar_when_empty(): void
    {
        $vacancy = new Vacancy(['salary' => null]);

        $this->assertSame('A combinar', $vacancy->formatted_salary);
    }
}
