-- ===================================================================
-- MySQL Triggers for Real-Time Database Change Detection
-- These triggers will fire Laravel events when bookings change in DB
-- ===================================================================

-- Drop existing triggers if they exist
DROP TRIGGER IF EXISTS booking_after_insert;
DROP TRIGGER IF EXISTS booking_after_update;

-- Trigger 1: After INSERT - Call Laravel command to broadcast event
DELIMITER $$

CREATE TRIGGER booking_after_insert
AFTER INSERT ON booking_booking
FOR EACH ROW
BEGIN
    -- We'll use a stored procedure to notify Laravel
    -- Store the new booking ID in a notification table
    INSERT INTO booking_notifications (booking_id, event_type, created_at)
    VALUES (NEW.id, 'created', NOW());
END$$

CREATE TRIGGER booking_after_update
AFTER UPDATE ON booking_booking
FOR EACH ROW
BEGIN
    -- Only trigger if important fields changed
    IF NEW.status != OLD.status OR 
       NEW.start_time != OLD.start_time OR 
       NEW.end_time != OLD.end_time OR 
       NEW.court_number != OLD.court_number THEN
        
        INSERT INTO booking_notifications (booking_id, event_type, created_at)
        VALUES (NEW.id, 'updated', NOW());
    END IF;
END$$

DELIMITER ;

-- ===================================================================
-- Create notification table to track database changes
-- ===================================================================

CREATE TABLE IF NOT EXISTS booking_notifications (
    id INT AUTO_INCREMENT PRIMARY KEY,
    booking_id INT NOT NULL,
    event_type ENUM('created', 'updated') NOT NULL,
    processed BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_processed (processed, created_at)
) ENGINE=InnoDB;

-- ===================================================================
-- Verification
-- ===================================================================

SELECT 'Triggers created successfully!' AS status;

SHOW TRIGGERS WHERE `Table` = 'booking_booking';
