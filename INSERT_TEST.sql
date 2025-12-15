-- ============================================
-- INSTANT REALTIME TEST
-- Run this SQL in phpMyAdmin
-- Page should update within 0.1-0.5 seconds!
-- ============================================

INSERT INTO booking_booking (
    user_id_id, complex_id_id, game_id_id, game_name,
    booking_date, user_name, user_number, court_number,
    start_time, end_time, duration, price,
    payment_status, status, qr_code, is_challenge_booking,
    created_at, updated_at
) VALUES (
    1, 1, 1, 'Football',
    CURDATE(), CONCAT('Realtime Test ', FLOOR(RAND() * 10000)), '+1234567890', '2',
    '17:00:00', '18:00:00', 60, 1800,
    'Pending', 'Confirmed', CONCAT('RT', FLOOR(RAND() * 100000)), 0,
    NOW(), NOW()
);

-- Check if trigger fired
SELECT * FROM booking_change_queue WHERE processed = 0 ORDER BY created_at DESC LIMIT 1;
