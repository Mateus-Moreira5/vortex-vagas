@extends('layouts.app')

@section('title', 'Novo candidato')

@section('content')
    <h1 class="h3 mb-4">Novo candidato</h1>

    <form method="POST" action="{{ route('candidates.store') }}" class="card card-body">
        @csrf
        @include('candidates._form')

        <div class="mt-4 d-flex gap-2">
            <button type="submit" class="btn btn-primary">Salvar</button>
            <a href="{{ route('candidates.index') }}" class="btn btn-outline-secondary">Cancelar</a>
        </div>
    </form>
@endsection
