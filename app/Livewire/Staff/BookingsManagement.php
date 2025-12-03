<?php

namespace App\Livewire\Staff;

use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Carbon\Carbon;
use App\Models\BookingBooking;
use App\Models\BookingSport;
use App\Models\UserUser;
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
    public $lastChecked;
    public $latestBookingId = 0;

    public $selectedGame = '';
    public $selectedDate = '';
    public $selectedTime = '';
    public $selectedCourt = '';
    public $playerName = '';
    public $phoneNumber = '';
    public $status = 'Booked';
    public $permanent = false;
    public $notes = '';
    public $appIndooruserId;


    protected $rules = [
        'selectedGame' => 'required|string',
        'selectedDate' => 'required|date_format:Y-m-d',
        'selectedTime' => 'required|date_format:H:i:s',
        'selectedCourt' => 'required|string',
        'playerName' => 'required|string|max:255',
        'phoneNumber' => 'required|string|max:20',
        'status' => 'required|in:Booked,Pending,Completed,Cancelled,No-Show',
        'notes' => 'nullable|string|max:1000',
    ];

    protected $listeners = ['setSelectedBookingData', 'refreshBookings', 'checkForNewBookings'];

    public function mount()
    {
        $this->complex_id = Auth::user()->complex_id;
        $this->loadSports();
        $this->lastChecked = now();
        
        // Get the latest booking ID
        $latestBooking = BookingBooking::where('complex_id_id', $this->complex_id)
            ->latest('id')
            ->first();
        $this->latestBookingId = $latestBooking ? $latestBooking->id : 0;
    }

    /**
     * Check for new bookings (called from JavaScript polling)
     */
    public function checkForNewBookings()
    {
        // Get any bookings newer than the last known booking ID
        $newBookings = BookingBooking::where('complex_id_id', $this->complex_id)
            ->where('id', '>', $this->latestBookingId)
            ->orderBy('id', 'asc')
            ->get();

        if ($newBookings->count() > 0) {
            // Update the latest booking ID
            $this->latestBookingId = $newBookings->last()->id;

            // Reload sports data to refresh the calendar
            $this->loadSports();
            
            // Dispatch event to frontend with new booking details
            foreach ($newBookings as $booking) {
                $this->dispatch('newBookingDetected', [
                    'id' => $booking->id,
                    'user_name' => $booking->user_name,
                    'game_name' => $booking->game_name,
                    'court_number' => $booking->court_number,
                    'booking_date' => $booking->booking_date,
                    'start_time' => $booking->start_time,
                ]);
            }
            
            Log::info('New bookings detected via polling', [
                'count' => $newBookings->count(),
                'ids' => $newBookings->pluck('id')->toArray()
            ]);
            
            return true;
        }
        
        return false;
    }

    public function refreshBookings()
    {
        $this->loadSports();
        Log::info('Bookings refreshed via real-time event');
    }

    public function loadSports()
    {
        try {
            $this->sports = BookingSport::where('venue_id', $this->complex_id)
                ->where('status', 'Active')
                ->get();

            $this->bookings = BookingBooking::where('complex_id_id', $this->complex_id)->get();
            
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
                if (empty($game)) {
                    Log::warning('Booking with empty game_name', ['booking_id' => $booking->id]);
                    continue;
                }
                
                $date = Carbon::parse($booking->booking_date)->format('Y-m-d');
                $court = $booking->court_number ?? '1';
                $start = $booking->start_time;
                $end = $booking->end_time;

                // Parse start and end times using Carbon
                $startTime = Carbon::parse($start);
                $endTime = Carbon::parse($end);

                // Calculate the number of hours the booking spans
                $hours = $startTime->diffInHours($endTime);
                if ($hours < 1) $hours = 1; // Minimum 1 hour

                // Iterate through each hour and create a separate booking entry
                for ($i = 0; $i < $hours; $i++) {
                    // Calculate the start time for the current hour
                    $currentStartTime = $startTime->copy()->addHours($i)->format('H:i:s');

                    // Calculate the end time for the current hour
                    $currentEndTime = $startTime->copy()->addHours($i + 1)->format('H:i:s');

                    // Create a unique key for the booking slot
                    $slotKey = $currentStartTime;

                    $this->bookingdetails[$game][$date][$court][$slotKey] = [
                        'player' => $booking->user_name ?? 'Unknown',
                        'phone' => $booking->user_number ?? 'N/A',
                        'status' => $booking->status ?? 'Pending',
                        'permanent_source_id' => $booking->permanent_source_id,
                        'end' => $currentEndTime,
                        'avatar' => '/storage/staff/user.png',
                        'id' => $booking->id,
                    ];
                }
            }

            Log::info('loadSports executed successfully', [
                'games_count' => count($this->games),
                'bookings_count' => count($this->bookings),
            ]);
        } catch (\Exception $e) {
            Log::error('Error in loadSports', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            $this->addError('general', 'Failed to load sports and bookings. Please refresh the page.');
        }
    }

    public function getBookingDetails()
    {
        $this->loadSports(); // Re-run loadSports to refresh bookingdetails
        return $this->bookingdetails;
    }

    public function setSelectedBookingData($data)
    {
        // dd($data); // Debugging line to check the input data
        if (is_string($data)) {
            $data = json_decode($data, true);
        }

        $this->selectedGame = $data['game'] ?? '';

        $this->selectedDate = $data['dateKey'] ?? '';
        $this->selectedTime = $data['time'] ?? '';
        $this->selectedCourt = $data['court'] ?? '';

        Log::info('setSelectedBookingData called', [
            'input_data' => $data,
            'updated_properties' => [
                'selectedGame' => $this->selectedGame,
                'selectedDate' => $this->selectedDate,
                'selectedTime' => $this->selectedTime,
                'selectedCourt' => $this->selectedCourt,
            ],
        ]);
    }

    public function addBooking()
    {
        $this->validate();

        // Find sport by name
        $sport = BookingSport::where('name', $this->selectedGame)
            ->where('venue_id', $this->complex_id)
            ->first();

        if (!$sport) {
            $this->addError('general', 'Selected game is not available for this complex.');
            return;
        }
   
        // Get the current user (staff/admin)
        $userEmail = Auth::user()->email;
        $appUser = UserUser::where('email', $userEmail)->first();
        
        if (!$appUser) {
            $this->addError('general', 'Your user account is not properly configured. Please contact support.');
            Log::error('User not found in users_user table', ['email' => $userEmail]);
            return;
        }
        
        $this->appIndooruserId = $appUser->id;

        $endTime = Carbon::parse($this->selectedTime)->addMinutes(60)->format('H:i:s');
        $price = $sport->price ?? 1800.00;
        // Base booking data
        $bookingData = [
            'user_id_id' => $this->appIndooruserId,
            'complex_id_id' => $this->complex_id,
            'game_id_id' => $sport->id,
            'game_name' => $this->selectedGame,
            'booking_date' => $this->selectedDate,
            'permanent_source_id' => null,
            'user_name' => $this->playerName,
            'user_number' => $this->phoneNumber,
            'court_number' => $this->selectedCourt,
            'start_time' => $this->selectedTime,
            'end_time' => $endTime,
            'duration' => 60,
            'price' => $price,
            'payment_status' => 'Pending',
            'payment_method' => 'Card',
            'status' => $this->status,
            'notes' => $this->notes ?: '',
            'admin_comments' => '',
            'is_challenge_booking' => false,
            'opponent_team_id' => null,
            'team_id' => null,
        ];
        // dd($bookingData);

        try {
            if ($this->permanent) {
                // Create 7 consecutive daily bookings
                for ($i = 0; $i < 7; $i++) {
                    $bookingDate = Carbon::parse($this->selectedDate)->addDays($i)->format('Y-m-d');
                    $permanentId = ($i === 0) ? null : BookingBooking::where('booking_date', Carbon::parse($this->selectedDate)->format('Y-m-d'))
                        ->where('complex_id_id', $this->complex_id)
                        ->where('game_name', $this->selectedGame)
                        ->where('court_number', $this->selectedCourt)
                        ->where('start_time', $this->selectedTime)
                        ->first()?->id;

                    BookingBooking::create(array_merge($bookingData, [
                        'booking_date' => $bookingDate,
                        'permanent_source_id' => $permanentId,
                        'qr_code' => 'QR' . strtoupper(substr(md5(uniqid()), 0, 6)),
                    ]));
                }

                Log::info('7 consecutive bookings created', [
                    'start_date' => $this->selectedDate,
                    'end_date' => Carbon::parse($this->selectedDate)->addDays(6)->format('Y-m-d'),
                    'game' => $this->selectedGame,
                    'court' => $this->selectedCourt,
                ]);
            } else {
                // Create single booking
                BookingBooking::create(array_merge($bookingData, [
                    'booking_date' => $this->selectedDate,
                    'qr_code' => 'QR' . strtoupper(substr(md5(uniqid()), 0, 6)),
                ]));
                
                Log::info('Single booking created', [
                    'date' => $this->selectedDate,
                    'game' => $this->selectedGame,
                    'court' => $this->selectedCourt,
                ]);
            }

            $this->dispatch('bookingCreated');
            $this->dispatch('closeModal');
            $this->loadSports();
            $this->resetFields();
            session()->flash('message', 'Booking created successfully!');
        } catch (\Illuminate\Database\QueryException $e) {
            Log::error('Database error during booking creation', [
                'error' => $e->getMessage(),
                'code' => $e->getCode(),
                'booking_data' => $bookingData,
            ]);
            
            if (str_contains($e->getMessage(), 'foreign key constraint')) {
                $this->addError('general', 'Booking failed: One or more required fields are invalid. Ensure all game, user, and complex IDs are correct.');
            } else {
                $this->addError('general', 'Booking failed: ' . $e->getMessage());
            }
        } catch (\Exception $e) {
            Log::error('Unexpected error during booking creation', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            $this->addError('general', 'An unexpected error occurred. Please try again.');
        }
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
        $this->resetErrorBag();
        Log::info('resetFields executed');
    }

    // Add this to your BookingsManagement class
    public function cancelBooking($bookingId)
    {
        $booking = BookingBooking::find($bookingId);
        // dd($booking);  
        // Find the booking based on the selected criteria
        $booking = BookingBooking::where('complex_id_id', $this->complex_id)
            ->where('game_name', $this->selectedGame)
            ->where('booking_date', $this->selectedDate)
            ->where('court_number', $this->selectedCourt)
            ->where('start_time', $this->selectedTime)
            ->first();

        if ($booking) {
            $booking->status = 'Cancelled';
            $booking->save();

            Log::info('Booking cancelled', [
                'booking_id' => $booking->booking_id,
                'details' => $booking->only(['game_name', 'booking_date', 'court_number', 'start_time'])
            ]);

            $this->dispatch('bookingCancelled');
            $this->loadSports(); // Refresh the data
            session()->flash('message', 'Booking cancelled successfully.');
        } else {
            Log::warning('Booking not found for cancellation', [
                'selectedGame' => $this->selectedGame,
                'selectedDate' => $this->selectedDate,
                'selectedCourt' => $this->selectedCourt,
                'selectedTime' => $this->selectedTime
            ]);
            session()->flash('error', 'Booking not found!');
        }
    }
    public function render()
    {
        // Debug logging
        Log::info('Rendering BookingsManagement', [
            'complex_id' => $this->complex_id,
            'games_count' => count($this->games ?? []),
            'games' => $this->games,
            'bookingdetails_count' => count($this->bookingdetails ?? []),
        ]);

        return view('livewire.staff.bookings-management', [
            'games' => $this->games ?? [],
            'bookingdetails' => $this->bookingdetails ?? [],
            'complex_id' => $this->complex_id,
        ]);
    }
}
