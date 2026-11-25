@extends('layouts.app')

@section('title', 'Candidatos')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0">Candidatos</h1>
        <a href="{{ route('candidates.create') }}" class="btn btn-primary">Novo candidato</a>
    </div>

    <form method="GET" action="{{ route('candidates.index') }}" class="card card-body mb-4">
        <div class="row g-3">
            <div class="col-md-4">
                <label for="f-name" class="form-label">Nome</label>
                <input type="text" id="f-name" name="name" value="{{ request('name') }}" class="form-control">
            </div>
            <div class="col-md-4">
                <label for="f-email" class="form-label">E-mail</label>
                <input type="text" id="f-email" name="email" value="{{ request('email') }}" class="form-control">
            </div>
            <div class="col-md-4">
                <label for="f-phone" class="form-label">Telefone</label>
                <input type="text" id="f-phone" name="phone" value="{{ request('phone') }}" class="form-control">
            </div>
            <div class="col-md-3">
                <label for="f-birth-from" class="form-label">Nascimento a partir de</label>
                <input type="date" id="f-birth-from" name="birth_date_from" value="{{ request('birth_date_from') }}" class="form-control">
            </div>
            <div class="col-md-3">
                <label for="f-birth-to" class="form-label">Nascimento até</label>
                <input type="date" id="f-birth-to" name="birth_date_to" value="{{ request('birth_date_to') }}" class="form-control">
            </div>
            <div class="col-md-2">
                <label for="f-per-page" class="form-label">Por página</label>
                <select id="f-per-page" name="per_page" class="form-select">
                    @foreach ($perPageOptions as $option)
                        <option value="{{ $option }}" @selected($candidates->perPage() === $option)>{{ $option }}</option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="mt-3 d-flex gap-2">
            <button type="submit" class="btn btn-secondary">Filtrar</button>
            <a href="{{ route('candidates.index') }}" class="btn btn-outline-secondary">Limpar</a>
        </div>
    </form>

    <div class="card">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Nome</th>
                        <th>E-mail</th>
                        <th>Telefone</th>
                        <th>Nascimento</th>
                        <th class="text-center">Candidaturas</th>
                        <th class="text-end">Ações</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($candidates as $candidate)
                        <tr>
                            <td><a href="{{ route('candidates.show', $candidate) }}">{{ $candidate->name }}</a></td>
                            <td>{{ $candidate->email }}</td>
                            <td>{{ $candidate->phone ?? '—' }}</td>
                            <td>{{ $candidate->birth_date?->format('d/m/Y') ?? '—' }}</td>
                            <td class="text-center">{{ $candidate->vacancies_count }}</td>
                            <td class="text-end text-nowrap">
                                <a href="{{ route('candidates.edit', $candidate) }}" class="btn btn-sm btn-outline-primary">Editar</a>
                                <form method="POST" action="{{ route('candidates.destroy', $candidate) }}" class="d-inline"
                                      onsubmit="return confirm('Excluir este candidato? As candidaturas dele também serão removidas.')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger">Excluir</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center text-muted py-4">Nenhum candidato encontrado.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-3">
        {{ $candidates->links() }}
    </div>
@endsection
