# Mobile App Integration - Real-Time Booking API

This guide helps mobile app developers integrate with your real-time booking system.

---

## Quick Setup

When your mobile app creates a booking, it automatically triggers real-time updates to all admin dashboards.

## API Endpoint

### Create Booking (Mobile App)

**Endpoint:**
```
POST /api/bookings
Content-Type: application/json
```

**Headers:**
```json
{
    "Authorization": "Bearer {token}",
    "Content-Type": "application/json",
    "Accept": "application/json"
}
```

**Request Body:**
```json
{
    "user_id": "user-123",
    "user_name": "Ahmed Ali",
    "user_number": "+92-300-1234567",
    "game_name": "Cricket",
    "complex_id": 1,
    "venue_id": 1,
    "sport_id": 5,
    "court_number": "2",
    "booking_date": "2024-01-15",
    "start_time": "10:00:00",
    "end_time": "11:00:00",
    "duration": "1 hour",
    "price": 500,
    "payment_method": "card",
    "payment_status": "Pending",
    "status": "Confirmed",
    "notes": "Booking from mobile app",
    "is_challenge_booking": false,
    "team_id": null,
    "opponent_team_id": null
}
```

**Success Response (201):**
```json
{
    "success": true,
    "message": "Booking created successfully",
    "booking": {
        "id": 456,
        "user_name": "Ahmed Ali",
        "game_name": "Cricket",
        "status": "Confirmed",
        "created_at": "2024-01-15 10:00:00",
        "complex_id": 1
    }
}
```

---

## Real-Time Behavior

### What Happens When You Create a Booking

1. **Mobile app** sends POST request to `/api/bookings`
2. **Server** saves booking to database
3. **Eloquent Observer** detects the insert
4. **Broadcast event** sent to Reverb (bookings.complex.1)
5. **All connected admins** receive notification instantly
6. **Admin dashboard** updates with new booking

### Admin Experience

```
[Admin Dashboard Open]
  ↓
[Mobile User Creates Booking]
  ↓
[Admin sees toast notification: "✨ New Booking: Ahmed Ali booked Cricket"]
  ↓
[Booking appears in calendar instantly - no refresh needed]
```

---

## Update Booking Status (Mobile App)

When payment is completed or booking status changes:

**Endpoint:**
```
PUT /api/bookings/{id}
Content-Type: application/json
```

**Example - Mark as Paid:**
```json
{
    "payment_status": "Paid",
    "status": "Confirmed"
}
```

**Triggered Broadcast:**
- Event: `booking.updated`
- Admins see: "📝 Booking Updated: Status changed to Confirmed"

---

## Cancel Booking (Mobile App)

**Endpoint:**
```
DELETE /api/bookings/{id}
```

**Triggered Broadcast:**
- Event: `booking.deleted`
- Admins see: "🗑️ Booking Deleted: Booking #456 has been cancelled"

---

## Sample Mobile Integration Code

### JavaScript/React Native

```javascript
// Import the API client
import axios from 'axios';

const API_BASE = 'http://127.0.0.1:8000/api';
const USER_TOKEN = 'your-bearer-token';

const apiClient = axios.create({
    baseURL: API_BASE,
    headers: {
        'Authorization': `Bearer ${USER_TOKEN}`,
        'Content-Type': 'application/json',
    }
});

// Create booking
async function createBooking(bookingData) {
    try {
        const response = await apiClient.post('/bookings', {
            user_id: bookingData.userId,
            user_name: bookingData.userName,
            user_number: bookingData.phoneNumber,
            game_name: bookingData.sport,
            complex_id: bookingData.complexId,
            court_number: bookingData.court,
            booking_date: bookingData.date,
            start_time: bookingData.startTime,
            end_time: bookingData.endTime,
            duration: bookingData.duration,
            price: bookingData.price,
            payment_method: 'card',
            payment_status: 'Pending',
            status: 'Pending'
        });

        console.log('✅ Booking created!', response.data);
        return response.data.booking;
    } catch (error) {
        console.error('❌ Booking failed:', error.response.data);
        throw error;
    }
}

// Update booking
async function updateBooking(bookingId, updates) {
    const response = await apiClient.put(`/bookings/${bookingId}`, updates);
    console.log('📝 Booking updated!', response.data);
    return response.data.booking;
}

// Cancel booking
async function cancelBooking(bookingId) {
    const response = await apiClient.delete(`/bookings/${bookingId}`);
    console.log('🗑️ Booking cancelled!', response.data);
    return true;
}
```

### Flutter/Dart

```dart
import 'package:http/http.dart' as http;
import 'dart:convert';

class BookingAPI {
  final String baseUrl = 'http://127.0.0.1:8000/api';
  final String token = 'your-bearer-token';

  Future<BookingResponse> createBooking(BookingRequest booking) async {
    final response = await http.post(
      Uri.parse('$baseUrl/bookings'),
      headers: {
        'Authorization': 'Bearer $token',
        'Content-Type': 'application/json',
      },
      body: jsonEncode(booking.toJson()),
    );

    if (response.statusCode == 201) {
      return BookingResponse.fromJson(jsonDecode(response.body));
    } else {
      throw Exception('Failed to create booking');
    }
  }

  Future<void> updateBookingStatus(int bookingId, String status) async {
    final response = await http.put(
      Uri.parse('$baseUrl/bookings/$bookingId'),
      headers: {
        'Authorization': 'Bearer $token',
        'Content-Type': 'application/json',
      },
      body: jsonEncode({'status': status, 'payment_status': 'Paid'}),
    );

    if (response.statusCode != 200) {
      throw Exception('Failed to update booking');
    }
  }

  Future<void> cancelBooking(int bookingId) async {
    final response = await http.delete(
      Uri.parse('$baseUrl/bookings/$bookingId'),
      headers: {
        'Authorization': 'Bearer $token',
      },
    );

    if (response.statusCode != 200) {
      throw Exception('Failed to cancel booking');
    }
  }
}
```

---

## Admin Experience - What They See

### New Booking Created
```
┌─────────────────────────────────────────┐
│ ✨ New Booking                          │
│ Ahmed Ali booked Cricket                │
└─────────────────────────────────────────┘

[Calendar instantly shows:]
10:00 AM - 11:00 AM | Ahmed Ali | Cricket | Court 2 | PENDING
```

### Booking Status Updated
```
┌─────────────────────────────────────────┐
│ 📝 Booking Updated                      │
│ Booking #456 status: Confirmed          │
└─────────────────────────────────────────┘

[Admin sees booking color change to CONFIRMED]
```

### Booking Cancelled
```
┌─────────────────────────────────────────┐
│ 🗑️ Booking Deleted                      │
│ Booking #456 has been deleted           │
└─────────────────────────────────────────┘

[Booking disappears from calendar instantly]
```

---

## Error Handling

### Common Errors

**401 Unauthorized**
```json
{
    "message": "Invalid token",
    "error": "Unauthenticated"
}
```
→ Check Bearer token is valid

**422 Validation Failed**
```json
{
    "message": "The given data was invalid",
    "errors": {
        "user_name": ["The user name is required"],
        "game_name": ["The game name must exist"]
    }
}
```
→ Validate all required fields before sending

**Timeout/Network Error**
→ Implement retry logic with exponential backoff

---

## Testing the Integration

### Test 1: Create Booking from Mobile
```bash
curl -X POST http://127.0.0.1:8000/api/bookings \
  -H "Authorization: Bearer {token}" \
  -H "Content-Type: application/json" \
  -d '{
    "user_name": "Test User",
    "game_name": "Cricket",
    "court_number": "1",
    "booking_date": "2024-01-15",
    "start_time": "10:00:00",
    "end_time": "11:00:00",
    "complex_id": 1,
    "payment_status": "Pending",
    "status": "Confirmed"
  }'
```

### Test 2: Watch Admin Dashboard
1. Open admin dashboard in browser
2. Run the curl command above
3. **Watch it appear instantly** ✨

---

## Best Practices

✅ **Always include complex_id** - Tells which facility the booking is for  
✅ **Validate data before sending** - Prevent 422 errors  
✅ **Handle network errors** - Add retry logic  
✅ **Use Bearer tokens** - Never send credentials in request body  
✅ **Send correct status values** - Confirmed, Pending, Cancelled, etc.  

---

## Support

If bookings aren't appearing in real-time:

1. Ensure Reverb server is running: `php artisan reverb:start`
2. Check network tab in browser DevTools - should see WebSocket connection
3. Verify complex_id in mobile request matches admin user's complex
4. Check Laravel logs: `tail -f storage/logs/laravel.log`

---

## Next Steps

1. Integrate with your mobile app API client
2. Test creating bookings from mobile
3. Watch admin dashboard update in real-time
4. Deploy to production (adjust Reverb settings)
5. Monitor real-time events in production logs

**Your mobile app and admin dashboard are now fully synchronized!** 🎉
