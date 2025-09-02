<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use App\Models\BookingBooking;
use App\Models\BookingVenue;
use App\Models\BookingSport;
use Carbon\Carbon;

#[Layout('components.layouts.admin')]
#[Title('Booking')]
class Bookings extends Component
{
    use WithPagination;

    // Filters
    public $dateFilter = 'month';
    public $statusFilter = '';
    public $venueFilter = '';
    public $customStartDate;
    public $customEndDate;

    // Stats
    public $todayBookingsCount;
    public $canceledBookingsCount;
    public $pendingBookingsCount;
    public $totalRevenue;

    // Date range for filtering
    public $startDate;
    public $endDate;

    public function mount()
    {
        $this->setDateRange();
        $this->loadStats();
    }

    public function updatedDateFilter()
    {
        $this->setDateRange();
        $this->loadStats();
    }

    private function setDateRange()
    {
        switch ($this->dateFilter) {
            case 'today':
                $this->startDate = Carbon::today();
                $this->endDate = Carbon::today()->endOfDay();
                break;
            case 'week':
                $this->startDate = Carbon::now()->startOfWeek();
                $this->endDate = Carbon::now()->endOfWeek();
                break;
            case 'month':
                $this->startDate = Carbon::now()->startOfMonth();
                $this->endDate = Carbon::now()->endOfMonth();
                break;
            case 'custom':
                // Custom dates will be handled separately
                break;
            default:
                $this->startDate = Carbon::now()->startOfMonth();
                $this->endDate = Carbon::now()->endOfMonth();
        }
    }

    private function loadStats()
    {
        try {
            // Today's bookings
            $this->todayBookingsCount = BookingBooking::whereDate('booking_date', Carbon::today())->count();
            
            // Canceled bookings
            $this->canceledBookingsCount = BookingBooking::where('status', 'canceled')
                ->whereBetween('booking_date', [$this->startDate, $this->endDate])
                ->count();
                
            // Pending bookings
            $this->pendingBookingsCount = BookingBooking::where('status', 'pending')
                ->whereBetween('booking_date', [$this->startDate, $this->endDate])
                ->count();
                
            // Total revenue
            $this->totalRevenue = BookingBooking::where('status', 'confirmed')
                ->whereBetween('booking_date', [$this->startDate, $this->endDate])
                ->sum('total');
                
        } catch (\Exception $e) {
            session()->flash('error', 'Error loading statistics: ' . $e->getMessage());
        }
    }

    public function applyFilters()
    {
        $this->setDateRange();
        $this->loadStats();
        $this->resetPage();
        
        session()->flash('success', 'Filters applied successfully.');
    }

    public function resetFilters()
    {
        $this->dateFilter = 'month';
        $this->statusFilter = '';
        $this->venueFilter = '';
        $this->customStartDate = null;
        $this->customEndDate = null;
        
        $this->setDateRange();
        $this->loadStats();
        $this->resetPage();
        
        session()->flash('success', 'Filters reset successfully.');
    }

    public function getVenueStatsProperty()
    {
        try {
            $venues = BookingVenue::withCount(['bookings' => function($query) {
                    $query->whereBetween('booking_date', [$this->startDate, $this->endDate]);
                }])
                ->with(['bookings' => function($query) {
                    $query->whereBetween('booking_date', [$this->startDate, $this->endDate]);
                }])
                ->get();

            $venueStats = [];
            
            foreach ($venues as $venue) {
                $confirmed = $venue->bookings->where('status', 'confirmed')->count();
                $pending = $venue->bookings->where('status', 'pending')->count();
                $canceled = $venue->bookings->where('status', 'canceled')->count();
                $revenue = $venue->bookings->where('status', 'confirmed')->sum('total');
                
                $venueStats[] = [
                    'id' => $venue->id,
                    'name' => $venue->name,
                    'location' => $venue->location,
                    'image' => $venue->image_url,
                    'total_bookings' => $venue->bookings_count,
                    'confirmed' => $confirmed,
                    'pending' => $pending,
                    'canceled' => $canceled,
                    'revenue' => $revenue
                ];
            }
            
            // Sort by revenue descending
            usort($venueStats, function($a, $b) {
                return $b['revenue'] <=> $a['revenue'];
            });
            
            return $venueStats;
            
        } catch (\Exception $e) {
            session()->flash('error', 'Error loading venue statistics: ' . $e->getMessage());
            return [];
        }
    }

    public function getRecentBookingsProperty()
    {
        try {
            $query = BookingBooking::with(['venue', 'sport', 'user'])
                ->whereBetween('booking_date', [$this->startDate, $this->endDate])
                ->orderBy('booking_date', 'desc')
                ->orderBy('start_time', 'desc');

            if ($this->statusFilter) {
                $query->where('status', $this->statusFilter);
            }

            if ($this->venueFilter) {
                $query->where('complex_id_id', $this->venueFilter);
            }

            return $query->paginate(10);
            
        } catch (\Exception $e) {
            session()->flash('error', 'Error loading recent bookings: ' . $e->getMessage());
            return [];
        }
    }

    public function render()
    {
        return view('livewire.admin.bookings', [
            'venues' => BookingVenue::all(),
            'venueStats' => $this->venueStats,
            'recentBookings' => $this->recentBookings
        ]);
    }
}