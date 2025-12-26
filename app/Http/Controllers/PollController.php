<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class PollController extends Controller
{
    /**
     * Show the polling view.
     */
    public function view()
    {
        return view('poll');
    }

    /**
     * Return a simple JSON status for polling.
     */
    public function status(Request $request): JsonResponse
    {
        return response()->json([
            'message' => 'Fix it better',
            'timestamp' => now()->toDateTimeString(),
        ]);
    }
}
