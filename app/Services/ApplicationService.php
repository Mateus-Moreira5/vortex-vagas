<?php

namespace App\Services;

use App\Exceptions\ApplicationException;
use App\Models\Candidate;
use App\Models\Vacancy;
use Illuminate\Database\UniqueConstraintViolationException;

class ApplicationService
{
    public function apply(Candidate $candidate, Vacancy $vacancy): void
    {
        if (! $vacancy->isOpen()) {
            throw ApplicationException::vacancyPaused();
        }

        if ($candidate->hasAppliedTo($vacancy)) {
            throw ApplicationException::alreadyApplied();
        }

        try {
            $candidate->vacancies()->attach($vacancy);
        } catch (UniqueConstraintViolationException) {
            throw ApplicationException::alreadyApplied();
        }
    }

    public function cancel(Candidate $candidate, Vacancy $vacancy): void
    {
        $candidate->vacancies()->detach($vacancy);
    }
}
