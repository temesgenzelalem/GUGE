<?php

namespace App\Http\Controllers\Api\Admin;

use App\Domain\Product\Contracts\ProductServiceInterface;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreProductRequest;
use App\Http\Requests\UpdateProductRequest;
use App\Http\Resources\ProductCollection;
use App\Http\Resources\ProductResource;
use App\Models\Product;
use App\Support\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function __construct(protected ProductServiceInterface $productService)
    {
        $this->middleware('auth:sanctum');
    }

    public function index(Request $request): JsonResponse
    {
        $this->authorize('viewAny', Product::class);

        $products = $this->productService->listProducts(
            $request->only(['category', 'region_id', 'search', 'status', 'featured', 'hidden']),
            $request->integer('per_page', 20)
        );

        return ApiResponse::success(new ProductCollection($products), 'Products retrieved successfully', [
            'current_page' => $products->currentPage(),
            'per_page' => $products->perPage(),
            'total' => $products->total(),
            'last_page' => $products->lastPage(),
        ], [
            'next' => $products->nextPageUrl(),
            'prev' => $products->previousPageUrl(),
        ]);
    }

    public function store(StoreProductRequest $request): JsonResponse
    {
        $this->authorize('create', Product::class);

        $product = $this->productService->createProduct($request->validated());

        return ApiResponse::success(new ProductResource($product), 'Product created successfully', [], [], 201);
    }

    public function show(Product $product): JsonResponse
    {
        $this->authorize('view', $product);

        return ApiResponse::success(new ProductResource($product), 'Product retrieved successfully');
    }

    public function update(UpdateProductRequest $request, Product $product): JsonResponse
    {
        $this->authorize('update', $product);

        $product = $this->productService->updateProduct($product, $request->validated());

        return ApiResponse::success(new ProductResource($product), 'Product updated successfully');
    }

    public function destroy(Product $product): JsonResponse
    {
        $this->authorize('delete', $product);

        $this->productService->deleteProduct($product);

        return ApiResponse::success(null, 'Product deleted successfully');
    }
}
