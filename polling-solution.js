// SIMPLE POLLING SOLUTION (Alternative to Pusher)
// Add this to your bookings-management.blade.php if you don't want to use Pusher

// Setup automatic polling every 5 seconds
function setupPollingUpdates() {
    let pollInterval = 5000; // 5 seconds
    
    setInterval(() => {
        if (document.hidden) {
            // Don't poll if tab is not visible
            return;
        }
        
        // Call Livewire to check for updates
        @this.call('pollForUpdates').then(response => {
            console.log('Polled for updates');
        }).catch(error => {
            console.error('Polling error:', error);
        });
    }, pollInterval);
    
    console.log('Polling setup complete - checking every', pollInterval / 1000, 'seconds');
}

// Listen for new booking detected
window.addEventListener('newBookingDetected', function(event) {
    const booking = event.detail.booking;
    
    console.log('New booking detected:', booking);
    
    // Show notification
    showNotification('New Booking!', `${booking.user_name} booked ${booking.game_name} - Court ${booking.court_number}`);
    
    // Refresh the calendar
    refreshBookingData().then(() => {
        updateCalendar();
    });
});

// Show notification for new bookings
function showNotification(title, message) {
    const toastHtml = `
        <div class="alert alert-info alert-dismissible fade show position-fixed top-0 end-0 m-3" 
             style="z-index: 9999; min-width: 300px;" role="alert">
            <strong><i class="fas fa-calendar-check me-2"></i>${title}</strong><br>
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    `;
    
    const tempDiv = document.createElement('div');
    tempDiv.innerHTML = toastHtml;
    document.body.appendChild(tempDiv.firstElementChild);
    
    setTimeout(() => {
        const alerts = document.querySelectorAll('.alert');
        alerts.forEach(alert => {
            if (alert.textContent.includes(title)) {
                alert.remove();
            }
        });
    }, 5000);
    
    playNotificationSound();
}

function playNotificationSound() {
    try {
        const audio = new Audio('data:audio/wav;base64,UklGRnoGAABXQVZFZm10IBAAAAABAAEAQB8AAEAfAAABAAgAZGF0YQoGAACBhYqFbF1fdJivrJBhNjVgodDbq2EcBj+a2/LDciUFLIHO8tiJNwgZaLvt559NEAxQp+PwtmMcBjiR1/LMeSwFJHfH8N2QQAoUXrTp66hVFApGn+DyvmwhBSuBzvLZiDcJGWi77eeaTRAKT6fj8LRgGwc4kdfy0HotBSd4yPDekj4KE12y6OynUxINR6Hh8rsrIQUsgs/y24c5CBpruuvm');
        audio.play().catch(e => console.log('Audio play failed:', e));
    } catch (error) {
        console.log('Notification sound unavailable');
    }
}

// Add to your initializeSystem() function:
// setupPollingUpdates();

// USAGE:
// 1. Add setupPollingUpdates(); to your initializeSystem() function
// 2. This will check for new bookings every 5 seconds
// 3. When a new booking is detected, it will show a notification and refresh the calendar
