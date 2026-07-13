<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateStoryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $storyId = $this->route('story')?->id;

        return [
            'title' => ['required', 'string', 'max:300', Rule::unique('stories', 'title')->ignore($storyId)],
            'slug' => ['nullable', 'string', 'max:320', Rule::unique('stories', 'slug')->ignore($storyId)],
            'region_id' => 'required|integer|exists:regions,id',
            'creator_id' => 'nullable|integer|exists:creators,id',
            'category' => 'required_without:type|string|max:120',
            'type' => ['nullable', 'string', Rule::in(['travel', 'product-origin', 'culture', 'festival', 'history', 'craft'])],
            'excerpt' => 'required|string|max:500',
            'content' => 'nullable|string',
            'body' => 'required|string',
            'wiki_article' => 'required|url|max:2048',
            'featured_image' => 'nullable|url|max:2048',
            'gallery' => 'nullable|array',
            'gallery.*' => 'nullable|url|max:2048',
            'status' => 'nullable|string|in:draft,published,archived',
            'featured' => 'nullable|boolean',
            'read_minutes' => 'nullable|integer|min:1|max:120',
            'language' => 'nullable|string|max:8',
            'seo_title' => 'nullable|string|max:120',
            'seo_description' => 'nullable|string|max:240',
            'published_at' => 'nullable|date_format:Y-m-d H:i:s',
            'tags' => 'nullable|array',
            'tags.*' => 'string|max:80',
            'product_ids' => 'nullable|array',
            'product_ids.*' => 'integer|exists:products,id',
        ];
    }

    public function messages(): array
    {
        return [
            'title.required' => 'Story title is required.',
            'type.required' => 'Story type is required.',
            'type.in' => 'Type must be one of: travel, product-origin, culture, festival, history, craft.',
            'region_id.required' => 'Story must be assigned to a region.',
            'region_id.exists' => 'The selected region does not exist.',
            'creator_id.exists' => 'The selected creator does not exist.',
            'image_url.url' => 'The story image must be a valid URL.',
        ];
    }
}
