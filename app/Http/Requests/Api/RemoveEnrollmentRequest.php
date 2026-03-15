<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;

class RemoveEnrollmentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'enrollment_id' => ['nullable', 'integer', 'exists:enrollments,id'],
            'student_id' => ['required_without:enrollment_id', 'integer', 'exists:students,id'],
            'subject_id' => ['required_without:enrollment_id', 'integer', 'exists:subjects,id'],
        ];
    }
}
