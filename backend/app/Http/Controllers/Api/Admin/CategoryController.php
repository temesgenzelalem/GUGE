<?php

namespace App\Http\Controllers\Api\Admin;

use App\Domain\Category\Contracts\CategoryServiceInterface;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreCategoryRequest;
use App\Http\Requests\UpdateCategoryRequest;
use App\Http\Resources\CategoryCollection;
use App\Http\Resources\CategoryResource;
use App\Models\Category;
use App\Support\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function __construct(protected CategoryServiceInterface $categoryService)
    {
        // index and show are public; writes require auth
        $this->middleware('auth:sanctum')->only(['store', 'update', 'destroy']);
    }

    public function index(Request $request): JsonResponse
    {
        $categories = $this->categoryService->listCategories(
            $request->only(['search']),
            $request->integer('per_page', 20)
        );

        return ApiResponse::success(new CategoryCollection($categories), 'Categories retrieved successfully', [
            'current_page' => $categories->currentPage(),
            'per_page' => $categories->perPage(),
            'total' => $categories->total(),
            'last_page' => $categories->lastPage(),
        ], [
            'next' => $categories->nextPageUrl(),
            'prev' => $categories->previousPageUrl(),
        ]);
    }

    public function store(StoreCategoryRequest $request): JsonResponse
    {
        $this->authorize('create', Category::class);

        $category = $this->categoryService->createCategory($request->validated());

        return ApiResponse::success(new CategoryResource($category), 'Category created successfully', [], [], 201);
    }

    public function show(Category $category): JsonResponse
    {
        $category = $this->categoryService->getCategory($category);

        return ApiResponse::success(new CategoryResource($category), 'Category retrieved successfully');
    }

    public function update(UpdateCategoryRequest $request, Category $category): JsonResponse
    {
        $this->authorize('update', $category);

        $category = $this->categoryService->updateCategory($category, $request->validated());

        return ApiResponse::success(new CategoryResource($category), 'Category updated successfully');
    }

    public function destroy(Category $category): JsonResponse
    {
        $this->authorize('delete', $category);

        $this->categoryService->deleteCategory($category);

        return ApiResponse::success(null, 'Category deleted successfully');
    }
}
