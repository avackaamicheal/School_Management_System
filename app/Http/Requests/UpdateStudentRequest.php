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
            'dob' => ['required', 'date', 'before:today'],
            'gender' => ['required', 'in:Male,Female'],
            'address' => ['nullable', 'string', 'max:500'],
            'class_level_id' => ['required', Rule::exists('class_levels', 'id')->where('school_id', session('active_school'))],
            'section_id' => ['required', Rule::exists('sections', 'id')->where('school_id', session('active_school'))],
        ];
    }
}
