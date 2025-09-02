<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Illuminate\Support\Facades\DB;
use App\Models\BookingBooking;
use App\Models\BookingVenue;
use App\Models\BookingSport;
use App\Models\User;
use Carbon\Carbon;

#[Layout('components.layouts.admin')]
#[Title('Dashboard')]
class AdminDashboard extends Component
{
    public $totalIndoors;
    public $totalBookings;
    public $totalUsers;
    public $pendingRequests;
    public $bookingAnalytics;
    public $upcomingBookings;

    public function mount()
    {
        // Get total venues (indoors)
        $this->totalIndoors = BookingVenue::count();

        // Get total bookings
        $this->totalBookings = BookingBooking::count();

        // Get total users
        $this->totalUsers = User::count();

        // Get pending requests (bookings with pending status)
        $this->pendingRequests = BookingBooking::where('status', 'pending')->count();

        // Get booking analytics data (last 6 months)
        $this->bookingAnalytics = $this->getBookingAnalytics();

        // Get upcoming bookings (next 7 days)
        $this->upcomingBookings = $this->getUpcomingBookings();
    }

    private function getBookingAnalytics()
    {
        $sixMonthsAgo = Carbon::now()->subMonths(5)->startOfMonth();
        $currentMonth = Carbon::now()->endOfMonth();
        
        $analyticsData = BookingBooking::whereBetween('booking_date', [$sixMonthsAgo, $currentMonth])
            ->select(
                DB::raw('YEAR(booking_date) as year'),
                DB::raw('MONTH(booking_date) as month'),
                DB::raw('COUNT(*) as total_bookings')
            )
            ->groupBy('year', 'month')
            ->orderBy('year', 'asc')
            ->orderBy('month', 'asc')
            ->get();

        // Format the data for the chart
        $labels = [];
        $data = [];
        
        $current = $sixMonthsAgo->copy();
        while ($current <= $currentMonth) {
            $monthYear = $current->format('M Y');
            $found = $analyticsData->first(function ($item) use ($current) {
                return $item->year == $current->year && $item->month == $current->month;
            });
            
            $labels[] = $monthYear;
            $data[] = $found ? $found->total_bookings : 0;
            
            $current->addMonth();
        }
        
        return [
            'labels' => $labels,
            'data' => $data
        ];
    }

    private function getUpcomingBookings()
    {
        return BookingBooking::with(['sport', 'venue'])
            ->where('booking_date', '>=', Carbon::today())
            ->orderBy('booking_date', 'asc')
            ->orderBy('start_time', 'asc')
            ->limit(3)
            ->get();
    }

    public function render()
    {
        return view('livewire.admin.admin-dashboard', [
            'totalIndoors' => $this->totalIndoors,
            'totalBookings' => $this->totalBookings,
            'totalUsers' => $this->totalUsers,
            'pendingRequests' => $this->pendingRequests,
            'bookingAnalytics' => $this->bookingAnalytics,
            'upcomingBookings' => $this->upcomingBookings
        ]);
    }
}