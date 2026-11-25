<?php

namespace Tests\Feature;

use App\Exceptions\ApplicationException;
use App\Models\Candidate;
use App\Models\Vacancy;
use App\Services\ApplicationService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ApplicationServiceTest extends TestCase
{
    use RefreshDatabase;

    private ApplicationService $service;

    protected function setUp(): void
    {
        parent::setUp();

        $this->service = new ApplicationService;
    }

    public function test_candidate_applies_to_open_vacancy(): void
    {
        $candidate = Candidate::factory()->create();
        $vacancy = Vacancy::factory()->create();

        $this->service->apply($candidate, $vacancy);

        $this->assertTrue($candidate->hasAppliedTo($vacancy));
    }

    public function test_candidate_applies_to_multiple_vacancies(): void
    {
        $candidate = Candidate::factory()->create();
        $vacancies = Vacancy::factory(3)->create();

        foreach ($vacancies as $vacancy) {
            $this->service->apply($candidate, $vacancy);
        }

        $this->assertSame(3, $candidate->vacancies()->count());
    }

    public function test_paused_vacancy_blocks_application(): void
    {
        $candidate = Candidate::factory()->create();
        $vacancy = Vacancy::factory()->paused()->create();

        $this->expectException(ApplicationException::class);
        $this->expectExceptionMessage('Esta vaga está pausada e não recebe novas candidaturas.');

        try {
            $this->service->apply($candidate, $vacancy);
        } finally {
            $this->assertDatabaseCount('applications', 0);
        }
    }

    public function test_duplicate_application_is_rejected(): void
    {
        $candidate = Candidate::factory()->create();
        $vacancy = Vacancy::factory()->create();
        $this->service->apply($candidate, $vacancy);

        $this->expectException(ApplicationException::class);
        $this->expectExceptionMessage('O candidato já está inscrito nesta vaga.');

        try {
            $this->service->apply($candidate, $vacancy);
        } finally {
            $this->assertDatabaseCount('applications', 1);
        }
    }

    public function test_cancel_removes_only_that_application(): void
    {
        $candidate = Candidate::factory()->create();
        [$first, $second] = Vacancy::factory(2)->create();
        $this->service->apply($candidate, $first);
        $this->service->apply($candidate, $second);

        $this->service->cancel($candidate, $first);

        $this->assertFalse($candidate->hasAppliedTo($first));
        $this->assertTrue($candidate->hasAppliedTo($second));
    }

    public function test_existing_applications_remain_when_vacancy_is_paused(): void
    {
        $candidate = Candidate::factory()->create();
        $vacancy = Vacancy::factory()->create();
        $this->service->apply($candidate, $vacancy);

        $vacancy->togglePause();

        $this->assertTrue($candidate->hasAppliedTo($vacancy));
    }
}
