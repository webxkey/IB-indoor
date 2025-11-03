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

// Booking channel for real-time updates
Broadcast::channel('bookings.{complexId}', function ($user, $complexId) {
    // Allow all authenticated users to listen to their complex's bookings
    return true; // You can add more specific authorization here
});
