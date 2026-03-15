<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;

class UpdateEnrollmentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'student_id' => ['sometimes', 'integer', 'exists:students,id'],
            'subject_id' => ['sometimes', 'integer', 'exists:subjects,id'],
            'program_id' => ['sometimes', 'integer', 'exists:programs,id'],
            'academic_year' => ['sometimes', 'string', 'max:20'],
            'semester' => ['sometimes', 'string', 'in:First,Second,Summer'],
            'status' => ['sometimes', 'string', 'in:Enrolled,Completed,Dropped'],
            'enrolled_at' => ['sometimes', 'date'],
        ];
    }
}
