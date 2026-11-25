<?php

namespace App\Http\Requests;

use App\Enums\VacancyType;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class VacancyRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'is_paused' => $this->boolean('is_paused'),
        ]);
    }

    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string', 'max:5000'],
            'type' => ['required', Rule::enum(VacancyType::class)],
            'location' => ['nullable', 'string', 'max:255'],
            'salary' => ['nullable', 'numeric', 'min:0', 'max:99999999.99'],
            'is_paused' => ['boolean'],
        ];
    }

    public function attributes(): array
    {
        return [
            'title' => 'título',
            'description' => 'descrição',
            'type' => 'tipo',
            'location' => 'localização',
            'salary' => 'salário',
            'is_paused' => 'pausada',
        ];
    }
}
