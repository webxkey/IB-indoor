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

/*
|--------------------------------------------------------------------------
| Mobile App Booking Notification Endpoint
|--------------------------------------------------------------------------
|
| Mobile app calls this endpoint after creating a booking to trigger
| real-time updates on the staff dashboard for ALL complexes
|
*/
Route::get('/booking/refresh', function (Request $request) {
    try {
        Log::info('📱 Refresh API called - broadcasting all bookings', [
            'endpoint' => 'api/booking/refresh',
            'timestamp' => now(),
        ]);
        
        // Get ALL bookings from database
        $bookings = BookingBooking::where('booking_date', '>=', now()->subDays(1)->format('Y-m-d'))
            ->get();
        
        Log::info("Found {$bookings->count()} bookings to broadcast");
        
        // Broadcast each booking to its respective complex channel
        $broadcastCount = 0;
        foreach ($bookings as $booking) {
            try {
                broadcast(new BookingCreated($booking))->toOthers();
                $broadcastCount++;
            } catch (\Exception $e) {
                Log::error("Failed to broadcast booking {$booking->id}", [
                    'error' => $e->getMessage()
                ]);
            }
        }
        
        Log::info("✅ REFRESHED ALL BOOKINGS", [
            'total_bookings' => $bookings->count(),
            'broadcasted' => $broadcastCount,
            'endpoint' => 'api/booking/refresh',
        ]);
        
        return response()->json([
            'status' => 'success',
            'message' => "All bookings refreshed - {$broadcastCount} broadcasted",
            'total_bookings' => $bookings->count(),
            'broadcasted' => $broadcastCount,
        ], 200);
        
    } catch (\Exception $e) {
        Log::error('Booking refresh error', [
            'error' => $e->getMessage(),
            'trace' => $e->getTraceAsString(),
        ]);
        
        return response()->json([
            'status' => 'error',
            'message' => 'Failed to refresh bookings',
        ], 500);
    }
});




