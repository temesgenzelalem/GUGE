<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreContactRequest;
use App\Support\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class ContactController extends Controller
{
    public function store(StoreContactRequest $request): JsonResponse
    {
        DB::table('contact_submissions')->insert([
            'name' => $request->name,
            'email' => $request->email,
            'topic' => $request->topic,
            'message' => $request->message,
            'ip_address' => $request->ip(),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return ApiResponse::success(
            null,
            'Message received. We will respond within 2–3 business days.',
            [],
            [],
            201
        );
    }
}
