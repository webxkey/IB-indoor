<?php

namespace App\Livewire\Staff;

use App\Models\Booking;
use App\Models\BookingVenue;
use App\Models\BookingSport;

use Illuminate\Support\Carbon;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Illuminate\Support\Facades\Auth;

#[Title("Staff Dashboard")]
#[Layout("components.layouts.staff")]
class StaffReport extends Component
{
    public $start_date;
    public $end_date;
    public $period = 'daily';
    public $totalBookings = 0;
    public $totalUpcomingBookings = 0;
    public $totalRevenue = 0;
    public $cancelledBookings = 0;
    public $occupancyRate = 0;
    public $reportData = [];
    public $sports = [];
    public $chartData = [
        'labels' => [],
        'revenueData' => [],
        'bookingsData' => []
    ];
    public $bookingDetails;
    public $bookingDetailModel;
    public $revenueReportData = [];
    public $upcomingBooked;
    public $UserId;
    public $activeReport = null;
    public $bookingDetail = [];
    public $totalHours = 0;
    public $bookedHours = 0;
    public $complexName;
    public $complexAddress;
    public $complexPhoto;

    protected $rules = [
        'period' => 'required|in:daily,weekly,monthly',
    ];

    public function mount()
    {
        $this->sports = BookingSport::select('id', 'name')->get();
        $this->generateReport();
        $this->loadComplexDetails();
        
    }

    public function loadComplexDetails()
    {
        // Assuming BookingVenue model has name, address, and photo fields
        $complex = BookingVenue::first(); // Adjust based on your logic (e.g., get complex by user or specific ID)
        $this->complexName = $complex->complex_name ?? 'Default Complex';
        $this->complexAddress = $complex->address ?? 'N/A';
        $this->complexPhoto = $complex->game_image ? asset('storage/' . $complex->game_image) : asset('fd.jpg');
    }

    private function loadReportData()
    {
        if ($this->activeReport === 'booking') {
            $this->bookingDetail = Booking::with('sport')
                ->where('user_id', $this->UserId)
                ->whereBetween('booking_date', [$this->start_date, $this->end_date])
                ->orderBy('booking_date', 'desc')
                ->get();
        }
    }
    public function generateReport()
    {
        $this->validate();

        $this->UserId = Auth::id();

        // Set date range based on period
        $end = Carbon::today();
        if ($this->period == 'daily') {
            $this->start_date = $end->copy()->subDays(6)->toDateString();
            $aggregation = 'day';
        } elseif ($this->period == 'weekly') {
            $this->start_date = $end->copy()->subWeeks(3)->startOfWeek()->toDateString();
            $aggregation = 'week';
        } else { // monthly
            $this->start_date = $end->copy()->subMonths(11)->startOfMonth()->toDateString();
            $aggregation = 'month';
        }
        $this->end_date = $end->toDateString();

        $query = Booking::query()
            ->where('user_id', $this->UserId)
            ->whereBetween('booking_date', [$this->start_date, $this->end_date]);

        $this->reportData = $query->get();

        $this->bookingDetails = Booking::all();
        dd(Booking::all());
        $this->bookingDetailModel = $query->clone()->get();
        $this->upcomingBooked = $query->clone()->whereIn('status', ['Booked', 'Upcoming'])->get();

        $this->totalUpcomingBookings = $this->upcomingBooked->count();
        $this->totalBookings = $this->bookingDetails->count();
        $this->totalRevenue = Booking::where('status', 'Completed')->sum('price');
        $this->cancelledBookings = Booking::where('status', 'Cancelled')->count();

        // Occupancy rate calculation
        $sports = BookingSport::all();
        $days = Carbon::parse($this->end_date)->diffInDays(Carbon::parse($this->start_date)) + 1;
        $totalHours = 0;
        $bookedHours = 0;

        foreach ($sports as $sport) {
            $totalHours += $sport->maximum_court * $days * 17; // Assuming 17 hours per day
        }

        foreach ($this->reportData as $booking) {
            $start = Carbon::parse($booking->start_time);
            $end = Carbon::parse($booking->end_time);
            $bookedHours += $end->diffInHours($start);
        }

        $this->occupancyRate = $totalHours > 0 ? ($bookedHours / $totalHours) * 100 : 0;

        $this->prepareChartData($aggregation);

        $this->loadReportData();
        $this->loadRevenueReport();
    }

    public function prepareChartData($aggregation)
    {
        $start = Carbon::parse($this->start_date);
        $end = Carbon::parse($this->end_date);
        $labels = [];
        $revenueData = [];
        $bookingsData = [];

        $query = Booking::query()
            ->selectRaw("
                CASE 
                    WHEN ? = 'day' THEN DATE(booking_date)
                    WHEN ? = 'week' THEN YEARWEEK(booking_date, 1)
                    ELSE DATE_FORMAT(booking_date, '%Y-%m')
                END as period,
                SUM(price) as total_revenue,
                COUNT(*) as total_bookings
            ", [$aggregation, $aggregation])
            ->where('user_id', $this->UserId)
            ->whereBetween('booking_date', [$start, $end])
            ->groupBy('period');

        $results = $query->get()->keyBy('period');

        if ($aggregation === 'day') {
            for ($date = $start->copy(); $date <= $end; $date->addDay()) {
                $periodStr = $date->format('Y-m-d');
                $labels[] = $date->format('M d');
                $revenueData[] = isset($results[$periodStr]) ? (float)$results[$periodStr]->total_revenue : 0;
                $bookingsData[] = isset($results[$periodStr]) ? (int)$results[$periodStr]->total_bookings : 0;
            }
        } elseif ($aggregation === 'week') {
            for ($date = $start->copy(); $date <= $end; $date->addWeek()) {
                $periodStr = $date->format('oW');
                $labels[] = $date->format('M d') . ' - ' . $date->copy()->endOfWeek()->format('M d');
                $revenueData[] = isset($results[$periodStr]) ? (float)$results[$periodStr]->total_revenue : 0;
                $bookingsData[] = isset($results[$periodStr]) ? (int)$results[$periodStr]->total_bookings : 0;
            }
        } else {
            for ($date = $start->copy(); $date <= $end; $date->addMonth()) {
                $periodStr = $date->format('Y-m');
                $labels[] = $date->format('M Y');
                $revenueData[] = isset($results[$periodStr]) ? (float)$results[$periodStr]->total_revenue : 0;
                $bookingsData[] = isset($results[$periodStr]) ? (int)$results[$periodStr]->total_bookings : 0;
            }
        }

        $this->chartData = [
            'labels' => $labels,
            'revenueData' => $revenueData,
            'bookingsData' => $bookingsData
        ];

        $this->dispatch('update-chart', data: $this->chartData);
    }

    public function loadRevenueReport()
    {
        $userId = $this->UserId ?? auth()->id();

        $startDate = \Carbon\Carbon::parse($this->start_date)->startOfDay()->toDateTimeString();
        $endDate = \Carbon\Carbon::parse($this->end_date)->endOfDay()->toDateTimeString();

        $this->revenueReportData = Booking::where('user_id', $userId)
            ->whereBetween('booking_date', [$startDate, $endDate])
            ->with('sport')
            ->selectRaw("
                game_id,
                court_number,
                COUNT(*) as total_bookings,
                SUM(price) as total_revenue,
                AVG(price) as average_revenue,
                SUM((TIME_TO_SEC(end_time) - TIME_TO_SEC(start_time)) / 3600) as total_hours
            ")
            ->groupBy('game_id', 'court_number')
            ->get()
            ->map(function ($revenue) {
                return (object) [
                    'sport_name'     => $revenue->sport ? $revenue->sport->name : 'N/A',
                    'court_number'   => $revenue->court_number ?? 'N/A',
                    'total_bookings' => $revenue->total_bookings,
                    'total_hours'    => round($revenue->total_hours, 2) ?? 0,
                    'total_revenue'  => $revenue->total_revenue ?? 0,
                    'average_revenue'=> $revenue->average_revenue ?? 0,
                ];
            })
            ->values();

        $this->dispatch('openRevenueModal');
    }
    


   
    public function resetFilters()
    {
        $this->period = 'daily';
        $this->generateReport();
    }

    public function render()
    {
        return view('livewire.staff.staff-report');
    }
}