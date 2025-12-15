@echo off
echo ============================================
echo   STARTING LARAVEL REVERB WEBSOCKET SYSTEM
echo ============================================
echo.

cd /d "%~dp0"

echo [1/3] Starting Reverb WebSocket Server...
start "Reverb WebSocket" powershell -NoExit -Command "cd '%CD%'; php artisan reverb:start"
timeout /t 2 /nobreak >nul

echo [2/3] Starting Queue Worker...
start "Queue Worker" powershell -NoExit -Command "cd '%CD%'; php artisan queue:work --queue=default"
timeout /t 2 /nobreak >nul

echo [3/3] Starting Laravel Development Server...
start "Laravel Server" powershell -NoExit -Command "cd '%CD%'; php artisan serve"
timeout /t 2 /nobreak >nul

echo.
echo ============================================
echo   ALL SERVICES STARTED!
echo ============================================
echo.
echo  - Reverb WebSocket: localhost:8080
echo  - Laravel Server: localhost:8000
echo  - Queue Worker: Processing events
echo.
echo  Open: http://localhost:8000
echo.
echo  Press any key to exit this window...
echo  (Services will keep running in other windows)
echo ============================================
pause >nul
