<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateCreatorRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $creatorId = $this->route('creator')?->id;

        return [
            'full_name' => ['required_without:name', 'string', 'max:200', Rule::unique('creators', 'full_name')->ignore($creatorId)],
            'name' => ['sometimes', 'string', 'max:200', Rule::unique('creators', 'name')->ignore($creatorId)],
            'username' => ['nullable', 'string', 'max:100', Rule::unique('creators', 'username')->ignore($creatorId)],
            'slug' => ['nullable', 'string', 'max:220', Rule::unique('creators', 'slug')->ignore($creatorId)],
            'region_id' => ['nullable', 'integer', 'exists:regions,id'],
            'role' => ['nullable', 'string', 'max:120'],
            'bio' => ['nullable', 'string', 'max:2000'],
            'status' => ['nullable', 'in:published,draft,archived'],
            'specialties' => ['nullable', 'array'],
            'specialties.*' => ['string', 'max:100'],
            'languages' => ['nullable', 'array'],
            'languages.*' => ['string', 'max:80'],
            'social_links' => ['nullable', 'array'],
            'social_links.*' => ['nullable', 'url', 'max:2048'],
            'contact_email' => ['nullable', 'email', 'max:255'],
            'website_url' => ['nullable', 'url', 'max:2048'],
            'portfolio_url' => ['nullable', 'url', 'max:2048'],
            'wiki_article' => ['nullable', 'string', 'max:500'],
            'image_url' => ['nullable', 'url', 'max:2048'],
            'rating' => ['nullable', 'numeric', 'min:0', 'max:5'],
            'review_count' => ['nullable', 'integer', 'min:0'],
            'story_count' => ['nullable', 'integer', 'min:0'],
            'product_count' => ['nullable', 'integer', 'min:0'],
            'meta_title' => ['nullable', 'string', 'max:120'],
            'meta_description' => ['nullable', 'string', 'max:300'],
        ];
    }

    public function messages(): array
    {
        return [
            'full_name.required_without' => 'Creator full name is required when no name is provided.',
            'name.required' => 'Creator name is required.',
            'role.required' => 'Creator role is required.',
            'bio.required' => 'Creator biography is required.',
            'contact_email.email' => 'Please provide a valid email address.',
            'image_url.url' => 'The creator image must be a valid URL.',
        ];
    }
}
