<?php

namespace App\Livewire\Staff;

use App\Models\BookingBooking;
use App\Models\BookingVenue;
use App\Models\BookingSport;
use App\Models\PoolsPool;
use App\Models\PoolsPoolbooking;

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
    public $complexPhone;
    public $complexEmail;
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
        $this->complexPhone = $complex->contact_number ?? (Auth::user()->contact ?? null);
        $this->complexEmail = $complex->email_address ?? (Auth::user()->email ?? null);
        $this->complexPhoto = $complex->cover_image ? asset('storage/' . $complex->cover_image) : asset('fd.jpg');
    }

    private function getCombinedBookings()
    {
        // 1. Regular Bookings
        $regularBookings = BookingBooking::with('sport')
            ->where('complex_id_id', $this->complex_id)
            ->whereBetween('booking_date', [$this->start_date, $this->end_date])
            ->get();

        $normalized = collect();

        foreach ($regularBookings as $b) {
            $sportName = $b->sport ? $b->sport->name : ($b->game_name ?? 'N/A');
            $startTimeStr = $b->start_time ? (strlen($b->start_time) === 5 ? $b->start_time . ':00' : $b->start_time) : '00:00:00';
            $endTimeStr = $b->end_time ? (strlen($b->end_time) === 5 ? $b->end_time . ':00' : $b->end_time) : '00:00:00';

            $normalized->push((object)[
                'id'             => $b->id,
                'raw_id'         => $b->id,
                'user_name'      => $b->user_name ?: 'N/A',
                'user_number'    => $b->user_number ?: 'N/A',
                'court_number'   => $b->court_number ?: 'Court N/A',
                'game_name'      => $sportName,
                'sport'          => (object)['name' => $sportName],
                'booking_date'   => Carbon::parse($b->booking_date)->toDateString(),
                'start_time'     => $startTimeStr,
                'end_time'       => $endTimeStr,
                'status'         => $b->status ?? 'Confirmed',
                'payment_status' => $b->payment_status ?? ($b->financial_status ?? 'N/A'),
                'price'          => (float)($b->price > 0 ? $b->price : $b->amount_paid),
                'type'           => 'regular',
            ]);
        }

        // 2. Pool Bookings
        $poolBookings = PoolsPoolbooking::with(['pool', 'occurrence', 'user'])
            ->whereHas('pool', function ($q) {
                $q->where('venue_id', $this->complex_id);
            })
            ->where(function ($q) {
                $q->whereHas('occurrence', function ($occQ) {
                    $occQ->whereBetween('session_date', [$this->start_date, $this->end_date]);
                })->orWhere(function ($createdQ) {
                    $createdQ->whereNull('occurrence_id')
                        ->whereBetween(\DB::raw('DATE(created_at)'), [$this->start_date, $this->end_date]);
                });
            })
            ->get();

        foreach ($poolBookings as $pb) {
            $poolName = $pb->pool ? $pb->pool->name : 'Pool';
            $userName = $pb->user_name ?: ($pb->user->name ?? 'Guest/Pool User');
            $userPhone = $pb->user_number ?: ($pb->user->phone ?? 'N/A');

            $bookingDate = $pb->occurrence
                ? Carbon::parse($pb->occurrence->session_date)->toDateString()
                : Carbon::parse($pb->created_at)->toDateString();

            $startTimeStr = $pb->occurrence
                ? (strlen($pb->occurrence->start_time) === 5 ? $pb->occurrence->start_time . ':00' : $pb->occurrence->start_time)
                : Carbon::parse($pb->created_at)->format('H:i:s');

            $endTimeStr = $pb->occurrence
                ? (strlen($pb->occurrence->end_time) === 5 ? $pb->occurrence->end_time . ':00' : $pb->occurrence->end_time)
                : Carbon::parse($pb->created_at)->addHour()->format('H:i:s');

            $price = (float)($pb->booking_total > 0 ? $pb->booking_total : $pb->amount_paid);

            $normalized->push((object)[
                'id'             => $pb->booking_reference ?? ('PB-' . $pb->id),
                'raw_id'         => $pb->id,
                'user_name'      => $userName,
                'user_number'    => $userPhone,
                'court_number'   => $poolName,
                'game_name'      => 'Pool (' . $poolName . ')',
                'sport'          => (object)['name' => 'Pool (' . $poolName . ')'],
                'booking_date'   => $bookingDate,
                'start_time'     => $startTimeStr,
                'end_time'       => $endTimeStr,
                'status'         => $pb->status ?? 'Confirmed',
                'payment_status' => $pb->financial_status ?: ($pb->payment_status ?? 'N/A'),
                'price'          => $price,
                'type'           => 'pool',
            ]);
        }

        return $normalized->sortByDesc('booking_date')->values();
    }

    private function loadReportData()
    {
        if ($this->activeReport === 'booking') {
            $this->bookingDetail = $this->getCombinedBookings();
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

        $allBookings = $this->getCombinedBookings();

        $this->reportData = $allBookings;
        $this->bookingDetails = $allBookings;
        $this->bookingDetailModel = $allBookings;
        $this->bookingDetail = $allBookings;

        $this->upcomingBooked = $allBookings->filter(function ($b) {
            return in_array(strtolower($b->status), ['confirmed', 'booked', 'upcoming']);
        });

        $this->totalUpcomingBookings = $this->upcomingBooked->count();
        $this->totalBookings = $allBookings->count();

        $this->totalRevenue = $allBookings->filter(function ($b) {
            return in_array(strtolower($b->status), ['completed', 'confirmed', 'played', 'booked']);
        })->sum('price');

        $this->cancelledBookings = $allBookings->filter(function ($b) {
            return strtolower($b->status) === 'cancelled';
        })->count();

        // Occupancy rate calculation
        $sports = BookingSport::where('venue_id', $this->complex_id)->get();
        $pools = PoolsPool::where('venue_id', $this->complex_id)->get();
        $days = Carbon::parse($this->end_date)->diffInDays(Carbon::parse($this->start_date)) + 1;
        $totalHours = 0;
        $bookedHours = 0;

        foreach ($sports as $sport) {
            $totalHours += ($sport->maximum_court ?? 1) * $days * 17; // Assuming 17 hours per day
        }
        foreach ($pools as $pool) {
            $totalHours += 1 * $days * 17; // 1 pool operating 17 hours per day
        }

        foreach ($allBookings as $booking) {
            if (strtolower($booking->status) !== 'cancelled' && $booking->start_time && $booking->end_time) {
                try {
                    $start = Carbon::parse($booking->start_time);
                    $end = Carbon::parse($booking->end_time);
                    $diff = $end->diffInMinutes($start) / 60;
                    $bookedHours += max(0, $diff);
                } catch (\Exception $e) {
                }
            }
        }

        $this->occupancyRate = $totalHours > 0 ? ($bookedHours / $totalHours) * 100 : 0;

        $this->prepareChartData($aggregation, $allBookings);

        $this->loadReportData();
        $this->loadRevenueReport($allBookings);
    }

    public function prepareChartData($aggregation, $allBookings = null)
    {
        if (!$allBookings) {
            $allBookings = $this->getCombinedBookings();
        }

        $start = Carbon::parse($this->start_date);
        $end = Carbon::parse($this->end_date);
        $labels = [];
        $revenueData = [];
        $bookingsData = [];

        $bucketRevenue = [];
        $bucketBookings = [];

        foreach ($allBookings as $b) {
            if (strtolower($b->status) === 'cancelled') {
                continue;
            }
            $bDate = Carbon::parse($b->booking_date);
            if ($bDate < $start || $bDate > $end) {
                continue;
            }

            if ($aggregation === 'day') {
                $periodKey = $bDate->format('Y-m-d');
            } elseif ($aggregation === 'week') {
                $periodKey = $bDate->format('oW');
            } else {
                $periodKey = $bDate->format('Y-m');
            }

            $bucketRevenue[$periodKey] = ($bucketRevenue[$periodKey] ?? 0) + (float)$b->price;
            $bucketBookings[$periodKey] = ($bucketBookings[$periodKey] ?? 0) + 1;
        }

        if ($aggregation === 'day') {
            for ($date = $start->copy(); $date <= $end; $date->addDay()) {
                $periodStr = $date->format('Y-m-d');
                $labels[] = $date->format('M d');
                $revenueData[] = isset($bucketRevenue[$periodStr]) ? (float)$bucketRevenue[$periodStr] : 0;
                $bookingsData[] = isset($bucketBookings[$periodStr]) ? (int)$bucketBookings[$periodStr] : 0;
            }
        } elseif ($aggregation === 'week') {
            for ($date = $start->copy(); $date <= $end; $date->addWeek()) {
                $periodStr = $date->format('oW');
                $labels[] = $date->format('M d') . ' - ' . $date->copy()->endOfWeek()->format('M d');
                $revenueData[] = isset($bucketRevenue[$periodStr]) ? (float)$bucketRevenue[$periodStr] : 0;
                $bookingsData[] = isset($bucketBookings[$periodStr]) ? (int)$bucketBookings[$periodStr] : 0;
            }
        } else {
            for ($date = $start->copy(); $date <= $end; $date->addMonth()) {
                $periodStr = $date->format('Y-m');
                $labels[] = $date->format('M Y');
                $revenueData[] = isset($bucketRevenue[$periodStr]) ? (float)$bucketRevenue[$periodStr] : 0;
                $bookingsData[] = isset($bucketBookings[$periodStr]) ? (int)$bucketBookings[$periodStr] : 0;
            }
        }

        $this->chartData = [
            'labels' => $labels,
            'revenueData' => $revenueData,
            'bookingsData' => $bookingsData
        ];

        $this->dispatch('update-chart', data: $this->chartData);
    }

    public function loadRevenueReport($allBookings = null)
    {
        if (!$allBookings) {
            $allBookings = $this->getCombinedBookings();
        }

        $groups = collect($allBookings)->groupBy(function ($item) {
            return $item->game_name . '|' . $item->court_number;
        });

        $this->revenueReportData = $groups->map(function ($items, $key) {
            $first = $items->first();
            $totalBookings = $items->count();

            $validItems = $items->filter(function ($b) {
                return strtolower($b->status) !== 'cancelled';
            });

            $totalRevenue = $validItems->sum('price');
            $totalHours = 0;

            foreach ($items as $b) {
                if ($b->start_time && $b->end_time) {
                    try {
                        $s = Carbon::parse($b->start_time);
                        $e = Carbon::parse($b->end_time);
                        $totalHours += max(0, $e->diffInMinutes($s) / 60);
                    } catch (\Exception $ex) {
                    }
                }
            }

            return [
                'sport_name'     => $first->game_name ?? 'N/A',
                'court_number'   => $first->court_number ?? 'N/A',
                'total_bookings' => $totalBookings,
                'total_hours'    => round($totalHours, 2),
                'total_revenue'  => $totalRevenue,
                'average_revenue' => $totalBookings > 0 ? round($totalRevenue / $totalBookings, 2) : 0,
            ];
        })->values()->all();

        $this->dispatch('openRevenueModal');
    }

    public function exportReport()
    {
        try {
            $allBookings = $this->getCombinedBookings();

            $csvContent = "Booking ID,Player Name,Court,Sport,Date,Start Time,End Time,Status,Payment Status,Revenue\n";
            foreach ($allBookings as $booking) {
                $csvContent .= implode(',', [
                    $booking->id,
                    '"' . str_replace('"', '""', $booking->user_name ?? 'N/A') . '"',
                    '"' . str_replace('"', '""', $booking->court_number ?? 'N/A') . '"',
                    '"' . str_replace('"', '""', $booking->game_name ?? 'N/A') . '"',
                    $booking->booking_date,
                    $booking->start_time,
                    $booking->end_time,
                    $booking->status,
                    $booking->payment_status ?? 'N/A',
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

