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
    public $totalRevenue;
    public $bookingAnalytics;
    public $upcomingBookings;

    public $revenueChartData;
    public $statusChartData;
    public $sportPopularityData;
    public $occupancyChartData;

    public function mount()
    {
        // Get total venues (indoors)
        $this->totalIndoors = BookingVenue::count();

        // Get total bookings
        $this->totalBookings = BookingBooking::count();

        // Get total users
        $this->totalUsers = User::count();

        // Get pending requests (bookings with pending status)
        $this->pendingRequests = BookingBooking::where('status', 'Pending')->count();

        // Get total revenue from completed bookings
        $this->totalRevenue = BookingBooking::where('status', 'Completed')->sum('price');

        // Get booking analytics data (last 6 months)
        $this->bookingAnalytics = $this->getBookingAnalytics();

        // Get upcoming bookings (next 7 days)
        $this->upcomingBookings = $this->getUpcomingBookings();

        // New chart data
        $this->revenueChartData = $this->getRevenueTrend();
        $this->statusChartData = $this->getStatusDistribution();
        $this->sportPopularityData = $this->getSportPopularity();
        $this->occupancyChartData = $this->getOccupancyData();
    }

    private function getBookingAnalytics()
    {
        $sixMonthsAgo = Carbon::now()->subMonths(5)->startOfMonth();
        $currentMonth = Carbon::now()->endOfMonth();

        $analyticsData = BookingBooking::whereBetween('booking_date', [$sixMonthsAgo, $currentMonth])
            ->select(
                DB::raw('EXTRACT(YEAR FROM booking_date) as year'),
                DB::raw('EXTRACT(MONTH FROM booking_date) as month'),
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

    private function getRevenueTrend()
    {
        $thirtyDaysAgo = Carbon::now()->subDays(29)->startOfDay();
        $today = Carbon::now()->endOfDay();

        $revenueData = BookingBooking::where('status', 'Completed')
            ->whereBetween('booking_date', [$thirtyDaysAgo, $today])
            ->select(
                DB::raw('DATE(booking_date) as date'),
                DB::raw('SUM(price) as total_revenue')
            )
            ->groupBy(DB::raw('DATE(booking_date)'))
            ->orderBy(DB::raw('DATE(booking_date)'), 'asc')
            ->get()
            ->keyBy('date');

        $labels = [];
        $data = [];

        for ($i = 29; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i)->format('Y-m-d');
            $labels[] = Carbon::now()->subDays($i)->format('M d');
            $data[] = isset($revenueData[$date]) ? (float) $revenueData[$date]->total_revenue : 0;
        }

        return [
            'labels' => $labels,
            'data' => $data,
        ];
    }

    private function getStatusDistribution()
    {
        $statuses = ['Confirmed', 'Cancelled', 'Completed', 'Pending', 'Playing', 'No-Show'];
        $colors = [
            'Confirmed'  => 'rgba(54, 162, 235, 0.8)',
            'Cancelled'  => 'rgba(255, 99, 132, 0.8)',
            'Completed'  => 'rgba(75, 192, 192, 0.8)',
            'Pending'    => 'rgba(255, 205, 86, 0.8)',
            'Playing'    => 'rgba(153, 102, 255, 0.8)',
            'No-Show'    => 'rgba(255, 159, 64, 0.8)',
        ];

        $counts = BookingBooking::select('status', DB::raw('COUNT(*) as total'))
            ->groupBy('status')
            ->get()
            ->keyBy('status');

        $labels = [];
        $data = [];
        $colorList = [];

        foreach ($statuses as $status) {
            $labels[] = $status;
            $data[] = isset($counts[$status]) ? (int) $counts[$status]->total : 0;
            $colorList[] = $colors[$status];
        }

        return [
            'labels' => $labels,
            'data'   => $data,
            'colors' => $colorList,
        ];
    }

    private function getSportPopularity()
    {
        $sports = BookingBooking::where('status', '!=', 'Cancelled')
            ->select('game_name', DB::raw('COUNT(*) as total'))
            ->groupBy('game_name')
            ->orderBy('total', 'desc')
            ->limit(5)
            ->get();

        $labels = [];
        $data = [];

        foreach ($sports as $sport) {
            $labels[] = $sport->game_name ?? 'Unknown';
            $data[] = (int) $sport->total;
        }

        return [
            'labels' => $labels,
            'data'   => $data,
        ];
    }

    private function getOccupancyData()
    {
        $sevenDaysAgo = Carbon::now()->subDays(6)->startOfDay();
        $today = Carbon::now()->endOfDay();

        $occupancy = BookingBooking::where('status', '!=', 'Cancelled')
            ->whereBetween('booking_date', [$sevenDaysAgo, $today])
            ->select(
                DB::raw('DATE(booking_date) as date'),
                DB::raw('COUNT(*) as total')
            )
            ->groupBy(DB::raw('DATE(booking_date)'))
            ->orderBy(DB::raw('DATE(booking_date)'), 'asc')
            ->get()
            ->keyBy('date');

        $labels = [];
        $data = [];

        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i)->format('Y-m-d');
            $labels[] = Carbon::now()->subDays($i)->format('D, M d');
            $data[] = isset($occupancy[$date]) ? (int) $occupancy[$date]->total : 0;
        }

        return [
            'labels' => $labels,
            'data'   => $data,
        ];
    }

    public function render()
    {
        return view('livewire.admin.admin-dashboard', [
            'totalIndoors'       => $this->totalIndoors,
            'totalBookings'      => $this->totalBookings,
            'totalUsers'         => $this->totalUsers,
            'pendingRequests'    => $this->pendingRequests,
            'totalRevenue'       => $this->totalRevenue,
            'bookingAnalytics'   => $this->bookingAnalytics,
            'upcomingBookings'   => $this->upcomingBookings,
            'revenueChartData'   => $this->revenueChartData,
            'statusChartData'    => $this->statusChartData,
            'sportPopularityData'=> $this->sportPopularityData,
            'occupancyChartData' => $this->occupancyChartData,
        ]);
    }
}
