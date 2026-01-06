# ============================================
# INDOOR BOOKING SYSTEM - SERVER DEPLOYMENT
# Windows PowerShell Script
# ============================================

Write-Host "╔════════════════════════════════════════════════════════════╗" -ForegroundColor Green
Write-Host "║   INDOOR BOOKING SYSTEM - SERVER DEPLOYMENT SETUP          ║" -ForegroundColor Green
Write-Host "╚════════════════════════════════════════════════════════════╝" -ForegroundColor Green
Write-Host ""

# Step 1: Install PHP dependencies
Write-Host "STEP 1️⃣  - Install PHP Dependencies" -ForegroundColor Yellow
Write-Host "─────────────────────────────────────────────────────────────" -ForegroundColor Gray
composer install --no-dev --optimize-autoloader
Write-Host "✅ Dependencies installed" -ForegroundColor Green
Write-Host ""

# Step 2: Install Node dependencies
Write-Host "STEP 2️⃣  - Install Node Dependencies" -ForegroundColor Yellow
Write-Host "─────────────────────────────────────────────────────────────" -ForegroundColor Gray
npm install
Write-Host "✅ Node dependencies installed" -ForegroundColor Green
Write-Host ""

# Step 3: Environment setup
Write-Host "STEP 3️⃣  - Environment Configuration" -ForegroundColor Yellow
Write-Host "─────────────────────────────────────────────────────────────" -ForegroundColor Gray
if (-not (Test-Path ".env")) {
    Copy-Item ".env.example" ".env"
    Write-Host "✅ .env file created from .env.example" -ForegroundColor Green
    Write-Host "⚠️  IMPORTANT: Edit .env and configure:" -ForegroundColor Red
    Write-Host "   • APP_KEY (run: php artisan key:generate)" -ForegroundColor Cyan
    Write-Host "   • Database credentials (DB_CONNECTION, DB_HOST, DB_DATABASE, etc)" -ForegroundColor Cyan
    Write-Host "   • REVERB_HOST=your-server-ip" -ForegroundColor Cyan
    Write-Host "   • REVERB_PORT=8080" -ForegroundColor Cyan
} else {
    Write-Host "✅ .env file already exists" -ForegroundColor Green
}
Write-Host ""

# Step 4: Generate APP_KEY
Write-Host "STEP 4️⃣  - Generate APP_KEY" -ForegroundColor Yellow
Write-Host "─────────────────────────────────────────────────────────────" -ForegroundColor Gray
php artisan key:generate
Write-Host "✅ APP_KEY generated" -ForegroundColor Green
Write-Host ""

# Step 5: Database migration
Write-Host "STEP 5️⃣  - Database Migration" -ForegroundColor Yellow
Write-Host "─────────────────────────────────────────────────────────────" -ForegroundColor Gray
php artisan migrate --force
Write-Host "✅ Database migrated" -ForegroundColor Green
Write-Host ""

# Step 6: Build assets
Write-Host "STEP 6️⃣  - Build Frontend Assets" -ForegroundColor Yellow
Write-Host "─────────────────────────────────────────────────────────────" -ForegroundColor Gray
npm run build
Write-Host "✅ Assets built" -ForegroundColor Green
Write-Host ""

# Step 7: Cache clear
Write-Host "STEP 7️⃣  - Clear Cache" -ForegroundColor Yellow
Write-Host "─────────────────────────────────────────────────────────────" -ForegroundColor Gray
php artisan config:clear
php artisan cache:clear
Write-Host "✅ Cache cleared" -ForegroundColor Green
Write-Host ""

Write-Host "╔════════════════════════════════════════════════════════════╗" -ForegroundColor Green
Write-Host "║              🎯 DEPLOYMENT COMPLETE!                       ║" -ForegroundColor Green
Write-Host "╚════════════════════════════════════════════════════════════╝" -ForegroundColor Green
Write-Host ""
Write-Host "Next steps:" -ForegroundColor Cyan
Write-Host "1. Edit .env file with your server configuration" -ForegroundColor White
Write-Host "2. Start Laravel: php artisan serve --host=0.0.0.0 --port=8000" -ForegroundColor White
Write-Host "3. Start Reverb (in new terminal): php artisan reverb:start --host=0.0.0.0 --port=8080" -ForegroundColor White
Write-Host ""
