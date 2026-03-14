<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;

class StudentsIndexRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'per_page' => ['nullable', 'integer', 'min:1', 'max:200'],
            'program_id' => ['nullable', 'integer', 'exists:programs,id'],
            'year_level' => ['nullable', 'integer', 'min:1', 'max:8'],
            'q' => ['nullable', 'string', 'max:120'],
        ];
    }
}
