<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Region;
use App\Models\Product;
use App\Models\Story;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class SearchController extends Controller
{
    /** GET /api/search?q=... */
    public function __invoke(Request $request): JsonResponse
    {
        $q = trim($request->string('q'));

        if (strlen($q) < 2) {
            return response()->json([
                'regions'  => [],
                'products' => [],
                'stories'  => [],
            ]);
        }

        $like = "%{$q}%";

        $regions = Region::whereRaw('name ILIKE ?', [$like])
            ->orWhereRaw('description ILIKE ?', [$like])
            ->orWhereRaw('zone ILIKE ?', [$like])
            ->limit(5)->get();

        $products = Product::with('region')
            ->whereRaw('name ILIKE ?', [$like])
            ->orWhereRaw('description ILIKE ?', [$like])
            ->limit(5)->get();

        $stories = Story::with('region')
            ->whereRaw('title ILIKE ?', [$like])
            ->orWhereRaw('excerpt ILIKE ?', [$like])
            ->limit(5)->get();

        return response()->json(compact('regions', 'products', 'stories'));
    }
}
