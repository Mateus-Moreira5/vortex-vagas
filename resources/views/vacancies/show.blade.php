@extends('layouts.app')

@section('title', $vacancy->title)

@section('content')
    <div class="d-flex justify-content-between align-items-start mb-4">
        <div>
            <h1 class="h3 mb-1">{{ $vacancy->title }}</h1>
            <div class="text-muted">
                {{ $vacancy->type->label() }} · {{ $vacancy->location ?? 'Local não informado' }} · {{ $vacancy->formatted_salary }}
                · @include('vacancies._status')
            </div>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('vacancies.edit', $vacancy) }}" class="btn btn-outline-primary">Editar</a>
            <form method="POST" action="{{ route('vacancies.toggle-pause', $vacancy) }}">
                @csrf
                @method('PATCH')
                <button type="submit" class="btn btn-outline-warning">{{ $vacancy->is_paused ? 'Reativar' : 'Pausar' }}</button>
            </form>
            <form method="POST" action="{{ route('vacancies.destroy', $vacancy) }}"
                  onsubmit="return confirm('Excluir esta vaga? As candidaturas dela também serão removidas.')">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-outline-danger">Excluir</button>
            </form>
        </div>
    </div>

    @if ($vacancy->is_paused)
        <div class="alert alert-warning">Esta vaga está pausada e não recebe novas candidaturas.</div>
    @endif

    <div class="card card-body mb-4">
        <h2 class="h5">Descrição</h2>
        <p class="mb-0" style="white-space: pre-line">{{ $vacancy->description }}</p>
    </div>

    <div class="card">
        <div class="card-header">Candidatos inscritos ({{ $candidates->count() }})</div>
        <ul class="list-group list-group-flush">
            @forelse ($candidates as $candidate)
                <li class="list-group-item d-flex justify-content-between align-items-center">
                    <span>
                        <a href="{{ route('candidates.show', $candidate) }}">{{ $candidate->name }}</a>
                        <span class="text-muted">· {{ $candidate->email }}</span>
                    </span>
                    <span class="d-flex align-items-center gap-3">
                        <small class="text-muted">{{ $candidate->pivot->created_at->format('d/m/Y H:i') }}</small>
                        <form method="POST" action="{{ route('candidates.applications.destroy', [$candidate, $vacancy]) }}"
                              onsubmit="return confirm('Remover a candidatura deste candidato?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-outline-danger">Remover</button>
                        </form>
                    </span>
                </li>
            @empty
                <li class="list-group-item text-muted">Nenhum candidato inscrito.</li>
            @endforelse
        </ul>
    </div>
@endsection
