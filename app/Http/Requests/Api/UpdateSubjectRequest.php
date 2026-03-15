<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateSubjectRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $subjectId = (int) $this->route('id');

        return [
            'subject_code' => [
                'sometimes',
                'string',
                'max:30',
                Rule::unique('subjects', 'subject_code')->ignore($subjectId),
            ],
            'subject_name' => ['sometimes', 'required', 'string', 'max:255'],
            'program_id' => ['sometimes', 'integer', 'exists:programs,id'],
            'units' => ['sometimes', 'numeric', 'min:1', 'max:6'],
            'semester' => ['sometimes', 'required', 'string', 'in:First,Second,Summer'],
        ];
    }
}
