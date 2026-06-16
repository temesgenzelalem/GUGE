<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Creator;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class CreatorController extends Controller
{
    /** GET /api/creators */
    public function index(Request $request): JsonResponse
    {
        $creators = Creator::orderBy('name')
            ->paginate($request->integer('per_page', 20));

        return response()->json($creators);
    }

    /** GET /api/creators/{slug} */
    public function show(Creator $creator): JsonResponse
    {
        $creator->load('stories.region');

        return response()->json(['data' => $creator]);
    }
}
