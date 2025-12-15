# 🚀 WebSocket Real-Time Update Testing Guide

## ✅ How It Works Now

The system uses **DUAL UPDATE MECHANISMS** to ensure all changes are detected:

1. **🔴 WebSocket (Laravel Reverb)** - For bookings created through the system
   - Instant real-time updates
   - No delay
   - Triggered by: Form submissions, API calls, Eloquent model saves

2. **🔵 Database Polling** - For direct database changes
   - Checks every 5 seconds
   - Catches bookings created via phpMyAdmin, SQL queries, or external tools
   - Always runs in background

**Result:** Whether you create a booking through the website OR directly in the database, the page will update automatically!

---

## ✅ Prerequisites (Keep These Running)

You need **3 terminals** running simultaneously:

### Terminal 1: Laravel Reverb WebSocket Server
```powershell
php artisan reverb:start --host="127.0.0.1" --port=8080 --debug
```
**Status:** Should show "Starting server on 127.0.0.1:8080"

### Terminal 2: Vite Dev Server
```powershell
npm run dev
```
**Status:** Should show "VITE ready" on http://localhost:5173

### Terminal 3: For Testing (Keep Available)
Use this to run test commands

---

## 🧪 Testing Steps

### Step 1: Open the Booking Management Page
1. Open browser and navigate to: `http://127.0.0.1:8000/facility_owner/bookings`
2. Open browser console (Press **F12**)
3. Look for these console messages:
   ```
   ✅ Laravel Echo is available
   📍 Complex ID: 1 (or your complex ID)
   📡 Subscribing to channel: bookings.1
   ✅ Channel subscribed
   ✅ WebSocket connected successfully
   ✅ Real-time WebSocket updates initialized for complex: 1
   ```

### Step 2: Run the Real-Time Test
In Terminal 3, run:
```powershell
php test_realtime_booking.php
```

### Step 3: Verify Real-Time Updates
You should see **immediately** (without refreshing):

✅ **In the Browser:**
- 🔔 A notification popup appears (top-right corner)
- 📊 New booking shows up in the calendar
- 🔊 Notification sound plays

✅ **In Browser Console:**
```javascript
🔔 New booking received via WebSocket: {booking_id: 615, user_name: "...", ...}
🔄 Booking updated via WebSocket: {booking_id: 615, status: "Playing", ...}
```

✅ **In Reverb Terminal (Terminal 1):**
Should show connection and message activity

✅ **In Laravel Logs:**
```bash
# Check logs
Get-Content storage\logs\laravel.log -Tail 20 | Select-String "Booking"
```
Should show:
```
BookingCreatedEvent broadcasted {"booking_id":615,"complex_id":1}
BookingUpdatedEvent broadcasted
```

---

## 🐛 Troubleshooting

### Problem: No WebSocket Connection in Browser Console

**Check 1: Is Reverb Running?**
```powershell
netstat -ano | Select-String ":8080"
```
Should show a LISTENING connection

**Fix:**
```powershell
php artisan reverb:start --host="127.0.0.1" --port=8080 --debug
```

---

### Problem: "Laravel Echo not initialized"

**Check 2: Is Vite Dev Server Running?**
```powershell
Get-Process | Where-Object { $_.ProcessName -like "*node*" }
```
Should show node processes

**Fix:**
```powershell
npm run dev
```

Then **refresh the browser page** (Ctrl+F5)

---

### Problem: Events Broadcast But Page Doesn't Update

**Check 3: Verify .env Configuration**
```powershell
Get-Content .env | Select-String "BROADCAST_DRIVER|REVERB"
```

Should show:
```
BROADCAST_DRIVER=reverb
REVERB_APP_ID=167934
REVERB_APP_KEY=3va3ybyizbhgzlwvydzv
REVERB_APP_SECRET=0risdz6myz7sfi8i30yk
REVERB_HOST="localhost"
REVERB_PORT=8080
REVERB_SCHEME=http
VITE_REVERB_APP_KEY="${REVERB_APP_KEY}"
VITE_REVERB_HOST="${REVERB_HOST}"
VITE_REVERB_PORT="${REVERB_PORT}"
VITE_REVERB_SCHEME="${REVERB_SCHEME}"
```

**Fix if wrong:**
1. Update `.env` file
2. Clear config cache: `php artisan config:cache`
3. Restart Reverb server
4. Restart Vite: `npm run dev`
5. Hard refresh browser (Ctrl+Shift+R)

---

### Problem: Wrong Complex ID or Channel

**Check 4: Verify Complex ID in Console**
In browser console, check:
```javascript
console.log(window.bookingsChannel);
```

Should show your subscribed channel name like `bookings.1`

**Fix:**
Make sure you're logged in as a user with the correct `complex_id`

---

### Problem: Still Not Working After All Checks

**Nuclear Option - Full Restart:**
```powershell
# 1. Stop all servers (Ctrl+C in each terminal)

# 2. Clear all caches
php artisan config:cache
php artisan cache:clear
php artisan view:clear

# 3. Restart in order:
# Terminal 1:
php artisan reverb:start --host="127.0.0.1" --port=8080 --debug

# Terminal 2:
npm run dev

# 4. Wait 5 seconds, then hard refresh browser (Ctrl+Shift+R)

# 5. Run test:
php test_realtime_booking.php
```

---

## 📊 Manual Testing (Without Script)

### Test 1: Create via System Form (WebSocket - Instant)
1. Open booking management page
2. Create a new booking through the form
3. Should see **instant** real-time update (< 1 second)
4. Console: `🔔 New booking received via WebSocket`

### Test 2: Create via phpMyAdmin/SQL (Polling - 5 sec delay)
Open phpMyAdmin and run this SQL (or use `test_direct_database_insert.sql`):
```sql
INSERT INTO booking_booking (
    user_id_id, complex_id_id, game_id_id, game_name, 
    booking_date, user_name, user_number, court_number,
    start_time, end_time, duration, price, 
    payment_status, status, qr_code, is_challenge_booking,
    created_at, updated_at
) VALUES (
    1, 1, 1, 'Football',
    CURDATE(), 'Direct DB Test', '+1234567890', '2',
    '16:00:00', '17:00:00', 60, 1800,
    'Pending', 'Confirmed', 'SQLTEST1', 0,
    NOW(), NOW()
);
```

Within **5 seconds** you should see:
- ✅ Notification: `🔄 New Booking Detected`
- ✅ Booking appears in calendar
- ✅ Console: `📊 New bookings detected via database polling`

### Test 3: Update Status via Database
```sql
UPDATE booking_booking 
SET status = 'Playing' 
WHERE id = (SELECT MAX(id) FROM (SELECT * FROM booking_booking) AS temp);
```
Within **5 seconds**, status updates on page automatically.

---

## 🎯 What Changed (Summary)

### Files Modified:
1. **`app/Events/BookingCreatedEvent.php`**
   - Changed: `ShouldBroadcast` → `ShouldBroadcastNow`
   - Effect: Broadcasts immediately without queue worker

2. **`app/Events/BookingUpdatedEvent.php`**
   - Changed: `ShouldBroadcast` → `ShouldBroadcastNow`
   - Effect: Broadcasts immediately without queue worker

3. **`resources/views/livewire/staff/bookings-management.blade.php`**
   - Added: Better console logging for debugging
   - Added: `window.bookingsChannel` for testing
   - **Modified: Database polling now runs ALONGSIDE WebSocket**
   - **Effect: Catches direct database inserts/updates**

### Why It Works Now:
- **WebSocket (Instant):** For bookings created through the system
- **Polling (5 sec):** For direct database changes via phpMyAdmin/SQL
- **Result:** ALL changes are detected automatically, no refresh needed!

---

## 📞 Need Help?

If WebSocket is still not working:

1. Share screenshot of browser console (F12)
2. Share output of: `Get-Content storage\logs\laravel.log -Tail 50`
3. Share Reverb terminal output
4. Confirm all 3 terminals are running

---

## ✨ Success Indicators

When everything is working correctly:

✅ Browser console shows WebSocket connected  
✅ New bookings appear instantly without refresh  
✅ Notifications show up for new bookings  
✅ Status updates reflect immediately  
✅ Multiple users see updates simultaneously  
✅ No need to manually refresh page  

**This is true real-time functionality!** 🎉
