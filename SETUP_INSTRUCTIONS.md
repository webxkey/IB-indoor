# Quick Start Guide - Real-Time Booking Updates

## ✅ What We've Implemented

Your dashboard will now automatically show new bookings from the mobile app without page reload!

---

## 🚀 Quick Setup (Choose ONE option)

### **OPTION 1: Pusher (Recommended - Most Reliable)** ⭐

#### Step 1: Install Pusher
```powershell
composer require pusher/pusher-php-server
```

#### Step 2: Get Pusher Credentials
1. Go to https://pusher.com and create free account
2. Create a new "Channels" app
3. Copy your credentials

#### Step 3: Update .env
```env
BROADCAST_DRIVER=pusher

PUSHER_APP_ID=your_app_id
PUSHER_APP_KEY=your_app_key
PUSHER_APP_SECRET=your_app_secret
PUSHER_APP_CLUSTER=mt1
```

#### Step 4: Enable Broadcasting
Open `config/app.php` and uncomment:
```php
App\Providers\BroadcastServiceProvider::class,
```

Open `app/Providers/BroadcastServiceProvider.php` and uncomment in boot():
```php
public function boot(): void
{
    Broadcast::routes();
    require base_path('routes/channels.php');
}
```

#### Step 5: Configure Channel
Open `routes/channels.php` and add:
```php
Broadcast::channel('bookings.{complexId}', function ($user, $complexId) {
    return true; // Allow all authenticated users
});
```

#### Step 6: Clear Cache
```powershell
php artisan config:clear
php artisan cache:clear
```

#### Step 7: Test!
- Open dashboard in two browser tabs
- Create a booking from mobile app or one tab
- Watch it appear in the other tab automatically! 🎉

---

### **OPTION 2: Simple Polling (No External Services)**

If you don't want to use Pusher, use this simpler approach:

#### Step 1: Update your Livewire component
Replace the current `BookingsManagement.php` content with the polling version:
```powershell
Copy-Item POLLING_ALTERNATIVE.php app\Livewire\Staff\BookingsManagement.php -Force
```

#### Step 2: Add polling to JavaScript
In your blade file, add this to the `initializeSystem()` function:

```javascript
function initializeSystem() {
    // ... existing code ...
    
    setupPollingUpdates(); // Add this line
}

// Add this function
function setupPollingUpdates() {
    setInterval(() => {
        if (!document.hidden) {
            Livewire.dispatch('pollForUpdates');
        }
    }, 5000); // Check every 5 seconds
}
```

#### Step 3: Listen for updates
Add this to your JavaScript:
```javascript
window.addEventListener('newBookingDetected', function(event) {
    const booking = event.detail.booking;
    showNotification('New Booking!', `${booking.user_name} booked ${booking.game_name}`);
    refreshBookingData().then(() => updateCalendar());
});
```

---

## 🔧 For Mobile App Integration

### Important: Make sure your mobile app does this:

When creating a booking in the mobile app, use the same database and table:

```dart
// Flutter/Dart example
await supabase.from('booking_booking').insert({
  'complex_id_id': complexId,
  'game_name': gameName,
  'booking_date': bookingDate,
  'court_number': courtNumber,
  'start_time': startTime,
  'end_time': endTime,
  'user_name': userName,
  'user_number': userNumber,
  // ... other fields
});
```

The `BookingObserver` will automatically detect this and broadcast the event!

---

## 🧪 Testing

### Test 1: From Dashboard
1. Open dashboard in two browser tabs
2. Create a booking in one tab
3. Should appear in other tab within 1-5 seconds

### Test 2: From Mobile App
1. Open dashboard on computer
2. Create a booking from mobile app
3. Should appear on dashboard within 1-5 seconds

### Check Browser Console
You should see:
- ✅ "Real-time updates initialized for complex: X"
- ✅ "New booking received: {...}"

### Check Laravel Logs
```powershell
Get-Content storage\logs\laravel.log -Tail 50
```

Should show:
- ✅ "BookingCreatedEvent broadcasted"
- ✅ "Bookings refreshed via real-time event"

---

## 🐛 Troubleshooting

### Problem: Events not broadcasting
**Solution:**
```powershell
# Check .env file
BROADCAST_DRIVER=pusher  # Must be set

# Clear config
php artisan config:clear

# Check observer is registered
# Open app/Providers/AppServiceProvider.php
# Should have: BookingBooking::observe(BookingObserver::class);
```

### Problem: Frontend not receiving events
**Solution:**
1. Check browser console for errors
2. Verify Pusher key in blade template matches .env
3. Check Pusher dashboard for connection status

### Problem: Works in dashboard but not from mobile app
**Solution:**
1. Ensure mobile app uses same database
2. Check mobile app inserts into `booking_booking` table
3. Verify `complex_id_id` matches

---

## 🎯 Performance Tips

### For Production:

1. **Use Queue Worker:**
```powershell
php artisan queue:work
```

2. **Optimize Polling Interval:**
- Pusher: Real-time (instant)
- Polling: Adjust from 5000ms to 10000ms if needed

3. **Add Redis Cache:**
```powershell
composer require predis/predis
```

Update `.env`:
```env
CACHE_DRIVER=redis
QUEUE_CONNECTION=redis
```

---

## 📊 Comparison

| Feature | Pusher | Polling |
|---------|--------|---------|
| Speed | Instant | 5-10 seconds |
| Server Load | Low | Medium |
| Setup Complexity | Medium | Easy |
| External Service | Yes (Free tier: 100 connections) | No |
| Recommended For | Production | Development/Testing |

---

## ✨ Features Included

✅ Real-time booking updates
✅ Visual notifications with sound
✅ Auto-refresh calendar
✅ Multi-complex support
✅ Works with mobile app
✅ No page reload needed
✅ Toast notifications
✅ Error handling

---

## 📞 Support

If you have issues:
1. Check Laravel logs: `storage/logs/laravel.log`
2. Check browser console (F12)
3. Verify database connection
4. Test with both dashboard and mobile app

Happy coding! 🚀
