<?php

namespace App\Http\Controllers;

use App\Filters\CandidateFilter;
use App\Http\Controllers\Concerns\HandlesPagination;
use App\Http\Controllers\Concerns\RedirectsToIndex;
use App\Http\Requests\CandidateRequest;
use App\Models\Candidate;
use App\Models\Vacancy;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CandidateController extends Controller
{
    use HandlesPagination, RedirectsToIndex;

    public function index(Request $request): View
    {
        $candidates = Candidate::query()
            ->filter(new CandidateFilter($request->query()))
            ->withCount('vacancies')
            ->latest('id')
            ->paginate($this->perPage($request))
            ->withQueryString();

        return view('candidates.index', [
            'candidates' => $candidates,
            'perPageOptions' => self::PER_PAGE_OPTIONS,
        ]);
    }

    public function create(): View
    {
        return view('candidates.create', ['candidate' => new Candidate]);
    }

    public function store(CandidateRequest $request): RedirectResponse
    {
        $candidate = Candidate::create($request->validated());

        return redirect()
            ->route('candidates.show', $candidate)
            ->with('success', 'Candidato cadastrado com sucesso.');
    }

    public function show(Candidate $candidate): View
    {
        $vacancies = $candidate->vacancies()
            ->orderByPivot('created_at', 'desc')
            ->get();

        $availableVacancies = Vacancy::open()
            ->whereNotIn('id', $vacancies->modelKeys())
            ->orderBy('title')
            ->get();

        return view('candidates.show', compact('candidate', 'vacancies', 'availableVacancies'));
    }

    public function edit(Candidate $candidate): View
    {
        return view('candidates.edit', compact('candidate'));
    }

    public function update(CandidateRequest $request, Candidate $candidate): RedirectResponse
    {
        $candidate->update($request->validated());

        return redirect()
            ->route('candidates.show', $candidate)
            ->with('success', 'Candidato atualizado com sucesso.');
    }

    public function destroy(Candidate $candidate): RedirectResponse
    {
        $candidate->delete();

        return $this->redirectToIndex('candidates.index')
            ->with('success', 'Candidato excluído com sucesso.');
    }
}
