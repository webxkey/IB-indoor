@echo off
REM Real-Time Booking System - Complete Setup Verification
REM Run this to verify everything is working

echo.
echo ============================================================
echo     REAL-TIME BOOKING SYSTEM - COMPLETE TEST
echo ============================================================
echo.

echo [1/5] Checking Reverb Server...
netstat -ano | findstr :8080 | findstr LISTENING >nul
if %errorlevel% equ 0 (
    echo     ✅ Reverb is RUNNING on port 8080
) else (
    echo     ❌ Reverb is NOT running
    echo     Start with: php artisan reverb:start
    exit /b 1
)

echo.
echo [2/5] Checking Laravel Server...
netstat -ano | findstr :8000 | findstr LISTENING >nul
if %errorlevel% equ 0 (
    echo     ✅ Laravel is RUNNING on port 8000
) else (
    echo     ❌ Laravel is NOT running
    echo     Start with: php artisan serve
    exit /b 1
)

echo.
echo [3/5] Rebuilding Frontend Assets...
call npm run build >nul 2>&1
if %errorlevel% equ 0 (
    echo     ✅ Assets rebuilt successfully
) else (
    echo     ❌ Asset build failed
    exit /b 1
)

echo.
echo [4/5] Clearing Cache...
php artisan cache:clear >nul 2>&1
php artisan config:clear >nul 2>&1
echo     ✅ Cache and config cleared

echo.
echo [5/5] Creating Test Booking...
php test-complex-2-available-time.php

echo.
echo ============================================================
echo     ✅ SYSTEM READY FOR TESTING!
echo ============================================================
echo.
echo Next steps:
echo   1. Open: http://127.0.0.1:8000
echo   2. Press F12 to open DevTools → Console
echo   3. Look for: "✅ Laravel Echo initialized..."
echo   4. Create another booking from mobile app
echo   5. You should see: "📱 New Booking Created!" in console
echo.
echo If live updates aren't showing:
echo   1. Full refresh: Ctrl+Shift+Delete then F5
echo   2. Check Network → WS shows WebSocket connection
echo   3. See LIVE_UPDATES_FIXED.md for troubleshooting
echo.
