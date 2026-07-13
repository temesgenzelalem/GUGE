<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateMediaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'filename' => 'nullable|string|max:255',
            'path' => 'nullable|string|max:1024',
            'mime_type' => 'nullable|string|max:120',
            'size' => 'nullable|integer|min:0',
            'metadata' => 'nullable|array',
            'gallery' => 'nullable|boolean',
            'uploaded_by' => 'nullable|integer|exists:users,id',
        ];
    }

    public function messages(): array
    {
        return [
            'filename.required' => 'Media filename is required.',
            'path.required' => 'Media path is required.',
        ];
    }
}
