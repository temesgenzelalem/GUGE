<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $roles = implode(',', array_keys(config('permissions.roles', [])));
        $userId = $this->route('user')?->id;

        return [
            'name' => 'required|string|max:120',
            'email' => 'required|email|unique:users,email,'.$userId,
            'password' => 'nullable|string|min:8|confirmed',
            'role' => 'nullable|string|in:'.$roles,
        ];
    }

    public function messages(): array
    {
        return [
            'password.confirmed' => 'Password confirmation does not match.',
        ];
    }
}
