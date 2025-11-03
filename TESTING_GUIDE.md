# 🧪 Quick Testing Guide

## You Are Here: http://127.0.0.1:8000/facility_owner/bookings
## Logged in as: BookingVenue ID 2 user
## Current Booking: 6-7 AM showing

---

## 🚀 FAST TEST (2 Methods)

### **METHOD 1: Two Browser Tabs Test** (Easiest)

1. **Keep current tab open** at: http://127.0.0.1:8000/facility_owner/bookings

2. **Open a NEW browser tab** (or incognito window) and go to:
   http://127.0.0.1:8000/facility_owner/bookings

3. **In the NEW tab**: 
   - Click on an available slot (any green slot)
   - Fill in the booking form
   - Click "Create Booking"

4. **Watch the FIRST tab**:
   - The new booking should appear automatically
   - You'll see a notification popup
   - No page refresh needed! 🎉

---

### **METHOD 2: Simulate Mobile App** (More Realistic)

This simulates a mobile app creating a booking.

#### Step 1: Clear cache
```powershell
cd C:\Users\MY\Desktop\indoor
php artisan config:clear
php artisan cache:clear
```

#### Step 2: Keep your dashboard open
- Keep http://127.0.0.1:8000/facility_owner/bookings open in browser
- Don't refresh the page

#### Step 3: Run test script
```powershell
php test_create_booking.php
```

#### Step 4: Watch the dashboard
- A new booking should appear automatically
- You'll see a notification
- Look at browser console (F12) for debug messages

---

## ⚠️ IMPORTANT: Get Real Pusher Credentials

**The credentials I added are dummy/example credentials!** 

You need to:

1. **Go to**: https://pusher.com/
2. **Sign up** for free account
3. **Create a new app** (choose "Channels")
4. **Copy your credentials** from the dashboard

5. **Update `.env` file** with YOUR real credentials:
```env
PUSHER_APP_ID=your_actual_app_id
PUSHER_APP_KEY=your_actual_key
PUSHER_APP_SECRET=your_actual_secret
PUSHER_APP_CLUSTER=your_cluster (like mt1, ap2, eu, etc.)
```

6. **Run**:
```powershell
php artisan config:clear
```

---

## 🔍 Debugging - What to Check

### Browser Console (Press F12)

You should see these messages:
```
✅ Initializing booking system...
✅ Real-time updates initialized for complex: 2
✅ New booking received: {...}
```

If you see errors:
- Check Pusher credentials in .env
- Make sure BroadcastServiceProvider is enabled
- Check browser console for specific errors

### Laravel Logs

Check: `storage/logs/laravel.log`

You should see:
```
BookingCreatedEvent broadcasted
Bookings refreshed via real-time event
```

### Pusher Debug Console

1. Go to https://dashboard.pusher.com/
2. Click on your app
3. Go to "Debug Console" tab
4. When a booking is created, you'll see the event broadcast there

---

## 📋 What You'll See When It Works

1. **Dashboard is open** at http://127.0.0.1:8000/facility_owner/bookings
2. **New booking is created** (from mobile app or other tab)
3. **Notification appears** in top-right corner:
   ```
   🔔 New Booking!
   Test User from Mobile booked Cricket - Court 1
   ```
4. **Calendar updates automatically** - new booking appears in the slot
5. **No page reload** needed!

---

## ⚡ Quick Checklist

Before testing, verify:

- [ ] Pusher installed: `composer show pusher/pusher-php-server`
- [ ] .env updated with Pusher credentials
- [ ] BroadcastServiceProvider enabled in config/app.php
- [ ] config:clear run
- [ ] Dashboard open in browser
- [ ] Browser console open (F12) to see debug messages

---

## 🆘 If It's Not Working

### Test 1: Check Pusher Connection
Open browser console and paste:
```javascript
const pusher = new Pusher('YOUR_PUSHER_KEY', {
  cluster: 'YOUR_CLUSTER'
});
console.log(pusher.connection.state);
```

Should show: `connected`

### Test 2: Check Observer
Run:
```powershell
php artisan tinker
```

Then:
```php
$booking = App\Models\BookingBooking::first();
event(new App\Events\BookingCreatedEvent($booking));
```

Check browser - should see the event.

### Test 3: Check Logs
```powershell
Get-Content storage\logs\laravel.log -Tail 20
```

---

## 🎯 For Mobile App Integration

When your mobile app creates a booking, it should:

1. **Insert into database** (table: `booking_booking`)
2. **Observer automatically fires** the event
3. **Event broadcasts** via Pusher
4. **Dashboard receives** and updates automatically

**No special code needed in mobile app** - just insert the booking normally!

---

## 📞 Need Help?

- Check browser console (F12) for errors
- Check Laravel logs: `storage/logs/laravel.log`
- Check Pusher dashboard: https://dashboard.pusher.com/
- Verify database connection
- Make sure you're logged in to the dashboard

---

Good luck! 🚀
