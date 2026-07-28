<?php

namespace App\Http\Requests;

use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateStudentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $studentId = $this->route('student');

        return [
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'email' => ['nullable', 'email', Rule::unique('users', 'email')->ignore($studentId)],
            'dob' => ['required', 'date'],
            'gender' => ['required', 'in:Male,Female'],
            'address' => ['nullable', 'string'],
            'class_level_id' => ['required', 'exists:class_levels,id'],
            'section_id' => ['required', 'exists:sections,id'],
        ];
    }
}
