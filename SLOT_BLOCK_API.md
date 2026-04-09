# Slot Block API — Integration Guide

## Overview

When a staff member blocks a time slot in the IndoorB dashboard,
the mobile app **must check this API before allowing a customer to book**.

If the slot is blocked, the app should show the reason and prevent booking.

---

## Endpoint

```
GET /api/slot/available
```

**Base URL:** `http://your-domain.com/api/slot/available`  
**Auth required:** No (public endpoint)

---

## Query Parameters

| Parameter  | Type   | Required | Description                          | Example        |
|------------|--------|----------|--------------------------------------|----------------|
| `sport_id` | integer | Yes     | ID of the sport (from booking_sport) | `9`            |
| `date`     | string  | Yes     | Booking date in `YYYY-MM-DD`         | `2026-04-09`   |
| `time`     | string  | Yes     | Time slot in `HH:mm:ss` or `HH:mm`  | `09:00:00`     |
| `court`    | string  | No      | Court number (default: `1`)          | `1`            |

---

## Response — Slot is AVAILABLE

```json
{
  "available": true
}
```

**→ Allow the customer to proceed with booking.**

---

## Response — Slot is BLOCKED by staff

```json
{
  "available": false,
  "reason": "Maintenance"
}
```

**→ Show the reason to the customer and block the booking.**

Possible reason values:
- `"Maintenance"` — court under maintenance
- `"Reserved"` — reserved for private event
- `"Cleaning"` — cleaning in progress
- Any custom reason the staff entered

---

## Response — Slot already booked by another customer

```json
{
  "available": false,
  "reason": "Already booked"
}
```

---

## Response — Sport not found

```json
{
  "available": false,
  "reason": "Sport not found"
}
```
HTTP status: `404`

---

## Response — Missing parameters

```json
{
  "errors": {
    "sport_id": ["The sport id field is required."],
    "date": ["The date field is required."],
    "time": ["The time field is required."]
  }
}
```
HTTP status: `422`

---

## Example Requests

### Check if court 1 is available at 9am on April 9:
```
GET /api/slot/available?sport_id=9&date=2026-04-09&time=09:00:00&court=1
```

### Check with HH:mm format (also accepted):
```
GET /api/slot/available?sport_id=9&date=2026-04-09&time=09:00&court=2
```

---

## Check All Courts for a Time Slot

To get availability of **all courts** at once for a given time:

```
GET /api/slot/available-courts?sport_id=9&date=2026-04-09&time=09:00:00
```

### Response:
```json
{
  "courts": [
    { "court": 1, "available": false, "reason": "Maintenance" },
    { "court": 2, "available": true,  "reason": null },
    { "court": 3, "available": false, "reason": "Already booked" }
  ]
}
```

---

## How to Integrate in the Mobile App

### Step 1 — When displaying available time slots

Before showing a time slot as bookable, call the API for each slot:

```
GET /api/slot/available?sport_id={id}&date={date}&time={time}&court={court}
```

- If `available: true` → show as green/bookable
- If `available: false` → show as grey/blocked with the `reason`

### Step 2 — When customer confirms booking

Call the API **one more time** right before submitting the booking
to prevent race conditions (another user booked it in the meantime):

```
GET /api/slot/available?sport_id={id}&date={date}&time={time}&court={court}
```

- If `available: true` → proceed to create the booking
- If `available: false` → show error: *"This slot is no longer available: {reason}"*

---

## Quick Test

Open this URL in a browser to test (replace values with real ones from your DB):

```
http://127.0.0.1:8000/api/slot/available?sport_id=9&date=2026-04-09&time=09:00:00&court=1
```

Expected response when no block exists:
```json
{"available": true}
```

To test a blocked slot, first block a slot in the staff dashboard at:
`http://127.0.0.1:8000/staff/sports` → click "Block Slot" on any sport,
then call the API with the same date/time/court.

---

## Notes for Developer

- `sport_id` is the ID from the `booking_sport` table — get it from `GET /api/venues/{id}/sports`
- Time can be sent as `09:00` or `09:00:00` — both are accepted
- Court numbers start from `1`
- This endpoint does **not** require an auth token — call it freely
- Staff block data is stored in `booking_sport.blocked_slots` as JSON
