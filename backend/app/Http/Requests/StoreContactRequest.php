<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreContactRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:120'],
            'email' => ['required', 'email', 'max:180'],
            'topic' => ['required', 'string', 'max:200'],
            'message' => ['required', 'string', 'min:10', 'max:4000'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Your name is required.',
            'email.required' => 'Your email address is required.',
            'email.email' => 'Please provide a valid email address.',
            'topic.required' => 'Please provide a topic.',
            'message.required' => 'Please write your message.',
            'message.min' => 'Message must be at least 10 characters.',
        ];
    }
}
