<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RegisterSchoolRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            // School details
            'school_name' => 'required|string|max:255',
            'school_email' => 'required|email|unique:schools,email',
            'school_phone' => 'required|string|max:20',
            'school_address' => 'required|string|max:500',
            'principal_name' => 'required|string|max:255',

            // Admin account
            'admin_name' => 'required|string|max:255',
            'admin_email' => 'required|email|unique:users,email',
            'admin_password' => 'required|min:8|confirmed',
        ];
    }

    public function messages(): array
    {
        return [
            'school_email.unique' => 'A school with this email already exists.',
            'admin_email.unique' => 'This email is already registered.',
            'admin_password.confirmed' => 'Passwords do not match.',
        ];
    }
}
