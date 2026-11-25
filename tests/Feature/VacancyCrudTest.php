<?php

namespace Tests\Feature;

use App\Enums\VacancyType;
use App\Models\Candidate;
use App\Models\Vacancy;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Pagination\LengthAwarePaginator;
use Tests\TestCase;

class VacancyCrudTest extends TestCase
{
    use RefreshDatabase;

    private function validData(array $overrides = []): array
    {
        return array_merge([
            'title' => 'Desenvolvedor Backend PHP Pleno',
            'description' => 'Atuar no desenvolvimento de APIs em Laravel.',
            'type' => VacancyType::Clt->value,
            'location' => 'Teresina - PI',
            'salary' => '6500.00',
        ], $overrides);
    }

    public function test_index_lists_vacancies(): void
    {
        $vacancy = Vacancy::factory()->create();

        $this->get(route('vacancies.index'))
            ->assertOk()
            ->assertSee($vacancy->title);
    }

    public function test_index_paginates_twenty_items_per_page(): void
    {
        Vacancy::factory(25)->create();

        $this->get(route('vacancies.index'))
            ->assertViewHas('vacancies', fn (LengthAwarePaginator $page) => $page->count() === 20 && $page->total() === 25);

        $this->get(route('vacancies.index', ['page' => 2]))
            ->assertViewHas('vacancies', fn (LengthAwarePaginator $page) => $page->count() === 5);
    }

    public function test_index_accepts_allowed_per_page_values(): void
    {
        Vacancy::factory(60)->create();

        $this->get(route('vacancies.index', ['per_page' => 50]))
            ->assertViewHas('vacancies', fn (LengthAwarePaginator $page) => $page->perPage() === 50 && $page->count() === 50);
    }

    public function test_index_falls_back_to_twenty_on_invalid_per_page(): void
    {
        Vacancy::factory(30)->create();

        $this->get(route('vacancies.index', ['per_page' => 999]))
            ->assertViewHas('vacancies', fn (LengthAwarePaginator $page) => $page->perPage() === 20);
    }

    public function test_create_form_is_displayed(): void
    {
        $this->get(route('vacancies.create'))->assertOk();
    }

    public function test_vacancy_is_created_with_valid_data(): void
    {
        $response = $this->post(route('vacancies.store'), $this->validData());

        $vacancy = Vacancy::first();

        $response->assertRedirect(route('vacancies.show', $vacancy))
            ->assertSessionHas('success');

        $this->assertDatabaseHas('vacancies', [
            'title' => 'Desenvolvedor Backend PHP Pleno',
            'type' => 'clt',
            'is_paused' => false,
        ]);
    }

    public function test_vacancy_can_be_created_paused(): void
    {
        $this->post(route('vacancies.store'), $this->validData(['is_paused' => '1']));

        $this->assertTrue(Vacancy::first()->is_paused);
    }

    public function test_required_fields_are_validated(): void
    {
        $this->post(route('vacancies.store'), [])
            ->assertSessionHasErrors([
                'title' => 'O campo título é obrigatório.',
                'description',
                'type',
            ]);

        $this->assertDatabaseCount('vacancies', 0);
    }

    public function test_data_types_are_validated(): void
    {
        $this->post(route('vacancies.store'), $this->validData([
            'title' => str_repeat('a', 256),
            'type' => 'estagio',
            'salary' => 'muito dinheiro',
        ]))->assertSessionHasErrors(['title', 'type', 'salary']);

        $this->post(route('vacancies.store'), $this->validData(['salary' => -100]))
            ->assertSessionHasErrors(['salary']);

        $this->assertDatabaseCount('vacancies', 0);
    }

    public function test_show_displays_vacancy_and_its_candidates(): void
    {
        $vacancy = Vacancy::factory()->create();
        $candidate = Candidate::factory()->create();
        $vacancy->candidates()->attach($candidate);

        $this->get(route('vacancies.show', $vacancy))
            ->assertOk()
            ->assertSee($vacancy->title)
            ->assertSee($candidate->name);
    }

    public function test_edit_form_is_displayed(): void
    {
        $vacancy = Vacancy::factory()->create();

        $this->get(route('vacancies.edit', $vacancy))
            ->assertOk()
            ->assertSee($vacancy->title);
    }

    public function test_vacancy_is_updated_with_valid_data(): void
    {
        $vacancy = Vacancy::factory()->create();

        $this->put(route('vacancies.update', $vacancy), $this->validData(['title' => 'Título atualizado', 'type' => 'pj']))
            ->assertRedirect(route('vacancies.show', $vacancy));

        $vacancy->refresh();
        $this->assertSame('Título atualizado', $vacancy->title);
        $this->assertSame(VacancyType::Pj, $vacancy->type);
    }

    public function test_vacancy_is_not_updated_with_invalid_data(): void
    {
        $vacancy = Vacancy::factory()->create(['title' => 'Título original']);

        $this->put(route('vacancies.update', $vacancy), $this->validData(['title' => '']))
            ->assertSessionHasErrors(['title']);

        $this->assertSame('Título original', $vacancy->fresh()->title);
    }

    public function test_deleting_vacancy_removes_its_applications(): void
    {
        $vacancy = Vacancy::factory()->create();
        $candidate = Candidate::factory()->create();
        $vacancy->candidates()->attach($candidate);

        $this->delete(route('vacancies.destroy', $vacancy))
            ->assertSessionHas('success');

        $this->assertModelMissing($vacancy);
        $this->assertDatabaseMissing('applications', ['vacancy_id' => $vacancy->id]);
        $this->assertModelExists($candidate);
    }

    public function test_deleting_from_filtered_list_returns_to_same_list(): void
    {
        $vacancy = Vacancy::factory()->create();
        $listUrl = route('vacancies.index', ['type' => 'clt', 'page' => 2]);

        $this->from($listUrl)
            ->delete(route('vacancies.destroy', $vacancy))
            ->assertRedirect($listUrl);
    }

    public function test_deleting_from_detail_page_returns_to_list(): void
    {
        $vacancy = Vacancy::factory()->create();

        $this->from(route('vacancies.show', $vacancy))
            ->delete(route('vacancies.destroy', $vacancy))
            ->assertRedirect(route('vacancies.index'));
    }

    public function test_vacancy_can_be_paused_and_resumed(): void
    {
        $vacancy = Vacancy::factory()->create();

        $this->patch(route('vacancies.toggle-pause', $vacancy));
        $this->assertTrue($vacancy->fresh()->is_paused);

        $this->patch(route('vacancies.toggle-pause', $vacancy));
        $this->assertFalse($vacancy->fresh()->is_paused);
    }

    public function test_missing_vacancy_returns_not_found(): void
    {
        $this->get(route('vacancies.show', 999))->assertNotFound();
    }
}
