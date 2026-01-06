<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\BookingStreamController;
use App\Models\BookingBooking;
use App\Events\BookingCreated;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// Real-time broadcast endpoints
Route::middleware('auth:sanctum')->group(function () {
    // Broadcast all today's bookings to trigger live updates
    Route::post('/broadcast-today-bookings', function (Request $request) {
        $today = now()->format('Y-m-d');
        $bookings = BookingBooking::where('booking_date', $today)->get();
        
        foreach ($bookings as $booking) {
            try {
                broadcast(new BookingCreated($booking))->toOthers();
            } catch (\Exception $e) {
                \Log::error('Broadcast failed for booking ' . $booking->id);
            }
        }
        
        return response()->json([
            'status' => 'success',
            'message' => 'Broadcasts sent for ' . $bookings->count() . ' bookings',
            'count' => $bookings->count(),
        ]);
    });
    
    // Broadcast bookings for a specific complex
    Route::post('/broadcast-complex/{complexId}', function (Request $request, $complexId) {
        $today = now()->format('Y-m-d');
        $bookings = BookingBooking::where('booking_date', $today)
            ->where('complex_id_id', $complexId)
            ->get();
        
        foreach ($bookings as $booking) {
            try {
                broadcast(new BookingCreated($booking))->toOthers();
            } catch (\Exception $e) {
                \Log::error('Broadcast failed for booking ' . $booking->id);
            }
        }
        
        return response()->json([
            'status' => 'success',
            'message' => 'Broadcasts sent for ' . $bookings->count() . ' bookings on complex ' . $complexId,
            'count' => $bookings->count(),
        ]);
    });
    
    // Broadcast a specific booking
    Route::post('/broadcast-booking/{bookingId}', function (Request $request, $bookingId) {
        $booking = BookingBooking::find($bookingId);
        
        if (!$booking) {
            return response()->json(['error' => 'Booking not found'], 404);
        }
        
        try {
            broadcast(new BookingCreated($booking))->toOthers();
            return response()->json([
                'status' => 'success',
                'message' => 'Broadcast sent for booking ' . $bookingId,
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Broadcast failed'], 500);
        }
    });
});

/*
|--------------------------------------------------------------------------
| Real-Time Booking Stream (Server-Sent Events)
|--------------------------------------------------------------------------
|
| This endpoint provides real-time booking updates via SSE.
| It's more efficient than polling and doesn't require WebSockets.
|
*/
Route::get('/bookings/stream/{complexId}', [BookingStreamController::class, 'stream'])
    ->name('bookings.stream');


