<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class BulkDestroyRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'ids' => ['required', 'array', 'min:1', 'max:100'],
            'ids.*' => ['integer', 'distinct'],
        ];
    }

    public function messages(): array
    {
        return [
            'ids.required' => 'Selecione ao menos um item para excluir.',
            'ids.min' => 'Selecione ao menos um item para excluir.',
            'ids.max' => 'Selecione no máximo 100 itens por vez.',
            'ids.*.integer' => 'Seleção inválida.',
        ];
    }
}
