<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Region;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class RegionController extends Controller
{
    /** GET /api/regions */
    public function index(Request $request): JsonResponse
    {
        $query = Region::query();

        if ($request->filled('direction')) {
            $query->where('direction', $request->direction);
        }

        if ($request->filled('search')) {
            $q = $request->search;
            $query->where(function ($builder) use ($q) {
                $builder->whereRaw('name ILIKE ?', ["%{$q}%"])
                        ->orWhereRaw('description ILIKE ?', ["%{$q}%"])
                        ->orWhereRaw('zone ILIKE ?', ["%{$q}%"]);
            });
        }

        $regions = $query
            ->orderBy('name')
            ->paginate($request->integer('per_page', 20));

        return response()->json($regions);
    }

    /** GET /api/regions/{slug} */
    public function show(Region $region): JsonResponse
    {
        return response()->json(['data' => $region]);
    }

    /** GET /api/regions/{slug}/products */
    public function products(Region $region): JsonResponse
    {
        $products = $region->products()
            ->with('region')
            ->orderBy('name')
            ->get();

        return response()->json(['data' => $products]);
    }

    /** GET /api/regions/{slug}/stories */
    public function stories(Region $region): JsonResponse
    {
        $stories = $region->stories()
            ->with(['region', 'creator'])
            ->orderBy('published_at', 'desc')
            ->get();

        return response()->json(['data' => $stories]);
    }
}
