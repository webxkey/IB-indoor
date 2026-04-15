# Django -> Laravel Webhook Spec (Outbound Events)

## Purpose

Django will send booking and slot state events to Laravel.
Laravel must receive these events, validate them, process them safely, and push realtime updates to super admin and indoor admin dashboards without page refresh.

This document defines the exact webhook contract for Laravel.

---

## Scope (Phase 1)

Initial event types from Django:

- `slot.hold.created`
- `slot.hold.released`
- `booking.created`
- `booking.cancelled`
- `slot.blocked`
- `slot.unblocked`

All events are notifications of committed state changes.

---

## Laravel Webhook Endpoint

Laravel must expose a public HTTPS endpoint for Django to call.

Example:

`POST /api/integration/webhooks/django-events`

Django environment variable example:

`LARAVEL_WEBHOOK_URL=https://admin.example.com/api/integration/webhooks/django-events`

---

## Security Requirements

Laravel must reject any webhook request that fails signature or timestamp checks.

### Required Headers

- `Content-Type: application/json`
- `X-Webhook-Source: django`
- `X-Webhook-Timestamp: <unix-seconds>`
- `X-Webhook-Signature: sha256=<hex-hmac>`
- `X-Event-Id: <uuid>`
- `X-Idempotency-Key: <string>`

### Signature Algorithm

Use HMAC-SHA256 with a shared secret.

- Secret: `DJANGO_LARAVEL_WEBHOOK_SECRET`
- Signed payload string:
  - `<timestamp>.<raw_request_body>`
- Signature:
  - hex digest of HMAC-SHA256
- Header format:
  - `X-Webhook-Signature: sha256=<hex-digest>`

Laravel must perform constant-time comparison.

### Timestamp Window

- Reject if absolute clock drift is greater than 300 seconds.
- Return HTTP `401` for signature or timestamp failure.

---

## Idempotency Rules

Laravel must process each `event_id` exactly once.

### Required behavior

- Store every received `event_id` in a durable table with unique index.
- If duplicate `event_id` is received, return HTTP `200` with `{ "status": "duplicate" }` and do not re-apply changes.

### Suggested table

`django_webhook_events`

Suggested columns:

- `id` (pk)
- `event_id` (unique)
- `event_type`
- `received_at`
- `processed_at`
- `status` (`received|processed|failed|duplicate`)
- `payload_json` (json)
- `error_message` (nullable)

---

## Request Body Schema

All events use one envelope.

```json
{
  "schema_version": "1.0",
  "event_id": "6f4308e3-4177-4d11-bf0a-fb97fef2a710",
  "event_type": "slot.hold.created",
  "occurred_at": "2026-04-15T12:05:34Z",
  "source_system": "django",
  "correlation_id": "c4f31bf2-a48f-4f09-81d3-b14dd44f2e26",
  "payload": {
    "sport_id": 9,
    "venue_id": 3,
    "date": "2026-04-18",
    "start_time": "09:00:00",
    "end_time": "10:00:00",
    "court": "1",
    "booking_id": null,
    "hold_id": 2881,
    "available": false,
    "available_courts": 2,
    "total_courts": 3,
    "reason": "Temporarily held",
    "actor": {
      "id": 44,
      "type": "user"
    }
  }
}
```

### Envelope field requirements

- `schema_version` required
- `event_id` required UUID
- `event_type` required string enum
- `occurred_at` required ISO8601 UTC
- `source_system` must be `django`
- `payload` required object

### Actor role mapping

Laravel should use the `actor.role` field to distinguish who caused the change.

- `super_admin` means the action was performed by your company-level super admin
- `indoor_admin` means the action was performed by a venue/indoor admin
- `user` means the action came from a normal mobile app customer

The `actor.type` field will usually be `admin` for `super_admin` and `indoor_admin`, and `user` for customer actions.

### Payload minimum requirements

For slot and booking events, Laravel must expect at least:

- `sport_id`
- `venue_id`
- `date`
- `start_time`
- `end_time`

Other fields are optional by event type.

---

## Response Contract

Laravel webhook endpoint must respond quickly and consistently.

### Success

- HTTP `200`
- Body example:

```json
{
  "status": "accepted",
  "event_id": "6f4308e3-4177-4d11-bf0a-fb97fef2a710"
}
```

### Duplicate

- HTTP `200`
- Body example:

```json
{
  "status": "duplicate",
  "event_id": "6f4308e3-4177-4d11-bf0a-fb97fef2a710"
}
```

### Invalid signature/timestamp

- HTTP `401`

### Validation error

- HTTP `422`

### Server error

- HTTP `500`

---

## Processing Model in Laravel

Webhook endpoint should be lightweight.

1. Verify signature and timestamp.
2. Validate schema.
3. Upsert event_id for idempotency.
4. Queue a background job for business processing.
5. Return `200` quickly.

Background job should:

1. Update Laravel read model/cache for slot state.
2. Broadcast websocket event to dashboard channels.
3. Mark webhook event as processed.

---

## Realtime Dashboard Behavior (No Refresh)

After processing a webhook event, Laravel must broadcast to subscribed admins.

### Recommended channel naming

- `slots.venue.{venue_id}.sport.{sport_id}`
- Optional global channel for super admin: `slots.global`

### Recommended broadcast payload

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
  "reason": "Temporarily held"
}
```

Dashboard frontend must patch local state from websocket message and re-render immediately.
No page refresh should be required.

---

## Retry Expectations (Django Sender)

Django will retry on non-2xx responses.

Expected Laravel behavior:

- Return 2xx once event is safely persisted or recognized as duplicate.
- Avoid long request processing timeouts.

Recommended timeout target:

- P95 less than 200 ms at webhook endpoint.

---

## Event Type Reference

### slot.hold.created

Meaning: a slot hold is active and temporary availability changed.

Expected payload extras:

- `hold_id`
- `available`
- `available_courts`
- `total_courts`
- `reason` (typically `Temporarily held`)

### slot.hold.released

Meaning: hold removed and availability changed.

Expected payload extras:

- `hold_id`
- `available`
- `available_courts`
- `total_courts`

### booking.created

Meaning: confirmed booking created.

Expected payload extras:

- `booking_id`
- `court`
- `available`
- `available_courts`
- `total_courts`
- `reason` (often `Already booked` when fully occupied)

### booking.cancelled

Meaning: booking cancelled and courts may become available.

Expected payload extras:

- `booking_id`
- `available`
- `available_courts`
- `total_courts`

### slot.blocked

Meaning: admin/system blocked a slot.

Expected payload extras:

- `reason` (for example `Maintenance`, `Reserved`, `Cleaning`, or custom)
- `court` (nullable, if block is court-specific)

### slot.unblocked

Meaning: previously blocked slot is unblocked.

Expected payload extras:

- `reason` may be null

---

## Laravel Validation Checklist

- [ ] Verify `X-Webhook-Source` equals `django`
- [ ] Verify timestamp freshness
- [ ] Verify HMAC signature
- [ ] Validate `event_id` UUID
- [ ] Validate `event_type` allowed enum
- [ ] Validate required payload fields
- [ ] Enforce idempotency via unique `event_id`
- [ ] Queue processing and broadcast to dashboards
- [ ] Return fast 2xx response

---

## Local Test Procedure

1. Expose local Laravel endpoint via ngrok or similar.
2. Set Django `LARAVEL_WEBHOOK_URL` to that URL.
3. Perform actions in Django app:
   - hold slot
   - release slot
   - create booking
   - cancel booking
4. Confirm Laravel receives events.
5. Confirm dashboard updates without refresh.

---

## Notes

- This is outbound-only phase (Django -> Laravel).
- Inbound phase (Laravel -> Django) will use a separate document and endpoint contract.
