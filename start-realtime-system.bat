@echo off
REM Real-Time Booking System - Quick Start Script
REM This script starts all required services for real-time booking updates

echo.
echo ============================================
echo Real-Time Booking System - Quick Start
echo ============================================
echo.

REM Check if node_modules exists
if not exist "node_modules\" (
    echo Installing npm dependencies...
    call npm install
    echo.
)

REM Build assets
echo Building frontend assets...
call npm run dev

echo.
echo ============================================
echo Services to start (Run each in a new terminal):
echo ============================================
echo.
echo 1. START REVERB SERVER (Terminal 1):
echo    php artisan reverb:start
echo.
echo 2. START LARAVEL SERVER (Terminal 2):
echo    php artisan serve
echo.
echo ============================================
echo.
echo Once both are running, open:
echo http://127.0.0.1:8000
echo.
echo Watch admin dashboard receive real-time booking updates!
echo.
