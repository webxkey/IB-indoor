<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\BookingBooking;
use App\Models\BookingSport;
use App\Models\BookingVenue;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Collection;

class DashboardController extends Controller
{
    private function getDateWindow(Request $request, int $defaultDays = 7): array
    {
        $period = strtolower((string) $request->get('period', 'daily'));
        $daysParam = $request->get('days');

        $days = match ($period) {
            'monthly' => 365,
            'weekly' => 28,
            default => 7,
        };

        if (is_numeric($daysParam) && (int) $daysParam > 0) {
            $days = (int) $daysParam;
        }

        $startDate = Carbon::today()->subDays(max($days - 1, 0))->startOfDay();
        $endDate = Carbon::today()->endOfDay();

        return [$period, $startDate, $endDate, $days];
    }

    private function normalizeStatus(?string $status): string
    {
        return str_replace(['_', '-'], ' ', strtolower(trim((string) $status)));
    }

    private function getTimeSlotLabel(?string $startTime, ?string $endTime): string
    {
        if (!$startTime && !$endTime) {
            return '';
        }

        $format = function (?string $value): string {
            if (!$value) {
                return '';
            }

            try {
                return Carbon::createFromFormat('H:i:s', $value)->format('h:i A');
            } catch (\Throwable $e) {
                try {
                    return Carbon::createFromFormat('H:i', $value)->format('h:i A');
                } catch (\Throwable $e) {
                    return $value;
                }
            }
        };

        $start = $format($startTime);
        $end = $format($endTime);

        return $start && $end ? $start . ' - ' . $end : ($start ?: $end);
    }

    private function getRevenueSummary(Collection $bookings): array
    {
        $totalRevenue = (float) $bookings->sum(fn ($booking) => (float) $booking->price);
        $totalBookings = $bookings->count();

        return [
            'total_revenue' => number_format($totalRevenue, 2, '.', ''),
            'total_bookings' => $totalBookings,
            'average_booking_value' => number_format($totalBookings > 0 ? $totalRevenue / $totalBookings : 0, 2, '.', ''),
        ];
    }

    /**
     * Get dashboard statistics.
     */
    public function getStats(Request $request)
    {
        $user = $request->user();
        $venueId = $user->complex_id;
        
        $totalBookings = BookingBooking::where('complex_id_id', $venueId)->count();
        $todayBookings = BookingBooking::where('complex_id_id', $venueId)->where('booking_date', now()->format('Y-m-d'))->count();
        $pendingBookings = BookingBooking::where('complex_id_id', $venueId)->where('status', 'pending')->count();
        $confirmedBookings = BookingBooking::where('complex_id_id', $venueId)->where('status', 'confirmed')->count();
        $cancelledBookings = BookingBooking::where('complex_id_id', $venueId)->where('status', 'cancelled')->count();
        
        // REVENUE: Only count PAID bookings
        $totalRevenue = BookingBooking::where('complex_id_id', $venueId)
            ->where('payment_status', 'Paid')
            ->sum('price');
            
        $todayRevenue = BookingBooking::where('complex_id_id', $venueId)
            ->where('payment_status', 'Paid')
            ->where('booking_date', now()->format('Y-m-d'))
            ->sum('price');
        
        $sports = BookingSport::where('venue_id', $venueId)->get();
        $venue = BookingVenue::find($venueId);

        return response()->json([
            'total_bookings' => $totalBookings,
            'today_bookings' => $todayBookings,
            'pending_bookings' => $pendingBookings,
            'confirmed_bookings' => $confirmedBookings,
            'cancelled_bookings' => $cancelledBookings,
            'completed_bookings' => $confirmedBookings, // Alias for confirmed in some frontend views
            'total_revenue' => (string)$totalRevenue,
            'today_revenue' => (string)$todayRevenue,
            'total_sports' => $sports->count(),
            'active_sports' => $sports->where('status', 'Active')->count(),
            'venue_rating' => (float)($venue->rating ?? 0),
            'total_reviews' => (int)($venue->reviews ?? 0),
        ]);
    }

    /**
     * Get revenue report data.
     */
    public function revenueReport(Request $request)
    {
        $user = $request->user();
        [$period, $startDate, $endDate, $days] = $this->getDateWindow($request);

        $baseQuery = BookingBooking::where('complex_id_id', $user->complex_id)
            ->where('payment_status', 'Paid')
            ->whereBetween('booking_date', [$startDate->toDateString(), $endDate->toDateString()]);

        $bookings = $baseQuery->get();

        $rows = match ($period) {
            'weekly' => $bookings->groupBy(fn ($booking) => Carbon::parse($booking->booking_date)->startOfWeek()->toDateString()),
            'monthly' => $bookings->groupBy(fn ($booking) => Carbon::parse($booking->booking_date)->format('Y-m')),
            default => $bookings->groupBy(fn ($booking) => Carbon::parse($booking->booking_date)->toDateString()),
        };

        $data = $rows->sortKeys()->map(function (Collection $group, string $key) use ($period) {
            $revenue = (float) $group->sum(fn ($booking) => (float) $booking->price);
            $bookingsCount = $group->count();

            if ($period === 'weekly') {
                $weekStart = Carbon::parse($key);
                $weekEnd = (clone $weekStart)->endOfWeek();

                return [
                    'date' => $weekStart->toDateString(),
                    'label' => $weekStart->format('M d') . ' - ' . $weekEnd->format('M d'),
                    'revenue' => number_format($revenue, 2, '.', ''),
                    'bookings' => $bookingsCount,
                ];
            }

            if ($period === 'monthly') {
                $month = Carbon::createFromFormat('Y-m', $key);

                return [
                    'date' => $month->startOfMonth()->toDateString(),
                    'label' => $month->format('M Y'),
                    'revenue' => number_format($revenue, 2, '.', ''),
                    'bookings' => $bookingsCount,
                ];
            }

            return [
                'date' => $key,
                'label' => Carbon::parse($key)->format('M d, Y'),
                'revenue' => number_format($revenue, 2, '.', ''),
                'bookings' => $bookingsCount,
            ];
        })->values();

        return response()->json([
            'period' => $period,
            'start_date' => $startDate->toDateString(),
            'end_date' => $endDate->toDateString(),
            'data' => $data,
            'total_revenue' => number_format($bookings->sum(fn ($booking) => (float) $booking->price), 2, '.', ''),
            'total_bookings' => $bookings->count(),
            'average_booking_value' => $this->getRevenueSummary($bookings)['average_booking_value'],
        ]);
    }

    /**
     * Get booking report data.
     */
    public function bookingReport(Request $request)
    {
        $user = $request->user();
        [$period, $startDate, $endDate] = $this->getDateWindow($request);

        $bookings = BookingBooking::with(['sport', 'user', 'venue'])
            ->where('complex_id_id', $user->complex_id)
            ->whereBetween('booking_date', [$startDate->toDateString(), $endDate->toDateString()])
            ->orderBy('booking_date', 'desc')
            ->orderBy('start_time', 'desc')
            ->get();

        $summary = [
            'total' => $bookings->count(),
            'confirmed' => $bookings->filter(fn ($booking) => $this->normalizeStatus($booking->status) === 'confirmed' || $this->normalizeStatus($booking->status) === 'completed')->count(),
            'cancelled' => $bookings->filter(fn ($booking) => $this->normalizeStatus($booking->status) === 'cancelled')->count(),
            'completed' => $bookings->filter(fn ($booking) => $this->normalizeStatus($booking->status) === 'completed')->count(),
            'pending' => $bookings->filter(fn ($booking) => $this->normalizeStatus($booking->status) === 'pending')->count(),
            'paid' => $bookings->filter(fn ($booking) => $this->normalizeStatus($booking->payment_status) === 'paid')->count(),
            'unpaid' => $bookings->filter(fn ($booking) => $this->normalizeStatus($booking->payment_status) !== 'paid')->count(),
            'total_revenue' => number_format($bookings->filter(fn ($booking) => $this->normalizeStatus($booking->payment_status) === 'paid')->sum(fn ($booking) => (float) $booking->price), 2, '.', ''),
        ];

        $rows = $bookings->map(function ($booking) {
            return [
                'id' => $booking->id,
                'venue_name' => optional($booking->venue)->name,
                'sport_name' => $booking->game_name,
                'court_number' => $booking->court_number,
                'booking_date' => optional($booking->booking_date)->format('Y-m-d') ?? (string) $booking->booking_date,
                'start_time' => $booking->start_time,
                'end_time' => $booking->end_time,
                'time_slot' => $this->getTimeSlotLabel($booking->start_time, $booking->end_time),
                'price' => (string) $booking->price,
                'status' => ucfirst($this->normalizeStatus($booking->status)) ?: 'Pending',
                'payment_status' => ucfirst($this->normalizeStatus($booking->payment_status)) ?: 'Pending',
                'user_name' => $booking->user_name,
                'user_email' => optional($booking->user)->email,
                'user_phone' => $booking->user_number,
                'duration' => $booking->duration,
                'notes' => $booking->notes,
                'is_permanent' => (bool) $booking->permanent_source_id,
            ];
        })->values();

        return response()->json([
            'period' => $period,
            'start_date' => $startDate->toDateString(),
            'end_date' => $endDate->toDateString(),
            'bookings' => $rows,
            'summary' => $summary,
        ]);
    }

    /**
     * Get revenue broken down by sport.
     */
    public function sportsRevenue(Request $request)
    {
        $user = $request->user();
        $bookings = BookingBooking::with(['sport'])
            ->where('complex_id_id', $user->complex_id)
            ->where('payment_status', 'Paid')
            ->get();

        $totalRevenue = (float) $bookings->sum(fn ($booking) => (float) $booking->price);

        $sports = $bookings
            ->groupBy(fn ($booking) => $booking->game_id_id ?: $booking->game_name)
            ->map(function (Collection $group) use ($totalRevenue) {
                $first = $group->first();
                $revenue = (float) $group->sum(fn ($booking) => (float) $booking->price);
                $image = null;

                if ($first && $first->sport) {
                    $image = $first->sport->image_url ?? $first->sport->image ?? null;
                }

                return [
                    'sport_id' => (int) ($first->game_id_id ?? 0),
                    'sport_name' => $first->game_name ?? 'Unknown Sport',
                    'sport_image' => $image,
                    'total_revenue' => number_format($revenue, 2, '.', ''),
                    'total_bookings' => $group->count(),
                    'percentage' => $totalRevenue > 0 ? round(($revenue / $totalRevenue) * 100, 1) : 0,
                ];
            })
            ->sortByDesc(fn ($item) => (float) $item['total_revenue'])
            ->values();

        return response()->json([
            'sports' => $sports,
            'total_revenue' => number_format($totalRevenue, 2, '.', ''),
            'total_sports' => $sports->count(),
        ]);
    }

    /**
     * Get performance metrics.
     */
    public function performance(Request $request)
    {
        $user = $request->user();
        $venueId = $user->complex_id;

        $bookings = BookingBooking::where('complex_id_id', $venueId)->get();
        $totalBookings = $bookings->count();
        $paidBookings = $bookings->filter(fn ($booking) => $this->normalizeStatus($booking->payment_status) === 'paid');
        $completedBookings = $bookings->filter(fn ($booking) => $this->normalizeStatus($booking->status) === 'completed');
        $cancelledBookings = $bookings->filter(fn ($booking) => $this->normalizeStatus($booking->status) === 'cancelled');
        $noShowBookings = $bookings->filter(fn ($booking) => $this->normalizeStatus($booking->status) === 'no show' || $this->normalizeStatus($booking->status) === 'no-show');
        $confirmedBookings = $bookings->filter(fn ($booking) => $this->normalizeStatus($booking->status) === 'confirmed');

        $hourlyDistribution = collect(range(0, 23))->map(function (int $hour) use ($bookings) {
            $count = $bookings->filter(function ($booking) use ($hour) {
                $time = $booking->start_time ?: $booking->time_slot;
                if (!$time) return false;
                try {
                    return Carbon::createFromFormat('H:i:s', $time)->hour === $hour;
                } catch (\Throwable $e) {
                    try {
                        return Carbon::createFromFormat('H:i', $time)->hour === $hour;
                    } catch (\Throwable $e) {
                        return false;
                    }
                }
            })->count();

            return [
                'hour' => $hour,
                'label' => Carbon::createFromTime($hour)->format('g A'),
                'count' => $count,
            ];
        })->values();

        $dailyDistribution = collect(range(0, 6))->map(function (int $day) use ($bookings) {
            $count = $bookings->filter(function ($booking) use ($day) {
                if (!$booking->booking_date) return false;
                return Carbon::parse($booking->booking_date)->dayOfWeek === $day;
            })->count();

            return [
                'day' => $day,
                'label' => Carbon::create()->startOfWeek()->addDays($day)->format('l'),
                'count' => $count,
            ];
        })->values();

        $courtUtilization = $bookings->groupBy(fn ($booking) => (string) ($booking->court_number ?? '1'))
            ->map(function (Collection $group, string $court) use ($totalBookings) {
                $count = $group->count();
                return [
                    'court' => $court,
                    'bookings' => $count,
                    'percentage' => $totalBookings > 0 ? round(($count / $totalBookings) * 100, 1) : 0,
                ];
            })
            ->sortByDesc('bookings')
            ->values();

        $sportUtilization = $bookings->groupBy(fn ($booking) => $booking->game_name ?: 'Unknown Sport')
            ->map(function (Collection $group, string $sportName) use ($totalBookings) {
                $count = $group->count();
                return [
                    'sport_name' => $sportName,
                    'bookings' => $count,
                    'percentage' => $totalBookings > 0 ? round(($count / $totalBookings) * 100, 1) : 0,
                ];
            })
            ->sortByDesc('bookings')
            ->values();

        $statusCounts = $bookings->groupBy(fn ($booking) => ucfirst($this->normalizeStatus($booking->status) ?: 'Pending'))
            ->map(function (Collection $group, string $status) use ($totalBookings) {
                $count = $group->count();
                return [
                    'status' => $status === 'No show' ? 'No-Show' : $status,
                    'count' => $count,
                    'percentage' => $totalBookings > 0 ? round(($count / $totalBookings) * 100, 1) : 0,
                ];
            })
            ->sortByDesc('count')
            ->values();

        $busiestDay = $dailyDistribution->sortByDesc('count')->first();
        $busiestHour = $hourlyDistribution->sortByDesc('count')->first();

        return response()->json([
            'summary' => [
                'total_bookings' => $totalBookings,
                'completed' => $completedBookings->count(),
                'cancelled' => $cancelledBookings->count(),
                'no_show' => $noShowBookings->count(),
                'confirmed' => $confirmedBookings->count(),
                'completion_rate' => $totalBookings > 0 ? round(($completedBookings->count() / $totalBookings) * 100, 1) : 0,
                'cancellation_rate' => $totalBookings > 0 ? round(($cancelledBookings->count() / $totalBookings) * 100, 1) : 0,
                'no_show_rate' => $totalBookings > 0 ? round(($noShowBookings->count() / $totalBookings) * 100, 1) : 0,
                'average_daily_bookings' => round($totalBookings / 7, 1),
                'busiest_day' => $busiestDay['label'] ?? 'N/A',
                'busiest_hour' => $busiestHour['label'] ?? 'N/A',
                'avg_revenue_per_booking' => number_format($paidBookings->count() > 0 ? ((float) $paidBookings->sum(fn ($booking) => (float) $booking->price) / $paidBookings->count()) : 0, 2, '.', ''),
            ],
            'hourly_distribution' => $hourlyDistribution,
            'daily_distribution' => $dailyDistribution,
            'court_utilization' => $courtUtilization,
            'sport_utilization' => $sportUtilization,
            'status_distribution' => $statusCounts,
        ]);
    }

    /**
     * Export dashboard data as CSV.
     */
    public function exportCSV(Request $request)
    {
        $user = $request->user();
        $venueId = $user->complex_id;
        $bookings = BookingBooking::where('complex_id_id', $venueId)
            ->orderBy('booking_date', 'desc')
            ->orderBy('start_time', 'desc')
            ->get();

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="bookings_report.csv"',
        ];

        $callback = function() use ($bookings) {
            $file = fopen('php://output', 'w');
            fputcsv($file, ['ID', 'Customer', 'Sport', 'Court', 'Date', 'Start Time', 'End Time', 'Price', 'Status', 'Payment']);

            foreach ($bookings as $booking) {
                fputcsv($file, [
                    $booking->id,
                    $booking->user_name,
                    $booking->game_name,
                    $booking->court_number,
                    optional($booking->booking_date)->format('Y-m-d') ?? (string) $booking->booking_date,
                    $booking->start_time,
                    $booking->end_time,
                    $booking->price,
                    $booking->status,
                    $booking->payment_status,
                ]);
            }
            fclose($file);
        };

        return Response::stream($callback, 200, $headers);
    }
}
