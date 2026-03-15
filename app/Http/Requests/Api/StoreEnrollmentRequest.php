<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;

class StoreEnrollmentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'student_id' => ['required', 'integer', 'exists:students,id'],
            'subject_id' => ['required', 'integer', 'exists:subjects,id'],
            'program_id' => ['required', 'integer', 'exists:programs,id'],
            'academic_year' => ['required', 'string', 'max:20'],
            'semester' => ['required', 'string', 'in:First,Second,Summer'],
            'status' => ['required', 'string', 'in:Enrolled,Completed,Dropped'],
            'enrolled_at' => ['nullable', 'date'],
        ];
    }
}
