<?php

namespace Tests\Feature;

use App\Enums\VacancyType;
use App\Models\Vacancy;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Collection;
use Tests\TestCase;

class VacancyFilterTest extends TestCase
{
    use RefreshDatabase;

    private function filteredIds(array $filters): Collection
    {
        return $this->get(route('vacancies.index', $filters))
            ->assertOk()
            ->viewData('vacancies')
            ->pluck('id');
    }

    public function test_filters_by_title_ignoring_case(): void
    {
        $backend = Vacancy::factory()->create(['title' => 'Desenvolvedor Backend PHP']);
        Vacancy::factory()->create(['title' => 'Analista de Dados']);

        $this->assertEquals([$backend->id], $this->filteredIds(['title' => 'BACKEND'])->all());
    }

    public function test_filters_by_description(): void
    {
        $laravel = Vacancy::factory()->create(['description' => 'Experiência com Laravel e filas.']);
        Vacancy::factory()->create(['description' => 'Experiência com React.']);

        $this->assertEquals([$laravel->id], $this->filteredIds(['description' => 'laravel'])->all());
    }

    public function test_filters_by_location(): void
    {
        $teresina = Vacancy::factory()->create(['location' => 'Teresina - PI']);
        Vacancy::factory()->create(['location' => 'Fortaleza - CE']);

        $this->assertEquals([$teresina->id], $this->filteredIds(['location' => 'teresina'])->all());
    }

    public function test_filters_by_type(): void
    {
        $pj = Vacancy::factory()->create(['type' => VacancyType::Pj]);
        Vacancy::factory()->create(['type' => VacancyType::Clt]);
        Vacancy::factory()->create(['type' => VacancyType::Freelancer]);

        $this->assertEquals([$pj->id], $this->filteredIds(['type' => 'pj'])->all());
    }

    public function test_filters_by_status(): void
    {
        $open = Vacancy::factory()->create();
        $paused = Vacancy::factory()->paused()->create();

        $this->assertEquals([$open->id], $this->filteredIds(['status' => 'open'])->all());
        $this->assertEquals([$paused->id], $this->filteredIds(['status' => 'paused'])->all());
    }

    public function test_filters_by_salary_range(): void
    {
        $low = Vacancy::factory()->create(['salary' => 2000]);
        $mid = Vacancy::factory()->create(['salary' => 5000]);
        $high = Vacancy::factory()->create(['salary' => 9000]);
        Vacancy::factory()->create(['salary' => null]);

        $this->assertEqualsCanonicalizing([$mid->id, $high->id], $this->filteredIds(['salary_min' => 3000])->all());
        $this->assertEqualsCanonicalizing([$low->id, $mid->id], $this->filteredIds(['salary_max' => 6000])->all());
        $this->assertEquals([$mid->id], $this->filteredIds(['salary_min' => 3000, 'salary_max' => 6000])->all());
    }

    public function test_combines_multiple_filters(): void
    {
        $match = Vacancy::factory()->paused()->create(['type' => VacancyType::Clt]);
        Vacancy::factory()->create(['type' => VacancyType::Clt]);
        Vacancy::factory()->paused()->create(['type' => VacancyType::Pj]);

        $this->assertEquals([$match->id], $this->filteredIds(['type' => 'clt', 'status' => 'paused'])->all());
    }

    public function test_invalid_filter_values_are_ignored(): void
    {
        Vacancy::factory(3)->create();

        $this->assertCount(3, $this->filteredIds([
            'type' => 'estagio',
            'status' => 'qualquer',
            'salary_min' => 'abc',
        ]));
    }

    public function test_preserves_filters_in_pagination_links(): void
    {
        Vacancy::factory(25)->create(['type' => VacancyType::Clt]);

        $this->get(route('vacancies.index', ['type' => 'clt']))
            ->assertSee('type=clt&amp;page=2', false);
    }
}
