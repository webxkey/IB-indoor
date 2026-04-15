# Real-Time Webhook Architecture
## Django → Laravel → Admin Dashboards

**Project:** IndoorB Indoor Booking System  
**Date:** 2026-04-15  
**Author:** Saman  

---

## Table of Contents

1. [System Overview](#1-system-overview)
2. [Architecture Diagram](#2-architecture-diagram)
3. [All New Files Created](#3-all-new-files-created)
4. [All Existing Files Modified](#4-all-existing-files-modified)
5. [Webhook Security Flow](#5-webhook-security-flow)
6. [Database Table: django_webhook_events](#6-database-table-django_webhook_events)
7. [Event Flow: Mobile Booking](#7-event-flow-mobile-booking)
8. [Event Flow: Slot Hold from Django](#8-event-flow-slot-hold-from-django)
9. [Broadcast Channels Reference](#9-broadcast-channels-reference)
10. [Admin Dashboard Behavior](#10-admin-dashboard-behavior)
11. [Calendar Slot Visual States](#11-calendar-slot-visual-states)
12. [Environment Variables](#12-environment-variables)
13. [Django Integration Guide](#13-django-integration-guide)
14. [Local Test Procedure](#14-local-test-procedure)
15. [Troubleshooting](#15-troubleshooting)

---

## 1. System Overview

This system connects three layers:

```
Django (Mobile Backend)
    ↓  HTTP POST webhook (HMAC signed)
Laravel (Admin Backend)
    ↓  WebSocket broadcast (Reverb)
Admin Dashboards (Super Admin + Indoor Admin)
```

**Two admin roles:**

| Role | Route | What they see |
|---|---|---|
| Super Admin | `/admin/bookings` | All venues, all bookings, global stats |
| Indoor Admin | `/staff/bookings` | Their venue only, slot calendar |

**Two real-time update sources:**

| Source | How | Channel |
|---|---|---|
| Mobile app creates booking | Sanctum API → Eloquent Observer → Broadcast | `bookings.complex.{venueId}` + `bookings.global` |
| Django sends webhook event | HTTP POST → Job → Broadcast | `slots.venue.{venueId}.sport.{sportId}` + `slots.global` |

---

## 2. Architecture Diagram

```
┌─────────────────────────────────────────────────────────────────┐
│                        MOBILE APP                               │
│   User browses slots → holds slot → confirms booking            │
└──────────────┬──────────────────────────┬───────────────────────┘
               │                          │
               │ POST /api/bookings        │  Django handles hold/release
               │ (Sanctum auth)            │  then calls Laravel webhook
               ↓                          ↓
┌─────────────────────────────────────────────────────────────────┐
│                     LARAVEL (Admin Backend)                     │
│                                                                 │
│  ┌─────────────────────────┐   ┌──────────────────────────────┐ │
│  │   Booking API Route     │   │  DjangoWebhookController     │ │
│  │   POST /api/bookings    │   │  POST /api/integration/      │ │
│  │                         │   │  webhooks/django-events      │ │
│  │  1. Validate request    │   │                              │ │
│  │  2. Create DB record    │   │  1. Verify X-Webhook-Source  │ │
│  │  3. Broadcast event     │   │  2. Check timestamp freshness│ │
│  └────────────┬────────────┘   │  3. Verify HMAC-SHA256 sig  │ │
│               │                │  4. Validate envelope schema │ │
│               │                │  5. Check event_id duplicate │ │
│               │                │  6. Save to DB               │ │
│               │                │  7. Dispatch background Job  │ │
│               │                │  8. Return 200 fast          │ │
│               │                └──────────────┬───────────────┘ │
│               │                               │                 │
│               ↓                               ↓                 │
│  ┌─────────────────────────┐   ┌──────────────────────────────┐ │
│  │   BookingCreated event  │   │  ProcessDjangoWebhookEvent   │ │
│  │   (ShouldBroadcast)     │   │  (Background Job)            │ │
│  │                         │   │                              │ │
│  │  Channels:              │   │  1. Load webhook record      │ │
│  │  bookings.complex.{id}  │   │  2. Build broadcast payload  │ │
│  │  bookings.global        │   │  3. Fire SlotStateChanged    │ │
│  └────────────┬────────────┘   │  4. Mark record processed    │ │
│               │                └──────────────┬───────────────┘ │
│               │                               │                 │
│               │                               ↓                 │
│               │                ┌──────────────────────────────┐ │
│               │                │   SlotStateChanged event     │ │
│               │                │   (ShouldBroadcast)          │ │
│               │                │                              │ │
│               │                │  Channels:                   │ │
│               │                │  slots.venue.{v}.sport.{s}   │ │
│               │                │  slots.global                │ │
│               │                └──────────────┬───────────────┘ │
│               │                               │                 │
└───────────────┼───────────────────────────────┼─────────────────┘
                │                               │
                │    Laravel Reverb WebSocket    │
                ↓                               ↓
┌──────────────────────────────┐ ┌──────────────────────────────────┐
│   INDOOR ADMIN DASHBOARD     │ │   SUPER ADMIN DASHBOARD          │
│   /staff/bookings            │ │   /admin/bookings                │
│                              │ │                                  │
│  Listens:                    │ │  Listens:                        │
│  bookings.complex.{id}       │ │  bookings.global                 │
│    → .booking.created        │ │    → .booking.created            │
│    → .booking.updated        │ │  slots.global                    │
│    → .booking.deleted        │ │    → .slot_state_changed         │
│  slots.venue.{v}.sport.{s}   │ │                                  │
│    → .slot_state_changed     │ │  On event:                       │
│                              │ │  - Show toast notification       │
│  On booking.created:         │ │  - Refresh stats (Livewire)      │
│  - Show booked cell          │ │  - Refresh booking table         │
│  On slot.hold.created:       │ │                                  │
│  - Show orange "On Hold"     │ └──────────────────────────────────┘
│  On slot.hold.released:      │
│  - Revert to Available       │
└──────────────────────────────┘
```

---

## 3. All New Files Created

### 3.1 Migration
**File:** `database/migrations/2026_04_15_000001_create_django_webhook_events_table.php`

Creates the `django_webhook_events` table for idempotency tracking.

```php
Schema::create('django_webhook_events', function (Blueprint $table) {
    $table->id();
    $table->uuid('event_id')->unique();          // prevents duplicate processing
    $table->string('event_type', 64);
    $table->timestamp('received_at')->useCurrent();
    $table->timestamp('processed_at')->nullable();
    $table->enum('status', ['received', 'processed', 'failed', 'duplicate'])->default('received');
    $table->jsonb('payload_json');               // full envelope stored
    $table->text('error_message')->nullable();
});
```

**Why:** If Django retries a webhook (network hiccup, timeout), Laravel must not process the same event twice. The `event_id` unique index guarantees this.

---

### 3.2 Eloquent Model
**File:** `app/Models/DjangoWebhookEvent.php`

Simple model for `django_webhook_events` table.

```php
namespace App\Models;

class DjangoWebhookEvent extends Model
{
    public $timestamps = false;
    protected $table = 'django_webhook_events';
    protected $casts = [
        'payload_json' => 'array',
        'received_at'  => 'datetime',
        'processed_at' => 'datetime',
    ];
}
```

---

### 3.3 Webhook Controller
**File:** `app/Http/Controllers/DjangoWebhookController.php`

The public endpoint that Django calls. Runs all security checks in under 200ms and dispatches a background job.

**Security checks in order:**
1. `X-Webhook-Source` header must equal `django`
2. `X-Webhook-Timestamp` must be within 300 seconds of server time
3. HMAC-SHA256 signature verified with `hash_equals()` (constant-time, prevents timing attacks)
4. Envelope schema validated (event_type enum, required payload fields)
5. `event_id` checked for duplicates in DB
6. Record saved to DB + background job dispatched
7. Return `200 accepted` fast

**Signature verification:**
```php
$signed_string = $timestamp . '.' . $raw_request_body;
$expected = 'sha256=' . hash_hmac('sha256', $signed_string, $secret);
hash_equals($expected, $header_value); // constant-time comparison
```

---

### 3.4 Background Job
**File:** `app/Jobs/ProcessDjangoWebhookEvent.php`

Runs after the webhook endpoint returns. Does the actual business logic.

```
Job steps:
1. Load DjangoWebhookEvent record by ID
2. Extract payload fields (sport_id, venue_id, date, time, court, etc.)
3. Build broadcast payload array
4. Fire SlotStateChanged event (broadcasts via Reverb)
5. Mark record as processed with timestamp
6. On failure: mark as failed, log error, re-throw for retry
```

**Retry:** `public int $tries = 3;` — Laravel will retry up to 3 times if broadcast fails.

---

### 3.5 Broadcast Event
**File:** `app/Events/SlotStateChanged.php`

Broadcasts slot state to admin dashboards via Laravel Reverb WebSocket.

```php
class SlotStateChanged implements ShouldBroadcast
{
    public function broadcastOn(): array
    {
        return [
            new Channel("slots.venue.{$venueId}.sport.{$sportId}"),  // indoor admin
            new Channel('slots.global'),                               // super admin
        ];
    }

    public function broadcastAs(): string
    {
        return 'slot_state_changed';  // JS listens for .slot_state_changed
    }
}
```

**Broadcast payload example:**
```json
{
    "type": "slot_state_changed",
    "event_type": "slot.hold.created",
    "sport_id": 9,
    "venue_id": 3,
    "date": "2026-04-18",
    "start_time": "09:00:00",
    "end_time": "10:00:00",
    "court": "1",
    "available": false,
    "available_courts": 2,
    "total_courts": 3,
    "reason": "Temporarily held",
    "booking_id": null,
    "hold_id": 2881,
    "actor": { "id": 44, "type": "user" },
    "occurred_at": "2026-04-18T09:05:34Z"
}
```

---

## 4. All Existing Files Modified

### 4.1 `app/Events/BookingCreated.php`
**Change:** Added `bookings.global` channel so super admin also receives new booking events.

```php
// Before
return [
    new Channel("bookings.complex.{$this->complexId}"),
];

// After
return [
    new Channel("bookings.complex.{$this->complexId}"),
    new Channel('bookings.global'),   // ← ADDED for super admin
];
```

---

### 4.2 `app/Livewire/Admin/Bookings.php`
**Change:** Added `refreshData()` method that Livewire JS can call remotely.

```php
public function refreshData(): void
{
    $this->loadStats();    // reload today count, cancelled count, revenue
    $this->resetPage();    // reset pagination to page 1
}
```

When a WebSocket event arrives in the browser, the JS calls:
```javascript
Livewire.dispatch('refreshData');
```
Livewire sends an AJAX request to the server, runs `refreshData()`, re-renders the component — no page reload.

---

### 4.3 `routes/api.php`
**Change:** Added the webhook route and controller import.

```php
use App\Http\Controllers\DjangoWebhookController;

// Django → Laravel webhook (no Sanctum auth — verified by HMAC signature instead)
Route::post('/integration/webhooks/django-events', [DjangoWebhookController::class, 'receive'])
    ->name('webhook.django');
```

**Full endpoint:** `POST /api/integration/webhooks/django-events`  
**No authentication middleware** — security is done via HMAC signature check inside the controller.

---

### 4.4 `config/services.php`
**Change:** Added Django webhook secret config entry.

```php
'django_webhook' => [
    'secret' => env('DJANGO_LARAVEL_WEBHOOK_SECRET'),
],
```

Used in controller as: `config('services.django_webhook.secret')`

---

### 4.5 `.env`
**Change:** Renamed old weak secret to proper name with strong 64-char hex key.

```
# Before
DJANGO_WEBHOOK_SECRET=webxkey123samanMohamed

# After
DJANGO_LARAVEL_WEBHOOK_SECRET=f4c9a27e1b8d3450a62fe9d3c8b1047f56a3d2c8b9f1e04a76d5c2b8f3e9a70b
```

---

### 4.6 `resources/views/livewire/admin/bookings.blade.php`
**Change:** Added WebSocket listeners in the `@push('scripts')` block.

**Listens to:**
- `bookings.global` → `.booking.created` → refreshes stats + shows toast
- `slots.global` → `.slot_state_changed` → shows toast, refreshes on booking events

```javascript
window.Echo.channel('bookings.global')
    .listen('.booking.created', () => {
        showAdminToast('New booking received from mobile app');
        Livewire.dispatch('refreshData');
    });

window.Echo.channel('slots.global')
    .listen('.slot_state_changed', (data) => {
        showAdminToast(`${msg} — Venue #${data.venue_id}`);
        if (['booking.created', 'booking.cancelled'].includes(data.event_type)) {
            Livewire.dispatch('refreshData');
        }
    });
```

Also added `showAdminToast()` helper function — displays a dismissible info toast in the top-right corner.

---

### 4.7 `resources/views/livewire/staff/bookings-management.blade.php`
**Two changes:**

**A. New CSS — held slot styling:**
```css
.time-slot.held-slot {
    background: linear-gradient(135deg, #fff7ed 0%, #fed7aa 100%);
    border: 2px dashed #f97316;
    color: #c2410c;
    animation: held-pulse 2s ease-in-out infinite;  /* pulsing orange */
}
```

**B. New JavaScript — slot state listener per sport:**
```javascript
// One channel subscription per sport the venue has
window.Echo.channel('slots.venue.{{ $complex_id }}.sport.{{ $sport->id }}')
    .listen('.slot_state_changed', (data) => {

        if (data.event_type === 'slot.hold.created') {
            // Store hold in memory
            window.heldSlots[sportId][date][time][court] = true;
            updateCalendar();  // re-render — slot shows orange

        } else if (data.event_type === 'slot.hold.released') {
            // Remove hold from memory
            delete window.heldSlots[sportId][date][time][court];
            updateCalendar();  // re-render — slot goes back to green

        } else if (data.event_type === 'booking.created') {
            // Remove hold + reload from DB (real booking now exists)
            Livewire.dispatch('refreshBookings');

        } else if (data.event_type === 'booking.cancelled') {
            Livewire.dispatch('refreshBookings');

        } else if (data.event_type === 'slot.blocked') {
            Livewire.dispatch('refreshBookings');

        } else if (data.event_type === 'slot.unblocked') {
            Livewire.dispatch('refreshBookings');
        }
    });
```

**C. Calendar render logic update:**
The `updateCalendar()` function now checks `window.heldSlots` before deciding how to render each cell:

```
isPastSlot?    → grey "Time Past"
isBlocked?     → yellow "Unavailable"
isHeld?        → orange pulsing "On Hold"   ← NEW
else           → green "Available"
```

---

## 5. Webhook Security Flow

```
Django sends:
  POST /api/integration/webhooks/django-events
  Headers:
    Content-Type: application/json
    X-Webhook-Source: django
    X-Webhook-Timestamp: 1744977934         (unix seconds)
    X-Webhook-Signature: sha256=abc123...   (HMAC of timestamp.body)
    X-Event-Id: 6f4308e3-4177-4d11-bf0a-fb97fef2a710
    X-Idempotency-Key: some-string

Laravel checks in order:
  1. X-Webhook-Source == "django"               → else 401
  2. abs(now - timestamp) <= 300 seconds        → else 401
  3. hash_equals(expected_hmac, header_hmac)    → else 401
  4. json_decode body, validate envelope        → else 422
  5. SELECT event_id FROM table                 → if exists, return 200 duplicate
  6. INSERT record, dispatch job               → else 500
  7. return 200 accepted
```

**Why constant-time comparison matters:**  
A regular `===` comparison short-circuits on first mismatch — an attacker can measure response time to guess the correct HMAC byte by byte. `hash_equals()` always takes the same time regardless of where strings differ.

**Why timestamp window matters:**  
Without it, an attacker who captures a valid webhook request can replay it days later. A 300-second window means the captured request is useless after 5 minutes.

---

## 6. Database Table: django_webhook_events

**Table name:** `django_webhook_events`

| Column | Type | Notes |
|---|---|---|
| `id` | bigint PK | auto-increment |
| `event_id` | uuid UNIQUE | from Django, prevents duplicates |
| `event_type` | varchar(64) | e.g. `slot.hold.created` |
| `received_at` | timestamp | set on INSERT |
| `processed_at` | timestamp nullable | set by background job |
| `status` | enum | `received` → `processed` or `failed` |
| `payload_json` | jsonb | full webhook envelope |
| `error_message` | text nullable | populated if job fails |

**Status lifecycle:**
```
received → (job runs) → processed
         → (job fails) → failed
         → (duplicate) → row not inserted, 200 returned immediately
```

---

## 7. Event Flow: Mobile Booking

```
1. User opens mobile app, selects venue/sport/date/time/court
2. User taps "Book Now"
3. Mobile sends: POST /api/bookings
   Headers: Authorization: Bearer {sanctum_token}
   Body: { sport_id, venue_id, booking_date, start_time, ... }

4. Laravel api.php route handler:
   a. Validates request fields
   b. Checks slot is not blocked (blocked_slots JSON)
   c. Checks slot is not already booked (DB query)
   d. Creates BookingBooking record
   e. Calls broadcast(new BookingCreated($booking))
   f. Creates BookingNotification for staff users
   g. Returns 201 with booking data

5. BookingCreated event broadcasts to:
   - bookings.complex.{venueId}   (indoor admin of that venue)
   - bookings.global              (super admin)

6. Indoor admin browser (bookings-management.blade.php):
   - Echo.channel('bookings.complex.{id}').listen('.booking.created')
   - Calls showNotification() → toast appears
   - Calls Livewire.dispatch('refreshBookings') → PHP reloads bookings
   - After 500ms: refreshBookingData() + updateCalendar()
   - Slot cell changes from "Available" (green) to booked card (dark)

7. Super admin browser (admin/bookings.blade.php):
   - Echo.channel('bookings.global').listen('.booking.created')
   - Calls showAdminToast()
   - Calls Livewire.dispatch('refreshData')
   - todayBookingsCount counter increments
   - Recent bookings table shows new row
```

---

## 8. Event Flow: Slot Hold from Django

```
1. Mobile user starts checkout flow (not confirmed yet)
2. Django temporarily holds the slot: decrements available_courts
3. Django sends: POST /api/integration/webhooks/django-events
   event_type: "slot.hold.created"
   payload: { sport_id, venue_id, date, start_time, end_time, court,
              hold_id, available_courts, total_courts, reason }

4. DjangoWebhookController:
   a. All 5 security checks pass
   b. Inserts django_webhook_events row (status=received)
   c. Dispatches ProcessDjangoWebhookEvent job
   d. Returns 200 immediately

5. ProcessDjangoWebhookEvent job runs:
   a. Builds broadcast payload from envelope
   b. Fires broadcast(new SlotStateChanged($data))
   c. Updates row status to processed

6. SlotStateChanged broadcasts to:
   - slots.venue.{venueId}.sport.{sportId}
   - slots.global

7. Indoor admin browser:
   - Echo.channel('slots.venue.3.sport.9').listen('.slot_state_changed')
   - data.event_type === 'slot.hold.created'
   - Sets window.heldSlots[9]['2026-04-18']['09:00:00']['1'] = true
   - Calls updateCalendar()
   - Slot cell re-renders as orange pulsing "On Hold"

8. If user abandons checkout:
   - Django sends slot.hold.released webhook
   - Same flow → window.heldSlots entry deleted → updateCalendar()
   - Slot goes back to green "Available"

9. If user completes booking:
   - Django sends booking.created webhook (hold became real booking)
   - Same flow → heldSlots entry deleted + Livewire.dispatch('refreshBookings')
   - Slot becomes full booked card (loaded from DB)
```

---

## 9. Broadcast Channels Reference

| Channel | Who subscribes | Events |
|---|---|---|
| `bookings.complex.{venueId}` | Indoor admin of that venue | `.booking.created`, `.booking.updated`, `.booking.deleted` |
| `bookings.global` | Super admin | `.booking.created` |
| `slots.venue.{venueId}.sport.{sportId}` | Indoor admin (one channel per sport) | `.slot_state_changed` |
| `slots.global` | Super admin | `.slot_state_changed` |

**Event type values inside `slot_state_changed`:**

| `event_type` | Meaning | Admin action |
|---|---|---|
| `slot.hold.created` | Mobile user in checkout | Show orange "On Hold" |
| `slot.hold.released` | Checkout abandoned/expired | Revert to green "Available" |
| `booking.created` | Confirmed booking | Show booked card (reload from DB) |
| `booking.cancelled` | Booking cancelled | Remove booked card (reload from DB) |
| `slot.blocked` | Admin blocked slot | Show yellow "Unavailable" (reload) |
| `slot.unblocked` | Admin unblocked slot | Revert to "Available" (reload) |

---

## 10. Admin Dashboard Behavior

### Super Admin (`/admin/bookings`)

| Trigger | What happens (no reload) |
|---|---|
| Mobile user creates booking | Toast "New booking received" + stats counter increments + booking appears in table |
| Django sends booking.created | Toast "New booking confirmed" + stats refresh |
| Django sends booking.cancelled | Toast "Booking cancelled" + stats refresh |
| Django sends slot.hold.created | Toast "Slot on hold — Venue #X" (stats do not refresh, holds are not bookings) |
| Django sends slot.blocked | Toast "Slot blocked by admin" |

### Indoor Admin (`/staff/bookings`)

| Trigger | What happens (no reload) |
|---|---|
| Mobile user creates booking | Toast + booked card appears in calendar slot |
| Mobile user updates booking | Toast + card refreshes |
| Mobile user cancels booking | Toast + card disappears, slot goes green |
| Django: slot.hold.created | Slot turns **orange pulsing** "On Hold" |
| Django: slot.hold.released | Slot reverts to **green** "Available" |
| Django: booking.created | Hold removed + booked card appears |
| Django: booking.cancelled | Booked card disappears, slot goes green |
| Django: slot.blocked | Toast + reload (yellow blocked cell appears) |
| Django: slot.unblocked | Toast + reload (slot goes green) |

---

## 11. Calendar Slot Visual States

```
┌──────────────────────────────────────────────────┐
│  SLOT CELL STATES IN INDOOR ADMIN CALENDAR       │
├──────────────┬───────────────────────────────────┤
│  State       │  Appearance                       │
├──────────────┼───────────────────────────────────┤
│  Available   │  Blue dashed border               │
│              │  "Available" + Book/Block/Wait btn │
├──────────────┼───────────────────────────────────┤
│  On Hold     │  Orange dashed border             │
│  (NEW)       │  Pulsing animation                │
│              │  "On Hold"                        │
│              │  "Mobile checkout in progress"    │
├──────────────┼───────────────────────────────────┤
│  Blocked     │  Yellow background                │
│              │  "Unavailable"                    │
│              │  Reason + Unblock button          │
├──────────────┼───────────────────────────────────┤
│  Booked      │  Dark card with player info       │
│              │  Name, phone, status badge        │
│              │  Timer, Cancel button             │
├──────────────┼───────────────────────────────────┤
│  Past        │  Grey, locked icon                │
│              │  "Time Past" (not clickable)      │
└──────────────┴───────────────────────────────────┘
```

**Priority order (what wins if multiple states apply):**
```
1. isPastSlot   → always grey (time already passed)
2. isBlocked    → yellow (admin explicitly blocked)
3. isHeld       → orange pulsing (mobile checkout in progress)
4. isBooked     → dark card (confirmed booking)
5. default      → green available
```

---

## 12. Environment Variables

### Laravel `.env`

```bash
# WebSocket broadcast driver
BROADCAST_DRIVER=reverb

# Queue (set to database for production to use real background jobs)
# sync = run jobs inline (fine for dev, slower for prod)
QUEUE_CONNECTION=sync

# Reverb WebSocket server
REVERB_APP_ID=1
REVERB_APP_KEY=reverb-app-key-local
REVERB_APP_SECRET=reverb-app-secret-local
REVERB_HOST=127.0.0.1
REVERB_PORT=8080
REVERB_SCHEME=http

# Django webhook shared secret (KEEP THIS SECRET — never commit to git)
DJANGO_LARAVEL_WEBHOOK_SECRET=f4c9a27e1b8d3450a62fe9d3c8b1047f56a3d2c8b9f1e04a76d5c2b8f3e9a70b
```

### Django `.env` (set on Django side)

```bash
LARAVEL_WEBHOOK_URL=https://yourdomain.com/api/integration/webhooks/django-events
DJANGO_LARAVEL_WEBHOOK_SECRET=f4c9a27e1b8d3450a62fe9d3c8b1047f56a3d2c8b9f1e04a76d5c2b8f3e9a70b
```

### Production Recommendation

Change queue to database for real async jobs:
```bash
QUEUE_CONNECTION=database
```

Then run the queue worker:
```bash
php artisan queue:work --queue=default --tries=3
```

---

## 13. Django Integration Guide

Django must send a signed `POST` request to the Laravel webhook URL for every slot/booking event.

### Required Headers

```
Content-Type: application/json
X-Webhook-Source: django
X-Webhook-Timestamp: <unix timestamp in seconds>
X-Webhook-Signature: sha256=<hmac_hex>
X-Event-Id: <uuid v4>
X-Idempotency-Key: <any unique string>
```

### How to generate the signature (Python example)

```python
import hmac
import hashlib
import time
import json
import uuid
import requests

SECRET = "f4c9a27e1b8d3450a62fe9d3c8b1047f56a3d2c8b9f1e04a76d5c2b8f3e9a70b"
LARAVEL_URL = "https://yourdomain.com/api/integration/webhooks/django-events"

def send_webhook(event_type, payload):
    timestamp = str(int(time.time()))
    event_id = str(uuid.uuid4())

    body = json.dumps({
        "schema_version": "1.0",
        "event_id": event_id,
        "event_type": event_type,
        "occurred_at": "2026-04-18T09:05:34Z",
        "source_system": "django",
        "correlation_id": str(uuid.uuid4()),
        "payload": payload,
    })

    signed_string = f"{timestamp}.{body}"
    signature = "sha256=" + hmac.new(
        SECRET.encode(),
        signed_string.encode(),
        hashlib.sha256
    ).hexdigest()

    response = requests.post(LARAVEL_URL, data=body, headers={
        "Content-Type": "application/json",
        "X-Webhook-Source": "django",
        "X-Webhook-Timestamp": timestamp,
        "X-Webhook-Signature": signature,
        "X-Event-Id": event_id,
        "X-Idempotency-Key": event_id,
    })

    return response.json()

# Example: send slot hold
send_webhook("slot.hold.created", {
    "sport_id": 9,
    "venue_id": 3,
    "date": "2026-04-18",
    "start_time": "09:00:00",
    "end_time": "10:00:00",
    "court": "1",
    "hold_id": 2881,
    "booking_id": None,
    "available": False,
    "available_courts": 2,
    "total_courts": 3,
    "reason": "Temporarily held",
    "actor": {"id": 44, "type": "user"},
})
```

### Accepted Event Types

```
slot.hold.created
slot.hold.released
booking.created
booking.cancelled
slot.blocked
slot.unblocked
```

### Response Codes

| Code | Body | Meaning |
|---|---|---|
| 200 | `{"status":"accepted"}` | Processed successfully |
| 200 | `{"status":"duplicate"}` | Already received, ignored safely |
| 401 | `{"error":"Unauthorized"}` | Bad signature, timestamp, or source |
| 422 | `{"error":"Validation failed"}` | Missing required fields |
| 500 | `{"error":"Server error"}` | Laravel side error, retry later |

**Django should retry on 500. Should NOT retry on 401 or 422.**

---

## 14. Local Test Procedure

### Step 1: Start Laravel services

```bash
# Terminal 1: PHP server
php artisan serve

# Terminal 2: Reverb WebSocket server
php artisan reverb:start

# Terminal 3: Queue worker (if QUEUE_CONNECTION=database)
php artisan queue:work
```

### Step 2: Open admin dashboards

- Super admin: `http://localhost:8000/admin/bookings`
- Indoor admin: `http://localhost:8000/staff/bookings`
- Open browser console → check for WebSocket connection messages

### Step 3: Test mobile booking (no Django needed)

```bash
# Get auth token first
curl -X POST http://localhost:8000/api/auth/login \
  -H "Content-Type: application/json" \
  -d '{"email":"user@example.com","password":"password"}'

# Create a booking
curl -X POST http://localhost:8000/api/bookings \
  -H "Authorization: Bearer {token}" \
  -H "Content-Type: application/json" \
  -d '{
    "sport_id": 1,
    "venue_id": 1,
    "booking_date": "2026-04-20",
    "start_time": "10:00",
    "end_time": "11:00",
    "court_number": "1",
    "user_name": "Test User",
    "user_number": "0771234567"
  }'
```

**Expected:** Both admin dashboards show the new booking instantly.

### Step 4: Test slot hold webhook (simulates Django)

```bash
# Generate HMAC signature with shell (or use Python script above)
SECRET="f4c9a27e1b8d3450a62fe9d3c8b1047f56a3d2c8b9f1e04a76d5c2b8f3e9a70b"
TIMESTAMP=$(date +%s)
BODY='{"schema_version":"1.0","event_id":"6f4308e3-4177-4d11-bf0a-fb97fef2a710","event_type":"slot.hold.created","occurred_at":"2026-04-18T09:05:34Z","source_system":"django","correlation_id":"c4f31bf2-a48f-4f09-81d3-b14dd44f2e26","payload":{"sport_id":1,"venue_id":1,"date":"2026-04-20","start_time":"10:00:00","end_time":"11:00:00","court":"1","hold_id":999,"booking_id":null,"available":false,"available_courts":2,"total_courts":3,"reason":"Temporarily held","actor":{"id":44,"type":"user"}}}'
SIG="sha256=$(echo -n "${TIMESTAMP}.${BODY}" | openssl dgst -sha256 -hmac "$SECRET" | cut -d' ' -f2)"

curl -X POST http://localhost:8000/api/integration/webhooks/django-events \
  -H "Content-Type: application/json" \
  -H "X-Webhook-Source: django" \
  -H "X-Webhook-Timestamp: $TIMESTAMP" \
  -H "X-Webhook-Signature: $SIG" \
  -H "X-Event-Id: 6f4308e3-4177-4d11-bf0a-fb97fef2a710" \
  -H "X-Idempotency-Key: test-hold-001" \
  -d "$BODY"
```

**Expected:**
- Response: `{"status":"accepted","event_id":"6f4308e3-..."}`
- Indoor admin calendar: slot at 10:00 on 2026-04-20 turns orange and shows "On Hold"
- Super admin: toast notification appears

### Step 5: Test duplicate rejection

Run the same curl command again with the same `event_id`.

**Expected:** `{"status":"duplicate","event_id":"6f4308e3-..."}` — no second broadcast.

### Step 6: Test bad signature

Change one character in the signature and resend.

**Expected:** `401 Unauthorized`

---

## 15. Troubleshooting

### Webhook returns 401

| Possible cause | Fix |
|---|---|
| Clock drift > 300s between Django and Laravel servers | Sync server clocks with NTP |
| Wrong secret on one side | Check `DJANGO_LARAVEL_WEBHOOK_SECRET` is identical on both |
| Signature built from wrong string | Must be `timestamp + "." + raw_body` exactly |
| Body modified after signing | Do not pretty-print JSON after computing HMAC |

### Webhook returns 422

| Possible cause | Fix |
|---|---|
| Missing `payload.sport_id` | Add to Django payload |
| `event_type` not in allowed list | Check spelling matches exactly |
| `source_system` is not `"django"` | Set `"source_system": "django"` in envelope |

### Calendar slot does not turn orange (hold not showing)

| Possible cause | Fix |
|---|---|
| WebSocket not connected | Check browser console for Reverb errors |
| `sport_id` in webhook payload does not match Laravel sport ID | Verify IDs are synced between Django and Laravel |
| `venue_id` in webhook does not match `complex_id` of logged-in staff | Verify venue mapping |
| `QUEUE_CONNECTION=sync` but job failed silently | Check `storage/logs/laravel.log` |

### Stats not refreshing on super admin page

| Possible cause | Fix |
|---|---|
| Livewire JS not loaded | Check `<livewire:scripts>` in layout |
| `window.Echo` not defined | Check Reverb/Echo setup in `app.js` |
| `refreshData` method not found | Clear view cache: `php artisan view:clear` |

### Queue jobs not running (holds not broadcasting)

If `QUEUE_CONNECTION=sync`, jobs run inline and may fail silently.  
Set `QUEUE_CONNECTION=database` and run:
```bash
php artisan queue:work --tries=3 --timeout=30
```

Check failed jobs:
```bash
php artisan queue:failed
```

---

## Summary of All Files

| File | Action | Purpose |
|---|---|---|
| `database/migrations/2026_04_15_..._create_django_webhook_events_table.php` | **Created** | Idempotency log table |
| `app/Models/DjangoWebhookEvent.php` | **Created** | Model for webhook events table |
| `app/Http/Controllers/DjangoWebhookController.php` | **Created** | Webhook endpoint with security |
| `app/Jobs/ProcessDjangoWebhookEvent.php` | **Created** | Background job to broadcast |
| `app/Events/SlotStateChanged.php` | **Created** | Broadcast event for slot changes |
| `app/Events/BookingCreated.php` | **Modified** | Added `bookings.global` channel |
| `app/Livewire/Admin/Bookings.php` | **Modified** | Added `refreshData()` method |
| `routes/api.php` | **Modified** | Added webhook route |
| `config/services.php` | **Modified** | Added webhook secret config |
| `.env` | **Modified** | Renamed + strengthened webhook secret |
| `resources/views/livewire/admin/bookings.blade.php` | **Modified** | Added WebSocket JS + toast |
| `resources/views/livewire/staff/bookings-management.blade.php` | **Modified** | Added held slot CSS + JS listener |
