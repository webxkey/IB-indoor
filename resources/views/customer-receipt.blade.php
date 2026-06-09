<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <title>Booking Receipt</title>
    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            color: #333;
            font-size: 13px;
        }

        .header {
            background: #19722d;
            color: white;
            padding: 20px;
            text-align: center;
        }

        .header h1 {
            margin: 0;
            font-size: 24px;
        }

        .header p {
            margin: 5px 0 0;
            opacity: 0.85;
        }

        .content {
            padding: 30px;
        }

        .section-title {
            font-size: 14px;
            font-weight: bold;
            color: #19722d;
            border-bottom: 2px solid #19722d;
            padding-bottom: 5px;
            margin: 20px 0 10px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        td {
            padding: 8px 0;
        }

        td:first-child {
            color: #666;
            width: 45%;
        }

        td:last-child {
            font-weight: bold;
        }

        .total-row td {
            font-size: 16px;
            color: #19722d;
            border-top: 1px solid #ddd;
            padding-top: 12px;
        }

        .status-badge {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 11px;
            font-weight: bold;
        }

        .status-Completed {
            background: #d1fae5;
            color: #065f46;
        }

        .status-Confirmed {
            background: #fef9c3;
            color: #713f12;
        }

        .status-Cancelled {
            background: #fee2e2;
            color: #991b1b;
        }

        .footer {
            text-align: center;
            color: #999;
            font-size: 11px;
            margin-top: 40px;
            border-top: 1px solid #eee;
            padding-top: 15px;
        }
    </style>
</head>

<body>
    <div class="header">
        <h1>Sportynix Hub</h1>
        <p>Booking Confirmation Receipt</p>
    </div>
    <div class="content">
        <div class="section-title">Booking Details</div>
        <table>
            <tr>
                <td>Booking ID</td>
                <td>#{{ $booking->id }}</td>
            </tr>
            <tr>
                <td>QR Code</td>
                <td>{{ $booking->qr_code ?? 'N/A' }}</td>
            </tr>
            <tr>
                <td>Sport</td>
                <td>{{ $booking->game_name }}</td>
            </tr>
            <tr>
                <td>Venue</td>
                <td>{{ $booking->venue->name ?? 'N/A' }}</td>
            </tr>
            <tr>
                <td>Date</td>
                <td>{{ \Carbon\Carbon::parse($booking->booking_date)->format('l, F j, Y') }}</td>
            </tr>
            <tr>
                <td>Time</td>
                <td>{{ \Carbon\Carbon::parse($booking->start_time)->format('g:i A') }} –
                    {{ \Carbon\Carbon::parse($booking->end_time)->format('g:i A') }}</td>
            </tr>
            <tr>
                <td>Court</td>
                <td>{{ $booking->court_number ?? '1' }}</td>
            </tr>
            <tr>
                <td>Status</td>
                <td><span class="status-badge status-{{ $booking->status }}">{{ $booking->status }}</span></td>
            </tr>
        </table>

        <div class="section-title">Customer Details</div>
        <table>
            <tr>
                <td>Name</td>
                <td>{{ $booking->user_name }}</td>
            </tr>
            <tr>
                <td>Phone</td>
                <td>{{ $booking->user_number }}</td>
            </tr>
        </table>

        <div class="section-title">Payment</div>
        <table>
            <tr>
                <td>Amount</td>
                <td>LKR {{ number_format($booking->price, 2) }}</td>
            </tr>
            <tr>
                <td>Payment Status</td>
                <td>{{ $booking->payment_status }}</td>
            </tr>
            <tr>
                <td>Payment Method</td>
                <td>{{ $booking->payment_method ?? 'N/A' }}</td>
            </tr>
        </table>

        <div class="footer">
            <p>Thank you for booking with Sportynix Hub!</p>
            <p>Generated on {{ now()->format('M d, Y \a\t g:i A') }}</p>
        </div>
    </div>
</body>

</html>