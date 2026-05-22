<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\BookingBooking;
use App\Models\BookingSport;
use App\Models\BookingVenue;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;

class DashboardController extends Controller
{
    public function getStats(Request $request)
    {
        $user = $request->user();
        $venueId = $user?->complex_id;

        if (!$venueId) {
            return response()->json(['message' => 'Venue not found'], 404);
        }

        $bookings = BookingBooking::where('complex_id_id', $venueId)->get();
        $venue = BookingVenue::find($venueId);
        $sports = BookingSport::where('venue_id', $venueId)->get();

        $today = Carbon::today()->toDateString();

        $statusCount = static function ($rows, string $status): int {
            return $rows->filter(function ($row) use ($status) {
                return strtolower((string) $row->status) === $status;
            })->count();
        };

        return response()->json([
            'total_bookings' => $bookings->count(),
            'today_bookings' => $bookings->filter(function ($b) use ($today) {
                $bDate = $b->booking_date instanceof Carbon ? $b->booking_date->format('Y-m-d') : (string) $b->booking_date;
                return substr($bDate, 0, 10) === $today;
            })->count(),
            'pending_bookings' => $statusCount($bookings, 'pending'),
            'confirmed_bookings' => $statusCount($bookings, 'confirmed'),
            'cancelled_bookings' => $statusCount($bookings, 'cancelled'),
            'completed_bookings' => $statusCount($bookings, 'completed'),
            'refunded_bookings' => $statusCount($bookings, 'refunded'),
            'total_revenue' => number_format((float) $bookings->filter(function ($b) {
                return strtolower((string) $b->payment_status) === 'paid';
            })->sum('price'), 2, '.', ''),
            'today_revenue' => number_format((float) $bookings->filter(function ($b) use ($today) {
                $bDate = $b->booking_date instanceof Carbon ? $b->booking_date->format('Y-m-d') : (string) $b->booking_date;
                return substr($bDate, 0, 10) === $today && strtolower((string) $b->payment_status) === 'paid';
            })->sum('price'), 2, '.', ''),
            'total_refunded' => number_format((float) $bookings->filter(function ($b) {
                return strtolower((string) $b->payment_status) === 'refunded';
            })->sum('price'), 2, '.', ''),
            'total_sports' => $sports->count(),
            'active_sports' => $sports->filter(function ($sport) {
                return strtolower((string) $sport->status) === 'active';
            })->count(),
            'venue_rating' => (float) ($venue?->rating ?? 0),
            'total_reviews' => (int) ($venue?->reviews ?? 0),
        ]);
    }

    public function bookingReport(Request $request)
    {
        $user = $request->user();
        $venueId = $user?->complex_id;

        if (!$venueId) {
            return response()->json(['message' => 'Venue not found'], 404);
        }

        $query = BookingBooking::where('complex_id_id', $venueId)
            ->with('sport');

        if ($request->filled('start_date')) {
            $query->whereDate('booking_date', '>=', $request->string('start_date'));
        }

        if ($request->filled('end_date')) {
            $query->whereDate('booking_date', '<=', $request->string('end_date'));
        }

        if ($request->filled('sport_id')) {
            $query->where('game_id_id', $request->integer('sport_id'));
        }

        if ($request->filled('status')) {
            $query->whereRaw('lower(status) = ?', [strtolower((string) $request->status)]);
        }

        $bookings = $query->orderByDesc('created_at')
            ->orderByDesc('id')
            ->get();

        $summary = [
            'total' => $bookings->count(),
            'confirmed' => $bookings->filter(fn($booking) => strtolower((string) $booking->status) === 'confirmed')->count(),
            'cancelled' => $bookings->filter(fn($booking) => strtolower((string) $booking->status) === 'cancelled')->count(),
            'completed' => $bookings->filter(fn($booking) => strtolower((string) $booking->status) === 'completed')->count(),
            'pending' => $bookings->filter(fn($booking) => strtolower((string) $booking->status) === 'pending')->count(),
            'paid' => $bookings->filter(fn($booking) => strtolower((string) $booking->payment_status) === 'paid')->count(),
            'unpaid' => $bookings->filter(fn($booking) => strtolower((string) $booking->payment_status) !== 'paid')->count(),
            'refunded' => $bookings->filter(fn($booking) => strtolower((string) $booking->payment_status) === 'refunded')->count(),
            'total_revenue' => number_format((float) $bookings->filter(fn($b) => strtolower((string) $b->payment_status) === 'paid')->sum('price'), 2, '.', ''),
        ];

        $payload = $bookings->map(function (BookingBooking $booking) {
            return [
                'id' => $booking->id,
                'user_name' => $booking->user_name,
                'user_email' => $booking->user?->email,
                'user_phone' => $booking->user_number,
                'court_number' => $booking->court_number,
                'sport_id' => $booking->game_id_id,
                'sport_name' => $booking->sport?->name ?? $booking->game_name ?? 'N/A',
                'booking_date' => optional($booking->booking_date)->format('Y-m-d') ?? (string) $booking->booking_date,
                'start_time' => $booking->start_time,
                'end_time' => $booking->end_time,
                'time_slot' => $booking->time_slot ?? null,
                'duration' => $booking->duration,
                'price' => (string) $booking->price,
                'status' => $booking->status,
                'payment_status' => $booking->payment_status,
                'payment_method' => $booking->payment_method,
                'is_permanent' => (bool) $booking->permanent_source_id,
                'permanent_source_id' => $booking->permanent_source_id,
                'booking_type' => $booking->permanent_source_id ? 'Permanent' : 'One-Time',
                'notes' => $booking->notes,
                'created_at' => optional($booking->created_at)->toDateTimeString(),
                'updated_at' => optional($booking->updated_at)->toDateTimeString(),
            ];
        })->values();

        return response()->json([
            'bookings' => $payload,
            'summary' => $summary,
        ]);
    }

    public function revenueReport(Request $request)
    {
        $user = $request->user();
        $venueId = $user?->complex_id;

        if (!$venueId) {
            return response()->json(['message' => 'Venue not found'], 404);
        }

        $period = strtolower((string) $request->query('period', 'daily'));
        $query = BookingBooking::where('complex_id_id', $venueId);

        if ($request->filled('start_date')) {
            $query->whereDate('booking_date', '>=', $request->string('start_date'));
        }

        if ($request->filled('end_date')) {
            $query->whereDate('booking_date', '<=', $request->string('end_date'));
        }

        $bookings = $query->get();

        $groupKey = match ($period) {
            'weekly' => fn(BookingBooking $booking) => Carbon::parse($booking->booking_date)->startOfWeek()->format('Y-m-d'),
            'monthly' => fn(BookingBooking $booking) => Carbon::parse($booking->booking_date)->format('Y-m'),
            default => fn(BookingBooking $booking) => Carbon::parse($booking->booking_date)->format('Y-m-d'),
        };

        $groupLabel = match ($period) {
            'weekly' => fn(string $value) => Carbon::parse($value)->format('M d, Y'),
            'monthly' => fn(string $value) => Carbon::parse($value . '-01')->format('M Y'),
            default => fn(string $value) => Carbon::parse($value)->format('M d, Y'),
        };

        $rows = $bookings
            ->groupBy($groupKey)
            ->map(function ($rows, string $key) use ($groupLabel) {
                return [
                    'date' => $key,
                    'label' => $groupLabel($key),
                    'revenue' => number_format((float) $rows->filter(fn($b) => strtolower((string) $b->payment_status) === 'paid')->sum('price'), 2, '.', ''),
                    'bookings' => $rows->count(),
                ];
            })
            ->sortKeys()
            ->values();

        return response()->json([
            'data' => $rows,
            'total_revenue' => number_format((float) $bookings->filter(fn($b) => strtolower((string) $b->payment_status) === 'paid')->sum('price'), 2, '.', ''),
            'total_bookings' => $bookings->count(),
            'average_booking_value' => $bookings->filter(fn($b) => strtolower((string) $b->payment_status) === 'paid')->count() > 0
                ? number_format((float) $bookings->filter(fn($b) => strtolower((string) $b->payment_status) === 'paid')->sum('price') / max($bookings->filter(fn($b) => strtolower((string) $b->payment_status) === 'paid')->count(), 1), 2, '.', '')
                : '0.00',
        ]);
    }

    public function sportsRevenue(Request $request)
    {
        $user = $request->user();
        $venueId = $user?->complex_id;

        if (!$venueId) {
            return response()->json(['message' => 'Venue not found'], 404);
        }

        $bookings = BookingBooking::where('complex_id_id', $venueId)
            ->with('sport')
            ->get();

        $totalRevenue = (float) $bookings->filter(fn($b) => strtolower((string) $b->payment_status) === 'paid')->sum('price');

        $sports = $bookings
            ->groupBy(function (BookingBooking $booking) {
                return $booking->sport?->id ?? $booking->game_id_id ?? 0;
            })
            ->map(function ($rows) use ($totalRevenue) {
                $first = $rows->first();
                $revenue = (float) $rows->filter(fn($b) => strtolower((string) $b->payment_status) === 'paid')->sum('price');

                return [
                    'sport_id' => (int) ($first->sport?->id ?? $first->game_id_id ?? 0),
                    'sport_name' => $first->sport?->name ?? $first->game_name ?? 'N/A',
                    'sport_image' => $first->sport?->image ?? null,
                    'total_revenue' => number_format($revenue, 2, '.', ''),
                    'total_bookings' => $rows->count(),
                    'percentage' => $totalRevenue > 0 ? round(($revenue / $totalRevenue) * 100, 2) : 0,
                ];
            })
            ->sortByDesc('total_revenue')
            ->values();

        return response()->json([
            'sports' => $sports,
            'total_revenue' => number_format($totalRevenue, 2, '.', ''),
            'total_sports' => $sports->count(),
        ]);
    }

    public function performance(Request $request)
    {
        $user = $request->user();
        $venueId = $user?->complex_id;

        if (!$venueId) {
            return response()->json(['message' => 'Venue not found'], 404);
        }

        $bookings = BookingBooking::where('complex_id_id', $venueId)->get();
        $total = $bookings->count();

        $hourly = $bookings
            ->groupBy(function (BookingBooking $booking) {
                return Carbon::parse($booking->start_time)->format('H');
            })
            ->map(function ($rows, string $hour) use ($total) {
                $count = $rows->count();
                return [
                    'hour' => (int) $hour,
                    'label' => Carbon::createFromTime((int) $hour, 0)->format('g A'),
                    'count' => $count,
                ];
            })
            ->sortBy('hour')
            ->values();

        $daily = $bookings
            ->groupBy(function (BookingBooking $booking) {
                return Carbon::parse($booking->booking_date)->dayOfWeekIso;
            })
            ->map(function ($rows, string $day) {
                $labels = [1 => 'Mon', 2 => 'Tue', 3 => 'Wed', 4 => 'Thu', 5 => 'Fri', 6 => 'Sat', 7 => 'Sun'];
                return [
                    'day' => (int) $day,
                    'label' => $labels[(int) $day] ?? $day,
                    'count' => $rows->count(),
                ];
            })
            ->sortBy('day')
            ->values();

        $courtUtilization = $bookings
            ->groupBy(fn(BookingBooking $booking) => (string) ($booking->court_number ?? '1'))
            ->map(function ($rows, string $court) use ($total) {
                $count = $rows->count();
                return [
                    'court' => 'Court ' . $court,
                    'bookings' => $count,
                    'percentage' => $total > 0 ? round(($count / $total) * 100, 2) : 0,
                ];
            })
            ->sortByDesc('bookings')
            ->values();

        $sportUtilization = $bookings
            ->groupBy(fn(BookingBooking $booking) => $booking->sport?->name ?? $booking->game_name ?? 'N/A')
            ->map(function ($rows, string $sportName) use ($total) {
                $count = $rows->count();
                return [
                    'sport_name' => $sportName,
                    'bookings' => $count,
                    'percentage' => $total > 0 ? round(($count / $total) * 100, 2) : 0,
                ];
            })
            ->sortByDesc('bookings')
            ->values();

        $statusCounts = $bookings->groupBy(function (BookingBooking $booking) {
            return strtolower((string) $booking->status);
        });

        $confirmed = $statusCounts->get('confirmed', collect())->count();
        $cancelled = $statusCounts->get('cancelled', collect())->count();
        $noShow = $statusCounts->get('no-show', collect())->count() + $statusCounts->get('no show', collect())->count();
        $completed = $statusCounts->get('completed', collect())->count();

        return response()->json([
            'summary' => [
                'total_bookings' => $total,
                'completed' => $completed,
                'cancelled' => $cancelled,
                'no_show' => $noShow,
                'confirmed' => $confirmed,
                'completion_rate' => $total > 0 ? round(($completed / $total) * 100, 2) : 0,
                'cancellation_rate' => $total > 0 ? round(($cancelled / $total) * 100, 2) : 0,
                'no_show_rate' => $total > 0 ? round(($noShow / $total) * 100, 2) : 0,
                'average_daily_bookings' => $bookings->groupBy(fn(BookingBooking $booking) => Carbon::parse($booking->booking_date)->toDateString())->count() > 0
                    ? round($total / max($bookings->groupBy(fn(BookingBooking $booking) => Carbon::parse($booking->booking_date)->toDateString())->count(), 1), 2)
                    : 0,
                'busiest_day' => $daily->sortByDesc('count')->first()['label'] ?? 'N/A',
                'busiest_hour' => $hourly->sortByDesc('count')->first()['label'] ?? 'N/A',
                'avg_revenue_per_booking' => $total > 0 ? number_format((float) $bookings->filter(fn($b) => strtolower((string) $b->payment_status) === 'paid')->sum('price') / $total, 2, '.', '') : '0.00',
            ],
            'hourly_distribution' => $hourly,
            'daily_distribution' => $daily,
            'court_utilization' => $courtUtilization,
            'sport_utilization' => $sportUtilization,
            'status_distribution' => [
                ['status' => 'confirmed', 'count' => $confirmed, 'percentage' => $total > 0 ? round(($confirmed / $total) * 100, 2) : 0],
                ['status' => 'cancelled', 'count' => $cancelled, 'percentage' => $total > 0 ? round(($cancelled / $total) * 100, 2) : 0],
                ['status' => 'completed', 'count' => $completed, 'percentage' => $total > 0 ? round(($completed / $total) * 100, 2) : 0],
                ['status' => 'no-show', 'count' => $noShow, 'percentage' => $total > 0 ? round(($noShow / $total) * 100, 2) : 0],
            ],
        ]);
    }

    public function exportCSV(Request $request)
    {
        $user = $request->user();
        $venueId = $user?->complex_id;

        if (!$venueId) {
            return response()->json(['message' => 'Venue not found'], 404);
        }

        $bookings = BookingBooking::where('complex_id_id', $venueId)
            ->with('sport')
            ->orderByDesc('created_at')
            ->orderByDesc('id')
            ->get();

        $csvContent = "Booking ID,Player Name,Court,Sport,Date,Start Time,End Time,Status,Payment Status,Revenue\n";
        foreach ($bookings as $booking) {
            $csvContent .= implode(',', [
                $booking->id,
                '"' . ($booking->user_name ?? 'N/A') . '"',
                '"' . ($booking->court_number ?? 'N/A') . '"',
                '"' . ($booking->sport?->name ?? $booking->game_name ?? 'N/A') . '"',
                optional($booking->booking_date)->format('Y-m-d') ?? (string) $booking->booking_date,
                $booking->start_time,
                $booking->end_time,
                $booking->status,
                $booking->payment_status,
                $booking->price ?? 0,
            ]) . "\n";
        }

        $filename = 'booking_report_' . now()->format('Y-m-d_His') . '.csv';

        return response()->streamDownload(function () use ($csvContent) {
            echo $csvContent;
        }, $filename, ['Content-Type' => 'text/csv']);
    }
}
