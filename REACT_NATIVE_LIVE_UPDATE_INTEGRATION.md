# React Native → Laravel Live Update Integration

**For the mobile app developer.** After a user successfully books a slot in the React Native app, call one Laravel endpoint to push a live update to the admin dashboard. No WebSocket setup on the mobile side, no Django changes required.

---

## 1. What this does

```
┌─────────────────────┐                                 ┌──────────────────────┐
│  React Native App   │                                 │  Admin Dashboard     │
│  user taps "Book"   │                                 │  /staff/bookings     │
└──────────┬──────────┘                                 └──────────▲───────────┘
           │                                                       │
           │  (1) your existing booking call                       │  (4) WS push
           │      creates row in booking_booking                   │      from Reverb
           │      and returns the new booking's id                 │      → calendar
           │                                                       │        cell flips
           │  (2) POST /api/integration/booking-notify             │        to Booked
           │      Body: { "booking_id": <id> }                     │
           │                                                       │
           ▼                                                       │
       ┌─────────────────────────────────────────────────┐         │
       │   Laravel                                       │─────────┘
       │   - reads booking from shared DB by id          │
       │   - broadcasts BookingCreated via Reverb        │
       └─────────────────────────────────────────────────┘
```

The mobile app calls this endpoint **once per successful booking**. That's the entire integration.

---

## 2. Endpoint contract

```
POST  http://<LARAVEL_HOST>:8000/api/integration/booking-notify
```

### Headers

| Header | Value |
|---|---|
| `Content-Type` | `application/json` |
| `X-Webhook-Secret` | shared secret (see section 5) |

### Body

```json
{ "booking_id": 495 }
```

| Field | Type | Required | Notes |
|---|---|---|---|
| `booking_id` | integer | yes | The id (primary key) of the booking row that was just created. |

### Responses

| Status | Body | Meaning |
|---|---|---|
| `200` | `{"status":"broadcasted","booking_id":495,"venue_id":27}` | Dashboard was notified — done |
| `401` | `{"error":"Unauthorized"}` | `X-Webhook-Secret` header missing or wrong |
| `404` | `{"error":"Booking not found"}` | `booking_id` does not exist in the DB |
| `422` | `{"error":"booking_id is required"}` | `booking_id` missing or not a positive integer |

A network failure is non-critical — the booking is already saved by your existing flow. The dashboard will simply not auto-update for that one event, but the data is still in the DB. **Do not block the user's UI on this call.**

---

## 3. React Native integration

### 3.1 Create the helper

Add a new file `src/services/notifyLaravel.js`:

```js
// src/services/notifyLaravel.js
const LARAVEL_NOTIFY_URL = 'http://YOUR_LARAVEL_HOST:8000/api/integration/booking-notify';
const SECRET             = 'f4c9a27e1b8d3450a62fe9d3c8b1047f56a3d2c8b9f1e04a76d5c2b8f3e9a70b';

/**
 * Tell Laravel to push a live-update to the admin dashboard.
 * Call this once after a successful booking creation.
 * Fire-and-forget — failures are non-critical.
 */
export async function notifyLaravel(bookingId) {
  try {
    await fetch(LARAVEL_NOTIFY_URL, {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'X-Webhook-Secret': SECRET,
      },
      body: JSON.stringify({ booking_id: bookingId }),
    });
  } catch (err) {
    // network failure is non-critical — booking already saved
    console.warn('Laravel notify failed:', err.message);
  }
}
```

### 3.2 Call it after booking succeeds

In your booking confirm handler, right after your existing booking call returns:

```js
import { notifyLaravel } from '../services/notifyLaravel';

async function handleBookSlot() {
  // 1. existing booking call — unchanged
  const result = await bookSlot(payload);

  // 2. tell Laravel to push the live update (one line)
  notifyLaravel(result.id);   // result.id = booking_id from your backend response

  // 3. continue with your existing UX
  navigation.navigate('Success');
}
```

That's it. **Two files touched (one new, one one-line addition).**

---

## 4. Host URL per environment

Replace `YOUR_LARAVEL_HOST` based on where the app is running:

| Where the app runs | URL |
|---|---|
| Android emulator | `http://10.0.2.2:8000/api/integration/booking-notify` |
| iOS simulator | `http://localhost:8000/api/integration/booking-notify` |
| Real phone (same Wi-Fi as PC) | `http://<PC_LAN_IP>:8000/api/integration/booking-notify` |
| Production | `https://yourdomain.com/api/integration/booking-notify` |

> **Real-device tip:** make sure Windows / macOS firewall allows inbound port 8000.
> Find your PC's LAN IP with `ipconfig` (Windows) / `ifconfig` (macOS).

Recommended: read these from your existing app config / env handler instead of hard-coding.

---

## 5. The shared secret

```
f4c9a27e1b8d3450a62fe9d3c8b1047f56a3d2c8b9f1e04a76d5c2b8f3e9a70b
```

This value lives in the Laravel `.env` as `DJANGO_LARAVEL_WEBHOOK_SECRET` (the name is historical — it's the same secret used for any internal-to-Laravel webhook). The same value must be sent in the `X-Webhook-Secret` header.

**Security note:** Embedding the secret in the mobile app bundle is acceptable for dev/staging. For production, move this notify call into your booking backend instead, so the secret never leaves the server.

---

## 6. Verify it works

### 6.1 Without the mobile app (smoke test)

In any terminal, with the admin dashboard `/staff/bookings` open in a browser, run:

```bash
curl -X POST http://127.0.0.1:8000/api/integration/booking-notify \
  -H "Content-Type: application/json" \
  -H "X-Webhook-Secret: f4c9a27e1b8d3450a62fe9d3c8b1047f56a3d2c8b9f1e04a76d5c2b8f3e9a70b" \
  -d '{"booking_id": 495}'
```

**Expected output:**
```json
{"status":"broadcasted","booking_id":495,"venue_id":27}
```

**Expected on the dashboard:** the slot for booking 495 should flip from "Available" to a booked card, no refresh.

If you see HTTP 200 in the terminal but the dashboard does **not** update, check section 7.

### 6.2 From the mobile app

1. Open the admin dashboard `/staff/bookings` in a browser (logged in as the staff for that venue).
2. In the app, complete a booking through your normal UI.
3. The slot you booked should immediately appear on the dashboard.

---

## 7. Troubleshooting

| Symptom | Likely cause | Fix |
|---|---|---|
| `curl` returns `401 Unauthorized` | Wrong / missing `X-Webhook-Secret` header | Copy the value from section 5 exactly |
| `curl` returns `404 Booking not found` | `booking_id` does not exist in `booking_booking` table | Confirm the id returned by your booking backend is the actual PK |
| `curl` returns `200 broadcasted` but dashboard doesn't update | Reverb WebSocket server not running OR `BROADCAST_DRIVER` not `reverb` | On the Laravel host run: `php artisan reverb:start --host=127.0.0.1 --port=8080` |
| Dashboard browser console shows `WebSocket connection to ws://...:8080 failed` | Reverb not running, or `REVERB_PORT` / `REVERB_SERVER_PORT` mismatched | Start Reverb (above) and ensure both env vars equal `8080` |
| App on real device cannot reach Laravel | Using `127.0.0.1` or `localhost` from the device | Use the PC LAN IP and open port 8000 in firewall |
| Toast appears but slot stays "Available" | Browser was on the booking page when the page first loaded but a JS error occurred since | Hard-reload (Ctrl+Shift+R) the dashboard, retry |

---

## 8. Summary

To add live dashboard updates from your React Native app:

1. Create one helper file (`notifyLaravel.js`) — about 12 lines.
2. Call `notifyLaravel(bookingId)` once, right after your existing booking call returns success.
3. Replace `YOUR_LARAVEL_HOST` with the right value per environment.

That is the entire integration. No WebSocket / Reverb / Echo setup is required inside the mobile app — Laravel handles all of that on the server. The mobile app only ever makes a single `POST` request after each booking.
