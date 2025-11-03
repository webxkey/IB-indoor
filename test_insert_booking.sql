-- ============================================================================
-- QUICK TEST: Insert a booking directly into database (simulates mobile app)
-- ============================================================================

-- Use this SQL in phpMyAdmin to test real-time updates
-- Copy and paste into phpMyAdmin SQL tab

-- Test Booking 1: Cricket Court 1
INSERT INTO `booking_booking` (
    `user_id_id`, `complex_id_id`, `game_id_id`, `game_name`, 
    `booking_date`, `user_name`, `user_number`, `court_number`, 
    `start_time`, `end_time`, `duration`, `price`, 
    `payment_status`, `payment_method`, `status`, 
    `notes`, `qr_code`, `permanent`, `created_at`, `updated_at`
) VALUES (
    1, 
    2, 
    1, 
    'Cricket',
    CURDATE(), 
    'Mobile User Test 1', 
    '+1234567890', 
    '1',
    '14:00:00', 
    '15:00:00', 
    60, 
    1800.00,
    'Pending', 
    'Stripe', 
    'Booked',
    'Test from phpMyAdmin - Should appear on dashboard automatically!', 
    CONCAT('QR', UPPER(SUBSTRING(MD5(RAND()), 1, 6))), 
    0,
    NOW(),
    NOW()
);

-- Test Booking 2: Cricket Court 2 (run this after first one works)
-- INSERT INTO `booking_booking` (
--     `user_id_id`, `complex_id_id`, `game_id_id`, `game_name`, 
--     `booking_date`, `user_name`, `user_number`, `court_number`, 
--     `start_time`, `end_time`, `duration`, `price`, 
--     `payment_status`, `payment_method`, `status`, 
--     `notes`, `qr_code`, `permanent`, `created_at`, `updated_at`
-- ) VALUES (
--     1, 2, 1, 'Cricket',
--     CURDATE(), 'Mobile User Test 2', '+0987654321', '2',
--     '15:00:00', '16:00:00', 60, 1800.00,
--     'Pending', 'Stripe', 'Booked',
--     'Second test booking!', 
--     CONCAT('QR', UPPER(SUBSTRING(MD5(RAND()), 1, 6))), 0,
--     NOW(), NOW()
-- );

-- ============================================================================
-- INSTRUCTIONS:
-- ============================================================================
-- 1. Open dashboard: http://127.0.0.1:8000/facility_owner/bookings
-- 2. Press F12 to open browser console
-- 3. Go to phpMyAdmin → indoor_booking_db → SQL tab
-- 4. Paste the first INSERT statement above
-- 5. Click "Go"
-- 6. Watch your dashboard - booking appears within 3 seconds! 🎉
--
-- You should see:
-- - Notification popup
-- - New booking in calendar
-- - Console message: "New booking detected from database!"
-- ============================================================================

-- To check what was inserted:
-- SELECT * FROM booking_booking ORDER BY id DESC LIMIT 5;
