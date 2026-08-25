<?php

namespace App\Livewire\Staff;

use App\Models\BookingBooking;
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
    public $complex_id;

    protected $rules = [
        'period' => 'required|in:daily,weekly,monthly',
    ];

    public function mount()
    {
        $this->complex_id = Auth::user()->complex_id;
        $this->sports = BookingSport::where('venue_id', $this->complex_id)->select('id', 'name')->get();
        $this->generateReport();
        $this->loadComplexDetails();
    }

    public function loadComplexDetails()
    {
        // Get the current user's complex
        $complex = BookingVenue::find($this->complex_id);
        $this->complexName = $complex->name ?? 'Default Complex';
        $this->complexAddress = $complex->address ?? 'N/A';
        $this->complexPhoto = $complex->cover_image ? asset('storage/' . $complex->cover_image) : asset('fd.jpg');
    }

    private function loadReportData()
    {
        if ($this->activeReport === 'booking') {
            $this->bookingDetail = BookingBooking::with('sport')
                ->where('complex_id_id', $this->complex_id)
                ->whereBetween('booking_date', [$this->start_date, $this->end_date])
                ->orderBy('booking_date', 'desc')
                ->get();
        }
    }
    public function generateReport()
    {
        $this->validate();

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

        // Filter by current venue/complex
        $query = BookingBooking::query()
            ->where('complex_id_id', $this->complex_id)
            ->whereBetween('booking_date', [$this->start_date, $this->end_date]);

        $this->reportData = $query->get();

        $this->bookingDetails = BookingBooking::where('complex_id_id', $this->complex_id)->get();
        $this->bookingDetailModel = $query->clone()->with('sport')->get();
        $this->upcomingBooked = $query->clone()->whereIn('status', ['Confirmed'])->get();

        $this->totalUpcomingBookings = $this->upcomingBooked->count();
        $this->totalBookings = $this->bookingDetails->count();
        $this->totalRevenue = BookingBooking::where('complex_id_id', $this->complex_id)
            ->where('status', 'Completed')->sum('price');
        $this->cancelledBookings = BookingBooking::where('complex_id_id', $this->complex_id)
            ->where('status', 'Cancelled')->count();

        // Occupancy rate calculation
        $sports = BookingSport::where('venue_id', $this->complex_id)->get();
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

        $query = BookingBooking::query()
            ->selectRaw("CASE 
                    WHEN ? = 'day' THEN to_char(booking_date::date, 'YYYY-MM-DD')
                    WHEN ? = 'week' THEN to_char(date_trunc('week', booking_date), 'IYYYIW')
                    ELSE to_char(booking_date, 'YYYY-MM')
                END as period,
                SUM(price) as total_revenue,
                COUNT(*) as total_bookings", [$aggregation, $aggregation])
            ->where('complex_id_id', $this->complex_id)
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
        $startDate = \Carbon\Carbon::parse($this->start_date)->startOfDay()->toDateTimeString();
        $endDate = \Carbon\Carbon::parse($this->end_date)->endOfDay()->toDateTimeString();

        $this->revenueReportData = BookingBooking::where('complex_id_id', $this->complex_id)
            ->whereBetween('booking_date', [$startDate, $endDate])
            ->with('sport')
            ->selectRaw("
                game_id_id,
                court_number,
                COUNT(*) as total_bookings,
                SUM(price) as total_revenue,
                AVG(price) as average_revenue,
                SUM((EXTRACT(EPOCH FROM end_time) - EXTRACT(EPOCH FROM start_time)) / 3600) as total_hours
            ")
            ->groupBy('game_id_id', 'court_number')
            ->get()
            ->map(function ($revenue) {
                return [
                    'sport_name'     => $revenue->sport ? $revenue->sport->name : 'N/A',
                    'court_number'   => $revenue->court_number ?? 'N/A',
                    'total_bookings' => $revenue->total_bookings,
                    'total_hours'    => round($revenue->total_hours, 2) ?? 0,
                    'total_revenue'  => $revenue->total_revenue ?? 0,
                    'average_revenue' => $revenue->average_revenue ?? 0,
                ];
            })
            ->values()
            ->all();

        $this->dispatch('openRevenueModal');
    }




    public function exportReport()
    {
        try {
            $data = BookingBooking::where('complex_id_id', $this->complex_id)
                ->whereBetween('booking_date', [$this->start_date, $this->end_date])
                ->with('sport')
                ->get();

            $csvContent = "Booking ID,Player Name,Court,Sport,Date,Start Time,End Time,Status,Payment Status,Revenue\n";
            foreach ($data as $booking) {
                $csvContent .= implode(',', [
                    $booking->id,
                    '"' . ($booking->user_name ?? 'N/A') . '"',
                    '"' . ($booking->court_number ?? 'N/A') . '"',
                    '"' . ($booking->sport->name ?? $booking->game_name ?? 'N/A') . '"',
                    $booking->booking_date,
                    $booking->start_time,
                    $booking->end_time,
                    $booking->status,
                    $booking->payment_status,
                    $booking->price ?? 0,
                ]) . "\n";
            }

            $filename = 'booking_report_' . $this->start_date . '_to_' . $this->end_date . '.csv';

            return response()->streamDownload(function () use ($csvContent) {
                echo $csvContent;
            }, $filename, ['Content-Type' => 'text/csv']);
        } catch (\Exception $e) {
            session()->flash('error', 'Export failed: ' . $e->getMessage());
        }
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
