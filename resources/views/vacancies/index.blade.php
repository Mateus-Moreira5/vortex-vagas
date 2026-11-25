@extends('layouts.app')

@section('title', 'Vagas')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0">Vagas</h1>
        <a href="{{ route('vacancies.create') }}" class="btn btn-primary">Nova vaga</a>
    </div>

    <form method="GET" action="{{ route('vacancies.index') }}" class="card card-body mb-4">
        <div class="row g-3">
            <div class="col-md-4">
                <label for="f-title" class="form-label">Título</label>
                <input type="text" id="f-title" name="title" value="{{ request('title') }}" class="form-control">
            </div>
            <div class="col-md-4">
                <label for="f-description" class="form-label">Descrição</label>
                <input type="text" id="f-description" name="description" value="{{ request('description') }}" class="form-control">
            </div>
            <div class="col-md-4">
                <label for="f-location" class="form-label">Localização</label>
                <input type="text" id="f-location" name="location" value="{{ request('location') }}" class="form-control">
            </div>
            <div class="col-md-3">
                <label for="f-type" class="form-label">Tipo</label>
                <select id="f-type" name="type" class="form-select">
                    <option value="">Todos</option>
                    @foreach ($types as $type)
                        <option value="{{ $type->value }}" @selected(request('type') === $type->value)>{{ $type->label() }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <label for="f-status" class="form-label">Status</label>
                <select id="f-status" name="status" class="form-select">
                    <option value="">Todos</option>
                    <option value="open" @selected(request('status') === 'open')>Aberta</option>
                    <option value="paused" @selected(request('status') === 'paused')>Pausada</option>
                </select>
            </div>
            <div class="col-md-2">
                <label for="f-salary-min" class="form-label">Salário mín.</label>
                <input type="number" id="f-salary-min" name="salary_min" value="{{ request('salary_min') }}" class="form-control" min="0" step="0.01">
            </div>
            <div class="col-md-2">
                <label for="f-salary-max" class="form-label">Salário máx.</label>
                <input type="number" id="f-salary-max" name="salary_max" value="{{ request('salary_max') }}" class="form-control" min="0" step="0.01">
            </div>
            <div class="col-md-2">
                <label for="f-per-page" class="form-label">Por página</label>
                <select id="f-per-page" name="per_page" class="form-select">
                    @foreach ($perPageOptions as $option)
                        <option value="{{ $option }}" @selected($vacancies->perPage() === $option)>{{ $option }}</option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="mt-3 d-flex gap-2">
            <button type="submit" class="btn btn-secondary">Filtrar</button>
            <a href="{{ route('vacancies.index') }}" class="btn btn-outline-secondary">Limpar</a>
        </div>
    </form>

    <div class="card">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Título</th>
                        <th>Tipo</th>
                        <th>Localização</th>
                        <th>Salário</th>
                        <th>Status</th>
                        <th class="text-center">Candidatos</th>
                        <th class="text-end">Ações</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($vacancies as $vacancy)
                        <tr>
                            <td><a href="{{ route('vacancies.show', $vacancy) }}">{{ $vacancy->title }}</a></td>
                            <td>{{ $vacancy->type->label() }}</td>
                            <td>{{ $vacancy->location ?? '—' }}</td>
                            <td>{{ $vacancy->formatted_salary }}</td>
                            <td>@include('vacancies._status')</td>
                            <td class="text-center">{{ $vacancy->candidates_count }}</td>
                            <td class="text-end text-nowrap">
                                <a href="{{ route('vacancies.edit', $vacancy) }}" class="btn btn-sm btn-outline-primary">Editar</a>
                                <form method="POST" action="{{ route('vacancies.toggle-pause', $vacancy) }}" class="d-inline">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="btn btn-sm btn-outline-warning">
                                        {{ $vacancy->is_paused ? 'Reativar' : 'Pausar' }}
                                    </button>
                                </form>
                                <form method="POST" action="{{ route('vacancies.destroy', $vacancy) }}" class="d-inline"
                                      onsubmit="return confirm('Excluir esta vaga? As candidaturas dela também serão removidas.')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger">Excluir</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center text-muted py-4">Nenhuma vaga encontrada.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-3">
        {{ $vacancies->links() }}
    </div>
@endsection
