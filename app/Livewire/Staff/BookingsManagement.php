<?php

namespace App\Livewire\Staff;

use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Carbon\Carbon;
use App\Models\BookingBooking;
use App\Models\BookingSport;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

#[Title("Staff Dashboard")]
#[Layout("components.layouts.staff")]
class BookingsManagement extends Component
{
    public $sports;
    public $bookings;
    public $complex_id;
    public $games;
    public $bookingdetails = [];

    public $selectedGame = '';
    public $selectedDate = '';
    public $selectedTime = '';
    public $selectedCourt = '';
    public $playerName = '';
    public $phoneNumber = '';
    public $status = 'Confirmed';
    public $permanent = false;
    public $notes = '';

    protected $rules = [
        'selectedGame' => 'required|string',
        'selectedDate' => 'required|date_format:Y-m-d',
        'selectedTime' => 'required',
        'selectedCourt' => 'required|string',
        'playerName' => 'required|string|max:255',
        'phoneNumber' => 'required|string|max:20',
        'status' => 'required|in:Confirmed,Pending,Completed,Cancelled,No-Show,Playing',
        'notes' => 'nullable|string|max:1000',
    ];

    protected $listeners = ['setSelectedBookingData', 'refreshBookings'];

    public function mount()
    {
        $this->complex_id = Auth::user()->complex_id;
        $this->checkAndUpdateBookingStatuses();
        $this->loadSports();
    }

    public function refreshBookings()
    {
        $this->loadSports();
    }

    public function loadSports()
    {
        try {
            $this->sports = BookingSport::where('venue_id', $this->complex_id)
                ->where('status', 'Active')
                ->get();

            // Only load non-cancelled bookings for display
            $this->bookings = BookingBooking::where('complex_id_id', $this->complex_id)
                ->whereNotIn('status', ['Cancelled'])
                ->get();

            $this->games = $this->sports->map(function ($sport) {
                return [
                    'name' => $sport->name ?? 'Unknown',
                    'maximum_court' => $sport->maximum_court ?? 1,
                    'game_id' => $sport->id,
                ];
            })->toArray();

            $this->bookingdetails = [];
            foreach ($this->bookings as $booking) {
                $game = strtolower($booking->game_name ?? '');
                if (empty($game)) continue;

                $date = Carbon::parse($booking->booking_date)->format('Y-m-d');
                $court = $booking->court_number ?? '1';
                $startTime = Carbon::parse($booking->start_time);
                $endTime = Carbon::parse($booking->end_time);

                $hours = max(1, $startTime->diffInHours($endTime));

                for ($i = 0; $i < $hours; $i++) {
                    $slotKey = $startTime->copy()->addHours($i)->format('H:i:s');
                    $slotEnd = $startTime->copy()->addHours($i + 1)->format('H:i:s');

                    $this->bookingdetails[$game][$date][$court][$slotKey] = [
                        'player' => $booking->user_name ?? 'Unknown',
                        'phone' => $booking->user_number ?? 'N/A',
                        'status' => $booking->status ?? 'Pending',
                        'permanent_source_id' => $booking->permanent_source_id,
                        'end' => $slotEnd,
                        'avatar' => '/storage/staff/user.png',
                        'id' => $booking->id,
                    ];
                }
            }
        } catch (\Exception $e) {
            Log::error('Error in loadSports', ['error' => $e->getMessage()]);
            $this->addError('general', 'Failed to load data. Please refresh.');
        }
    }

    public function getBookingDetails()
    {
        $this->loadSports();
        return $this->bookingdetails;
    }

    public function setSelectedBookingData($data)
    {
        if (is_string($data)) {
            $data = json_decode($data, true);
        }

        $this->selectedGame = $data['game'] ?? '';
        $this->selectedDate = $data['dateKey'] ?? '';
        $this->selectedTime = $data['time'] ?? '';
        $this->selectedCourt = $data['court'] ?? '';
    }

    public function addBooking()
    {
        $this->validate();

        // Find sport
        $sport = BookingSport::where('name', $this->selectedGame)
            ->where('venue_id', $this->complex_id)
            ->where('status', 'Active')
            ->first();

        if (!$sport) {
            $this->addError('general', 'Selected game is not available.');
            return;
        }

        $staffUser = Auth::user();
        if (!$staffUser || !$staffUser->complex_id) {
            $this->addError('general', 'Your account is not properly configured.');
            return;
        }

        // Normalize time format
        $startTime = $this->selectedTime;
        if (strlen($startTime) === 5) {
            $startTime .= ':00';
        }

        $endTime = Carbon::parse($startTime)->addMinutes(60)->format('H:i:s');

        $bookingData = [
            'user_id_id' => $staffUser->id,
            'complex_id_id' => $this->complex_id,
            'game_id_id' => $sport->id,
            'game_name' => $this->selectedGame,
            'booking_date' => $this->selectedDate,
            'permanent_source_id' => null,
            'user_name' => $this->playerName,
            'user_number' => $this->phoneNumber,
            'court_number' => $this->selectedCourt,
            'start_time' => $startTime,
            'end_time' => $endTime,
            'duration' => 60,
            'price' => $sport->price ?? 1800.00,
            'payment_status' => 'Pending',
            'payment_method' => null,
            'status' => $this->status,
            'notes' => $this->notes ?: '',
            'admin_comments' => '',
            'is_challenge_booking' => false,
            'opponent_team_id' => null,
            'team_id' => null,
        ];

        // Create booking(s)
        if ($this->permanent) {
            $sourceBookingId = null;
            for ($i = 0; $i < 7; $i++) {
                $bookingDate = Carbon::parse($this->selectedDate)->addDays($i)->format('Y-m-d');
                $newBooking = BookingBooking::create(array_merge($bookingData, [
                    'booking_date' => $bookingDate,
                    'permanent_source_id' => $sourceBookingId,
                    'qr_code' => 'QR' . strtoupper(substr(md5(uniqid()), 0, 6)),
                ]));
                if ($i === 0) {
                    $sourceBookingId = $newBooking->id;
                }
            }
        } else {
            BookingBooking::create(array_merge($bookingData, [
                'booking_date' => $this->selectedDate,
                'qr_code' => 'QR' . strtoupper(substr(md5(uniqid()), 0, 6)),
            ]));
        }

        // Refresh data and close modal
        $this->loadSports();
        $this->resetFields();

        // Dispatch events (don't let broadcast errors affect success)
        try {
            $this->dispatch('bookingCreated');
            $this->dispatch('closeModal');
        } catch (\Exception $e) {
            Log::warning('Broadcast event failed', ['error' => $e->getMessage()]);
        }

        session()->flash('message', 'Booking created successfully!');
    }

    public function resetFields()
    {
        $this->reset([
            'selectedGame',
            'selectedDate',
            'selectedTime',
            'selectedCourt',
            'playerName',
            'phoneNumber',
            'status',
            'permanent',
            'notes',
        ]);
        $this->status = 'Confirmed';
        $this->resetErrorBag();
    }

    /**
     * Cancel a booking - slot becomes available again
     */
    public function cancelBooking($bookingId = null)
    {
        $booking = $bookingId ? BookingBooking::find($bookingId) : null;

        if (!$booking && $this->selectedGame && $this->selectedDate && $this->selectedCourt && $this->selectedTime) {
            $booking = BookingBooking::where('complex_id_id', $this->complex_id)
                ->where('game_name', $this->selectedGame)
                ->where('booking_date', $this->selectedDate)
                ->where('court_number', $this->selectedCourt)
                ->where('start_time', $this->selectedTime)
                ->first();
        }

        if ($booking) {
            $booking->status = 'Cancelled';
            $booking->save();

            $this->dispatch('bookingCancelled');
            $this->loadSports();
            session()->flash('message', 'Booking cancelled. Slot is now available.');
            return true;
        }

        session()->flash('error', 'Booking not found!');
        return false;
    }

    /**
     * Start a booking - changes status to Playing
     */
    public function startBooking($bookingId)
    {
        $booking = BookingBooking::find($bookingId);

        if ($booking && in_array($booking->status, ['Confirmed', 'Pending'])) {
            $booking->status = 'Playing';
            $booking->save();

            $this->loadSports();
            return true;
        }

        return false;
    }

    /**
     * Complete a booking - changes status to Completed
     */
    public function completeBooking($bookingId)
    {
        $booking = BookingBooking::find($bookingId);

        if ($booking && $booking->status === 'Playing') {
            $booking->status = 'Completed';
            $booking->save();

            $this->loadSports();
            return true;
        }

        return false;
    }

    /**
     * Alias for checkAndUpdateBookingStatuses (for backward compatibility)
     */
    public function checkNoShows()
    {
        return $this->checkAndUpdateBookingStatuses();
    }

    /**
     * Check and update booking statuses based on current time
     * - Past confirmed/pending bookings become "No-Show"
     * - Playing bookings past end time become "Completed"
     */
    public function checkAndUpdateBookingStatuses()
    {
        $now = Carbon::now();

        // Find confirmed/pending bookings where end time has passed -> No-Show
        $expiredBookings = BookingBooking::where('complex_id_id', $this->complex_id)
            ->whereIn('status', ['Confirmed', 'Pending'])
            ->whereDate('booking_date', '<=', $now->toDateString())
            ->get()
            ->filter(function ($booking) use ($now) {
                try {
                    $bookingDate = Carbon::parse($booking->booking_date)->format('Y-m-d');
                    $endTimeStr = $booking->end_time;
                    if (strpos($endTimeStr, ' ') !== false) {
                        $endTimeStr = Carbon::parse($endTimeStr)->format('H:i:s');
                    }
                    $bookingEnd = Carbon::parse($bookingDate . ' ' . $endTimeStr);
                    return $bookingEnd->lessThan($now);
                } catch (\Exception $e) {
                    return false;
                }
            });

        foreach ($expiredBookings as $booking) {
            $booking->status = 'No-Show';
            $booking->save();
        }

        // Find playing bookings where end time has passed -> Completed
        $playingBookings = BookingBooking::where('complex_id_id', $this->complex_id)
            ->where('status', 'Playing')
            ->whereDate('booking_date', '<=', $now->toDateString())
            ->get()
            ->filter(function ($booking) use ($now) {
                try {
                    $bookingDate = Carbon::parse($booking->booking_date)->format('Y-m-d');
                    $endTimeStr = $booking->end_time;
                    if (strpos($endTimeStr, ' ') !== false) {
                        $endTimeStr = Carbon::parse($endTimeStr)->format('H:i:s');
                    }
                    $bookingEnd = Carbon::parse($bookingDate . ' ' . $endTimeStr);
                    return $bookingEnd->lessThan($now);
                } catch (\Exception $e) {
                    return false;
                }
            });

        foreach ($playingBookings as $booking) {
            $booking->status = 'Completed';
            $booking->save();
        }
    }

    public function render()
    {
        return view('livewire.staff.bookings-management', [
            'games' => $this->games ?? [],
            'bookingdetails' => $this->bookingdetails ?? [],
            'complex_id' => $this->complex_id,
        ]);
    }
}
