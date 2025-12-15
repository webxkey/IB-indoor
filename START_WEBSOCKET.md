# 🚀 START WEBSOCKET SERVICES - Quick Guide

## ✅ Setup Complete!

WebSocket (Laravel Reverb) is now installed and configured for real-time booking updates.

---

## 🎯 How to Start Everything

You need to run **3 services** simultaneously. Open **3 separate PowerShell terminals**:

### Terminal 1: Laravel Reverb WebSocket Server

```powershell
cd "c:\Users\ABC\Desktop\WebXkey Project\IB-indoor"
php artisan reverb:start
```

**What this does:** Starts the WebSocket server on `localhost:8080`

**Expected output:**

```
  INFO  Starting server on 0.0.0.0:8080
  2025-12-03 14:30:25 Server started successfully
```

**Keep this terminal open** - it must stay running!

---

### Terminal 2: Queue Worker (Broadcasts Events)

```powershell
cd "c:\Users\ABC\Desktop\WebXkey Project\IB-indoor"
php artisan queue:work --queue=default
```

**What this does:** Processes broadcasting jobs that send events to WebSocket

**Expected output:**

```
  INFO  Processing jobs from the [default] queue.
```

**Keep this terminal open** - it must stay running!

---

### Terminal 3: Laravel Development Server

```powershell
cd "c:\Users\ABC\Desktop\WebXkey Project\IB-indoor"
php artisan serve
```

**What this does:** Runs Laravel application on `http://localhost:8000`

**Expected output:**

```
  INFO  Server running on [http://127.0.0.1:8000].
  Press Ctrl+C to stop the server.
```

**Keep this terminal open** - it must stay running!

---

## 🧪 Testing Real-Time Updates

### Test 1: Check WebSocket Connection

1. Open `http://localhost:8000` in your browser
2. Navigate to **Bookings Management** page
3. Open **Browser Console** (F12 → Console tab)
4. Look for:
    ```
    ✅ WebSocket connected successfully
    ✅ Real-time WebSocket updates initialized for complex: 1
    ```

### Test 2: Real-Time Booking Test

1. Open **Browser Window 1** → `http://localhost:8000/staff/dashboard`
2. Open **Browser Window 2** → Same URL (new window/incognito)
3. In **Window 1**: Create a new booking
4. **Window 2** should show the new booking **instantly** (without page refresh!)
5. You should see a notification: "🎉 New Booking! Player Name booked Game - Court X"

### Test 3: Multiple Users Test

1. Open 3+ browser windows (different browsers: Chrome, Firefox, Edge)
2. All windows on booking page
3. Create booking in one window
4. **All other windows update simultaneously!**

---

## 🔍 Monitoring & Debugging

### Check Active Connections

**In Reverb Terminal**, you'll see:

```
2025-12-03 14:35:10 New connection: socket-id-12345
2025-12-03 14:35:12 Subscribed to channel: bookings.1
```

### Check Broadcasting Events

**In Queue Worker Terminal**, you'll see:

```
[2025-12-03 14:35:15] Processing: Illuminate\Broadcasting\BroadcastEvent
[2025-12-03 14:35:15] Processed:  Illuminate\Broadcasting\BroadcastEvent
```

### Check Browser Console

Press **F12** → **Console** tab:

```
✅ WebSocket connected successfully
🔔 New booking received via WebSocket: {booking_id: 592, user_name: "John"}
📊 Booking data refreshed
```

### Check Network Tab

Press **F12** → **Network** tab → **WS** filter:

-   Should see: `ws://localhost:8080/app/3va3ybyizbhgzlwvydzv`
-   Status: `101 Switching Protocols` ✅
-   Messages: Should see booking events in real-time

---

## 🛠️ Troubleshooting

### Issue: "Echo is not defined"

**Cause:** Frontend assets not rebuilt

**Fix:**

```powershell
cd "c:\Users\ABC\Desktop\WebXkey Project\IB-indoor"
npm run build
# Then refresh browser with Ctrl+Shift+R
```

---

### Issue: "WebSocket connection failed"

**Cause:** Reverb server not running

**Fix:** Start Reverb in Terminal 1:

```powershell
php artisan reverb:start
```

Check firewall isn't blocking port 8080.

---

### Issue: "New bookings not appearing"

**Cause:** Queue worker not running

**Fix:** Start queue worker in Terminal 2:

```powershell
php artisan queue:work
```

---

### Issue: "Falling back to polling mode"

**Cause:** Echo not initialized (assets not loaded)

**Fix:**

1. Hard refresh browser: `Ctrl+Shift+R`
2. Clear cache: `Ctrl+Shift+Delete`
3. Check if `app.js` is loaded in Network tab
4. Rebuild: `npm run build`

---

### Issue: Port 8080 already in use

**Fix:**

```powershell
# Find process using port 8080
netstat -ano | findstr :8080

# Kill the process (replace PID with actual number)
taskkill /PID <PID> /F

# Or change Reverb port in .env:
REVERB_PORT=8081
```

---

## 📊 Performance Comparison

### Before (Polling):

-   ⏱️ **3-5 second delay** to see new bookings
-   📡 **20 HTTP requests/minute** per user
-   💾 High server load with 10+ users
-   ❌ Wasted bandwidth checking for nothing

### After (WebSocket):

-   ⚡ **< 100ms delay** (instant!)
-   📡 **1 WebSocket connection** per user
-   💾 Minimal server load
-   ✅ Events pushed only when needed

**With 10 users:**

-   Polling: **200 requests/minute**
-   WebSocket: **10 connections total + events**

---

## 🎉 Success Indicators

You know everything is working when:

-   ✅ 3 terminals running without errors
-   ✅ Browser console shows "WebSocket connected"
-   ✅ Creating booking in one window updates all windows instantly
-   ✅ Notification appears: "🎉 New Booking!"
-   ✅ No "falling back to polling" messages
-   ✅ No page refresh needed to see updates

---

## 🚫 Common Mistakes

1. ❌ **Not running all 3 terminals** → Events won't broadcast
2. ❌ **Not rebuilding assets after changes** → Old code runs
3. ❌ **Firewall blocking port 8080** → Connection fails
4. ❌ **Hard refresh not done** → Old cached code
5. ❌ **Queue worker stopped** → Events queued but not sent

---

## 📝 Quick Start Checklist

Before testing, ensure:

-   [ ] `.env` has `BROADCAST_DRIVER=reverb`
-   [ ] `.env` has `QUEUE_CONNECTION=database`
-   [ ] Frontend assets built: `npm run build`
-   [ ] Terminal 1: `php artisan reverb:start` ✅ Running
-   [ ] Terminal 2: `php artisan queue:work` ✅ Running
-   [ ] Terminal 3: `php artisan serve` ✅ Running
-   [ ] Browser console shows "WebSocket connected"
-   [ ] No error messages in any terminal

---

## 🔄 Restart Everything

If something goes wrong:

```powershell
# Stop all terminals (Ctrl+C in each)

# Terminal 1
cd "c:\Users\ABC\Desktop\WebXkey Project\IB-indoor"
php artisan config:clear
php artisan cache:clear
php artisan reverb:start

# Terminal 2
cd "c:\Users\ABC\Desktop\WebXkey Project\IB-indoor"
php artisan queue:restart
php artisan queue:work

# Terminal 3
cd "c:\Users\ABC\Desktop\WebXkey Project\IB-indoor"
php artisan serve
```

Then:

-   Clear browser cache (Ctrl+Shift+Delete)
-   Hard refresh (Ctrl+Shift+R)
-   Test again

---

## 📞 Need Help?

Check logs:

```powershell
# Laravel logs
Get-Content storage\logs\laravel.log -Tail 50

# Queue failed jobs
php artisan queue:failed
```

---

**Ready to test? Start all 3 terminals and open the booking page!** 🚀
