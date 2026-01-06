# Real-Time Booking System - Quick Troubleshooting Checklist

## ✅ Before You Start

- [ ] PHP 8.1+ installed (`php --version`)
- [ ] Node.js 16+ installed (`node --version`)
- [ ] PostgreSQL running and accessible
- [ ] Laravel installed globally (optional but helpful)

---

## 🚀 Getting Started

### Step 1: Build Assets
```bash
npm install
npm run dev
```
Keep this terminal open!

### Step 2: Start Reverb WebSocket Server (Terminal 2)
```bash
php artisan reverb:start
```

**Expected output:**
```
Starting Reverb server on ws://127.0.0.1:8080...
```

Keep this open! Don't close it.

### Step 3: Start Laravel (Terminal 3)
```bash
php artisan serve
```

**Expected output:**
```
Server running on [http://127.0.0.1:8000]
```

### Step 4: Test It!
1. Open http://127.0.0.1:8000 in browser
2. Go to Admin Bookings Dashboard
3. Open a **second browser tab** with same URL
4. Create a test booking in one tab
5. Watch the other tab update **instantly** ✨

---

## ❌ If It's Not Working

### Problem: "WebSocket connection failed"

**Check 1:** Is Reverb running?
```bash
# In terminal running reverb:start
# You should see: "WebSocket listening..."
```

**Check 2:** Check console for errors
- Open browser DevTools (F12)
- Go to Console tab
- Look for red errors about WebSocket

**Check 3:** Firewall blocking port 8080?
```bash
# Windows - Check if port 8080 is open
netstat -ano | findstr :8080
```

**Check 4:** Wrong Reverb config
```env
# .env should have:
BROADCAST_DRIVER=reverb
REVERB_HOST=127.0.0.1
REVERB_PORT=8080
REVERB_SCHEME=http
```

---

### Problem: "Events not broadcasting"

**Check 1:** Is `BROADCAST_DRIVER=reverb`?
```bash
grep BROADCAST_DRIVER .env
```

**Check 2:** Assets built?
```bash
npm run dev
# Keep running in background
```

**Check 3:** Observer registered?
```bash
# Check AppServiceProvider.php line 21:
BookingBooking::observe(BookingBookingObserver::class);
```

**Check 4:** Laravel logs show errors?
```bash
tail -f storage/logs/laravel.log
# Create a booking and watch for errors
```

---

### Problem: "Real-time updates work once, then stop"

**Check 1:** Browser cache issue
- Hard refresh: `Ctrl+Shift+R` or `Cmd+Shift+R`
- Clear cookies
- Try incognito mode

**Check 2:** WebSocket disconnected
- Check browser DevTools > Network > WS
- Look for "101 Switching Protocols"
- If showing as closed, restart Reverb

**Check 3:** Too many connections
- Check Reverb terminal for errors
- Restart: `php artisan reverb:start`

---

### Problem: "Admin sees old booking data"

**Check 1:** Database has the booking?
```bash
php artisan tinker
App\Models\BookingBooking::latest()->first();
```

**Check 2:** Correct complex_id?
```bash
# Logged-in admin's complex_id:
auth()->user()->complex_id

# Booking's complex_id:
$booking->complex_id_id
# These must match!
```

**Check 3:** Reload page to fetch initial data
- The real-time system updates **after initial load**
- If page loaded before booking was created, reload page once

---

## 🧪 Testing Procedures

### Test 1: Manual Booking Creation
```bash
php artisan tinker
```

```php
# Create a booking
App\Models\BookingBooking::create([
    'user_name' => 'Test User',
    'game_name' => 'Cricket',
    'user_number' => '123456789',
    'court_number' => '1',
    'booking_date' => '2024-01-15',
    'start_time' => '10:00:00',
    'end_time' => '11:00:00',
    'complex_id_id' => 1,
    'payment_status' => 'Pending',
    'status' => 'Confirmed'
]);
```

**Result:** Should see notification in dashboard instantly

### Test 2: Status Update
```php
$booking = App\Models\BookingBooking::latest()->first();
$booking->status = 'Playing';
$booking->save();
```

**Result:** Status should update instantly on all admin dashboards

### Test 3: Booking Deletion
```php
$booking = App\Models\BookingBooking::latest()->first();
$booking->delete();
```

**Result:** Booking should disappear instantly from all dashboards

---

## 📊 Monitoring

### Browser Console (F12)
```javascript
// Should see these messages:
✅ Laravel Echo initialized for real-time updates
✅ Real-time WebSocket listener connected for bookings.complex.1
📱 New Booking Created! {...}
```

### Reverb Terminal
```
New client connected: ws://127.0.0.1:8080
Subscribed to channel: bookings.complex.1
Broadcasting event: booking.created
```

### Laravel Logs
```bash
tail -f storage/logs/laravel.log

# Look for:
# "BookingBooking created and broadcasted"
# "BookingBooking updated and broadcasted"
```

---

## 🔄 Restart Procedure

If something breaks:

**1. Stop everything** (Ctrl+C in all terminals)

**2. Clear cache**
```bash
php artisan cache:clear
php artisan config:clear
php artisan view:clear
```

**3. Restart Reverb** (Terminal 1)
```bash
php artisan reverb:start
```

**4. Restart Laravel** (Terminal 2)
```bash
php artisan serve
```

**5. Rebuild assets** (Terminal 3)
```bash
npm run dev
```

**6. Hard refresh browser** (Ctrl+Shift+R)

---

## 📈 Performance Check

### Monitor active connections
```bash
# In Reverb terminal, watch for:
# "New client connected"
# "Client disconnected"
# Should match number of admin tabs open
```

### Check database queries
```bash
# Enable query logging in config/app.php
'log' => 'queries' // Add this

# Then watch:
tail -f storage/logs/laravel.log | grep SELECT
```

---

## 🎯 Production Checklist

Before deploying:

- [ ] Test with 10+ simultaneous admin users
- [ ] Verify Reverb handles the load
- [ ] Setup SSL certificates for WSS (secure WebSocket)
- [ ] Configure Redis scaling in `config/reverb.php`
- [ ] Update `.env` for production domain
- [ ] Test on staging environment first
- [ ] Monitor logs and performance
- [ ] Setup alerts for Reverb server going down

---

## 📞 Getting Help

### Common Issues & Solutions

| Issue | Solution |
|-------|----------|
| Port 8080 in use | Change `REVERB_PORT` in `.env` |
| CSRF token mismatch | Clear cookies and hard refresh |
| "Too many open files" | Increase system file limit: `ulimit -n 65535` |
| Slow updates | Check network latency: DevTools > Network |
| Memory issues | Monitor: `php artisan reverb:start --debug` |

### Debug Mode

Enable detailed logging:
```bash
# See every event
php artisan reverb:start --debug

# In Laravel
LOG_LEVEL=debug  # Set in .env
```

---

## ✨ Success Signs

You'll know it's working when:

1. ✅ Browser console shows WebSocket connected
2. ✅ Creating booking shows instant notification
3. ✅ No page refresh needed for new bookings
4. ✅ Multiple admins see updates simultaneously
5. ✅ Reverb terminal shows "Broadcasting event"
6. ✅ Laravel logs show "created and broadcasted"

---

## Final Checklist

- [ ] Reverb running (`php artisan reverb:start`)
- [ ] Laravel running (`php artisan serve`)
- [ ] Assets built (`npm run dev`)
- [ ] Browser console shows ✅ messages
- [ ] Tested with multiple browser tabs
- [ ] Created test booking successfully
- [ ] Real-time update appeared instantly

**If all checkmarks pass: Your system is working perfectly! 🎉**

---

## Quick One-Liners

```bash
# All-in-one test
npm run dev & php artisan serve & php artisan reverb:start

# Check all services
lsof -i :8000      # Laravel
lsof -i :8080      # Reverb
npm list reverb    # Check Reverb installation

# Clear everything
php artisan cache:clear && php artisan config:clear && npm run build
```

---

**Ready to ship real-time bookings!** 🚀
