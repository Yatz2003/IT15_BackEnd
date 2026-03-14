<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;

class EnrollmentsIndexRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'per_page' => ['nullable', 'integer', 'min:1', 'max:200'],
            'student_id' => ['nullable', 'integer', 'exists:students,id'],
            'subject_id' => ['nullable', 'integer', 'exists:subjects,id'],
            'program_id' => ['nullable', 'integer', 'exists:programs,id'],
        ];
    }
}
