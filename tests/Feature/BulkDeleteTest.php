<?php

namespace Tests\Feature;

use App\Models\Candidate;
use App\Models\Vacancy;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BulkDeleteTest extends TestCase
{
    use RefreshDatabase;

    public function test_selected_vacancies_are_deleted(): void
    {
        [$first, $second, $kept] = Vacancy::factory(3)->create();

        $this->delete(route('vacancies.bulk-destroy'), ['ids' => [$first->id, $second->id]])
            ->assertSessionHas('success', '2 vaga(s) excluída(s) com sucesso.');

        $this->assertModelMissing($first);
        $this->assertModelMissing($second);
        $this->assertModelExists($kept);
    }

    public function test_bulk_deleting_vacancies_removes_their_applications(): void
    {
        $vacancy = Vacancy::factory()->create();
        $candidate = Candidate::factory()->create();
        $vacancy->candidates()->attach($candidate);

        $this->delete(route('vacancies.bulk-destroy'), ['ids' => [$vacancy->id]]);

        $this->assertDatabaseCount('applications', 0);
        $this->assertModelExists($candidate);
    }

    public function test_selected_candidates_are_deleted(): void
    {
        [$first, $second, $kept] = Candidate::factory(3)->create();

        $this->delete(route('candidates.bulk-destroy'), ['ids' => [$first->id, $second->id]])
            ->assertSessionHas('success', '2 candidato(s) excluído(s) com sucesso.');

        $this->assertModelMissing($first);
        $this->assertModelMissing($second);
        $this->assertModelExists($kept);
    }

    public function test_bulk_delete_requires_a_selection(): void
    {
        Vacancy::factory()->create();

        $this->delete(route('vacancies.bulk-destroy'), [])
            ->assertSessionHasErrors(['ids' => 'Selecione ao menos um item para excluir.']);

        $this->assertDatabaseCount('vacancies', 1);
    }

    public function test_bulk_delete_rejects_invalid_ids(): void
    {
        Candidate::factory()->create();

        $this->delete(route('candidates.bulk-destroy'), ['ids' => ['abc']])
            ->assertSessionHasErrors(['ids.0']);

        $this->assertDatabaseCount('candidates', 1);
    }

    public function test_nonexistent_ids_are_ignored(): void
    {
        $vacancy = Vacancy::factory()->create();

        $this->delete(route('vacancies.bulk-destroy'), ['ids' => [$vacancy->id, 999]])
            ->assertSessionHas('success', '1 vaga(s) excluída(s) com sucesso.');
    }

    public function test_bulk_delete_returns_to_filtered_list(): void
    {
        $vacancy = Vacancy::factory()->create();
        $listUrl = route('vacancies.index', ['type' => 'clt', 'page' => 2]);

        $this->from($listUrl)
            ->delete(route('vacancies.bulk-destroy'), ['ids' => [$vacancy->id]])
            ->assertRedirect($listUrl);
    }

    public function test_listing_renders_selection_checkboxes(): void
    {
        $vacancy = Vacancy::factory()->create();

        $this->get(route('vacancies.index'))
            ->assertSee('name="ids[]" value="'.$vacancy->id.'"', false);
    }
}
