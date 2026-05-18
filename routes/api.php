<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;
use Illuminate\Database\QueryException;
use App\Http\Controllers\DjangoWebhookController;
use App\Models\BookingBooking;
use App\Models\BookingSport;
use App\Models\BookingVenue;
use App\Models\BookingVenueReview;
use App\Models\UserUser;
use App\Models\OtpCode;
use App\Mail\SendOtpMail;
use App\Http\Controllers\Api\Admin\BookingController;
use App\Http\Controllers\Api\Admin\DashboardController;
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
 * Returns: { token, tokens: { access, refresh }, user: { ... , user_type } }
 */
Route::post('/auth/login', function (Request $request) {
    $v = Validator::make($request->all(), [
        'email'    => 'required|email',
        'password' => 'required|string',
    ]);
    if ($v->fails()) return response()->json(['errors' => $v->errors()], 422);

    $laravelUser = \App\Models\User::where('email', $request->email)->first();
    $userUser = UserUser::where('email', $request->email)->first();

    $safePasswordCheck = function (?string $rawPassword, ?string $hashedPassword): bool {
        if (!$rawPassword || !$hashedPassword) return false;
        try {
            return Hash::check($rawPassword, $hashedPassword);
        } catch (\Throwable $e) {
            return false;
        }
    };

    $verifyDjangoPassword = function (string $password, string $djangoHash): bool {
        if (!str_starts_with($djangoHash, 'pbkdf2_sha256$')) return false;
        $parts = explode('$', $djangoHash);
        if (count($parts) !== 4) return false;
        list($algorithm, $iterations, $salt, $hash) = $parts;
        $calc = hash_pbkdf2('sha256', $password, $salt, (int)$iterations, 32, true);
        return base64_encode($calc) === $hash;
    };

    $validLaravelPassword = $laravelUser ? $safePasswordCheck($request->password, $laravelUser->password) : false;
    $validUserUserPassword = $userUser ? $safePasswordCheck($request->password, $userUser->password) : false;
    
    // Check for legacy Django hash if standard checks failed
    $isLegacyLogin = false;
    if (!$validLaravelPassword && !$validUserUserPassword && $userUser && str_starts_with($userUser->password, 'pbkdf2_sha256$')) {
        if ($verifyDjangoPassword($request->password, $userUser->password)) {
            $validUserUserPassword = true;
            $isLegacyLogin = true;
        }
    }

    if (!$validLaravelPassword && !$validUserUserPassword) {
        return response()->json(['message' => 'Invalid credentials'], 401);
    }

    // If it was a legacy login or if we are missing the Laravel user, 
    // we should ensure the passwords are synced and use Bcrypt from now on.
    $newHash = Hash::make($request->password);

    // Ensure we always have a Laravel user for Sanctum token creation.
    if (!$laravelUser && $userUser) {
        $fullName = trim(($userUser->first_name ?? '') . ' ' . ($userUser->last_name ?? ''));
        $laravelUser = \App\Models\User::create([
            'name' => $fullName ?: $userUser->email,
            'email' => $userUser->email,
            'password' => $newHash, // Store as Bcrypt
            'role' => 'customer',
            'contact' => $userUser->phone_number ?? '',
        ]);
    } elseif ($isLegacyLogin && $laravelUser) {
        // Upgrade Laravel user to Bcrypt
        $laravelUser->update(['password' => $newHash]);
    }

    // Upgrade UserUser to Bcrypt if it was legacy
    if ($isLegacyLogin && $userUser) {
        $userUser->update(['password' => $newHash]);
    }

    if (!$laravelUser) {
        return response()->json(['message' => 'User account not provisioned'], 500);
    }

    $token = $laravelUser->createToken('mobile')->plainTextToken;

    // Normalize role naming expected by mobile app.
    $role = strtolower((string) ($laravelUser->role ?? 'customer'));
    $userType = match ($role) {
        'admin', 'superadmin' => 'superadmin',
        'facility_owner', 'indoor_admin' => 'indoor_admin',
        'staff' => 'staff',
        default => 'user',
    };

    $displayName = trim((string) ($laravelUser->name ?? ''));
    if (!$displayName && $userUser) {
        $displayName = trim(($userUser->first_name ?? '') . ' ' . ($userUser->last_name ?? ''));
    }

    $laravelUser->load('complex');

    return response()->json([
        'token' => $token,
        'tokens' => [
            'access' => $token,
            'refresh' => null,
        ],
        'access' => $token,
        'refresh' => null,
        'user'  => [
            'id'              => $laravelUser->id,
            'email'           => $laravelUser->email,
            'first_name'      => $laravelUser->first_name,
            'last_name'       => $laravelUser->last_name,
            'user_type'       => $userType,
            'is_active'       => true, 
            'phone_number'    => $userUser?->phone_number ?? $laravelUser->contact,
            'profile_picture' => $userUser?->profile_picture ?? $laravelUser->profile_photo_url,
            'venue_id'        => $laravelUser->complex_id,
            'venue_name'      => $laravelUser->complex?->name,
            'points'          => $userUser?->points ?? 0,
        ],
    ]);
});

/**
 * POST /api/auth/register
 * Body: { first_name, last_name, email, password, phone_number }
 * Returns: { token, user }
 */
Route::post('/auth/register', function (Request $request) {
    $normalizedEmail = strtolower(trim((string) $request->input('email', '')));
    $normalizedPhoneNumber = preg_replace('/\D+/', '', (string) $request->input('phone_number', ''));
    $request->merge([
        'email' => $normalizedEmail,
        'phone_number' => $normalizedPhoneNumber,
    ]);

    $v = Validator::make($request->all(), [
        'first_name'   => 'required|string|max:100',
        'last_name'    => 'nullable|string|max:100',
        'email'        => 'required|email|unique:users_user,email|unique:users,email',
        'password'     => 'required|string|min:6',
        'phone_number' => 'required|string|digits:10|unique:users_user,phone_number',
    ]);
    if ($v->fails()) return response()->json(['errors' => $v->errors()], 422);

    $emailVerificationToken = trim((string) $request->input('email_verification_token', ''));
    if ($emailVerificationToken === '') {
        return response()->json([
            'message' => 'Email verification is required before signup.',
        ], 422);
    }

    $verifiedEmail = Cache::get('signup_email_verification_token:' . $emailVerificationToken);
    if ($verifiedEmail !== $request->email) {
        return response()->json([
            'message' => 'Email verification does not match this email address.',
        ], 422);
    }

    Cache::forget('signup_email_verification_token:' . $emailVerificationToken);
    Cache::forget('signup_email_verification_token_by_email:' . $request->email);

    try {
        $user = UserUser::create([
            'first_name'       => $request->first_name,
            'last_name'        => $request->last_name ?? '',
            'email'            => $request->email,
            'password'         => Hash::make($request->password),
            'phone_number'     => $normalizedPhoneNumber,
            'is_active'        => true,
            'is_staff'         => false,
            'is_superuser'     => false,
            'points'           => 0,
            'referral_code'    => strtoupper(\Illuminate\Support\Str::random(8)),
            'availability'     => '',
            'is_public_profile'=> false,
            'is_show_contact'  => false,
        ]);

        $role = 'customer';
        if ($request->account_type === 'indoor_admin') {
            $role = 'facility_owner';
        } elseif ($request->account_type === 'superadmin') {
            $role = 'admin';
        } elseif ($request->account_type === 'staff') {
            $role = 'staff';
        }

        $venueId = null;
        if ($request->filled('venue_name')) {
            $venue = \App\Models\BookingVenue::create([
                'name'           => $request->venue_name,
                'address'        => $request->venue_address ?? '',
                'contact_number' => preg_replace('/\D+/', '', (string) ($request->venue_contact_number ?? '')),
                'email_address'  => $request->venue_email ?? '',
                'location'       => $request->venue_location ?? '',
                'complex_type'   => $request->venue_complex_type ?? 'Indoor',
                'rating'         => '0',
                'reviews'        => 0,
                'status'         => 'Active',
            ]);
            $venueId = $venue->id;
        }

        // Also create Laravel user for Sanctum
        $laravelUser = \App\Models\User::create([
            'name'       => trim($request->first_name . ' ' . ($request->last_name ?? '')),
            'email'      => $request->email,
            'password'   => Hash::make($request->password),
            'role'       => $role,
            'contact'    => $normalizedPhoneNumber,
            'complex_id' => $venueId,
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
    } catch (QueryException $e) {
        if ((string) $e->getCode() === '23505') {
            return response()->json([
                'message' => 'Phone number already registered.',
            ], 409);
        }

        throw $e;
    }
});

/**
 * POST /api/auth/logout  [protected]
 */
Route::middleware('auth:sanctum')->post('/auth/logout', function (Request $request) {
    $request->user()->currentAccessToken()->delete();
    return response()->json(['message' => 'Logged out']);
});

// =========================================================================
// EMAIL VERIFICATION
// =========================================================================

/**
 * POST /api/auth/send-verification-email
 * Body: { email }
 * Sends OTP to email for email verification during signup
 */
Route::post('/auth/send-verification-email', function (Request $request) {
    $normalizedEmail = strtolower(trim((string) $request->input('email', '')));
    $request->merge([
        'email' => $normalizedEmail,
    ]);

    $v = Validator::make($request->all(), [
        'email' => 'required|email',
    ]);
    if ($v->fails()) return response()->json(['errors' => $v->errors()], 422);

    $emailAlreadyUsed = UserUser::where('email', $request->email)->exists()
        || \App\Models\User::where('email', $request->email)->exists();
    if ($emailAlreadyUsed) {
        return response()->json([
            'message' => 'Email is already used. Please use a different email address.',
        ], 409);
    }

    try {
        $previousToken = Cache::get('signup_email_verification_token_by_email:' . $request->email);
        if ($previousToken) {
            Cache::forget('signup_email_verification_token:' . $previousToken);
        }
        Cache::forget('signup_email_verification_token_by_email:' . $request->email);

        $otp = OtpCode::createForEmail($request->email, 'email_verification');

        // Only perform real SMTP sends in non-local, non-log mailer environments.
        // In local or when mailer is configured to 'log' we skip the SMTP send
        // to avoid authentication failures during development; the OTP is
        // returned in the response for debugging in those cases.
        if (!app()->environment('local') && config('mail.default') !== 'log' && env('MAIL_MAILER') !== 'log') {
            Mail::to($request->email)->send(new SendOtpMail($otp->code, 'User', 'Email Verification'));
        }

        $response = [
            'message' => 'Verification code sent to your email',
            'expires_in' => 15 * 60, // 15 minutes in seconds
        ];

        // For local/dev environments or when mailer is set to "log",
        // include the OTP in the response so testing can proceed without SMTP.
        if (app()->environment('local') || env('MAIL_MAILER') === 'log' || config('mail.default') === 'log') {
            $response['debug_code'] = $otp->code;
        }

        return response()->json($response);
    } catch (\Exception $e) {
        Log::error('Error sending verification email: ' . $e->getMessage());
        return response()->json([
            'message' => 'Failed to send verification email. Please try again.',
        ], 500);
    }
});

/**
 * POST /api/auth/verify-email
 * Body: { email, code }
 * Verifies email with OTP code
 */
Route::post('/auth/verify-email', function (Request $request) {
    $normalizedEmail = strtolower(trim((string) $request->input('email', '')));
    $request->merge([
        'email' => $normalizedEmail,
    ]);

    $v = Validator::make($request->all(), [
        'email' => 'required|email',
        'code'  => 'required|string|size:6',
    ]);
    if ($v->fails()) return response()->json(['errors' => $v->errors()], 422);

    $otp = OtpCode::verify($request->email, $request->code, 'email_verification');

    if (!$otp) {
        return response()->json([
            'message' => 'Invalid or expired verification code',
        ], 422);
    }

    // Mark OTP as used
    $otp->markAsUsed();

    $previousToken = Cache::get('signup_email_verification_token_by_email:' . $request->email);
    if ($previousToken) {
        Cache::forget('signup_email_verification_token:' . $previousToken);
    }

    $verificationToken = (string) Str::uuid();
    Cache::put('signup_email_verification_token:' . $verificationToken, $request->email, now()->addMinutes(15));
    Cache::put('signup_email_verification_token_by_email:' . $request->email, $verificationToken, now()->addMinutes(15));

    return response()->json([
        'message' => 'Email verified successfully',
        'verified' => true,
        'verification_token' => $verificationToken,
    ]);
});

/**
 * POST /api/auth/resend-verification-email
 * Body: { email }
 * Resends OTP to email
 */
Route::post('/auth/resend-verification-email', function (Request $request) {
    $normalizedEmail = strtolower(trim((string) $request->input('email', '')));
    $request->merge([
        'email' => $normalizedEmail,
    ]);

    $v = Validator::make($request->all(), [
        'email' => 'required|email',
    ]);
    if ($v->fails()) return response()->json(['errors' => $v->errors()], 422);

    try {
        $previousToken = Cache::get('signup_email_verification_token_by_email:' . $request->email);
        if ($previousToken) {
            Cache::forget('signup_email_verification_token:' . $previousToken);
        }
        Cache::forget('signup_email_verification_token_by_email:' . $request->email);

        $otp = OtpCode::createForEmail($request->email, 'email_verification');
        if (!app()->environment('local') && config('mail.default') !== 'log' && env('MAIL_MAILER') !== 'log') {
            Mail::to($request->email)->send(new SendOtpMail($otp->code, 'User', 'Email Verification'));
        }

        $resp = [
            'message' => 'New verification code sent to your email',
            'expires_in' => 15 * 60,
        ];

        if (app()->environment('local') || env('MAIL_MAILER') === 'log' || config('mail.default') === 'log') {
            $resp['debug_code'] = $otp->code;
        }

        return response()->json($resp);
    } catch (\Exception $e) {
        Log::error('Error resending verification email: ' . $e->getMessage());
        return response()->json([
            'message' => 'Failed to resend verification email. Please try again.',
        ], 500);
    }
});

// =========================================================================
// TWO-STEP VERIFICATION
// =========================================================================

/**
 * POST /api/auth/login-with-verification
 * Body: { email, password }
 * Returns: { require_2step: true, verification_token, expires_in } or { token, user } if 2-step disabled
 */
Route::post('/auth/login-with-verification', function (Request $request) {
    $v = Validator::make($request->all(), [
        'email'    => 'required|email',
        'password' => 'required|string',
    ]);
    if ($v->fails()) return response()->json(['errors' => $v->errors()], 422);

    $laravelUser = \App\Models\User::where('email', $request->email)->first();
    if (!$laravelUser) {
        return response()->json(['message' => 'Invalid credentials'], 401);
    }

    // Check password
    if (!Hash::check($request->password, $laravelUser->password)) {
        return response()->json(['message' => 'Invalid credentials'], 401);
    }

    // If 2-step is enabled, send OTP and require verification
    if ($laravelUser->two_step_enabled) {
        try {
            $otp = OtpCode::createForEmail(
                $laravelUser->email,
                'login_verification',
                $laravelUser->id
            );

            // Send OTP email (skip real SMTP in local/log environments)
            if (!app()->environment('local') && config('mail.default') !== 'log' && env('MAIL_MAILER') !== 'log') {
                Mail::to($laravelUser->email)->send(
                    new SendOtpMail($otp->code, $laravelUser->first_name ?: 'User', 'Login Verification')
                );
            }

            // Generate temporary token for 2-step verification
            $verificationToken = Str::random(64);
            Cache::put('2step_' . $verificationToken, [
                'user_id' => $laravelUser->id,
                'email' => $laravelUser->email,
            ], now()->addMinutes(15));

            return response()->json([
                'require_2step' => true,
                'verification_token' => $verificationToken,
                'expires_in' => 15 * 60,
                'message' => 'Verification code sent to your email',
            ]);
        } catch (\Exception $e) {
            Log::error('Error sending 2-step OTP: ' . $e->getMessage());
            return response()->json([
                'message' => 'Failed to send verification code. Please try again.',
            ], 500);
        }
    }

    // If 2-step is not enabled, proceed with normal login
    $token = $laravelUser->createToken('mobile')->plainTextToken;
    $role = strtolower((string) ($laravelUser->role ?? 'customer'));
    $userType = match ($role) {
        'admin', 'superadmin' => 'superadmin',
        'facility_owner', 'indoor_admin' => 'indoor_admin',
        'staff' => 'staff',
        default => 'user',
    };

    return response()->json([
        'token' => $token,
        'tokens' => ['access' => $token, 'refresh' => null],
        'access' => $token,
        'user' => [
            'id' => $laravelUser->id,
            'email' => $laravelUser->email,
            'first_name' => $laravelUser->first_name,
            'last_name' => $laravelUser->last_name,
            'user_type' => $userType,
            'is_active' => true,
            'phone_number' => $laravelUser->contact,
            'profile_picture' => $laravelUser->profile_photo_url,
            'venue_id' => $laravelUser->complex_id,
            'venue_name' => $laravelUser->complex?->name,
        ],
    ]);
});

/**
 * POST /api/auth/verify-login
 * Body: { verification_token, code }
 * Verifies 2-step OTP and returns session token
 */
Route::post('/auth/verify-login', function (Request $request) {
    $v = Validator::make($request->all(), [
        'verification_token' => 'required|string',
        'code' => 'required|string|size:6',
    ]);
    if ($v->fails()) return response()->json(['errors' => $v->errors()], 422);

    // Get verification data from cache
    $verificationData = Cache::get('2step_' . $request->verification_token);
    if (!$verificationData) {
        return response()->json([
            'message' => 'Verification session expired. Please login again.',
        ], 422);
    }

    // Verify OTP
    $otp = OtpCode::verify($verificationData['email'], $request->code, 'login_verification');
    if (!$otp) {
        return response()->json([
            'message' => 'Invalid or expired verification code',
        ], 422);
    }

    // Mark OTP as used
    $otp->markAsUsed();

    // Remove verification token from cache
    Cache::forget('2step_' . $request->verification_token);

    // Get user and create token
    $laravelUser = \App\Models\User::find($verificationData['user_id']);
    if (!$laravelUser) {
        return response()->json(['message' => 'User not found'], 404);
    }

    $token = $laravelUser->createToken('mobile')->plainTextToken;
    $role = strtolower((string) ($laravelUser->role ?? 'customer'));
    $userType = match ($role) {
        'admin', 'superadmin' => 'superadmin',
        'facility_owner', 'indoor_admin' => 'indoor_admin',
        'staff' => 'staff',
        default => 'user',
    };

    return response()->json([
        'token' => $token,
        'tokens' => ['access' => $token, 'refresh' => null],
        'access' => $token,
        'user' => [
            'id' => $laravelUser->id,
            'email' => $laravelUser->email,
            'first_name' => $laravelUser->first_name,
            'last_name' => $laravelUser->last_name,
            'user_type' => $userType,
            'is_active' => true,
            'phone_number' => $laravelUser->contact,
            'profile_picture' => $laravelUser->profile_photo_url,
            'venue_id' => $laravelUser->complex_id,
            'venue_name' => $laravelUser->complex?->name,
        ],
    ]);
});

/**
 * POST /api/auth/enable-2step [protected]
 * Body: { method: 'email'|'phone' }
 * Enables 2-step verification for user
 */
Route::middleware('auth:sanctum')->post('/auth/enable-2step', function (Request $request) {
    $v = Validator::make($request->all(), [
        'method' => 'required|in:email,phone',
    ]);
    if ($v->fails()) return response()->json(['errors' => $v->errors()], 422);

    $user = $request->user();
    
    // Send verification OTP
        try {
            $otp = OtpCode::createForEmail($user->email, 'phone_verification', $user->id);
            if (!app()->environment('local') && config('mail.default') !== 'log' && env('MAIL_MAILER') !== 'log') {
                Mail::to($user->email)->send(
                    new SendOtpMail($otp->code, $user->first_name ?: 'User', '2-Step Verification Setup')
                );
            }

            $resp = [
                'message' => 'Verification code sent to confirm 2-step setup',
                'expires_in' => 15 * 60,
            ];
            if (app()->environment('local') || env('MAIL_MAILER') === 'log' || config('mail.default') === 'log') {
                $resp['debug_code'] = $otp->code;
            }

            return response()->json($resp);
        } catch (\Exception $e) {
            Log::error('Error enabling 2-step: ' . $e->getMessage());
            return response()->json([
                'message' => 'Failed to send verification code',
            ], 500);
        }
});

/**
 * POST /api/auth/confirm-2step [protected]
 * Body: { method: 'email'|'phone', code }
 * Confirms and enables 2-step verification
 */
Route::middleware('auth:sanctum')->post('/auth/confirm-2step', function (Request $request) {
    $v = Validator::make($request->all(), [
        'method' => 'required|in:email,phone',
        'code'   => 'required|string|size:6',
    ]);
    if ($v->fails()) return response()->json(['errors' => $v->errors()], 422);

    $user = $request->user();
    
    // Verify OTP
    $otp = OtpCode::verify($user->email, $request->code, 'phone_verification');
    if (!$otp) {
        return response()->json([
            'message' => 'Invalid or expired verification code',
        ], 422);
    }

    // Mark OTP as used
    $otp->markAsUsed();

    // Enable 2-step verification
    $user->update([
        'two_step_enabled' => true,
        'two_step_method' => $request->method,
    ]);

    return response()->json([
        'message' => '2-step verification enabled successfully',
        'two_step_enabled' => true,
        'two_step_method' => $request->method,
    ]);
});

/**
 * POST /api/auth/disable-2step [protected]
 * Body: { }
 * Disables 2-step verification for user
 */
Route::middleware('auth:sanctum')->post('/auth/disable-2step', function (Request $request) {
    $user = $request->user();
    $user->update([
        'two_step_enabled' => false,
    ]);

    return response()->json([
        'message' => '2-step verification disabled',
        'two_step_enabled' => false,
    ]);
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
        ->get(['id','name','price','rate_type','maximum_court','image','average_rating','description','game_type']);

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
        ->whereNotIn('status', ['cancelled', 'Cancelled'])
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
            ->whereNotIn('status', ['cancelled', 'Cancelled'])
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
    Route::post('/bookings', [BookingController::class, 'create']);

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
        $laravelUser->load('complex');
        $userUser    = UserUser::where('email', $laravelUser->email)->first();
        $role = strtolower((string) ($laravelUser->role ?? 'customer'));
        $userType = match ($role) {
            'admin', 'superadmin' => 'superadmin',
            'facility_owner', 'indoor_admin' => 'indoor_admin',
            'staff' => 'staff',
            default => 'user',
        };
        return response()->json([
            'id'              => $laravelUser->id,
            'email'           => $laravelUser->email,
            'first_name'      => $laravelUser->first_name,
            'last_name'       => $laravelUser->last_name,
            'user_type'       => $userType,
            'is_active'       => true,
            'phone_number'    => $userUser?->phone_number ?? $laravelUser->contact,
            'profile_picture' => $userUser?->profile_picture ?? $laravelUser->profile_photo_url,
            'venue_id'        => $laravelUser->complex_id,
            'venue_name'      => $laravelUser->complex?->name,
            'points'          => $userUser?->points ?? 0,
            'bio'             => $userUser?->bio,
        ]);
    });

    // Compatibility alias for clients using /api/auth/profile
    Route::get('/auth/profile', function (Request $request) {
        $laravelUser = $request->user();
        $laravelUser->load('complex');
        $userUser    = UserUser::where('email', $laravelUser->email)->first();
        $role = strtolower((string) ($laravelUser->role ?? 'customer'));
        $userType = match ($role) {
            'admin', 'superadmin' => 'superadmin',
            'facility_owner', 'indoor_admin' => 'indoor_admin',
            'staff' => 'staff',
            default => 'user',
        };

        return response()->json([
            'id'              => $laravelUser->id,
            'email'           => $laravelUser->email,
            'first_name'      => $laravelUser->first_name,
            'last_name'       => $laravelUser->last_name,
            'user_type'       => $userType,
            'is_active'       => true,
            'phone_number'    => $userUser?->phone_number ?? $laravelUser->contact,
            'profile_picture' => $userUser?->profile_picture ?? $laravelUser->profile_photo_url,
            'venue_id'        => $laravelUser->complex_id,
            'venue_name'      => $laravelUser->complex?->name,
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

    // Compatibility endpoint for clients posting old/new password payloads
    Route::post('/auth/password/change', function (Request $request) {
        $v = Validator::make($request->all(), [
            'old_password' => 'required|string',
            'new_password' => 'required|string|min:8',
            'confirm_password' => 'required|same:new_password',
        ]);
        if ($v->fails()) return response()->json(['errors' => $v->errors()], 422);

        $laravelUser = $request->user();
        if (!Hash::check($request->old_password, $laravelUser->password)) {
            return response()->json(['message' => 'Current password is incorrect'], 422);
        }

        $laravelUser->password = Hash::make($request->new_password);
        $laravelUser->save();

        // Keep Django-origin mirror table in sync when available.
        $userUser = UserUser::where('email', $laravelUser->email)->first();
        if ($userUser) {
            $userUser->password = Hash::make($request->new_password);
            $userUser->save();
        }

        return response()->json(['message' => 'Password updated successfully']);
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

// =========================================================================
// DJANGO -> LARAVEL WEBHOOK  (no auth — verified by HMAC signature)
// =========================================================================

Route::post('/integration/webhooks/django-events', [DjangoWebhookController::class, 'receive'])
    ->name('webhook.django');

Route::post('/integration/booking-notify', [DjangoWebhookController::class, 'notifyBooking'])
    ->name('webhook.booking-notify');

// =========================================================================

Route::get('/booking/refresh', function (Request $request) {
    $bookings = BookingBooking::where('booking_date', '>=', now()->subDays(1)->format('Y-m-d'))->get();
    $count = 0;
    foreach ($bookings as $booking) {
        try { broadcast(new BookingCreated($booking))->toOthers(); $count++; } catch (\Exception $e) {}
    }
    return response()->json(['status' => 'success', 'broadcasted' => $count]);
});

// =========================================================================
// INDOOR ADMIN API (for mobile app)
// =========================================================================

// Public proxy routes for uploaded/local assets.
// Must remain outside auth middleware because <Image> requests don't include bearer tokens.
Route::prefix('indoor-admin')->group(function () {
    Route::get('/local-storage/{path}', function ($path) {
        $fullPath = storage_path('app/public/' . $path);
        if (!file_exists($fullPath)) return response()->json(['message' => 'File not found: ' . $path], 404);
        return response()->file($fullPath);
    })->where('path', '.*');

    Route::get('/local-images/{path}', function ($path) {
        $fullPath = public_path('images/' . $path);
        if (!file_exists($fullPath)) return response()->json(['message' => 'File not found: ' . $path], 404);
        return response()->file($fullPath);
    })->where('path', '.*');
});

Route::middleware(['auth:sanctum', 'api.role:admin,superadmin,facility_owner,indoor_admin'])->prefix('indoor-admin')->group(function () {

    // Dashboard Stats
    Route::get('/dashboard/stats', [DashboardController::class, 'getStats']);

    // Venue Management
    Route::get('/venue', function (Request $request) {
        $user = $request->user();
        $venue = \App\Models\BookingVenue::find($user->complex_id);
        if (!$venue) return response()->json(['message' => 'Venue not found'], 404);
        return response()->json($venue);
    });

    Route::patch('/venue', function (Request $request) {
        $user = $request->user();
        $venue = \App\Models\BookingVenue::find($user->complex_id);
        if (!$venue) return response()->json(['message' => 'Venue not found'], 404);

        $data = $request->only(['name', 'address', 'county', 'location', 'description', 'contact_number', 'opening_hours', 'complex_type']);
        
        if ($request->hasFile('cover_image_file')) {
            if ($venue->cover_image && \Illuminate\Support\Facades\Storage::disk('public')->exists($venue->cover_image)) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($venue->cover_image);
            }
            $data['cover_image'] = $request->file('cover_image_file')->store('venues', 'public');
        }

        $venue->update($data);
        return response()->json($venue);
    });


    // Sports Management
    Route::get('/sports/', function (Request $request) {
        $user = $request->user();
        $sports = \App\Models\BookingSport::where('venue_id', $user->complex_id)->get();
        return response()->json(['results' => $sports]);
    });

    Route::post('/sports/', function (Request $request) {
        $user = $request->user();
        $v = Validator::make($request->all(), [
            'name' => 'required|string',
            'price' => 'required|numeric',
        ]);
        if ($v->fails()) return response()->json(['errors' => $v->errors()], 422);

        $imagePath = null;
        if ($request->hasFile('image_file')) {
            $imagePath = $request->file('image_file')->store('sports', 'public');
        }

        $sport = \App\Models\BookingSport::create([
            'venue_id' => $user->complex_id,
            'name' => $request->name,
            'price' => $request->price,
            'rate_type' => $request->rate_type ?? 'Hourly',
            'maximum_court' => $request->maximum_court ?? 1,
            'description' => $request->description ?? '',
            'game_type' => $request->game_type ?? 'Other',
            'status' => $request->status ?? 'Active',
            'image' => $imagePath,
            'time_interval' => 60,
            'availability' => [],
            'blocked_slots' => [],
        ]);
        return response()->json($sport, 201);
    });


    Route::post('/sports/{id}/', function (Request $request, $id) {
        $user = $request->user();
        $sport = \App\Models\BookingSport::where('venue_id', $user->complex_id)->find($id);
        if (!$sport) return response()->json(['message' => 'Not found'], 404);

        $data = $request->only(['name', 'price', 'rate_type', 'maximum_court', 'description', 'game_type', 'status']);
        
        if ($request->hasFile('image_file')) {
            if ($sport->image && \Illuminate\Support\Facades\Storage::disk('public')->exists($sport->image)) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($sport->image);
            }
            $data['image'] = $request->file('image_file')->store('sports', 'public');
        }

        $sport->update($data);
        return response()->json($sport);
    });

    Route::patch('/sports/{id}/', function (Request $request, $id) {
        $user = $request->user();
        $sport = \App\Models\BookingSport::where('venue_id', $user->complex_id)->find($id);
        if (!$sport) return response()->json(['message' => 'Not found'], 404);

        $data = $request->only(['name', 'price', 'rate_type', 'maximum_court', 'description', 'game_type', 'status']);
        
        if ($request->hasFile('image_file')) {
            if ($sport->image && \Illuminate\Support\Facades\Storage::disk('public')->exists($sport->image)) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($sport->image);
            }
            $data['image'] = $request->file('image_file')->store('sports', 'public');
        }

        $sport->update($data);
        return response()->json($sport);
    });


    Route::delete('/sports/{id}/', function (Request $request, $id) {
        $user = $request->user();
        $sport = \App\Models\BookingSport::where('venue_id', $user->complex_id)->find($id);
        if (!$sport) return response()->json(['message' => 'Not found'], 404);

        $sport->delete();
        return response()->json(['message' => 'Deleted']);
    });

    Route::post('/sports/{id}/toggle_status/', function (Request $request, $id) {
        $user = $request->user();
        $sport = \App\Models\BookingSport::where('venue_id', $user->complex_id)->find($id);
        if (!$sport) return response()->json(['message' => 'Not found'], 404);

        $newStatus = $sport->status === 'Active' ? 'Inactive' : 'Active';
        $sport->update(['status' => $newStatus]);
        return response()->json(['message' => 'Status updated', 'status' => $newStatus]);
    });

    // Bookings
    Route::get('/bookings/upcoming/', [BookingController::class, 'upcoming']);
    Route::get('/bookings/today/', [BookingController::class, 'today']);
    Route::get('/bookings/slots/', [BookingController::class, 'getSlots']);
    Route::get('/bookings/permanent/', [BookingController::class, 'permanentList']);
    Route::post('/bookings/create/', [BookingController::class, 'create']);
    Route::patch('/bookings/permanent/{id}/cancel-all/', [BookingController::class, 'permanentCancelAll']);

    // Staff Management
    Route::get('/staff/', function (Request $request) {
        $user = $request->user();
        $staff = \App\Models\VenueStaff::where('venue_id', $user->complex_id)->get();
        return response()->json(['results' => $staff]);
    });

    Route::post('/staff/', function (Request $request) {
        $user = $request->user();
        $v = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'role' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:20',
        ]);
        if ($v->fails()) return response()->json(['errors' => $v->errors()], 422);

        $photoPath = null;
        if ($request->hasFile('photo')) {
            $photoPath = $request->file('photo')->store('staff', 'public');
        }

        $staff = \App\Models\VenueStaff::create([
            'venue_id' => $user->complex_id,
            'name' => $request->name,
            'role' => $request->role,
            'phone' => $request->phone,
            'shift' => $request->shift,
            'status' => $request->status ?? 'Active',
            'status_detail' => $request->status_detail,
            'is_online' => $request->is_online ?? false,
            'photo' => $photoPath,
            'email' => $request->email,
        ]);
        return response()->json($staff, 201);
    });

    Route::post('/staff/{id}/', function (Request $request, $id) {
        $user = $request->user();
        $staff = \App\Models\VenueStaff::where('venue_id', $user->complex_id)->find($id);
        if (!$staff) return response()->json(['message' => 'Not found'], 404);

        $data = $request->only(['name', 'role', 'phone', 'shift', 'status', 'is_online', 'email', 'status_detail']);
        
        if ($request->hasFile('photo')) {
            if ($staff->photo && \Illuminate\Support\Facades\Storage::disk('public')->exists($staff->photo)) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($staff->photo);
            }
            $data['photo'] = $request->file('photo')->store('staff', 'public');
        }

        $staff->update($data);
        return response()->json($staff);
    });

    Route::delete('/staff/{id}/', function (Request $request, $id) {
        $user = $request->user();
        $staff = \App\Models\VenueStaff::where('venue_id', $user->complex_id)->find($id);
        if (!$staff) return response()->json(['message' => 'Not found'], 404);

        if ($staff->photo && \Illuminate\Support\Facades\Storage::disk('public')->exists($staff->photo)) {
            \Illuminate\Support\Facades\Storage::disk('public')->delete($staff->photo);
        }

        $staff->delete();
        return response()->json(['message' => 'Deleted']);
    });

    // Booking Actions
    Route::patch('/bookings/{id}/update_status/', [BookingController::class, 'updateStatus']);
    Route::patch('/bookings/{id}/update_payment/', [BookingController::class, 'updatePayment']);
    Route::patch('/bookings/{id}/cancel/', [BookingController::class, 'cancel']);


    // Reports
    Route::get('/dashboard/revenue-report/', [DashboardController::class, 'revenueReport']);
    Route::get('/dashboard/booking-report/', [DashboardController::class, 'bookingReport']);
    Route::get('/dashboard/sports-revenue/', [DashboardController::class, 'sportsRevenue']);
    Route::get('/dashboard/performance/', [DashboardController::class, 'performance']);
    Route::get('/dashboard/export-csv/', [DashboardController::class, 'exportCSV']);

    // Notifications
    Route::get('/notifications/', function (Request $request) {
        $user = $request->user();
        $pageSize = (int)($request->query('page_size', 20));
        if ($pageSize <= 0) $pageSize = 20;
        $pageSize = min($pageSize, 100);

        $query = \App\Models\BookingNotification::where('user_id', $user->id);
        $notifications = $query
            ->latest()
            ->paginate($pageSize);

        $results = collect($notifications->items())->map(function ($item) {
            return [
                'id' => $item->id,
                'type' => $item->type,
                'title' => $item->title,
                'message' => $item->message,
                'data' => is_array($item->data) ? $item->data : (json_decode($item->data ?? '{}', true) ?: []),
                'is_read' => (bool)$item->is_read,
                'created_at' => optional($item->created_at)->toDateTimeString(),
            ];
        })->values();

        return response()->json([
            'count' => $notifications->total(),
            'page' => $notifications->currentPage(),
            'page_size' => $notifications->perPage(),
            'results' => $results,
            'unread_count' => (int)$query->where('is_read', false)->count(),
        ]);
    });

    Route::get('/notifications/unread-count/', function (Request $request) {
        $user = $request->user();
        $unreadCount = \App\Models\BookingNotification::where('user_id', $user->id)
            ->where('is_read', false)
            ->count();

        return response()->json([
            'unread_count' => (int)$unreadCount,
        ]);
    });

    Route::post('/notifications/{id}/mark-read/', function (Request $request, $id) {
        $user = $request->user();
        $notification = \App\Models\BookingNotification::where('user_id', $user->id)->find($id);
        if (!$notification) return response()->json(['message' => 'Not found'], 404);

        $notification->update(['is_read' => true]);
        return response()->json([
            'message' => 'Marked as read',
            'notification' => [
                'id' => $notification->id,
                'is_read' => (bool)$notification->is_read,
            ],
        ]);
    });

    Route::post('/notifications/mark-all-read/', function (Request $request) {
        $user = $request->user();
        $updated = \App\Models\BookingNotification::where('user_id', $user->id)
            ->where('is_read', false)
            ->update(['is_read' => true]);

        return response()->json([
            'message' => 'All marked as read',
            'updated_count' => (int)$updated,
            'unread_count' => 0,
        ]);
    });

    Route::delete('/notifications/{id}/', function (Request $request, $id) {
        $user = $request->user();
        $notification = \App\Models\BookingNotification::where('user_id', $user->id)->find($id);
        if (!$notification) return response()->json(['message' => 'Not found'], 404);

        $deletedId = $notification->id;
        $notification->delete();

        $unreadCount = \App\Models\BookingNotification::where('user_id', $user->id)
            ->where('is_read', false)
            ->count();

        return response()->json([
            'message' => 'Deleted',
            'deleted_id' => $deletedId,
            'unread_count' => (int)$unreadCount,
        ]);
    });

    // Applications
    Route::get('/applications/', function (Request $request) {
        return response()->json(['results' => []]);
    });


});

