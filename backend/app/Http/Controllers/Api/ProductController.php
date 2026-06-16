<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class ProductController extends Controller
{
    /** GET /api/products */
    public function index(Request $request): JsonResponse
    {
        $query = Product::with('region');

        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }

        if ($request->filled('region_id')) {
            $query->where('region_id', $request->integer('region_id'));
        }

        if ($request->filled('search')) {
            $q = $request->search;
            $query->where(function ($b) use ($q) {
                $b->whereRaw('name ILIKE ?', ["%{$q}%"])
                  ->orWhereRaw('description ILIKE ?', ["%{$q}%"])
                  ->orWhereRaw('story ILIKE ?', ["%{$q}%"]);
            });
        }

        $products = $query
            ->orderBy('name')
            ->paginate($request->integer('per_page', 20));

        return response()->json($products);
    }

    /** GET /api/products/{slug} */
    public function show(Product $product): JsonResponse
    {
        $product->load('region');

        // Related products from same region or category
        $related = Product::with('region')
            ->where('id', '!=', $product->id)
            ->where(function ($q) use ($product) {
                $q->where('region_id', $product->region_id)
                  ->orWhere('category', $product->category);
            })
            ->limit(4)
            ->get();

        return response()->json([
            'data'    => $product,
            'related' => $related,
        ]);
    }
}
