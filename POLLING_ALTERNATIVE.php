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
class BookingsManagementPolling extends Component
{
    public $sports;
    public $bookings;
    public $complex_id;
    public $games;
    public $bookingdetails = [];
    public $lastUpdated;
    public $autoRefreshEnabled = true;

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

    protected $listeners = ['setSelectedBookingData', 'refreshBookings', 'pollForUpdates'];

    public function mount()
    {
        $this->complex_id = Auth::user()->complex_id;
        $this->loadSports();
        $this->lastUpdated = now();
    }

    public function pollForUpdates()
    {
        if (!$this->autoRefreshEnabled) {
            return;
        }

        $latestBooking = BookingBooking::where('complex_id_id', $this->complex_id)
            ->latest('created_at')
            ->first();

        if ($latestBooking && $latestBooking->created_at > $this->lastUpdated) {
            $this->loadSports();
            $this->lastUpdated = now();
            $this->dispatch('newBookingDetected', [
                'booking' => $latestBooking->toArray()
            ]);
        }
    }

    public function toggleAutoRefresh()
    {
        $this->autoRefreshEnabled = !$this->autoRefreshEnabled;
    }

    public function loadSports()
    {
        $this->sports = BookingSport::where('venue_id', $this->complex_id)
            ->where('status', 'Active')
            ->get();

        $this->bookings = BookingBooking::where('complex_id_id', $this->complex_id)->get();
        
        $this->games = $this->sports->map(function ($sport) {
            return [
                'name' => $sport->name ?? $sport->game_name ?? 'Unknown',
                'maximum_court' => $sport->maximum_court,
                'game_id' => $sport->id ?? $sport->game_id,
            ];
        })->toArray();

        $this->bookingdetails = [];
        foreach ($this->bookings as $booking) {
            $game = strtolower($booking->game_name);
            $date = Carbon::parse($booking->booking_date)->format('Y-m-d');
            $court = $booking->court_number;
            $start = $booking->start_time;
            $end = $booking->end_time;

            $startTime = Carbon::parse($start);
            $endTime = Carbon::parse($end);
            $hours = $startTime->diffInHours($endTime);

            for ($i = 0; $i < $hours; $i++) {
                $currentStartTime = $startTime->copy()->addHours($i)->format('H:i:s');
                $currentEndTime = $startTime->copy()->addHours($i + 1)->format('H:i:s');
                $slotKey = $currentStartTime;

                $permanentSourceId = null;
                try {
                    $permanentSourceId = $booking->permanent_source_id ?? null;
                } catch (\Throwable $e) {
                    Log::error('Error accessing permanent_source_id', [
                        'booking_id' => $booking->id ?? null,
                        'error' => $e->getMessage()
                    ]);
                }
                
                $this->bookingdetails[$game][$date][$court][$slotKey] = [
                    'player' => $booking->user_name,
                    'phone' => $booking->user_number,
                    'status' => $booking->status,
                    'permanent_source_id' => $permanentSourceId,
                    'end' => $currentEndTime,
                    'avatar' => '/storage/staff/user.png',
                ];
            }
        }

        Log::info('loadSports executed', [
            'games' => $this->games,
        ]);
    }

    public function refreshBookings()
    {
        $this->loadSports();
        $this->lastUpdated = now();
        Log::info('Bookings refreshed via real-time event');
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
   
        $userEmail = Auth::user()->email;
        $this->appIndooruserId = UserUser::where('email', $userEmail)->first()->id ?? null;
    
        $endTime = Carbon::parse($this->selectedTime)->addMinutes(60)->format('H:i:s');
        $price = $sport->price_per_hour ?? 1800.00;
        
        $bookingData = [
            'user_id_id' => $this->appIndooruserId,
            'complex_id_id' => $this->complex_id,
            'game_id_id' => $sport->id,
            'game_name' => $this->selectedGame,
            'booking_date' => $this->selectedDate,
            'permanent_source_id' => $this->permanentSourceId ?? null,
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

        try {
            if ($this->permanent) {
                for ($i = 0; $i < 7; $i++) {
                    $bookingDate = Carbon::parse($this->selectedDate)->addDays($i)->format('Y-m-d');

                    BookingBooking::create(array_merge($bookingData, [
                        'booking_date' => $bookingDate,
                        'permanent' => ($i === 0),
                        'qr_code' => 'QR' . strtoupper(substr(md5(uniqid()), 0, 6)),
                    ]));
                }

                Log::info('7 consecutive bookings created', [
                    'start_date' => $this->selectedDate,
                    'end_date' => Carbon::parse($this->selectedDate)->addDays(6)->format('Y-m-d')
                ]);
            } else {
                BookingBooking::create(array_merge($bookingData, [
                    'booking_date' => $this->selectedDate,
                    'permanent' => false,
                    'qr_code' => 'QR' . strtoupper(substr(md5(uniqid()), 0, 6)),
                ]));
            }

            $this->dispatch('bookingCreated');
            $this->dispatch('closeModal');
            $this->loadSports();
            $this->lastUpdated = now();
            $this->resetFields();
        } catch (\Illuminate\Database\QueryException $e) {
            if (str_contains($e->getMessage(), 'foreign key constraint fails')) {
                $this->addError('general', 'Booking failed: Your user account is not valid for booking. Please contact support.');
                Log::error('Booking failed due to foreign key constraint', [
                    'user_id_id' => Auth::id(),
                    'error' => $e->getMessage(),
                ]);
            } else {
                $this->addError('general', 'Booking failed: ' . $e->getMessage());
                Log::error('Booking failed', [
                    'error' => $e->getMessage(),
                ]);
            }
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

    public function cancelBooking($bookingId)
    {
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
            $this->loadSports();
            $this->lastUpdated = now();
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
