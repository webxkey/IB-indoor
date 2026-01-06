import "bootstrap";

/**
 * We'll load the axios HTTP library which allows us to easily issue requests
 * to our Laravel back-end. This library automatically handles sending the
 * CSRF token as a header based on the value of the "XSRF" token cookie.
 */

import axios from "axios";
window.axios = axios;

window.axios.defaults.headers.common["X-Requested-With"] = "XMLHttpRequest";

/**
 * Laravel Echo - Real-time WebSocket Broadcasting
 * Provides efficient, real-time communication for booking updates
 */
import Echo from 'laravel-echo';
import Pusher from 'pusher-js';

window.Pusher = Pusher;

window.Echo = new Echo({
    broadcaster: 'reverb',
    key: import.meta.env.VITE_REVERB_APP_KEY,
    wsHost: import.meta.env.VITE_REVERB_HOST,
    wsPort: import.meta.env.VITE_REVERB_PORT,
    wssPort: import.meta.env.VITE_REVERB_PORT,
    forceTLS: (import.meta.env.VITE_REVERB_SCHEME || 'https') === 'https',
    enabledTransports: ['ws', 'wss'],
});

console.log('✅ Laravel Echo initialized for real-time updates');

/**
 * Real-time booking updates using Server-Sent Events (SSE)
 * This provides efficient one-way server-to-client communication
 * without WebSockets or polling overhead.
 */
window.BookingSSE = {
    eventSource: null,
    reconnectAttempts: 0,
    maxReconnectAttempts: 5,
    reconnectDelay: 3000,
    listeners: new Map(),

    /**
     * Connect to SSE endpoint for real-time booking updates
     */
    connect(complexId) {
        if (this.eventSource) {
            this.disconnect();
        }

        if (!complexId) {
            console.warn('BookingSSE: No complex ID provided');
            return;
        }

        try {
            const url = `/api/bookings/stream/${complexId}`;
            this.eventSource = new EventSource(url);

            this.eventSource.onopen = () => {
                console.log('✅ SSE connected for real-time booking updates');
                this.reconnectAttempts = 0;
            };

            this.eventSource.addEventListener('booking.created', (event) => {
                const data = JSON.parse(event.data);
                console.log('🔔 New booking received:', data);
                this.notifyListeners('booking.created', data);
            });

            this.eventSource.addEventListener('booking.updated', (event) => {
                const data = JSON.parse(event.data);
                console.log('🔄 Booking updated:', data);
                this.notifyListeners('booking.updated', data);
            });

            this.eventSource.addEventListener('booking.cancelled', (event) => {
                const data = JSON.parse(event.data);
                console.log('❌ Booking cancelled:', data);
                this.notifyListeners('booking.cancelled', data);
            });

            this.eventSource.addEventListener('heartbeat', () => {
                // Keep-alive signal, no action needed
            });

            this.eventSource.onerror = (error) => {
                console.warn('SSE connection error:', error);
                this.handleReconnect(complexId);
            };

        } catch (error) {
            console.error('Failed to establish SSE connection:', error);
        }
    },

    /**
     * Handle reconnection with exponential backoff
     */
    handleReconnect(complexId) {
        if (this.eventSource) {
            this.eventSource.close();
            this.eventSource = null;
        }

        if (this.reconnectAttempts < this.maxReconnectAttempts) {
            this.reconnectAttempts++;
            const delay = this.reconnectDelay * this.reconnectAttempts;
            console.log(`Reconnecting SSE in ${delay}ms (attempt ${this.reconnectAttempts})`);
            setTimeout(() => this.connect(complexId), delay);
        } else {
            console.error('SSE: Max reconnection attempts reached');
        }
    },

    /**
     * Disconnect from SSE
     */
    disconnect() {
        if (this.eventSource) {
            this.eventSource.close();
            this.eventSource = null;
        }
        this.reconnectAttempts = 0;
    },

    /**
     * Add event listener
     */
    on(event, callback) {
        if (!this.listeners.has(event)) {
            this.listeners.set(event, []);
        }
        this.listeners.get(event).push(callback);
    },

    /**
     * Remove event listener
     */
    off(event, callback) {
        if (this.listeners.has(event)) {
            const callbacks = this.listeners.get(event);
            const index = callbacks.indexOf(callback);
            if (index > -1) {
                callbacks.splice(index, 1);
            }
        }
    },

    /**
     * Notify all listeners for an event
     */
    notifyListeners(event, data) {
        if (this.listeners.has(event)) {
            this.listeners.get(event).forEach(callback => callback(data));
        }
        // Also dispatch browser event for Livewire integration
        window.dispatchEvent(new CustomEvent(`sse:${event}`, { detail: data }));
    }
};
