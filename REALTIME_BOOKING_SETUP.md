# Real-Time Booking System Setup

## Overview

This system uses **Laravel Reverb** (WebSocket) to provide real-time updates when bookings are made from the mobile app.

## How It Works

1. **Mobile App** → Creates booking in database → **BookingObserver** triggers
2. **BookingObserver** → Broadcasts `BookingCreatedEvent` via WebSocket
3. **Staff Dashboard** → Receives event via Laravel Echo → Updates calendar instantly

## Starting the WebSocket Server

### Option 1: Use the batch file

Double-click `start-reverb.bat` in the project root.

### Option 2: Run manually

```bash
cd c:\Users\ABC\Desktop\WebXkey Project\IB-indoor
php artisan reverb:start --host=0.0.0.0 --port=8080
```

### Option 3: Run in background (production)

```bash
php artisan reverb:start --host=0.0.0.0 --port=8080 --daemon
```

## Configuration

### .env Settings (already configured)

```env
BROADCAST_DRIVER=reverb
REVERB_APP_ID=167934
REVERB_APP_KEY=3va3ybyizbhgzlwvydzv
REVERB_APP_SECRET=0risdz6myz7sfi8i30yk
REVERB_HOST=localhost
REVERB_PORT=8080
REVERB_SCHEME=http

VITE_REVERB_APP_KEY="${REVERB_APP_KEY}"
VITE_REVERB_HOST="${REVERB_HOST}"
VITE_REVERB_PORT="${REVERB_PORT}"
VITE_REVERB_SCHEME="${REVERB_SCHEME}"
```

## Testing Real-Time Updates

### Test from PHP (simulates mobile app booking)

```php
// Run: php artisan tinker

use App\Models\BookingBooking;

BookingBooking::create([
    'user_id_id' => 1,
    'complex_id_id' => 1,  // Your complex ID
    'game_id_id' => 1,
    'game_name' => 'football',
    'booking_date' => now()->format('Y-m-d'),
    'court_number' => '1',
    'start_time' => '19:00:00',
    'end_time' => '20:00:00',
    'duration' => 60,
    'price' => 1800,
    'user_name' => 'Mobile User',
    'user_number' => '0771234567',
    'status' => 'Confirmed',
    'payment_status' => 'Pending',
    'qr_code' => 'QR' . strtoupper(substr(md5(uniqid()), 0, 6)),
]);
```

## Fallback Mechanism

If WebSocket is unavailable:

-   **Polling** automatically activates every 10 seconds
-   Staff dashboard will still receive updates (with slight delay)
-   No manual action needed

## Troubleshooting

### WebSocket not connecting

1. Make sure Reverb server is running: `php artisan reverb:start`
2. Check browser console for errors
3. Verify .env settings match

### Bookings not appearing

1. Check Laravel logs: `storage/logs/laravel.log`
2. Verify BookingObserver is registered in `AppServiceProvider`
3. Test manually using tinker command above

### Mobile app configuration

The mobile app should create bookings in the same database. The BookingObserver will automatically broadcast the event.

## Files Involved

-   `app/Observers/BookingObserver.php` - Triggers broadcast on create/update
-   `app/Events/BookingCreatedEvent.php` - Event that gets broadcast
-   `routes/channels.php` - Channel authorization
-   `resources/js/bootstrap.js` - Laravel Echo configuration
-   `resources/views/livewire/staff/bookings-management.blade.php` - Listens for events
