# WebSocket & Notification System Fixes (June 11, 2026)

## 1. Notification Foreign Key Fix
**Issue:** `Failed to notify staff: SQLSTATE[23503]: Foreign key violation`.
**Cause:** The `booking_notification` table has a foreign key constraint linking to the Django `users_user` table. The code was attempting to use IDs from the Laravel `users` table, which caused a mismatch.
**Fix:** Updated `App\Http\Controllers\Api\Admin\BookingController::notifyStaff()` to:
1. Identify staff members by email in the Laravel `users` table.
2. Look up the corresponding IDs in the Django `users_user` table.
3. Use those Django IDs when creating the notification record.

## 2. "Invalid socket ID" Broadcasting Fix
**Issue:** `Failed to broadcast: Invalid socket ID undefined`.
**Cause:** The code used `->toOthers()` on broadcast events. This method requires a valid `X-Socket-ID` header from the client. If the client (browser) fails to connect to the WebSocket server, this ID is missing, causing the backend broadcast to fail.
**Fix:** Removed `->toOthers()` from:
- `App\Observers\BookingBookingObserver.php`
- `routes/api.php`
- `App\Http\Controllers\Api\Admin\BookingController.php` (if applicable)

Events now broadcast to all connected clients (including the sender) without crashing when the socket ID is unavailable.

## 3. WebSocket Configuration (Production)
**Issue:** Browser attempting to connect to `127.0.0.1:8080` instead of production domain.
**Fix:**
1. Updated `.env` with production Reverb credentials:
   - `REVERB_HOST=admin.sportynix.com`
   - `REVERB_PORT=443`
   - `REVERB_SCHEME=https`
   - `REVERB_SERVER_PORT=8081` (internal)
2. Re-compiled frontend assets using `npm run build` to bake these settings into the production JavaScript files.

## 4. Cache Clearing
Ensured all Laravel caches were cleared on the server to apply `.env` changes:
```bash
php artisan optimize:clear
```

## 5. Laravel-Django Integration
Laravel and Django work together using a shared database and real-time webhooks.

### Integration Type:
- **Shared Database:** Both systems read/write to the same PostgreSQL database, ensuring data consistency for bookings and users.
- **Webhooks:** Django notifies Laravel of real-time events to trigger WebSocket broadcasts for the staff dashboard.
- **Security:** Webhooks are secured via a shared secret (`DJANGO_LARAVEL_WEBHOOK_SECRET`) and HMAC-SHA256 signatures.

### API Endpoints for Integration:
1. **`POST /api/integration/webhooks/django-events`**
   - **Purpose:** Handles complex state changes (holds, blocks, cancellations).
   - **Headers:** `X-Webhook-Source: django`, `X-Webhook-Timestamp`, `X-Webhook-Signature`.
   - **Events handled:** `slot.hold.created`, `slot.hold.released`, `booking.created`, `booking.cancelled`, `slot.blocked`, `slot.unblocked`.

2. **`POST /api/integration/booking-notify`**
   - **Purpose:** Fast notification for new bookings created via mobile/Django.
   - **Header:** `X-Webhook-Secret` (Shared secret).
   - **Payload:** `{ "booking_id": 123 }`.
   - **Action:** Laravel fetches the record from the shared DB and broadcasts `BookingCreated` to the staff dashboard via Reverb.

