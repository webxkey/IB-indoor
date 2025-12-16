@echo off
echo ========================================
echo Starting Laravel Reverb WebSocket Server
echo ========================================
echo.
echo Make sure you have run: composer require laravel/reverb
echo.
cd /d "%~dp0"
php artisan reverb:start --host=0.0.0.0 --port=8080
pause
