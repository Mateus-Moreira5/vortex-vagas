<?php

namespace App\Http\Controllers;

use App\Exceptions\ApplicationException;
use App\Http\Requests\ApplicationRequest;
use App\Models\Candidate;
use App\Models\Vacancy;
use App\Services\ApplicationService;
use Illuminate\Http\RedirectResponse;

class ApplicationController extends Controller
{
    public function __construct(private readonly ApplicationService $applications) {}

    public function store(ApplicationRequest $request, Candidate $candidate): RedirectResponse
    {
        $vacancy = Vacancy::findOrFail($request->integer('vacancy_id'));

        try {
            $this->applications->apply($candidate, $vacancy);
        } catch (ApplicationException $exception) {
            return back()->withErrors(['vacancy_id' => $exception->getMessage()]);
        }

        return back()->with('success', "Candidatura na vaga \"{$vacancy->title}\" realizada.");
    }

    public function destroy(Candidate $candidate, Vacancy $vacancy): RedirectResponse
    {
        $this->applications->cancel($candidate, $vacancy);

        return back()->with('success', 'Candidatura cancelada.');
    }
}
