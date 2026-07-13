<?php

namespace App\Http\Controllers\Api;

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
        $this->middleware('auth:sanctum')->only(['store', 'update', 'destroy']);
        $this->authorizeResource(Product::class, 'product', ['except' => ['index', 'show']]);
    }

    /** GET /api/products */
    public function index(Request $request): JsonResponse
    {
        $products = $this->productService->listProducts($request->only(['category', 'region_id', 'search']), $request->integer('per_page', 20));

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

    /** POST /api/products */
    public function store(StoreProductRequest $request): JsonResponse
    {
        $product = $this->productService->createProduct($request->validated());

        return ApiResponse::success(new ProductResource($product), 'Product created successfully', [], [], 201);
    }

    /** GET /api/products/{slug} */
    public function show(Product $product): JsonResponse
    {
        $product = $this->productService->getProduct($product);
        $related = $this->productService->getRelatedProducts($product, 4);

        return ApiResponse::success([
            'data' => new ProductResource($product),
            'related' => ProductResource::collection($related),
        ], 'Product retrieved successfully');
    }

    /** PUT /api/products/{slug} */
    public function update(UpdateProductRequest $request, Product $product): JsonResponse
    {
        $product = $this->productService->updateProduct($product, $request->validated());

        return ApiResponse::success(new ProductResource($product), 'Product updated successfully');
    }

    /** DELETE /api/products/{slug} */
    public function destroy(Product $product): JsonResponse
    {
        $this->productService->deleteProduct($product);

        return ApiResponse::success(null, 'Product deleted successfully');
    }
}
