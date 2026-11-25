@extends('layouts.app')

@section('title', 'Nova vaga')

@section('content')
    <h1 class="h3 mb-4">Nova vaga</h1>

    <form method="POST" action="{{ route('vacancies.store') }}" class="card card-body">
        @csrf
        @include('vacancies._form')

        <div class="mt-4 d-flex gap-2">
            <button type="submit" class="btn btn-primary">Salvar</button>
            <a href="{{ route('vacancies.index') }}" class="btn btn-outline-secondary">Cancelar</a>
        </div>
    </form>
@endsection
