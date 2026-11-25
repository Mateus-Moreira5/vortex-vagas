<?php

namespace Tests\Feature;

use App\Models\Candidate;
use App\Models\Vacancy;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Collection;
use Tests\TestCase;

class ApplicationTest extends TestCase
{
    use RefreshDatabase;

    public function test_candidate_page_offers_only_open_vacancies_not_yet_applied(): void
    {
        $candidate = Candidate::factory()->create();
        $applied = Vacancy::factory()->create();
        $available = Vacancy::factory()->create();
        $paused = Vacancy::factory()->paused()->create();
        $candidate->vacancies()->attach($applied);

        $this->get(route('candidates.show', $candidate))
            ->assertOk()
            ->assertViewHas('availableVacancies', fn (Collection $vacancies) => $vacancies->modelKeys() === [$available->id]);
    }

    public function test_candidate_applies_to_vacancy(): void
    {
        $candidate = Candidate::factory()->create();
        $vacancy = Vacancy::factory()->create();

        $this->from(route('candidates.show', $candidate))
            ->post(route('candidates.applications.store', $candidate), ['vacancy_id' => $vacancy->id])
            ->assertRedirect(route('candidates.show', $candidate))
            ->assertSessionHas('success');

        $this->assertDatabaseHas('applications', [
            'candidate_id' => $candidate->id,
            'vacancy_id' => $vacancy->id,
        ]);
    }

    public function test_application_to_paused_vacancy_is_blocked(): void
    {
        $candidate = Candidate::factory()->create();
        $vacancy = Vacancy::factory()->paused()->create();

        $this->post(route('candidates.applications.store', $candidate), ['vacancy_id' => $vacancy->id])
            ->assertSessionHasErrors(['vacancy_id' => 'Esta vaga está pausada e não recebe novas candidaturas.']);

        $this->assertDatabaseCount('applications', 0);
    }

    public function test_duplicate_application_is_blocked(): void
    {
        $candidate = Candidate::factory()->create();
        $vacancy = Vacancy::factory()->create();
        $candidate->vacancies()->attach($vacancy);

        $this->post(route('candidates.applications.store', $candidate), ['vacancy_id' => $vacancy->id])
            ->assertSessionHasErrors(['vacancy_id' => 'O candidato já está inscrito nesta vaga.']);

        $this->assertDatabaseCount('applications', 1);
    }

    public function test_vacancy_is_required(): void
    {
        $candidate = Candidate::factory()->create();

        $this->post(route('candidates.applications.store', $candidate), [])
            ->assertSessionHasErrors(['vacancy_id' => 'Selecione uma vaga.']);
    }

    public function test_nonexistent_vacancy_is_rejected(): void
    {
        $candidate = Candidate::factory()->create();

        $this->post(route('candidates.applications.store', $candidate), ['vacancy_id' => 999])
            ->assertSessionHasErrors(['vacancy_id' => 'A vaga selecionada não existe.']);
    }

    public function test_application_is_cancelled(): void
    {
        $candidate = Candidate::factory()->create();
        $vacancy = Vacancy::factory()->create();
        $candidate->vacancies()->attach($vacancy);

        $this->delete(route('candidates.applications.destroy', [$candidate, $vacancy]))
            ->assertSessionHas('success');

        $this->assertDatabaseMissing('applications', [
            'candidate_id' => $candidate->id,
            'vacancy_id' => $vacancy->id,
        ]);
        $this->assertModelExists($candidate);
        $this->assertModelExists($vacancy);
    }

    public function test_paused_vacancy_page_shows_warning(): void
    {
        $vacancy = Vacancy::factory()->paused()->create();

        $this->get(route('vacancies.show', $vacancy))
            ->assertSee('Esta vaga está pausada e não recebe novas candidaturas.');
    }
}
