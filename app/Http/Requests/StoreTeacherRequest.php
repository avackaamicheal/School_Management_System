<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreTeacherRequest extends FormRequest
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

            'first_name'      => ['required', 'string', 'max:255'],
            'last_name'       => ['required', 'string', 'max:255'],
            'middle_name'     => ['nullable', 'string', 'max:255'],
            'email'           => ['required', 'email', 'unique:users,email'],
            'password'        => ['required', 'min:8'],
            'qualification'   => ['nullable', 'string', 'max:255'],
            'hire_date'       => ['nullable', 'date'],
            'phone'           => ['nullable', 'string', 'max:20'],
            'date_of_birth'   => ['nullable', 'date', 'before:today'],
            'gender'          => ['nullable', 'in:Male,Female,Other'],
            'marital_status'  => ['nullable', 'in:Single,Married,Divorced,Widowed'],
            'address'         => ['nullable', 'string', 'max:500'],
        ];
    }

    public function messages(): array
    {
        return [
            'first_name.required'    => 'First name is required.',
            'last_name.required'     => 'Last name is required.',
            'email.required'         => 'Email address is required.',
            'email.unique'           => 'This email is already registered.',
            'password.required'      => 'A temporary password is required.',
            'password.min'           => 'Password must be at least 8 characters.',
            'date_of_birth.before'   => 'Date of birth must be a past date.',
        ];
    }
}
