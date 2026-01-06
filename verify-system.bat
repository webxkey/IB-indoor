@echo off
REM Real-Time Booking System - Quick Verification Script
REM Run this after starting the servers to verify everything is working

echo.
echo ============================================================
echo     REAL-TIME BOOKING SYSTEM - QUICK VERIFICATION
echo ============================================================
echo.

REM Check if servers are running
echo [1/4] Checking if Reverb WebSocket server is running on port 8080...
netstat -ano | findstr :8080 | findstr LISTENING >nul
if %errorlevel% equ 0 (
    echo     ✅ Reverb is RUNNING (WebSocket port 8080)
) else (
    echo     ❌ Reverb is NOT RUNNING - Start with: php artisan reverb:start
    exit /b 1
)

echo.
echo [2/4] Checking if Laravel server is running on port 8000...
netstat -ano | findstr :8000 | findstr LISTENING >nul
if %errorlevel% equ 0 (
    echo     ✅ Laravel is RUNNING (HTTP port 8000)
) else (
    echo     ❌ Laravel is NOT RUNNING - Start with: php artisan serve
    exit /b 1
)

echo.
echo [3/4] Checking .env configuration...
findstr "BROADCAST_DRIVER=reverb" .env >nul
if %errorlevel% equ 0 (
    echo     ✅ BROADCAST_DRIVER is set to reverb
) else (
    echo     ⚠️  WARNING: BROADCAST_DRIVER might not be set to reverb
)

echo.
echo [4/4] Creating test booking...
php test-realtime.php

echo.
echo ============================================================
echo     VERIFICATION COMPLETE!
echo ============================================================
echo.
echo Next steps:
echo   1. Open admin dashboard: http://127.0.0.1:8000
echo   2. Test WebSocket: http://127.0.0.1:8000/websocket-test.html
echo   3. Create a booking from mobile app or admin panel
echo   4. Watch for real-time updates!
echo.
echo Need help? Check TEST_RESULTS.md for detailed instructions.
echo.
