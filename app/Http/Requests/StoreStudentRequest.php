<?php

namespace App\Http\Requests;

use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

class StoreStudentRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            // Student Bio
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'email' => ['nullable', 'email', 'unique:users,email'], // Optional for younger students
            'dob' => ['required', 'date', 'before:today'],
            'gender' => ['required', 'in:Male,Female'],
            'address' => ['nullable', 'string', 'max:500'],

            // Academic Info
            'admission_number' => [
                'required',
                'string',
                'max:50',
                Rule::unique('student_profiles', 'admission_number')
                    ->where('school_id', session('active_school')),
            ],
            'class_level_id' => ['required', Rule::exists('class_levels', 'id')->where('school_id', session('active_school'))],
            'section_id' => ['required', Rule::exists('sections', 'id')->where('school_id', session('active_school'))],

            // Parent Info (We check parent email to see if they already exist)
            'parent_email' => ['required', 'email', 'max:255'],
            'alt_phone' => ['required', 'string', 'max:20'],
            'parent_name' => ['required', 'string', 'max:255'],
            'relationship' => ['required', 'in:Father,Mother,Guardian'],
        ];
    }
}
