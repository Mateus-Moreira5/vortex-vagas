<?php

namespace App\Http\Controllers;

use App\Enums\VacancyType;
use App\Filters\VacancyFilter;
use App\Http\Controllers\Concerns\HandlesPagination;
use App\Http\Requests\VacancyRequest;
use App\Models\Vacancy;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class VacancyController extends Controller
{
    use HandlesPagination;

    public function index(Request $request): View
    {
        $vacancies = Vacancy::query()
            ->filter(new VacancyFilter($request->query()))
            ->withCount('candidates')
            ->latest('id')
            ->paginate($this->perPage($request))
            ->withQueryString();

        return view('vacancies.index', [
            'vacancies' => $vacancies,
            'types' => VacancyType::cases(),
            'perPageOptions' => self::PER_PAGE_OPTIONS,
        ]);
    }

    public function create(): View
    {
        return view('vacancies.create', [
            'vacancy' => new Vacancy,
            'types' => VacancyType::cases(),
        ]);
    }

    public function store(VacancyRequest $request): RedirectResponse
    {
        $vacancy = Vacancy::create($request->validated());

        return redirect()
            ->route('vacancies.show', $vacancy)
            ->with('success', 'Vaga criada com sucesso.');
    }

    public function show(Vacancy $vacancy): View
    {
        $candidates = $vacancy->candidates()
            ->orderByPivot('created_at', 'desc')
            ->get();

        return view('vacancies.show', compact('vacancy', 'candidates'));
    }

    public function edit(Vacancy $vacancy): View
    {
        return view('vacancies.edit', [
            'vacancy' => $vacancy,
            'types' => VacancyType::cases(),
        ]);
    }

    public function update(VacancyRequest $request, Vacancy $vacancy): RedirectResponse
    {
        $vacancy->update($request->validated());

        return redirect()
            ->route('vacancies.show', $vacancy)
            ->with('success', 'Vaga atualizada com sucesso.');
    }

    public function destroy(Vacancy $vacancy): RedirectResponse
    {
        $vacancy->delete();

        $previous = url()->previous();
        $index = route('vacancies.index');

        return redirect()
            ->to(Str::before($previous, '?') === $index ? $previous : $index)
            ->with('success', 'Vaga excluída com sucesso.');
    }

    public function togglePause(Vacancy $vacancy): RedirectResponse
    {
        $vacancy->togglePause();

        return back()->with(
            'success',
            $vacancy->is_paused ? 'Vaga pausada. Ela não recebe novas candidaturas.' : 'Vaga reativada.'
        );
    }
}
