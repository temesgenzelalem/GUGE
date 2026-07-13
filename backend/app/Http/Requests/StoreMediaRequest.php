<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreMediaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        // Support both file upload and manual metadata registration
        if ($this->hasFile('file')) {
            return [
                'file' => ['required', 'file', 'max:204800', // 200MB
                    'mimes:jpg,jpeg,png,gif,webp,svg,mp4,mpeg,mov,webm,pdf,doc,docx,txt'],
                'collection' => ['nullable', 'string', 'max:80'],
                'gallery' => ['nullable', 'boolean'],
            ];
        }

        return [
            'uuid' => ['required', 'uuid', 'unique:media,uuid'],
            'filename' => ['required', 'string', 'max:255'],
            'path' => ['required', 'string', 'max:1024'],
            'mime_type' => ['nullable', 'string', 'max:120'],
            'size' => ['required', 'integer', 'min:0'],
            'metadata' => ['nullable', 'array'],
            'gallery' => ['nullable', 'boolean'],
            'collection' => ['nullable', 'string', 'max:80'],
            'uploaded_by' => ['nullable', 'integer', 'exists:users,id'],
        ];
    }

    public function messages(): array
    {
        return [
            'uuid.required' => 'Media UUID is required.',
            'uuid.unique' => 'Media UUID must be unique.',
            'filename.required' => 'Media filename is required.',
            'path.required' => 'Media path is required.',
            'file.mimes' => 'Unsupported file type. Allowed: jpg, png, gif, webp, svg, mp4, pdf, doc, docx, txt.',
            'file.max' => 'File must not exceed 200MB.',
        ];
    }
}
