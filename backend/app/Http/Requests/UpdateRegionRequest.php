<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateRegionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $regionId = $this->route('region')?->id;

        return [
            'name' => ['required', 'string', 'max:120', Rule::unique('regions', 'name')->ignore($regionId)],
            'slug' => ['nullable', 'string', 'max:140', Rule::unique('regions', 'slug')->ignore($regionId)],
            'zone' => 'required|string|max:120',
            'direction' => 'required|string|in:north,south,east,west',
            'description' => 'required|string',
            'tagline' => 'required|string|max:220',
            'wiki_article' => 'required|string|max:220',
            'image_url' => 'nullable|url|max:2048',
            'tags' => 'nullable|array',
            'tags.*' => 'string|max:80',
            'stats' => 'nullable|array',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Region name is required.',
            'direction.in' => 'Direction must be one of north, south, east or west.',
            'wiki_article.required' => 'A Wikipedia article reference is required for the region.',
            'image_url.url' => 'The region image must be a valid URL.',
        ];
    }
}
