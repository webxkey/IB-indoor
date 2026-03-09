#!/bin/bash

# 🎯 LIVE BOOKING VERIFICATION SCRIPT
# Run this to check if everything is working correctly

clear

echo "╔════════════════════════════════════════════════════════════════════════════════╗"
echo "║                                                                                ║"
echo "║         🎯 LIVE BOOKING UPDATE VERIFICATION & SETUP                           ║"
echo "║            Checking your real-time booking system                             ║"
echo "║                                                                                ║"
echo "╚════════════════════════════════════════════════════════════════════════════════╝"
echo ""

PROJECT_DIR="/var/www/ib_laravel"
cd "$PROJECT_DIR" || exit 1

# Colors
GREEN='\033[0;32m'
RED='\033[0;31m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m'

pass=0
fail=0

echo -e "${BLUE}═══════════════════════════════════════════════════════════════════════════════${NC}"
echo -e "${BLUE}1️⃣  CONFIGURATION CHECK${NC}"
echo -e "${BLUE}═══════════════════════════════════════════════════════════════════════════════${NC}"
echo ""

# Check .env
if [ -f ".env" ]; then
    echo -e "${GREEN}✓${NC} .env file exists"
    ((pass++))
    
    # Check BROADCAST_DRIVER
    if grep -q "BROADCAST_DRIVER=reverb" .env; then
        echo -e "${GREEN}✓${NC} BROADCAST_DRIVER=reverb (CORRECT)"
        ((pass++))
    else
        echo -e "${RED}✗${NC} BROADCAST_DRIVER is not set to 'reverb'"
        ((fail++))
    fi
    
    # Check REVERB_HOST
    reverb_host=$(grep "REVERB_HOST=" .env | cut -d '=' -f 2)
    echo "  REVERB_HOST: $reverb_host"
    
    # Check REVERB_PORT
    reverb_port=$(grep "REVERB_PORT=" .env | cut -d '=' -f 2)
    echo "  REVERB_PORT: $reverb_port"
else
    echo -e "${RED}✗${NC} .env file NOT FOUND"
    ((fail++))
fi

echo ""
echo -e "${BLUE}═══════════════════════════════════════════════════════════════════════════════${NC}"
echo -e "${BLUE}2️⃣  REQUIRED FILES CHECK${NC}"
echo -e "${BLUE}═══════════════════════════════════════════════════════════════════════════════${NC}"
echo ""

files=(
    "app/Events/BookingCreated.php"
    "app/Events/BookingUpdated.php"
    "app/Events/BookingDeleted.php"
    "app/Observers/BookingBookingObserver.php"
    "config/broadcasting.php"
    "routes/channels.php"
    "resources/js/bootstrap.js"
)

for file in "${files[@]}"; do
    if [ -f "$file" ]; then
        echo -e "${GREEN}✓${NC} $file"
        ((pass++))
    else
        echo -e "${RED}✗${NC} $file - MISSING"
        ((fail++))
    fi
done

echo ""
echo -e "${BLUE}═══════════════════════════════════════════════════════════════════════════════${NC}"
echo -e "${BLUE}3️⃣  OBSERVER REGISTRATION CHECK${NC}"
echo -e "${BLUE}═══════════════════════════════════════════════════════════════════════════════${NC}"
echo ""

if grep -q "BookingBooking::observe(BookingBookingObserver::class)" app/Providers/AppServiceProvider.php; then
    echo -e "${GREEN}✓${NC} Observer registered in AppServiceProvider"
    ((pass++))
else
    echo -e "${RED}✗${NC} Observer NOT registered in AppServiceProvider"
    ((fail++))
fi

echo ""
echo -e "${BLUE}═══════════════════════════════════════════════════════════════════════════════${NC}"
echo -e "${BLUE}4️⃣  ECHO LISTENER SETUP CHECK${NC}"
echo -e "${BLUE}═══════════════════════════════════════════════════════════════════════════════${NC}"
echo ""

if grep -q "window.Echo.channel" resources/views/livewire/staff/bookings-management.blade.php; then
    echo -e "${GREEN}✓${NC} Echo channel listener setup in bookings view"
    ((pass++))
else
    echo -e "${RED}✗${NC} Echo channel listener NOT setup"
    ((fail++))
fi

if grep -q "channel.listen" resources/views/livewire/staff/bookings-management.blade.php; then
    echo -e "${GREEN}✓${NC} Event listeners configured (booking.created, etc.)"
    ((pass++))
else
    echo -e "${RED}✗${NC} Event listeners NOT configured"
    ((fail++))
fi

echo ""
echo -e "${BLUE}═══════════════════════════════════════════════════════════════════════════════${NC}"
echo -e "${BLUE}5️⃣  SERVICES STATUS${NC}"
echo -e "${BLUE}═══════════════════════════════════════════════════════════════════════════════${NC}"
echo ""

# Check Laravel (port 8000)
if netstat -tulpn 2>/dev/null | grep -q ":8000 "; then
    echo -e "${GREEN}✓${NC} Laravel Server running (port 8000)"
    ((pass++))
else
    echo -e "${YELLOW}⚠${NC}  Laravel Server NOT running (port 8000)"
    echo "   To start: php artisan serve --host=0.0.0.0 --port=8000"
fi

# Check Reverb (port 8080)
if netstat -tulpn 2>/dev/null | grep -q ":8080 "; then
    echo -e "${GREEN}✓${NC} Reverb WebSocket Server running (port 8080)"
    ((pass++))
else
    echo -e "${YELLOW}⚠${NC}  Reverb WebSocket Server NOT running (port 8080)"
    echo "   To start: php artisan reverb:start --host=0.0.0.0 --port=8080"
fi

# Check PostgreSQL (port 5432)
if netstat -tulpn 2>/dev/null | grep -q ":5432 "; then
    echo -e "${GREEN}✓${NC} PostgreSQL Database running (port 5432)"
    ((pass++))
else
    echo -e "${YELLOW}⚠${NC}  PostgreSQL Database NOT running (port 5432)"
fi

echo ""
echo -e "${BLUE}═══════════════════════════════════════════════════════════════════════════════${NC}"
echo -e "${BLUE}6️⃣  DATABASE CONNECTIVITY CHECK${NC}"
echo -e "${BLUE}═══════════════════════════════════════════════════════════════════════════════${NC}"
echo ""

if psql -h 127.0.0.1 -U webxkey -d indoor_booking -c "SELECT 1" &>/dev/null; then
    echo -e "${GREEN}✓${NC} PostgreSQL database connection successful"
    ((pass++))
    
    # Count bookings
    booking_count=$(psql -h 127.0.0.1 -U webxkey -d indoor_booking -t -c "SELECT COUNT(*) FROM booking_booking" 2>/dev/null)
    echo "  Total bookings in database: $booking_count"
else
    echo -e "${RED}✗${NC} PostgreSQL database connection FAILED"
    ((fail++))
fi

echo ""
echo -e "${BLUE}═══════════════════════════════════════════════════════════════════════════════${NC}"
echo -e "${BLUE}7️⃣  EVENT BROADCASTING SIMULATION${NC}"
echo -e "${BLUE}═══════════════════════════════════════════════════════════════════════════════${NC}"
echo ""

echo "Creating test booking for Complex ID 5..."
test_output=$(php create-live-test-booking.php 2>&1)

if echo "$test_output" | grep -q "✅ SUCCESS"; then
    echo -e "${GREEN}✓${NC} Test booking created successfully"
    echo "   Event should have been broadcast to bookings.complex.5 channel"
    ((pass++))
    
    # Extract booking ID
    booking_id=$(echo "$test_output" | grep "Booking ID:" | awk '{print $3}')
    echo "   Created Booking ID: $booking_id"
else
    echo -e "${RED}✗${NC} Test booking creation failed"
    echo "$test_output"
    ((fail++))
fi

echo ""
echo -e "${BLUE}═══════════════════════════════════════════════════════════════════════════════${NC}"
echo -e "${BLUE}RESULTS SUMMARY${NC}"
echo -e "${BLUE}═══════════════════════════════════════════════════════════════════════════════${NC}"
echo ""

total=$((pass + fail))
echo "✅ Passed: $pass"
echo "❌ Failed: $fail"
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"
echo "Total: $total checks"

echo ""
if [ $fail -eq 0 ]; then
    echo -e "${GREEN}╔════════════════════════════════════════════════════════════════════╗${NC}"
    echo -e "${GREEN}║  ✅ ALL SYSTEMS OPERATIONAL!                                       ║${NC}"
    echo -e "${GREEN}╚════════════════════════════════════════════════════════════════════╝${NC}"
    echo ""
    echo "Your live booking system is ready!"
    echo ""
    echo "📊 WHAT TO DO NOW:"
    echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"
    echo ""
    echo "1️⃣  Open your dashboard in a browser:"
    echo "    http://your-server-ip:8000/admin/bookings"
    echo ""
    echo "2️⃣  Filter or navigate to Complex ID 5"
    echo ""
    echo "3️⃣  Look for the test booking: 'Live Test User - XX:XX:XX'"
    echo ""
    echo "4️⃣  If you see it appear WITHOUT refreshing the page = ✅ LIVE UPDATES WORKING!"
    echo ""
    echo "5️⃣  Create more bookings and watch them appear in real-time:"
    echo "    php create-live-test-booking.php"
    echo ""
    echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"
else
    echo -e "${RED}╔════════════════════════════════════════════════════════════════════╗${NC}"
    echo -e "${RED}║  ⚠️  SOME ISSUES DETECTED                                           ║${NC}"
    echo -e "${RED}╚════════════════════════════════════════════════════════════════════╝${NC}"
    echo ""
    echo "Issues found: $fail"
    echo ""
fi

echo ""
echo -e "${BLUE}📋 QUICK COMMANDS:${NC}"
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"
echo ""
echo "Start all services:"
echo "  npm run dev &"
echo "  php artisan reverb:start --host=0.0.0.0 --port=8080 &"
echo "  php artisan serve --host=0.0.0.0 --port=8000"
echo ""
echo "Create test booking:"
echo "  php create-live-test-booking.php"
echo ""
echo "View logs (real-time):"
echo "  tail -f storage/logs/laravel.log"
echo ""
echo "Check which services are running:"
echo "  netstat -tulpn | grep -E '8000|8080|5432'"
echo ""
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"
echo ""
