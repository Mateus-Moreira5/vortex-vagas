@extends('layouts.app')

@section('title', $candidate->name)

@section('content')
    <div class="d-flex justify-content-between align-items-start mb-4">
        <div>
            <h1 class="h3 mb-1">{{ $candidate->name }}</h1>
            <div class="text-muted">
                {{ $candidate->email }}
                · {{ $candidate->phone ?? 'Telefone não informado' }}
                · {{ $candidate->birth_date ? 'Nascimento: '.$candidate->birth_date->format('d/m/Y') : 'Nascimento não informado' }}
            </div>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('candidates.edit', $candidate) }}" class="btn btn-outline-primary">Editar</a>
            <form method="POST" action="{{ route('candidates.destroy', $candidate) }}"
                  onsubmit="return confirm('Excluir este candidato? As candidaturas dele também serão removidas.')">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-outline-danger">Excluir</button>
            </form>
        </div>
    </div>

    <div class="card card-body mb-4">
        <h2 class="h5">Inscrever em uma vaga</h2>
        @if ($availableVacancies->isEmpty())
            <p class="text-muted mb-0">Não há vagas abertas disponíveis para este candidato.</p>
        @else
            <form method="POST" action="{{ route('candidates.applications.store', $candidate) }}" class="row g-2 align-items-start">
                @csrf
                <div class="col-md-9">
                    <select name="vacancy_id" class="form-select @error('vacancy_id') is-invalid @enderror" required>
                        <option value="">Selecione uma vaga aberta</option>
                        @foreach ($availableVacancies as $available)
                            <option value="{{ $available->id }}" @selected((string) old('vacancy_id') === (string) $available->id)>
                                {{ $available->title }} · {{ $available->type->label() }}
                            </option>
                        @endforeach
                    </select>
                    @error('vacancy_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
                <div class="col-md-3 d-grid">
                    <button type="submit" class="btn btn-primary">Inscrever</button>
                </div>
            </form>
        @endif
    </div>

    <div class="card">
        <div class="card-header">Candidaturas ({{ $vacancies->count() }})</div>
        <ul class="list-group list-group-flush">
            @forelse ($vacancies as $vacancy)
                <li class="list-group-item d-flex justify-content-between align-items-center">
                    <span>
                        <a href="{{ route('vacancies.show', $vacancy) }}">{{ $vacancy->title }}</a>
                        <span class="text-muted">· {{ $vacancy->type->label() }}</span>
                        @include('vacancies._status')
                    </span>
                    <span class="d-flex align-items-center gap-3">
                        <small class="text-muted">{{ $vacancy->pivot->created_at->format('d/m/Y H:i') }}</small>
                        <form method="POST" action="{{ route('candidates.applications.destroy', [$candidate, $vacancy]) }}"
                              onsubmit="return confirm('Cancelar esta candidatura?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-outline-danger">Cancelar</button>
                        </form>
                    </span>
                </li>
            @empty
                <li class="list-group-item text-muted">Nenhuma candidatura.</li>
            @endforelse
        </ul>
    </div>
@endsection
