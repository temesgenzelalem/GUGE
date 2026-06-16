<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Story;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class StoryController extends Controller
{
    /** GET /api/stories */
    public function index(Request $request): JsonResponse
    {
        $query = Story::with(['region', 'creator']);

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        if ($request->filled('region_id')) {
            $query->where('region_id', $request->integer('region_id'));
        }

        if ($request->filled('search')) {
            $q = $request->search;
            $query->where(function ($b) use ($q) {
                $b->whereRaw('title ILIKE ?', ["%{$q}%"])
                  ->orWhereRaw('excerpt ILIKE ?', ["%{$q}%"]);
            });
        }

        $stories = $query
            ->orderBy('published_at', 'desc')
            ->paginate($request->integer('per_page', 12));

        return response()->json($stories);
    }

    /** GET /api/stories/{slug} */
    public function show(Story $story): JsonResponse
    {
        $story->load(['region', 'creator']);

        $related = Story::with(['region', 'creator'])
            ->where('id', '!=', $story->id)
            ->where('region_id', $story->region_id)
            ->limit(3)
            ->get();

        return response()->json([
            'data'    => $story,
            'related' => $related,
        ]);
    }
}
