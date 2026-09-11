<?php

namespace App\Http\Requests;

use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

class StoreSectionRequest extends FormRequest
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
            'class_level_id' => [
                'required',
                Rule::exists('class_levels', 'id')->where('school_id', session('active_school')),
            ],
            'name' => [
                'required',
                'string',
                'max:50',
                // COMPOSITE UNIQUE CHECK scoped to school + class level.
                Rule::unique('sections')
                    ->where('class_level_id', $this->class_level_id)
                    ->where('school_id', session('active_school')),
            ],
            'capacity' => ['required', 'integer', 'min:1', 'max:500']
        ];
    }
}
