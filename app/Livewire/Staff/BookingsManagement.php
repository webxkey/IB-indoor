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
    public $status = 'Booked';
    public $permanent = false;
    public $notes = '';

    protected $rules = [
        'selectedGame' => 'required|string',
        'selectedDate' => 'required|date_format:Y-m-d',
        'selectedTime' => 'required|date_format:H:i:s',
        'selectedCourt' => 'required|string',
        'playerName' => 'required|string|max:255',
        'phoneNumber' => 'required|string|max:20',
        'status' => 'required|in:Booked,Pending,Completed,Cancelled,No-Show',
        'permanent' => 'boolean',
        'notes' => 'nullable|string|max:1000',
    ];

    protected $listeners = ['setSelectedBookingData'];

    public function mount()
    {
        $this->complex_id = Auth::user()->complex_id;
        $this->loadSports();
    }

    public function loadSports()
    {
        $this->sports = BookingSport::where('venue_id', $this->complex_id)
            ->where('status', 'Active')
            ->get();

        // dd($this->sports);
        $this->bookings = BookingBooking::where('complex_id_id', $this->complex_id)->get();
        // dd($this->bookings);
        $this->games = $this->sports->map(function ($sport) {
            // Use the correct attribute for the sport name
            return [
                'name' => $sport->name ?? $sport->game_name ?? 'Unknown',
                'maximum_court' => $sport->maximum_court,
                'game_id' => $sport->id ?? $sport->game_id,
            ];
        })->toArray();

        // dd($this->games);

        $this->bookingdetails = [];
        foreach ($this->bookings as $booking) {
            $game = strtolower($booking->game_name);
            $date = Carbon::parse($booking->booking_date)->format('Y-m-d');
            $court = $booking->court_number;
            $start = $booking->start_time;
            $end = $booking->end_time;

            // Parse start and end times using Carbon
            $startTime = Carbon::parse($start);
            $endTime = Carbon::parse($end);

            // Calculate the number of hours the booking spans
            $hours = $startTime->diffInHours($endTime);

            // Iterate through each hour and create a separate booking entry
            for ($i = 0; $i < $hours; $i++) {
                // Calculate the start time for the current hour
                $currentStartTime = $startTime->copy()->addHours($i)->format('H:i:s');

                // Calculate the end time for the current hour
                $currentEndTime = $startTime->copy()->addHours($i + 1)->format('H:i:s');

                // Create a unique key for the booking slot
                $slotKey = $currentStartTime;

                // Populate booking details for the current hour
                $this->bookingdetails[$game][$date][$court][$slotKey] = [
                    'player' => $booking->user_name,
                    'phone' => $booking->user_number,
                    'status' => $booking->status,
                    'permanent' => $booking->permanent,
                    'end' => $currentEndTime, // Set the end time for the hourly slot
                    'avatar' => '/storage/staff/user.png', // Assuming a default avatar
                ];
            }
        }

        Log::info('loadSports executed', [
            'games' => $this->games,
        ]);
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

        $sport = BookingSport::where('name', $this->selectedGame)
            ->where('venue_id', $this->complex_id)
            ->first();

        if (!$sport) {
            $this->addError('general', 'Selected game is not available for this complex.');
            return;
        }

        $endTime = Carbon::parse($this->selectedTime)->addMinutes(60)->format('H:i:s');
        $price = $sport->price_per_hour ?? 1800.00;

        // Base booking data
        $bookingData = [
            'user_id_id' => Auth::id(), // Ensure user_id_id is set
            'complex_id_id' => $this->complex_id, // Ensure complex_id_id is set
            'game_id_id' => $sport->id, // Ensure game_id_id is set - use $sport->id
            'game_name' => $this->selectedGame,
            'user_name' => $this->playerName,
            'user_number' => $this->phoneNumber,
            'court_number' => $this->selectedCourt,
            'start_time' => $this->selectedTime,
            'end_time' => $endTime,
            'duration' => 60,
            'price' => $price,
            'payment_status' => 'Pending',
            'payment_method' => 'Stripe',
            'status' => $this->status,
            'notes' => $this->notes ?: '',
            'admin_comments' => '',
        ];
        // dd($bookingData);

        if ($this->permanent) {
            // Create 7 consecutive daily bookings
            for ($i = 0; $i < 7; $i++) {
                $bookingDate = Carbon::parse($this->selectedDate)->addDays($i)->format('Y-m-d');

                BookingBooking::create(array_merge($bookingData, [
                    'booking_date' => $bookingDate,
                    'permanent' => ($i === 0), // Only mark first booking as permanent
                    'qr_code' => 'QR' . strtoupper(substr(md5(uniqid()), 0, 6)),
                ]));
            }

            Log::info('7 consecutive bookings created', [
                'start_date' => $this->selectedDate,
                'end_date' => Carbon::parse($this->selectedDate)->addDays(6)->format('Y-m-d')
            ]);
        } else {
            // Create single booking
            BookingBooking::create(array_merge($bookingData, [
                'booking_date' => $this->selectedDate,
                'permanent' => false,
                'qr_code' => 'QR' . strtoupper(substr(md5(uniqid()), 0, 6)),
            ]));
        }

        $this->dispatch('bookingCreated');
        $this->dispatch('closeModal');
        $this->loadSports();
        $this->resetFields();
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
        dd($booking);
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
        return view('livewire.staff.bookings-management', [
            'games' => $this->games,
            'bookingdetails' => $this->bookingdetails,
        ]);
    }
}
