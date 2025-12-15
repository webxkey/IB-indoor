# 🚀 WebSocket Real-Time Booking System Setup Guide

## Overview

Replace polling with WebSocket connections for instant real-time booking updates using **Laravel Reverb**.

---

## Current Status Analysis

### ✅ What Already Exists

1. **BookingCreatedEvent** - Properly configured for broadcasting
2. **BookingObserver** - Automatically triggers events when bookings are created
3. **Frontend Code** - Has Pusher client library and event listeners
4. **Database Polling** - Currently checking every 3 seconds (inefficient)

### ❌ What's Missing

1. **WebSocket Server** - No Reverb or Pusher configured
2. **Broadcasting Configuration** - BROADCAST_DRIVER=log (not broadcasting)
3. **Queue Worker** - Events need queue processing

---

## Solution: Laravel Reverb (Recommended)

Laravel Reverb is Laravel's official WebSocket server - **free, self-hosted, no external dependencies**.

### Why Reverb over Pusher?

-   ✅ **Free** (Pusher costs money after 200k messages/day)
-   ✅ **Self-hosted** (no external service dependency)
-   ✅ **Laravel native** (perfect integration)
-   ✅ **Easy setup** (3 commands to install)
-   ✅ **Production ready** (used by Laravel Cloud)

---

## Installation Steps

### Step 1: Install Laravel Reverb

```bash
cd "c:\Users\ABC\Desktop\WebXkey Project\IB-indoor"
composer require laravel/reverb
php artisan reverb:install
```

**What this does:**

-   Installs Reverb package
-   Creates `config/reverb.php`
-   Updates `.env` with Reverb credentials
-   Publishes necessary assets

### Step 2: Update .env File

The install command will add these lines to your `.env`:

```env
BROADCAST_DRIVER=reverb
QUEUE_CONNECTION=database

REVERB_APP_ID=your-app-id
REVERB_APP_KEY=your-app-key
REVERB_APP_SECRET=your-app-secret
REVERB_HOST="localhost"
REVERB_PORT=8080
REVERB_SCHEME=http

VITE_REVERB_APP_KEY="${REVERB_APP_KEY}"
VITE_REVERB_HOST="${REVERB_HOST}"
VITE_REVERB_PORT="${REVERB_PORT}"
VITE_REVERB_SCHEME="${REVERB_SCHEME}"
```

### Step 3: Create Queue Table (if not exists)

```bash
php artisan queue:table
php artisan migrate
```

### Step 4: Start Required Services

Open **3 separate terminals** (PowerShell):

**Terminal 1 - Reverb WebSocket Server:**

```powershell
cd "C:\Users\ABC\Desktop\WebXkey Project\IB-indoor"
php artisan reverb:start --host="127.0.0.1" --port=8080
```

**Terminal 2 - Booking Change Processor (Monitors MySQL Triggers):**

```powershell
cd "C:\Users\ABC\Desktop\WebXkey Project\IB-indoor"
php artisan bookings:process-changes
```

**Terminal 3 - Vite Dev Server:**

```powershell
cd "C:\Users\ABC\Desktop\WebXkey Project\IB-indoor"
npm run dev
```

> ⚠️ **IMPORTANT:** Do NOT run `php artisan queue:work` - we use triggers instead!

### Step 5: Update Frontend Code

The frontend code is already 90% ready! We just need to change from Pusher to Reverb.

---

## Code Changes Required

### 1. Update `resources/js/bootstrap.js`

Create or update this file:

```javascript
import Echo from "laravel-echo";
import Pusher from "pusher-js";

window.Pusher = Pusher;

window.Echo = new Echo({
    broadcaster: "reverb",
    key: import.meta.env.VITE_REVERB_APP_KEY,
    wsHost: import.meta.env.VITE_REVERB_HOST,
    wsPort: import.meta.env.VITE_REVERB_PORT,
    wssPort: import.meta.env.VITE_REVERB_PORT,
    forceTLS: (import.meta.env.VITE_REVERB_SCHEME ?? "https") === "https",
    enabledTransports: ["ws", "wss"],
});
```

### 2. Install Frontend Dependencies

```bash
npm install --save-dev laravel-echo pusher-js
```

### 3. Update Blade Template

In `resources/views/livewire/staff/bookings-management.blade.php`, replace the Pusher initialization:

**REMOVE THIS:**

```javascript
const pusher = new Pusher(
    '{{ config("broadcasting.connections.pusher.key") }}',
    {
        cluster:
            '{{ config("broadcasting.connections.pusher.options.cluster") }}',
        encrypted: true,
    }
);
const channel = pusher.subscribe(`bookings.${complexId}`);
channel.bind("booking.created", function (data) {
    // handler code
});
```

**REPLACE WITH:**

```javascript
// Use Laravel Echo (already configured in bootstrap.js)
window.Echo.channel(`bookings.${complexId}`).listen(
    ".booking.created",
    (data) => {
        console.log("New booking received via WebSocket:", data);

        // Show notification
        showNotification(
            "New Booking!",
            `${data.user_name} booked ${data.game_name} - Court ${data.court_number}`
        );

        // Refresh booking data
        refreshBookingData().then(() => {
            updateCalendar();
        });
    }
);
```

### 4. Remove Polling Code

Since we now have real-time updates, remove the polling function:

**REMOVE THIS:**

```javascript
function setupDatabasePolling() {
    setInterval(() => {
        @this.call('checkForNewBookings').then(hasNewBookings => {
            // ...
        });
    }, 3000);
}
```

**KEEP THE METHOD** in BookingsManagement.php for manual refresh, but don't call it automatically.

---

## Testing the WebSocket Setup

### Test 1: Check Reverb is Running

Open browser console and check:

```javascript
console.log(window.Echo);
// Should show Echo instance, not undefined
```

### Test 2: Monitor WebSocket Connection

In browser dev tools → Network tab → WS (WebSockets):

-   Should see: `ws://localhost:8080/app/your-app-key`
-   Status: `101 Switching Protocols` (success)

### Test 3: Create a Booking

1. Open booking page in **Browser 1**
2. Open booking page in **Browser 2** (different window/tab)
3. Create a booking in Browser 1
4. Browser 2 should **instantly** show the new booking without page refresh

### Test 4: Check Laravel Logs

```bash
tail -f storage/logs/laravel.log
```

Should see:

```
BookingCreatedEvent broadcasted
```

---

## Troubleshooting

### Issue: "Echo is not defined"

**Fix:** Make sure `resources/js/bootstrap.js` is imported in your main JS file:

```javascript
// resources/js/app.js
import "./bootstrap";
```

Then rebuild assets:

```bash
npm run dev
```

### Issue: WebSocket connection fails

**Fix:** Check if Reverb server is running:

```bash
php artisan reverb:start --debug
```

### Issue: Events not broadcasting

**Fix:** Make sure queue worker is running:

```bash
php artisan queue:work --queue=default --tries=3
```

### Issue: Multiple bookings showing

**Fix:** Clear browser cache and reload:

```
Ctrl+Shift+Delete → Clear all → Reload
```

---

## Production Deployment

### 1. Use Supervisor to Keep Reverb Running

Create `/etc/supervisor/conf.d/reverb.conf`:

```ini
[program:reverb]
command=php /path/to/your/project/artisan reverb:start
directory=/path/to/your/project
user=www-data
autostart=true
autorestart=true
redirect_stderr=true
stdout_logfile=/path/to/your/project/storage/logs/reverb.log
```

### 2. Use Supervisor for Queue Worker

Create `/etc/supervisor/conf.d/queue.conf`:

```ini
[program:queue]
command=php /path/to/your/project/artisan queue:work --sleep=3 --tries=3
directory=/path/to/your/project
user=www-data
autostart=true
autorestart=true
redirect_stderr=true
stdout_logfile=/path/to/your/project/storage/logs/queue.log
```

### 3. Update .env for Production

```env
BROADCAST_DRIVER=reverb
QUEUE_CONNECTION=database

REVERB_HOST=yourdomain.com
REVERB_PORT=443
REVERB_SCHEME=https
```

### 4. Configure Nginx Reverse Proxy

Add to your Nginx config:

```nginx
location /reverb {
    proxy_pass http://localhost:8080;
    proxy_http_version 1.1;
    proxy_set_header Upgrade $http_upgrade;
    proxy_set_header Connection "upgrade";
    proxy_set_header Host $host;
    proxy_cache_bypass $http_upgrade;
}
```

---

## Performance Benefits

### Before (Polling):

-   ❌ **3-second delay** before seeing new bookings
-   ❌ **20 requests/minute** per client (inefficient)
-   ❌ **High server load** with multiple users
-   ❌ **Wasted bandwidth** checking for no updates

### After (WebSocket):

-   ✅ **Instant updates** (< 100ms latency)
-   ✅ **1 persistent connection** per client
-   ✅ **Low server load** (event-driven)
-   ✅ **Efficient bandwidth** (only push when needed)

---

## Cost Comparison

### Pusher (External Service):

-   Free tier: 200,000 messages/day
-   After that: $49/month for 500k messages
-   Need internet connection always

### Laravel Reverb (Self-Hosted):

-   **FREE forever**
-   No message limits
-   Works on local network
-   Full control

---

## Summary Checklist

-   [ ] Install Reverb: `composer require laravel/reverb`
-   [ ] Run install: `php artisan reverb:install`
-   [ ] Create queue table: `php artisan queue:table && php artisan migrate`
-   [ ] Update `.env`: Set `BROADCAST_DRIVER=reverb`
-   [ ] Install npm packages: `npm install laravel-echo pusher-js`
-   [ ] Update `resources/js/bootstrap.js` with Echo config
-   [ ] Build assets: `npm run dev`
-   [ ] Start Reverb: `php artisan reverb:start`
-   [ ] Start queue worker: `php artisan queue:work`
-   [ ] Update frontend code to use Echo instead of direct Pusher
-   [ ] Remove polling code
-   [ ] Test with multiple browser windows
-   [ ] Celebrate 🎉

---

**Ready to implement?** Follow the steps in order, and you'll have real-time WebSocket bookings in 15 minutes!
