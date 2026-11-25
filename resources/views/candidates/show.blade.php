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
                    <small class="text-muted">{{ $vacancy->pivot->created_at->format('d/m/Y H:i') }}</small>
                </li>
            @empty
                <li class="list-group-item text-muted">Nenhuma candidatura.</li>
            @endforelse
        </ul>
    </div>
@endsection
