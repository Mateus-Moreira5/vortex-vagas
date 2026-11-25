<?php

namespace Tests\Feature;

use App\Models\Candidate;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Collection;
use Tests\TestCase;

class CandidateFilterTest extends TestCase
{
    use RefreshDatabase;

    private function filteredIds(array $filters): Collection
    {
        return $this->get(route('candidates.index', $filters))
            ->assertOk()
            ->viewData('candidates')
            ->pluck('id');
    }

    public function test_filters_by_name_ignoring_case(): void
    {
        $maria = Candidate::factory()->create(['name' => 'Maria Silva']);
        Candidate::factory()->create(['name' => 'João Souza']);

        $this->assertEquals([$maria->id], $this->filteredIds(['name' => 'MARIA'])->all());
    }

    public function test_filters_by_email(): void
    {
        $maria = Candidate::factory()->create(['email' => 'maria@empresa.com']);
        Candidate::factory()->create(['email' => 'joao@outra.com']);

        $this->assertEquals([$maria->id], $this->filteredIds(['email' => 'empresa'])->all());
    }

    public function test_filters_by_phone(): void
    {
        $maria = Candidate::factory()->create(['phone' => '(86) 99999-1234']);
        Candidate::factory()->create(['phone' => '(85) 98888-5678']);

        $this->assertEquals([$maria->id], $this->filteredIds(['phone' => '(86)'])->all());
    }

    public function test_filters_by_birth_date_range(): void
    {
        $older = Candidate::factory()->create(['birth_date' => '1985-03-01']);
        $middle = Candidate::factory()->create(['birth_date' => '1995-06-15']);
        $younger = Candidate::factory()->create(['birth_date' => '2003-11-20']);

        $this->assertEqualsCanonicalizing([$middle->id, $younger->id], $this->filteredIds(['birth_date_from' => '1990-01-01'])->all());
        $this->assertEqualsCanonicalizing([$older->id, $middle->id], $this->filteredIds(['birth_date_to' => '2000-01-01'])->all());
        $this->assertEquals([$middle->id], $this->filteredIds(['birth_date_from' => '1990-01-01', 'birth_date_to' => '2000-01-01'])->all());
    }

    public function test_combines_multiple_filters(): void
    {
        $match = Candidate::factory()->create(['name' => 'Maria Silva', 'email' => 'maria@empresa.com']);
        Candidate::factory()->create(['name' => 'Maria Souza', 'email' => 'maria@outra.com']);

        $this->assertEquals([$match->id], $this->filteredIds(['name' => 'maria', 'email' => 'empresa'])->all());
    }

    public function test_invalid_filter_values_are_ignored(): void
    {
        Candidate::factory(3)->create();

        $this->assertCount(3, $this->filteredIds([
            'birth_date_from' => '31/12/1990',
            'birth_date_to' => 'amanhã',
        ]));
    }
}
