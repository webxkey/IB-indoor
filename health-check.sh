#!/bin/bash

# 🏥 INDOOR BOOKING SYSTEM - HEALTH CHECK
# Run this to verify everything is working correctly

echo "╔════════════════════════════════════════════════════════════════╗"
echo "║     🏥 BOOKING SYSTEM HEALTH CHECK                             ║"
echo "║     Check if all services are running correctly               ║"
echo "╚════════════════════════════════════════════════════════════════╝"
echo ""

# Color codes
GREEN='\033[0;32m'
RED='\033[0;31m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m'

pass=0
fail=0

# Helper functions
check_service() {
    local name=$1
    local port=$2
    
    if nc -zv 127.0.0.1 $port &> /dev/null; then
        echo -e "${GREEN}✓${NC} $name is running (port $port)"
        ((pass++))
    else
        echo -e "${RED}✗${NC} $name is NOT running (port $port)"
        ((fail++))
    fi
}

check_command() {
    local name=$1
    local cmd=$2
    
    if command -v $cmd &> /dev/null; then
        version=$($cmd --version 2>&1 | head -n 1)
        echo -e "${GREEN}✓${NC} $name: $version"
        ((pass++))
    else
        echo -e "${RED}✗${NC} $name is NOT installed"
        ((fail++))
    fi
}

check_file() {
    local name=$1
    local file=$2
    
    if [ -f "$file" ]; then
        echo -e "${GREEN}✓${NC} $name exists"
        ((pass++))
    else
        echo -e "${RED}✗${NC} $name is MISSING"
        ((fail++))
    fi
}

# ================================================================
# 1. REQUIRED SOFTWARE
# ================================================================
echo -e "${BLUE}1. Required Software${NC}"
echo "──────────────────────────────────────────────────────────────────"
check_command "PHP" "php"
check_command "Node.js" "node"
check_command "npm" "npm"
check_command "Composer" "composer"
check_command "PostgreSQL" "psql"
echo ""

# ================================================================
# 2. RUNNING SERVICES
# ================================================================
echo -e "${BLUE}2. Running Services${NC}"
echo "──────────────────────────────────────────────────────────────────"
check_service "Laravel Server" "8000"
check_service "Reverb WebSocket" "8080"
echo ""

# Check PostgreSQL
echo -n "PostgreSQL Service: "
if systemctl is-active --quiet postgresql 2>/dev/null || systemctl is-active --quiet postgres 2>/dev/null; then
    echo -e "${GREEN}✓${NC} Running"
    ((pass++))
else
    echo -e "${RED}✗${NC} NOT running"
    ((fail++))
fi
echo ""

# ================================================================
# 3. CONFIGURATION FILES
# ================================================================
echo -e "${BLUE}3. Configuration Files${NC}"
echo "──────────────────────────────────────────────────────────────────"
check_file ".env file" ".env"
check_file "Laravel config" "config/app.php"
check_file "Reverb config" "config/reverb.php"
check_file "Broadcasting config" "config/broadcasting.php"
echo ""

# ================================================================
# 4. DATABASE
# ================================================================
echo -e "${BLUE}4. Database Status${NC}"
echo "──────────────────────────────────────────────────────────────────"

# Get DB credentials from .env
if [ -f ".env" ]; then
    DB_HOST=$(grep "DB_HOST=" .env | cut -d '=' -f 2)
    DB_USER=$(grep "DB_USERNAME=" .env | cut -d '=' -f 2)
    DB_NAME=$(grep "DB_DATABASE=" .env | cut -d '=' -f 2)
    DB_PASS=$(grep "DB_PASSWORD=" .env | cut -d '=' -f 2)
    
    # Try to connect to database
    if psql -h "$DB_HOST" -U "$DB_USER" -d "$DB_NAME" -c "SELECT 1" &> /dev/null; then
        echo -e "${GREEN}✓${NC} Database connection: OK"
        ((pass++))
        
        # Check tables
        tables=$(psql -h "$DB_HOST" -U "$DB_USER" -d "$DB_NAME" -c "\dt" 2>/dev/null | grep -c "public")
        if [ $tables -gt 0 ]; then
            echo -e "${GREEN}✓${NC} Database tables: $tables found"
            ((pass++))
        fi
        
        # Check bookings
        bookings=$(psql -h "$DB_HOST" -U "$DB_USER" -d "$DB_NAME" -c "SELECT COUNT(*) FROM booking_booking" 2>/dev/null | grep -v "count" | head -n 1 | xargs)
        if [ ! -z "$bookings" ]; then
            echo -e "${GREEN}✓${NC} Bookings in database: $bookings"
            ((pass++))
        fi
    else
        echo -e "${RED}✗${NC} Database connection: FAILED"
        ((fail++))
    fi
else
    echo -e "${RED}✗${NC} .env file not found"
    ((fail++))
fi
echo ""

# ================================================================
# 5. KEY FILES
# ================================================================
echo -e "${BLUE}5. Application Files${NC}"
echo "──────────────────────────────────────────────────────────────────"
check_file "Bootstrap file" "bootstrap/app.php"
check_file "Main routes" "routes/web.php"
check_file "API routes" "routes/api.php"
check_file "Broadcasting channels" "routes/channels.php"
check_file "BookingBooking model" "app/Models/BookingBooking.php"
check_file "BookingBooking observer" "app/Observers/BookingBookingObserver.php"
check_file "BookingCreated event" "app/Events/BookingCreated.php"
echo ""

# ================================================================
# 6. WEB CONNECTIVITY
# ================================================================
echo -e "${BLUE}6. Web Connectivity${NC}"
echo "──────────────────────────────────────────────────────────────────"

echo -n "Laravel Server (8000): "
if curl -s http://127.0.0.1:8000 &> /dev/null; then
    echo -e "${GREEN}✓${NC} Responding"
    ((pass++))
else
    echo -e "${RED}✗${NC} Not responding (should be running)"
    ((fail++))
fi

echo -n "Reverb WebSocket (8080): "
if curl -s http://127.0.0.1:8080 &> /dev/null; then
    echo -e "${GREEN}✓${NC} Responding"
    ((pass++))
else
    echo -e "${RED}✗${NC} Not responding (should be running)"
    ((fail++))
fi
echo ""

# ================================================================
# 7. LOG FILES
# ================================================================
echo -e "${BLUE}7. Log Files${NC}"
echo "──────────────────────────────────────────────────────────────────"

if [ -f "storage/logs/laravel.log" ]; then
    size=$(du -h "storage/logs/laravel.log" | cut -f 1)
    echo -e "${GREEN}✓${NC} Laravel log: $size"
    
    # Check for recent errors
    errors=$(grep -c "ERROR\|Exception" "storage/logs/laravel.log" 2>/dev/null || echo "0")
    if [ $errors -gt 0 ]; then
        echo -e "${YELLOW}⚠${NC}  Recent errors found: $errors"
    else
        echo -e "${GREEN}✓${NC} No errors in recent logs"
        ((pass++))
    fi
else
    echo -e "${RED}✗${NC} Laravel log file not found"
    ((fail++))
fi
echo ""

# ================================================================
# 8. ENVIRONMENT SETUP
# ================================================================
echo -e "${BLUE}8. Environment Configuration${NC}"
echo "──────────────────────────────────────────────────────────────────"

if [ -f ".env" ]; then
    broadcast=$(grep "BROADCAST_DRIVER=" .env | cut -d '=' -f 2)
    debug=$(grep "APP_DEBUG=" .env | cut -d '=' -f 2)
    env_app=$(grep "APP_ENV=" .env | cut -d '=' -f 2)
    
    echo "Broadcast Driver: $broadcast"
    if [ "$broadcast" = "reverb" ]; then
        echo -e "${GREEN}✓${NC} Broadcasting configured correctly"
        ((pass++))
    else
        echo -e "${RED}✗${NC} Broadcasting not set to 'reverb'"
        ((fail++))
    fi
    
    echo "App Environment: $env_app"
    echo "Debug Mode: $debug"
    
    # Check Reverb config
    reverb_host=$(grep "REVERB_HOST=" .env | cut -d '=' -f 2)
    reverb_port=$(grep "REVERB_PORT=" .env | cut -d '=' -f 2)
    echo "Reverb Host: $reverb_host"
    echo "Reverb Port: $reverb_port"
fi
echo ""

# ================================================================
# SUMMARY
# ================================================================
echo "╔════════════════════════════════════════════════════════════════╗"
total=$((pass + fail))
echo "║  Results: $pass/$total checks passed"
echo "╚════════════════════════════════════════════════════════════════╝"
echo ""

if [ $fail -eq 0 ]; then
    echo -e "${GREEN}✓ ALL SYSTEMS OPERATIONAL!${NC}"
    echo ""
    echo "Your booking system is ready to go live! 🚀"
    echo ""
    echo "Access your system at: http://your-server-ip:8000"
    exit 0
else
    echo -e "${RED}✗ SOME ISSUES DETECTED${NC}"
    echo ""
    echo "Issues to fix:"
    echo "1. Check if Laravel server is running (port 8000)"
    echo "2. Check if Reverb server is running (port 8080)"
    echo "3. Check if PostgreSQL is running"
    echo "4. Review .env configuration"
    echo ""
    echo "For detailed help, see: LIVE_DEPLOYMENT_GUIDE.md"
    exit 1
fi
