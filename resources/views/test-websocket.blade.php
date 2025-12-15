<!DOCTYPE html>
<html>
<head>
    <title>WebSocket Test</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body style="font-family: Arial; padding: 20px;">
    <h1>🔌 WebSocket Connection Test</h1>
    
    <div id="status" style="padding: 15px; border-radius: 5px; margin: 20px 0;">
        <strong>Status:</strong> <span id="statusText">Checking...</span>
    </div>

    <div id="echo-status" style="padding: 15px; border-radius: 5px; margin: 20px 0; background: #f0f0f0;">
        <strong>Echo Status:</strong> <span id="echoStatusText">Checking...</span>
    </div>

    <div id="connection-status" style="padding: 15px; border-radius: 5px; margin: 20px 0; background: #f0f0f0;">
        <strong>Connection:</strong> <span id="connectionText">-</span>
    </div>

    <div id="events" style="padding: 15px; border: 1px solid #ccc; margin: 20px 0; max-height: 300px; overflow-y: auto;">
        <strong>Events Log:</strong>
        <div id="eventList"></div>
    </div>

    <button onclick="testBooking()" style="padding: 10px 20px; font-size: 16px; cursor: pointer;">
        Test Create Booking
    </button>

    <script>
        const statusEl = document.getElementById('statusText');
        const echoStatusEl = document.getElementById('echoStatusText');
        const connectionEl = document.getElementById('connectionText');
        const eventListEl = document.getElementById('eventList');

        function logEvent(message, type = 'info') {
            const time = new Date().toLocaleTimeString();
            const color = type === 'success' ? 'green' : type === 'error' ? 'red' : 'blue';
            eventListEl.innerHTML = `<div style="color: ${color};">[${time}] ${message}</div>` + eventListEl.innerHTML;
            console.log(message);
        }

        // Check if Echo is available
        if (typeof window.Echo !== 'undefined') {
            statusEl.textContent = '✅ Echo loaded successfully';
            statusEl.parentElement.style.background = '#d4edda';
            echoStatusEl.textContent = '✅ Available';
            echoStatusEl.parentElement.style.background = '#d4edda';
            
            logEvent('✅ Echo is available', 'success');

            // Monitor connection
            if (window.Echo.connector && window.Echo.connector.pusher) {
                const pusher = window.Echo.connector.pusher;
                
                pusher.connection.bind('connected', () => {
                    connectionEl.textContent = '✅ Connected';
                    connectionEl.parentElement.style.background = '#d4edda';
                    logEvent('✅ WebSocket connected!', 'success');
                });

                pusher.connection.bind('disconnected', () => {
                    connectionEl.textContent = '❌ Disconnected';
                    connectionEl.parentElement.style.background = '#f8d7da';
                    logEvent('❌ WebSocket disconnected', 'error');
                });

                pusher.connection.bind('error', (err) => {
                    connectionEl.textContent = '❌ Error';
                    connectionEl.parentElement.style.background = '#f8d7da';
                    logEvent('❌ Connection error: ' + JSON.stringify(err), 'error');
                });

                // Get initial state
                const state = pusher.connection.state;
                connectionEl.textContent = `State: ${state}`;
                logEvent(`Initial connection state: ${state}`, 'info');
            }

            // Subscribe to test channel (replace with your complex_id)
            const complexId = 1; // Change this to your actual complex ID
            logEvent(`Subscribing to channel: bookings.${complexId}`, 'info');

            window.Echo.channel(`bookings.${complexId}`)
                .listen('.booking.created', (data) => {
                    logEvent('🎉 NEW BOOKING RECEIVED: ' + JSON.stringify(data), 'success');
                    alert('New booking detected! Check the events log.');
                })
                .error((error) => {
                    logEvent('Channel error: ' + JSON.stringify(error), 'error');
                });

            logEvent('✅ Subscribed to booking channel', 'success');

        } else {
            statusEl.textContent = '❌ Echo NOT loaded';
            statusEl.parentElement.style.background = '#f8d7da';
            echoStatusEl.textContent = '❌ Not Available';
            echoStatusEl.parentElement.style.background = '#f8d7da';
            logEvent('❌ Echo is not available! Assets may not be loaded correctly.', 'error');
        }

        function testBooking() {
            logEvent('Creating test booking via API...', 'info');
            
            fetch('/api/test-booking', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                }
            })
            .then(response => response.json())
            .then(data => {
                logEvent('Test booking created: ' + JSON.stringify(data), 'success');
            })
            .catch(error => {
                logEvent('Error creating test booking: ' + error, 'error');
            });
        }
    </script>
</body>
</html>
