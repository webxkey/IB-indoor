<?php

use Illuminate\Support\Facades\Broadcast;

/*
|--------------------------------------------------------------------------
| Broadcast Channels
|--------------------------------------------------------------------------
|
| Here you may register all of the event broadcasting channels that your
| application supports. The given channel authorization callbacks are
| used to check if an authenticated user can listen to the channel.
|
*/

Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});

/**
 * Booking updates channel
 * Allows staff/admins to listen for real-time booking changes
 * Format: bookings.complex.{complexId}
 * Events:
 *  - booking.created: New booking from mobile app
 *  - booking.updated: Booking status or details changed
 *  - booking.deleted: Booking cancelled or deleted
 */
Broadcast::channel('bookings.complex.{complexId}', function ($user, $complexId) {
    // Allow access if user is admin or staff member for this complex
    return $user->role === 'admin' || 
           $user->role === 'staff' || 
           (int)$user->complex_id === (int)$complexId;
});
