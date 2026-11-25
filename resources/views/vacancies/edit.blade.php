@extends('layouts.app')

@section('title', 'Editar vaga')

@section('content')
    <h1 class="h3 mb-4">Editar vaga</h1>

    <form method="POST" action="{{ route('vacancies.update', $vacancy) }}" class="card card-body">
        @csrf
        @method('PUT')
        @include('vacancies._form')

        <div class="mt-4 d-flex gap-2">
            <button type="submit" class="btn btn-primary">Salvar alterações</button>
            <a href="{{ route('vacancies.show', $vacancy) }}" class="btn btn-outline-secondary">Cancelar</a>
        </div>
    </form>
@endsection
