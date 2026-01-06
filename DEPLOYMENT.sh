#!/usr/bin/env bash
# ============================================
# INDOOR BOOKING SYSTEM - SERVER DEPLOYMENT
# ============================================

echo "╔════════════════════════════════════════════════════════════╗"
echo "║   INDOOR BOOKING SYSTEM - SERVER DEPLOYMENT SETUP          ║"
echo "╚════════════════════════════════════════════════════════════╝"
echo ""

# Step 1: Install PHP dependencies
echo "STEP 1️⃣  - Install PHP Dependencies"
echo "─────────────────────────────────────────────────────────────"
composer install --no-dev --optimize-autoloader
echo "✅ Dependencies installed"
echo ""

# Step 2: Install Node dependencies
echo "STEP 2️⃣  - Install Node Dependencies"
echo "─────────────────────────────────────────────────────────────"
npm install
echo "✅ Node dependencies installed"
echo ""

# Step 3: Environment setup
echo "STEP 3️⃣  - Environment Configuration"
echo "─────────────────────────────────────────────────────────────"
if [ ! -f .env ]; then
    cp .env.example .env
    echo "✅ .env file created from .env.example"
    echo "⚠️  IMPORTANT: Edit .env and configure:"
    echo "   • APP_KEY"
    echo "   • Database credentials"
    echo "   • REVERB settings"
else
    echo "✅ .env file already exists"
fi
echo ""

# Step 4: Generate APP_KEY
echo "STEP 4️⃣  - Generate APP_KEY"
echo "─────────────────────────────────────────────────────────────"
php artisan key:generate
echo "✅ APP_KEY generated"
echo ""

# Step 5: Database migration
echo "STEP 5️⃣  - Database Migration"
echo "─────────────────────────────────────────────────────────────"
php artisan migrate --force
echo "✅ Database migrated"
echo ""

# Step 6: Build assets
echo "STEP 6️⃣  - Build Frontend Assets"
echo "─────────────────────────────────────────────────────────────"
npm run build
echo "✅ Assets built"
echo ""

# Step 7: Cache clear
echo "STEP 7️⃣  - Clear Cache"
echo "─────────────────────────────────────────────────────────────"
php artisan config:clear
php artisan cache:clear
echo "✅ Cache cleared"
echo ""

echo "╔════════════════════════════════════════════════════════════╗"
echo "║              🎯 DEPLOYMENT COMPLETE!                       ║"
echo "╚════════════════════════════════════════════════════════════╝"
echo ""
echo "Next steps:"
echo "1. Edit .env file with your server configuration"
echo "2. Start Laravel: php artisan serve"
echo "3. Start Reverb: php artisan reverb:start"
echo ""
