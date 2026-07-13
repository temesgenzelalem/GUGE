<?php

namespace App\Http\Controllers\Api;

use App\Domain\Search\Contracts\SearchServiceInterface;
use App\Http\Controllers\Controller;
use App\Support\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SearchController extends Controller
{
    public function __construct(protected SearchServiceInterface $searchService) {}

    /** GET /api/search?q=... */
    public function __invoke(Request $request): JsonResponse
    {
        $q = trim($request->string('q'));

        if (strlen($q) < 2) {
            return ApiResponse::success([
                'regions' => [],
                'products' => [],
                'stories' => [],
                'creators' => [],
            ], 'Search results');
        }

        $results = $this->searchService->search($q, $request->only(['direction', 'category', 'region']));

        return ApiResponse::success($results, 'Search results');
    }
}
