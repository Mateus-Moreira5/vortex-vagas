@extends('layouts.app')

@section('title', 'Editar candidato')

@section('content')
    <h1 class="h3 mb-4">Editar candidato</h1>

    <form method="POST" action="{{ route('candidates.update', $candidate) }}" class="card card-body">
        @csrf
        @method('PUT')
        @include('candidates._form')

        <div class="mt-4 d-flex gap-2">
            <button type="submit" class="btn btn-primary">Salvar alterações</button>
            <a href="{{ route('candidates.show', $candidate) }}" class="btn btn-outline-secondary">Cancelar</a>
        </div>
    </form>
@endsection
