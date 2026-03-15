<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;

class StoreSubjectRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'subject_code' => ['required', 'string', 'max:30', 'unique:subjects,subject_code'],
            'subject_name' => ['required', 'string', 'max:255'],
            'program_id' => ['required', 'integer', 'exists:programs,id'],
            'units' => ['required', 'numeric', 'min:1', 'max:6'],
            'semester' => ['required', 'string', 'in:First,Second,Summer'],
        ];
    }
}
