<?php

namespace Tests\Feature;

use App\Models\Candidate;
use App\Models\Vacancy;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Pagination\LengthAwarePaginator;
use Tests\TestCase;

class CandidateCrudTest extends TestCase
{
    use RefreshDatabase;

    private function validData(array $overrides = []): array
    {
        return array_merge([
            'name' => 'Maria Silva',
            'email' => 'maria@example.com',
            'phone' => '(86) 99999-1234',
            'birth_date' => '1998-05-10',
        ], $overrides);
    }

    public function test_index_lists_candidates(): void
    {
        $candidate = Candidate::factory()->create();

        $this->get(route('candidates.index'))
            ->assertOk()
            ->assertSee($candidate->name);
    }

    public function test_index_paginates_twenty_items_per_page(): void
    {
        Candidate::factory(25)->create();

        $this->get(route('candidates.index'))
            ->assertViewHas('candidates', fn (LengthAwarePaginator $page) => $page->count() === 20 && $page->total() === 25);

        $this->get(route('candidates.index', ['page' => 2]))
            ->assertViewHas('candidates', fn (LengthAwarePaginator $page) => $page->count() === 5);
    }

    public function test_index_accepts_allowed_per_page_values(): void
    {
        Candidate::factory(15)->create();

        $this->get(route('candidates.index', ['per_page' => 10]))
            ->assertViewHas('candidates', fn (LengthAwarePaginator $page) => $page->perPage() === 10 && $page->count() === 10);
    }

    public function test_create_form_is_displayed(): void
    {
        $this->get(route('candidates.create'))->assertOk();
    }

    public function test_candidate_is_created_with_valid_data(): void
    {
        $response = $this->post(route('candidates.store'), $this->validData());

        $candidate = Candidate::first();

        $response->assertRedirect(route('candidates.show', $candidate))
            ->assertSessionHas('success');

        $this->assertDatabaseHas('candidates', [
            'name' => 'Maria Silva',
            'email' => 'maria@example.com',
        ]);
    }

    public function test_optional_fields_can_be_empty(): void
    {
        $this->post(route('candidates.store'), $this->validData(['phone' => '', 'birth_date' => '']))
            ->assertSessionHasNoErrors();

        $this->assertNull(Candidate::first()->phone);
        $this->assertNull(Candidate::first()->birth_date);
    }

    public function test_email_is_normalized_to_lowercase(): void
    {
        $this->post(route('candidates.store'), $this->validData(['email' => '  Maria@Example.COM ']));

        $this->assertSame('maria@example.com', Candidate::first()->email);
    }

    public function test_required_fields_are_validated(): void
    {
        $this->post(route('candidates.store'), [])
            ->assertSessionHasErrors([
                'name' => 'O campo nome é obrigatório.',
                'email',
            ]);

        $this->assertDatabaseCount('candidates', 0);
    }

    public function test_data_types_are_validated(): void
    {
        $this->post(route('candidates.store'), $this->validData([
            'name' => str_repeat('a', 256),
            'email' => 'email-invalido',
            'phone' => str_repeat('9', 21),
            'birth_date' => 'ontem',
        ]))->assertSessionHasErrors(['name', 'email', 'phone', 'birth_date']);

        $this->assertDatabaseCount('candidates', 0);
    }

    public function test_birth_date_must_be_in_the_past(): void
    {
        $this->post(route('candidates.store'), $this->validData(['birth_date' => now()->addDay()->format('Y-m-d')]))
            ->assertSessionHasErrors(['birth_date' => 'A data de nascimento deve ser anterior a hoje.']);
    }

    public function test_email_must_be_unique(): void
    {
        Candidate::factory()->create(['email' => 'maria@example.com']);

        $this->post(route('candidates.store'), $this->validData(['email' => 'MARIA@example.com']))
            ->assertSessionHasErrors(['email']);

        $this->assertDatabaseCount('candidates', 1);
    }

    public function test_show_displays_candidate_and_applications(): void
    {
        $candidate = Candidate::factory()->create();
        $vacancy = Vacancy::factory()->create();
        $candidate->vacancies()->attach($vacancy);

        $this->get(route('candidates.show', $candidate))
            ->assertOk()
            ->assertSee($candidate->name)
            ->assertSee($vacancy->title);
    }

    public function test_edit_form_is_displayed(): void
    {
        $candidate = Candidate::factory()->create();

        $this->get(route('candidates.edit', $candidate))
            ->assertOk()
            ->assertSee($candidate->email);
    }

    public function test_candidate_is_updated_with_valid_data(): void
    {
        $candidate = Candidate::factory()->create();

        $this->put(route('candidates.update', $candidate), $this->validData(['name' => 'Nome atualizado']))
            ->assertRedirect(route('candidates.show', $candidate));

        $this->assertSame('Nome atualizado', $candidate->fresh()->name);
    }

    public function test_candidate_can_keep_own_email_on_update(): void
    {
        $candidate = Candidate::factory()->create(['email' => 'maria@example.com']);

        $this->put(route('candidates.update', $candidate), $this->validData(['email' => 'maria@example.com']))
            ->assertSessionHasNoErrors();
    }

    public function test_candidate_cannot_use_another_candidates_email(): void
    {
        Candidate::factory()->create(['email' => 'outra@example.com']);
        $candidate = Candidate::factory()->create(['email' => 'maria@example.com']);

        $this->put(route('candidates.update', $candidate), $this->validData(['email' => 'outra@example.com']))
            ->assertSessionHasErrors(['email']);

        $this->assertSame('maria@example.com', $candidate->fresh()->email);
    }

    public function test_deleting_candidate_removes_applications_but_keeps_vacancies(): void
    {
        $candidate = Candidate::factory()->create();
        $vacancy = Vacancy::factory()->create();
        $candidate->vacancies()->attach($vacancy);

        $this->delete(route('candidates.destroy', $candidate))
            ->assertSessionHas('success');

        $this->assertModelMissing($candidate);
        $this->assertDatabaseMissing('applications', ['candidate_id' => $candidate->id]);
        $this->assertModelExists($vacancy);
    }

    public function test_deleting_from_filtered_list_returns_to_same_list(): void
    {
        $candidate = Candidate::factory()->create();
        $listUrl = route('candidates.index', ['name' => 'silva', 'page' => 2]);

        $this->from($listUrl)
            ->delete(route('candidates.destroy', $candidate))
            ->assertRedirect($listUrl);
    }

    public function test_missing_candidate_returns_not_found(): void
    {
        $this->get(route('candidates.show', 999))->assertNotFound();
    }
}
