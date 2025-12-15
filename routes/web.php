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
    });


    //!! Staff routes
    Route::middleware('role:staff')->prefix('staff')->name('staff.')->group(function () {
        Route::get('/dashboard', StaffDashboard::class)->name('dashboard');
        Route::get('/sports', SportsManagement::class)->name('sports');
        Route::get('/bookings', BookingsManagement::class)->name('bookings');
        Route::get('/reports', StaffReport::class)->name('reports');
        Route::get('/feedbacks', StaffFeedbacks::class)->name('feedbacks');
        Route::get('/setting', StaffSetting::class)->name('setting');
        Route::get('/help', StaffHelp::class)->name('help');
    });

    Route::middleware('role:facility_owner')->prefix('facility_owner')->name('staff.')->group(function () {
        Route::get('/dashboard', StaffDashboard::class)->name('dashboard');
        Route::get('/sports', SportsManagement::class)->name('sports');
        Route::get('/bookings', BookingsManagement::class)->name('bookings');
        Route::get('/reports', StaffReport::class)->name('reports');
        Route::get('/feedbacks', StaffFeedbacks::class)->name('feedbacks');
        Route::get('/setting', StaffSetting::class)->name('setting');
        Route::get('/help', StaffHelp::class)->name('help');
    });


    // !! Export routes (accessible to authenticated users)


});

// WebSocket test route
Route::get('/test-websocket', function () {
    return view('test-websocket');
})->name('test.websocket');
