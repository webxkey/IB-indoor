<?php

namespace App\Http\Controllers;

use App\Models\BookingBooking;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Auth;

class CustomerReceiptController extends Controller
{
    public function download($bookingId)
    {
        $booking = BookingBooking::with(['venue', 'sport'])->findOrFail($bookingId);

        // Security: only allow if the booking belongs to the logged-in user OR staff/admin
        $user = Auth::user();
        if (!$user) {
            abort(403);
        }

        $isOwner = ($booking->user_id_id === $user->id)
            || ($booking->user_name === $user->name)
            || in_array($user->role ?? '', ['staff', 'admin', 'facility_owner']);

        if (!$isOwner) {
            abort(403, 'Access denied.');
        }

        $pdf = Pdf::loadView('customer-receipt', ['booking' => $booking]);
        $pdf->setPaper('A4', 'portrait');

        $filename = 'booking-receipt-' . $booking->id . '.pdf';
        return $pdf->download($filename);
    }
}
