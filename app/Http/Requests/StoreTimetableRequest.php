<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreTimetableRequest extends FormRequest
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
            'section_id'  => ['required', Rule::exists('sections', 'id')->where('school_id', session('active_school'))],
            'subject_id'  => ['required', Rule::exists('subjects', 'id')->where('school_id', session('active_school'))],
            'teacher_id'  => ['required', Rule::exists('users', 'id')->where('school_id', session('active_school'))],
            'day_of_week' => ['required', 'in:Monday,Tuesday,Wednesday,Thursday,Friday'],
            'start_time'  => ['required', 'date_format:H:i'],
            'end_time'    => ['required', 'date_format:H:i', 'after:start_time'],
        ];
    }
}
