# Real-Time Booking Updates Setup Guide

This guide will help you set up real-time booking updates for your indoor booking dashboard.

## Option 1: Using Pusher (Recommended - Easiest)

### Step 1: Create a Pusher Account

1. Go to [https://pusher.com/](https://pusher.com/)
2. Sign up for a free account
3. Create a new Channels app
4. Get your credentials from the dashboard

### Step 2: Install Pusher PHP SDK

Run this command in your terminal:

```powershell
composer require pusher/pusher-php-server
```

### Step 3: Configure Broadcasting

1. Open `.env` file and update these values:

```env
BROADCAST_DRIVER=pusher

PUSHER_APP_ID=your_app_id
PUSHER_APP_KEY=your_app_key
PUSHER_APP_SECRET=your_app_secret
PUSHER_APP_CLUSTER=your_cluster
```

2. Open `config/broadcasting.php` and ensure Pusher is configured:

```php
'pusher' => [
    'driver' => 'pusher',
    'key' => env('PUSHER_APP_KEY'),
    'secret' => env('PUSHER_APP_SECRET'),
    'app_id' => env('PUSHER_APP_ID'),
    'options' => [
        'cluster' => env('PUSHER_APP_CLUSTER'),
        'encrypted' => true,
    ],
],
```

### Step 4: Update BroadcastServiceProvider

Open `app/Providers/BroadcastServiceProvider.php` and uncomment:

```php
public function boot(): void
{
    Broadcast::routes();

    require base_path('routes/channels.php');
}
```

Also, in `config/app.php`, uncomment this line in the providers array:

```php
App\Providers\BroadcastServiceProvider::class,
```

### Step 5: Configure Channels

Open `routes/channels.php` and add:

```php
Broadcast::channel('bookings.{complexId}', function ($user, $complexId) {
    return true; // Adjust based on your authentication logic
});
```

---

## Option 2: Using Laravel Reverb (For Laravel 11+)

### Step 1: Install Laravel Reverb

```powershell
php artisan install:broadcasting
```

### Step 2: Configure .env

```env
BROADCAST_DRIVER=reverb

REVERB_APP_ID=your-app-id
REVERB_APP_KEY=your-app-key
REVERB_APP_SECRET=your-app-secret
REVERB_HOST="localhost"
REVERB_PORT=8080
REVERB_SCHEME=http
```

### Step 3: Start Reverb Server

```powershell
php artisan reverb:start
```

### Step 4: Update Frontend Script

In your blade file, replace Pusher initialization with:

```javascript
const echo = new Echo({
    broadcaster: 'reverb',
    key: '{{ config("broadcasting.connections.reverb.key") }}',
    wsHost: '{{ config("broadcasting.connections.reverb.options.host") }}',
    wsPort: {{ config("broadcasting.connections.reverb.options.port") }},
    wssPort: {{ config("broadcasting.connections.reverb.options.port") }},
    forceTLS: false,
    enabledTransports: ['ws', 'wss'],
});

echo.channel(`bookings.${complexId}`)
    .listen('.booking.created', (data) => {
        console.log('New booking received:', data);
        showNotification('New Booking!', `${data.user_name} booked ${data.game_name}`);
        refreshBookingData().then(() => updateCalendar());
    });
```

---

## Testing

### Test from Mobile App

When your mobile app creates a booking, it should automatically appear on the dashboard.

### Test Manually

1. Open two browser tabs with the dashboard
2. Create a booking in one tab
3. Watch it appear in the other tab automatically

### Debug

Check browser console for messages:
- "Real-time updates initialized for complex: X"
- "New booking received: {...}"

Check Laravel logs:
- `storage/logs/laravel.log` should show "BookingCreatedEvent broadcasted"

---

## Important Notes

1. **Queue Worker**: For production, run the queue worker:
   ```powershell
   php artisan queue:work
   ```

2. **Mobile App Integration**: Ensure your mobile app is creating `BookingBooking` records in the same database. The observer will automatically broadcast events.

3. **Multiple Complexes**: The channel is scoped by complex_id, so each complex only receives their own bookings.

4. **Security**: In production, update the channel authorization in `routes/channels.php` to verify user permissions.

---

## Troubleshooting

### Events not broadcasting?
- Check BROADCAST_DRIVER in .env
- Ensure BroadcastServiceProvider is registered
- Check queue worker is running
- Verify Pusher/Reverb credentials

### Frontend not receiving events?
- Check browser console for errors
- Verify Pusher key in blade template
- Test Pusher connection in Pusher dashboard

### Database not updating?
- Check that BookingObserver is registered in AppServiceProvider
- Verify the mobile app is using the same database
