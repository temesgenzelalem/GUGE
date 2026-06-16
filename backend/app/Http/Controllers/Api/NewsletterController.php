<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class NewsletterController extends Controller
{
    public function subscribe(Request $request): JsonResponse
    {
        $request->validate(['email' => 'required|email|max:180']);

        $exists = DB::table('newsletter_subscribers')
            ->where('email', $request->email)
            ->exists();

        if ($exists) {
            return response()->json(['message' => 'Already subscribed.']);
        }

        DB::table('newsletter_subscribers')->insert([
            'email'      => $request->email,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return response()->json(['message' => 'Subscribed successfully. Welcome to GUGE.'], 201);
    }
}
