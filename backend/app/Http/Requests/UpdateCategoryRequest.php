<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateCategoryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $categoryId = $this->route('category')?->id;

        return [
            'name' => 'required|string|max:120|unique:categories,name,'.$categoryId,
            'slug' => 'nullable|string|max:140|unique:categories,slug,'.$categoryId,
            'description' => 'nullable|string|max:500',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Category name is required.',
            'name.unique' => 'Category name must be unique.',
            'slug.unique' => 'Category slug must be unique.',
        ];
    }
}
