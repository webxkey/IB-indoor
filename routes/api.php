<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\BookingStreamController;
use App\Models\BookingBooking;
use App\Models\BookingSport;
use App\Models\BookingVenue;
use App\Models\BookingVenueReview;
use App\Models\UserUser;
use App\Events\BookingCreated;

/*
|==========================================================================
| IndoorB Mobile API
|==========================================================================
|
| Base URL: /api
| Auth: Bearer token (Sanctum) — pass as header: Authorization: Bearer {token}
|
| Public routes  — no token needed
| Protected routes — require Authorization: Bearer {token}
|
*/

// =========================================================================
// AUTH
// =========================================================================

/**
 * POST /api/auth/login
 * Body: { email, password }
 * Returns: { token, user: { id, name, email, phone_number, profile_picture, points } }
 */
Route::post('/auth/login', function (Request $request) {
    $v = Validator::make($request->all(), [
        'email'    => 'required|email',
        'password' => 'required|string',
    ]);
    if ($v->fails()) return response()->json(['errors' => $v->errors()], 422);

    $user = UserUser::where('email', $request->email)->first();

    if (!$user || !Hash::check($request->password, $user->password)) {
        return response()->json(['message' => 'Invalid credentials'], 401);
    }

    // Use Laravel User model for Sanctum token
    $laravelUser = \App\Models\User::where('email', $request->email)->first();
    $token = $laravelUser ? $laravelUser->createToken('mobile')->plainTextToken : null;

    return response()->json([
        'token' => $token,
        'user'  => [
            'id'              => $user->id,
            'name'            => trim($user->first_name . ' ' . $user->last_name),
            'email'           => $user->email,
            'phone_number'    => $user->phone_number,
            'profile_picture' => $user->profile_picture,
            'points'          => $user->points ?? 0,
        ],
    ]);
});

/**
 * POST /api/auth/register
 * Body: { first_name, last_name, email, password, phone_number }
 * Returns: { token, user }
 */
Route::post('/auth/register', function (Request $request) {
    $v = Validator::make($request->all(), [
        'first_name'   => 'required|string|max:100',
        'last_name'    => 'nullable|string|max:100',
        'email'        => 'required|email|unique:users_user,email',
        'password'     => 'required|string|min:6',
        'phone_number' => 'nullable|string|max:20',
    ]);
    if ($v->fails()) return response()->json(['errors' => $v->errors()], 422);

    $user = UserUser::create([
        'first_name'   => $request->first_name,
        'last_name'    => $request->last_name ?? '',
        'email'        => $request->email,
        'password'     => Hash::make($request->password),
        'phone_number' => $request->phone_number,
        'is_active'    => true,
        'is_staff'     => false,
        'is_superuser' => false,
        'points'       => 0,
        'referral_code'=> strtoupper(\Illuminate\Support\Str::random(8)),
    ]);

    // Also create Laravel user for Sanctum
    $laravelUser = \App\Models\User::create([
        'name'     => trim($request->first_name . ' ' . ($request->last_name ?? '')),
        'email'    => $request->email,
        'password' => Hash::make($request->password),
        'role'     => 'customer',
        'contact'  => $request->phone_number ?? '',
    ]);
    $token = $laravelUser->createToken('mobile')->plainTextToken;

    return response()->json([
        'token' => $token,
        'user'  => [
            'id'           => $user->id,
            'name'         => trim($user->first_name . ' ' . $user->last_name),
            'email'        => $user->email,
            'phone_number' => $user->phone_number,
            'points'       => 0,
        ],
    ], 201);
});

/**
 * POST /api/auth/logout  [protected]
 */
Route::middleware('auth:sanctum')->post('/auth/logout', function (Request $request) {
    $request->user()->currentAccessToken()->delete();
    return response()->json(['message' => 'Logged out']);
});

// =========================================================================
// VENUES
// =========================================================================

/**
 * GET /api/venues
 * Query: ?county=Colombo&search=abc&page=1
 * Returns paginated list of active venues with their sports
 */
Route::get('/venues', function (Request $request) {
    $query = BookingVenue::where('status', 'Active')
        ->with(['sports' => function ($q) {
            $q->where('status', 'Active')->select('id','venue_id','name','price','rate_type','maximum_court','image','average_rating');
        }])
        ->select('id','name','address','county','location','image_url','cover_image','rating','reviews','complex_type','description','contact_number','opening_hours');

    if ($request->county)  $query->where('county', $request->county);
    if ($request->search)  $query->where('name', 'ilike', '%'.$request->search.'%');

    $venues = $query->paginate(10);

    return response()->json($venues);
});

/**
 * GET /api/venues/{id}
 * Returns full venue detail with sports, gallery, reviews
 */
Route::get('/venues/{id}', function ($id) {
    $venue = BookingVenue::with([
        'sports' => function ($q) {
            $q->where('status', 'Active');
        },
        'reviews' => function ($q) {
            $q->latest()->limit(10);
        },
    ])->find($id);

    if (!$venue) return response()->json(['message' => 'Venue not found'], 404);

    return response()->json($venue);
});

/**
 * GET /api/venues/{id}/sports
 * Returns sports for a venue
 */
Route::get('/venues/{id}/sports', function ($id) {
    $sports = BookingSport::where('venue_id', $id)
        ->where('status', 'Active')
        ->get(['id','name','price','rate_type','maximum_court','image','average_rating','description','game_type','pricing_rules']);

    return response()->json($sports);
});

// =========================================================================
// SLOT AVAILABILITY
// =========================================================================

/**
 * GET /api/slot/available
 * Query: ?sport_id=9&date=2026-04-09&time=09:00:00&court=1
 * Returns: { available: true } or { available: false, reason: "Maintenance" }
 */
Route::get('/slot/available', function (Request $request) {
    $v = Validator::make($request->all(), [
        'sport_id' => 'required|integer',
        'date'     => 'required|date',
        'time'     => 'required',
        'court'    => 'nullable|string',
    ]);
    if ($v->fails()) return response()->json(['errors' => $v->errors()], 422);

    $sport = BookingSport::find($request->sport_id);
    if (!$sport) return response()->json(['available' => false, 'reason' => 'Sport not found'], 404);

    $timeKey = strlen($request->time) === 5 ? $request->time . ':00' : $request->time;
    $court   = $request->court ?? '1';

    // 1. Check blocked_slots
    $blocked  = is_array($sport->blocked_slots) ? $sport->blocked_slots : [];
    $slotData = $blocked[$request->date][$timeKey][$court] ?? null;
    if ($slotData !== null) {
        $reason = is_array($slotData) ? ($slotData['reason'] ?? 'Unavailable') : $slotData;
        return response()->json(['available' => false, 'reason' => $reason]);
    }

    // 2. Check already booked
    $booked = BookingBooking::where('game_id_id', $request->sport_id)
        ->where('booking_date', $request->date)
        ->where('start_time', $timeKey)
        ->where('court_number', $court)
        ->whereNotIn('status', ['cancelled'])
        ->exists();

    if ($booked) return response()->json(['available' => false, 'reason' => 'Already booked']);

    return response()->json(['available' => true]);
});

/**
 * GET /api/slot/available-courts
 * Query: ?sport_id=9&date=2026-04-09&time=09:00:00
 * Returns list of courts with their availability status
 */
Route::get('/slot/available-courts', function (Request $request) {
    $v = Validator::make($request->all(), [
        'sport_id' => 'required|integer',
        'date'     => 'required|date',
        'time'     => 'required',
    ]);
    if ($v->fails()) return response()->json(['errors' => $v->errors()], 422);

    $sport = BookingSport::find($request->sport_id);
    if (!$sport) return response()->json(['message' => 'Sport not found'], 404);

    $timeKey  = strlen($request->time) === 5 ? $request->time . ':00' : $request->time;
    $blocked  = is_array($sport->blocked_slots) ? $sport->blocked_slots : [];
    $courts   = [];

    for ($c = 1; $c <= $sport->maximum_court; $c++) {
        $court    = (string) $c;
        $slotData = $blocked[$request->date][$timeKey][$court] ?? null;
        $isBooked = BookingBooking::where('game_id_id', $request->sport_id)
            ->where('booking_date', $request->date)
            ->where('start_time', $timeKey)
            ->where('court_number', $court)
            ->whereNotIn('status', ['cancelled'])
            ->exists();

        $courts[] = [
            'court'     => $c,
            'available' => $slotData === null && !$isBooked,
            'reason'    => $slotData ? (is_array($slotData) ? ($slotData['reason'] ?? 'Blocked') : $slotData) : ($isBooked ? 'Already booked' : null),
        ];
    }

    return response()->json(['courts' => $courts]);
});

// =========================================================================
// BOOKINGS  [all protected]
// =========================================================================

Route::middleware('auth:sanctum')->group(function () {

    /**
     * POST /api/bookings
     * Body: { sport_id, venue_id, booking_date, start_time, end_time, court_number,
     *         user_name, user_number, duration, price, payment_method, notes }
     * Returns: { booking }
     */
    Route::post('/bookings', function (Request $request) {
        $v = Validator::make($request->all(), [
            'sport_id'      => 'required|integer|exists:booking_sport,id',
            'venue_id'      => 'required|integer|exists:booking_venue,id',
            'booking_date'  => 'required|date',
            'start_time'    => 'required',
            'end_time'      => 'nullable',
            'court_number'  => 'required|string',
            'user_name'     => 'required|string|max:255',
            'user_number'   => 'required|string|max:20',
            'duration'      => 'nullable|integer',
            'price'         => 'nullable|numeric',
            'payment_method'=> 'nullable|string',
            'notes'         => 'nullable|string',
        ]);
        if ($v->fails()) return response()->json(['errors' => $v->errors()], 422);

        $sport   = BookingSport::find($request->sport_id);
        $timeKey = strlen($request->start_time) === 5 ? $request->start_time . ':00' : $request->start_time;
        $court   = $request->court_number;

        // Block check
        $blocked  = is_array($sport->blocked_slots) ? $sport->blocked_slots : [];
        $slotData = $blocked[$request->booking_date][$timeKey][$court] ?? null;
        if ($slotData !== null) {
            $reason = is_array($slotData) ? ($slotData['reason'] ?? 'Unavailable') : $slotData;
            return response()->json(['message' => 'Slot is blocked: ' . $reason], 409);
        }

        // Double-booking check
        $exists = BookingBooking::where('game_id_id', $request->sport_id)
            ->where('booking_date', $request->booking_date)
            ->where('start_time', $timeKey)
            ->where('court_number', $court)
            ->whereNotIn('status', ['cancelled'])
            ->exists();
        if ($exists) return response()->json(['message' => 'Slot already booked'], 409);

        $booking = BookingBooking::create([
            'game_id_id'     => $request->sport_id,
            'game_name'      => $sport->name,
            'complex_id_id'  => $request->venue_id,
            'booking_date'   => $request->booking_date,
            'start_time'     => $timeKey,
            'end_time'       => $request->end_time,
            'court_number'   => $court,
            'user_name'      => $request->user_name,
            'user_number'    => $request->user_number,
            'duration'       => $request->duration ?? 60,
            'price'          => $request->price ?? $sport->price,
            'payment_method' => $request->payment_method ?? 'cash',
            'payment_status' => 'pending',
            'status'         => 'confirmed',
            'notes'          => $request->notes,
        ]);

        // Broadcast to staff dashboard
        try { broadcast(new BookingCreated($booking))->toOthers(); } catch (\Exception $e) {}

        // Notify all staff users for this venue
        try {
            $staffUsers = \App\Models\User::whereIn('role', ['staff', 'facility_owner'])
                ->where('complex_id', $request->venue_id)
                ->pluck('id');
            foreach ($staffUsers as $staffId) {
                \App\Models\BookingNotification::create([
                    'user_id'    => $staffId,
                    'type'       => 'booking_created',
                    'title'      => 'New Booking (Mobile)',
                    'message'    => "New booking for {$sport->name} on {$request->booking_date} at {$timeKey} — {$request->user_name}",
                    'data'       => ['booking_id' => $booking->id, 'source' => 'mobile'],
                    'is_read'    => false,
                    'created_at' => now(),
                ]);
            }
        } catch (\Exception $e) {}

        return response()->json(['booking' => $booking], 201);
    });

    /**
     * GET /api/bookings/my
     * Query: ?page=1&status=confirmed
     * Returns authenticated user's bookings
     */
    Route::get('/bookings/my', function (Request $request) {
        $laravelUser = $request->user();
        $userUser    = UserUser::where('email', $laravelUser->email)->first();

        $query = BookingBooking::where('user_name', $laravelUser->name)
            ->orWhere('user_number', $laravelUser->contact);

        if ($userUser) {
            $query->orWhere('user_id_id', $userUser->id);
        }

        if ($request->status) $query->where('status', $request->status);

        $bookings = $query->orderByDesc('booking_date')
            ->orderByDesc('start_time')
            ->paginate(15);

        return response()->json($bookings);
    });

    /**
     * GET /api/bookings/{id}
     * Returns single booking detail
     */
    Route::get('/bookings/{id}', function ($id) {
        $booking = BookingBooking::find($id);
        if (!$booking) return response()->json(['message' => 'Not found'], 404);
        return response()->json($booking);
    });

    /**
     * PATCH /api/bookings/{id}/cancel
     * Cancel a booking
     */
    Route::patch('/bookings/{id}/cancel', function ($id) {
        $booking = BookingBooking::find($id);
        if (!$booking) return response()->json(['message' => 'Not found'], 404);
        if ($booking->status === 'cancelled') {
            return response()->json(['message' => 'Already cancelled'], 409);
        }
        $booking->update(['status' => 'cancelled']);
        return response()->json(['message' => 'Booking cancelled', 'booking' => $booking]);
    });

    // =========================================================================
    // USER PROFILE
    // =========================================================================

    /**
     * GET /api/profile
     */
    Route::get('/profile', function (Request $request) {
        $laravelUser = $request->user();
        $userUser    = UserUser::where('email', $laravelUser->email)->first();
        return response()->json([
            'id'              => $userUser?->id ?? $laravelUser->id,
            'name'            => $laravelUser->name,
            'email'           => $laravelUser->email,
            'phone_number'    => $userUser?->phone_number ?? $laravelUser->contact,
            'profile_picture' => $userUser?->profile_picture ?? $laravelUser->profile_photo_url,
            'points'          => $userUser?->points ?? 0,
            'bio'             => $userUser?->bio,
        ]);
    });

    /**
     * PATCH /api/profile
     * Body: { first_name, last_name, phone_number, bio }
     */
    Route::patch('/profile', function (Request $request) {
        $laravelUser = $request->user();
        $v = Validator::make($request->all(), [
            'first_name'   => 'nullable|string|max:100',
            'last_name'    => 'nullable|string|max:100',
            'phone_number' => 'nullable|string|max:20',
            'bio'          => 'nullable|string|max:500',
        ]);
        if ($v->fails()) return response()->json(['errors' => $v->errors()], 422);

        $name = trim(($request->first_name ?? '') . ' ' . ($request->last_name ?? ''));
        if ($name) $laravelUser->update(['name' => $name]);

        $userUser = UserUser::where('email', $laravelUser->email)->first();
        if ($userUser) {
            $userUser->update(array_filter([
                'first_name'   => $request->first_name,
                'last_name'    => $request->last_name,
                'phone_number' => $request->phone_number,
                'bio'          => $request->bio,
            ]));
        }

        return response()->json(['message' => 'Profile updated']);
    });

    // =========================================================================
    // REVIEWS
    // =========================================================================

    /**
     * POST /api/venues/{id}/reviews
     * Body: { rating, comment, would_recommend }
     */
    Route::post('/venues/{id}/reviews', function (Request $request, $id) {
        $venue = BookingVenue::find($id);
        if (!$venue) return response()->json(['message' => 'Venue not found'], 404);

        $v = Validator::make($request->all(), [
            'rating'           => 'required|integer|min:1|max:5',
            'comment'          => 'nullable|string|max:1000',
            'would_recommend'  => 'nullable|boolean',
        ]);
        if ($v->fails()) return response()->json(['errors' => $v->errors()], 422);

        $userUser = UserUser::where('email', $request->user()->email)->first();

        $review = BookingVenueReview::create([
            'venue_id'         => $id,
            'user_id'          => $userUser?->id,
            'rating'           => $request->rating,
            'comment'          => $request->comment,
            'would_recommend'  => $request->would_recommend ?? true,
        ]);

        // Update venue average rating
        $avg = BookingVenueReview::where('venue_id', $id)->avg('rating');
        $cnt = BookingVenueReview::where('venue_id', $id)->count();
        $venue->update(['rating' => round($avg, 1), 'reviews' => $cnt]);

        return response()->json(['review' => $review], 201);
    });

    /**
     * GET /api/venues/{id}/reviews
     */
});

// Public review listing
Route::get('/venues/{id}/reviews', function ($id) {
    $reviews = BookingVenueReview::where('venue_id', $id)
        ->latest()
        ->paginate(10);
    return response()->json($reviews);
});

// =========================================================================
// BROADCAST ENDPOINTS (internal — staff dashboard)
// =========================================================================

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/broadcast-today-bookings', function (Request $request) {
        $bookings = BookingBooking::where('booking_date', now()->format('Y-m-d'))->get();
        foreach ($bookings as $booking) {
            try { broadcast(new BookingCreated($booking))->toOthers(); } catch (\Exception $e) {}
        }
        return response()->json(['status' => 'success', 'count' => $bookings->count()]);
    });

    Route::post('/broadcast-complex/{complexId}', function (Request $request, $complexId) {
        $bookings = BookingBooking::where('booking_date', now()->format('Y-m-d'))
            ->where('complex_id_id', $complexId)->get();
        foreach ($bookings as $booking) {
            try { broadcast(new BookingCreated($booking))->toOthers(); } catch (\Exception $e) {}
        }
        return response()->json(['status' => 'success', 'count' => $bookings->count()]);
    });

    Route::post('/broadcast-booking/{bookingId}', function (Request $request, $bookingId) {
        $booking = BookingBooking::find($bookingId);
        if (!$booking) return response()->json(['error' => 'Not found'], 404);
        try { broadcast(new BookingCreated($booking))->toOthers(); } catch (\Exception $e) {}
        return response()->json(['status' => 'success']);
    });
});

Route::get('/bookings/stream/{complexId}', [BookingStreamController::class, 'stream'])
    ->name('bookings.stream');

Route::get('/booking/refresh', function (Request $request) {
    $bookings = BookingBooking::where('booking_date', '>=', now()->subDays(1)->format('Y-m-d'))->get();
    $count = 0;
    foreach ($bookings as $booking) {
        try { broadcast(new BookingCreated($booking))->toOthers(); $count++; } catch (\Exception $e) {}
    }
    return response()->json(['status' => 'success', 'broadcasted' => $count]);
});
