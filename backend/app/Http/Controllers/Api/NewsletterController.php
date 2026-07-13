<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Support\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class NewsletterController extends Controller
{
    public function subscribe(Request $request): JsonResponse
    {
        $request->validate(['email' => ['required', 'email', 'max:180']]);

        $exists = DB::table('newsletter_subscribers')
            ->where('email', $request->email)
            ->exists();

        if ($exists) {
            return ApiResponse::success(null, 'You are already subscribed.');
        }

        DB::table('newsletter_subscribers')->insert([
            'email' => $request->email,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return ApiResponse::success(null, 'Subscribed successfully. Welcome to GUGE.', [], [], 201);
    }
}
