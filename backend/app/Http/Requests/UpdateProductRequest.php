<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateProductRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $productId = $this->route('product')?->id;

        return [
            'name' => ['required', 'string', 'max:200', Rule::unique('products', 'name')->ignore($productId)],
            'slug' => ['nullable', 'string', 'max:220', Rule::unique('products', 'slug')->ignore($productId)],
            'region_id' => 'required|integer|exists:regions,id',
            'category' => 'required|string|max:120',
            'description' => 'required|string|max:1000',
            'story' => 'nullable|string|max:2000',
            'wiki_article' => 'required|string|max:500',
            'image_url' => 'nullable|url|max:2048',
            'tags' => 'nullable|array',
            'tags.*' => 'string|max:80',
            'how_to_order' => 'nullable|string|max:500',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Product name is required.',
            'category.required' => 'Product category is required.',
            'region_id.required' => 'Product must be assigned to a region.',
            'region_id.exists' => 'The selected region does not exist.',
            'image_url.url' => 'The product image must be a valid URL.',
        ];
    }
}
