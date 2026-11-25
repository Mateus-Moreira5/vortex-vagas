<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ApplicationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'vacancy_id' => ['required', 'integer', 'exists:vacancies,id'],
        ];
    }

    public function messages(): array
    {
        return [
            'vacancy_id.required' => 'Selecione uma vaga.',
            'vacancy_id.integer' => 'A vaga selecionada é inválida.',
            'vacancy_id.exists' => 'A vaga selecionada não existe.',
        ];
    }
}
