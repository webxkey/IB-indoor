<?php

use Illuminate\Http\Request;
use App\Livewire\CustomLogin;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Livewire\Admin\Indoors;
use App\Livewire\Admin\Bookings;
use App\Livewire\Admin\Customers;
use App\Livewire\Admin\IndoorAdmins;
use App\Livewire\Admin\AdminDashboard;
use App\Livewire\Staff\StaffDashboard;
use App\Livewire\Staff\SportsManagement;
use App\Livewire\Staff\BookingsManagement;
use App\Livewire\Staff\StaffFeedbacks;
use App\Livewire\Staff\StaffHelp;
use App\Livewire\Staff\StaffReport;
use App\Livewire\Staff\StaffSetting;
use App\Livewire\Staff\TournamentManagement;
use App\Livewire\LandingPage\Home;
use App\Livewire\LandingPage\About;
use App\Livewire\LandingPage\Contact;
use App\Livewire\LandingPage\Indoor;
use App\Livewire\LandingPage\Register;
use App\Livewire\Admin\LandingPage;
use App\Livewire\Admin\LandingPageCMS;
use App\Livewire\LandingPage\Blog;
use App\Livewire\LandingPage\Features;
use App\Livewire\LandingPage\TermsOfService;
use App\Livewire\LandingPage\Security;
use App\Livewire\LandingPage\ForBusiness;
use App\Livewire\LandingPage\Privacy;
use App\Livewire\LandingPage\Careers;
use App\Livewire\Admin\BlogsManagement;
use App\Livewire\Admin\TournamentApprovals;
use App\Livewire\Admin\AdminSettings;
use App\Livewire\Admin\Announcements as AdminAnnouncements;
use App\Livewire\Staff\Announcements as StaffAnnouncements;

// Note: PollController removed - using WebSocket real-time updates instead

// Landing Page Routes

Route::get('/', Home::class)->name('home');
Route::get('/indoor', Indoor::class)->name('indoor');
Route::get('/about', About::class)->name('about');
Route::get('/contact', Contact::class)->name('contact');
Route::get('/register', Register::class)->name('register');
Route::get('/terms-of-service', TermsOfService::class)->name('terms-of-service');
Route::get('/features', Features::class)->name('features');
Route::get('/blog', Blog::class)->name('blog');
Route::get('/security', Security::class)->name('security');
Route::get('/for-business', ForBusiness::class)->name('for-business');
Route::get('/privacy', Privacy::class)->name('privacy');
Route::get('/careers', Careers::class)->name('careers');

// Customer login alias (same CustomLogin component, guest only)
Route::get('/customer/login', CustomLogin::class)->name('customer.login')->middleware('guest');

// Customer portal
Route::middleware(['auth:sanctum', config('jetstream.auth_session')])->group(function () {
    Route::get('/my-bookings', \App\Livewire\Customer\MyBookings::class)->name('customer.my-bookings');
    Route::get('/my-bookings/receipt/{bookingId}', [\App\Http\Controllers\CustomerReceiptController::class, 'download'])->name('customer.receipt');
});

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

// Public routes
Route::get('/login', CustomLogin::class)->name('login')->middleware('guest');

// Custom logout route
Route::post('/logout', function (Request $request) {
    Auth::logout();
    $request->session()->invalidate();
    $request->session()->regenerateToken();
    return redirect('/');
})->name('logout');

// Dashboard redirect — required by Jetstream (e.g. after profile save)
Route::middleware(['auth:sanctum', config('jetstream.auth_session')])->get('/dashboard', function () {
    $role = auth()->user()->role ?? '';
    if ($role === 'admin') return redirect()->route('admin.dashboard');
    if (in_array($role, ['staff', 'facility_owner'])) return redirect()->route('staff.dashboard');
    return redirect('/');
})->name('dashboard');

// Routes that require authentication
Route::middleware(['auth:sanctum', config('jetstream.auth_session'), 'verified'])->group(function () {

    // !! Admin routes
    Route::middleware('role:admin')->prefix('admin')->name('admin.')->group(function () {
        Route::get('/dashboard', AdminDashboard::class)->name('dashboard');
        Route::get('/indoors', Indoors::class)->name('indoors');
        Route::get('/bookings', Bookings::class)->name('bookings');
        Route::get('/customers', Customers::class)->name('customers');
        Route::get('/indoor-admins', IndoorAdmins::class)->name('indoor-admins');
        Route::get('/landing-page', LandingPageCMS::class)->name('landing-page');
        Route::get('/blogs-management', BlogsManagement::class)->name('blogs-management');
        Route::get('/tournaments', TournamentApprovals::class)->name('tournaments');
        Route::get('/announcements', AdminAnnouncements::class)->name('announcements');
        Route::get('/settings', AdminSettings::class)->name('settings');
    });


    //!! Staff & Facility Owner routes (both roles share the same URLs and route names)
    Route::middleware('role:staff,facility_owner')->prefix('staff')->name('staff.')->group(function () {
        Route::get('/dashboard', StaffDashboard::class)->name('dashboard');
        Route::get('/sports', SportsManagement::class)->name('sports');
        Route::get('/bookings', BookingsManagement::class)->name('bookings');
        Route::get('/reports', StaffReport::class)->name('reports');
        Route::get('/feedbacks', StaffFeedbacks::class)->name('feedbacks');
        Route::get('/setting', StaffSetting::class)->name('setting');
        Route::get('/tournament', TournamentManagement::class)->name('tournament');
        Route::get('/announcements', StaffAnnouncements::class)->name('announcements');
        Route::get('/help', StaffHelp::class)->name('help');

        // Global notification polling endpoint
        Route::get('/notifications/poll', function (Request $request) {
            $since = $request->query('since'); // ISO timestamp or null
            $userId = auth()->id();

            $query = \App\Models\BookingNotification::where('user_id', $userId)
                ->where('is_read', false)
                ->orderByDesc('created_at')
                ->limit(10);

            if ($since) {
                try {
                    $query->where('created_at', '>', \Carbon\Carbon::parse($since)->timezone(config('app.timezone')));
                } catch (\Exception $e) {
                    // Fallback to basic string comparison if parsing fails
                    $query->where('created_at', '>', $since);
                }
            }

            $notifications = $query->get()->map(fn($n) => [
                'id'         => $n->id,
                'type'       => $n->type,
                'title'      => $n->title,
                'message'    => $n->message,
                'created_at' => $n->created_at,
            ]);

            return response()->json([
                'notifications' => $notifications,
                'unread_count'  => \App\Models\BookingNotification::where('user_id', $userId)->where('is_read', false)->count(),
                'server_time'   => now()->toISOString(),
            ]);
        })->name('notifications.poll');

        // Mark all notifications as read
        Route::post('/notifications/mark-read', function () {
            \App\Models\BookingNotification::where('user_id', auth()->id())
                ->where('is_read', false)
                ->update(['is_read' => true]);
            return response()->json(['ok' => true]);
        })->name('notifications.mark-read');
    });


    // !! Export routes (accessible to authenticated users)


});

// Real-time WebSocket updates via Laravel Reverb
// Old polling system removed - now using efficient persistent WebSocket connections
// No more /poll endpoint - all real-time updates come via WebSocket broadcasts

// ─────────────────────────────────────────────────────────────────────────────
// TEMPORARY TEST ROUTES FOR UI DEMONSTRATION
// Visit these URLs in a new tab while your dashboard is open to see live updates
// ─────────────────────────────────────────────────────────────────────────────

Route::get('/ws-test/hold', function () {
    $sport = BookingSport::where('venue_id', 2)->first();
    if (!$sport) return "Error: No active sport found for venue 2";

    $data = [
        'venue_id'   => 2,
        'sport_id'   => $sport->id,
        'event_type' => 'slot.hold.created',
        'date'       => now()->format('Y-m-d'),
        'start_time' => '11:00:00',
        'court'      => '1'
    ];

    broadcast(new SlotStateChanged($data));
    return "✅ [SLOT HOLD] event broadcasted for {$sport->name} at 11:00 AM. Check your dashboard!";
});

Route::get('/ws-test/release', function () {
    $sport = BookingSport::where('venue_id', 2)->first();
    if (!$sport) return "Error: No active sport found for venue 2";

    $data = [
        'venue_id'   => 2,
        'sport_id'   => $sport->id,
        'event_type' => 'slot.hold.released',
        'date'       => now()->format('Y-m-d'),
        'start_time' => '11:00:00',
        'court'      => '1'
    ];

    broadcast(new SlotStateChanged($data));
    return "✅ [HOLD RELEASED] event broadcasted. The orange pulse should disappear.";
});

Route::get('/ws-test/booking', function () {
    // We'll mock a booking created event
    $booking = BookingBooking::where('complex_id_id', 2)->latest()->first();
    if (!$booking) return "Error: No existing booking found to use as template";

    broadcast(new BookingCreated($booking));
    return "✅ [NEW BOOKING] event broadcasted for {$booking->user_name}. You should see a success toast and sound!";
});
