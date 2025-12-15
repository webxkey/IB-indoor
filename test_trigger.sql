INSERT INTO booking_booking (
    user_id_id, complex_id_id, game_id_id, game_name,
    booking_date, user_name, user_number, court_number,
    start_time, end_time, duration, price,
    payment_status, status, qr_code, is_challenge_booking,
    created_at, updated_at
) VALUES (
    1, 1, 1, 'Football',
    CURDATE(), 'MySQL Trigger Test', '+1234567890', '2',
    '17:00:00', '18:00:00', 60, 1800,
    'Pending', 'Confirmed', 'TRIG123', 0,
    NOW(), NOW()
);

-- Check the queue to see if trigger fired
SELECT * FROM booking_change_queue ORDER BY created_at DESC LIMIT 1;
