<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateParentRequest extends FormRequest
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
        $parentId = $this->route('parent')->id;

        $parentId = $parentId instanceof \App\Models\User
        ? $parentId->id
        : $this->route()->parameter('parent')->id ?? null;

        return [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $parentId,
            'alt_phone' => 'nullable|string|max:20',
            'occupation' => 'nullable|string|max:255',
            'address' => 'nullable|string|max:500',
        ];
    }
}
