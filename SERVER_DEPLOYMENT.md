# 🚀 INDOOR BOOKING SYSTEM - SERVER DEPLOYMENT GUIDE

## Prerequisites
- PHP 8.2+ (with OpenSSL, PDO, PostgreSQL extensions)
- PostgreSQL 12+
- Node.js 18+
- Composer
- Git

---

## Step-by-Step Deployment

### 1️⃣ Clone/Pull Code from Git
```bash
git clone <your-repo-url> indoor-booking
cd indoor-booking
```

### 2️⃣ Install PHP Dependencies
```bash
composer install --no-dev --optimize-autoloader
```
**What this does:** Installs Laravel framework, Laravel Reverb, Echo, and all backend packages.

---

### 3️⃣ Install Node Dependencies
```bash
npm install
```
**What this does:** Installs Vite, Tailwind CSS, and frontend build tools.

---

### 4️⃣ Configure Environment (.env)

Copy example file:
```bash
cp .env.example .env
```

Edit `.env` and set:

```bash
# Application
APP_NAME="Indoor Booking"
APP_ENV=production
APP_DEBUG=false
APP_KEY=           # Will be generated in next step
APP_URL=http://your-server-ip:8000

# Database
DB_CONNECTION=pgsql
DB_HOST=localhost
DB_PORT=5432
DB_DATABASE=indoor_booking
DB_USERNAME=postgres
DB_PASSWORD=your_password

# Reverb WebSocket
REVERB_HOST=your-server-ip        # IP or domain
REVERB_PORT=8080                  # WebSocket port
REVERB_SCHEME=http                # http or https
VITE_REVERB_HOST=your-server-ip
VITE_REVERB_PORT=8080
VITE_REVERB_SCHEME=http

# Broadcasting
BROADCAST_DRIVER=reverb
BROADCAST_CONNECTION=reverb
```

---

### 5️⃣ Generate Application Key
```bash
php artisan key:generate
```
**What this does:** Encrypts sensitive application data.

---

### 6️⃣ Create Database
```bash
# Create PostgreSQL database
createdb indoor_booking

# Or via psql
psql -U postgres
CREATE DATABASE indoor_booking;
```

---

### 7️⃣ Run Migrations
```bash
php artisan migrate --force
```
**What this does:** Creates all database tables.

---

### 8️⃣ Build Frontend Assets
```bash
npm run build
```
**What this does:** Compiles Vite + Tailwind CSS for production.

For development:
```bash
npm run dev
```

---

### 9️⃣ Clear Cache
```bash
php artisan config:clear
php artisan cache:clear
php artisan view:clear
```

---

## ⚡ Running the Application

### Terminal 1: Start Laravel Server
```bash
php artisan serve --host=0.0.0.0 --port=8000
```
- Access at: `http://your-server-ip:8000`

### Terminal 2: Start Reverb WebSocket Server
```bash
php artisan reverb:start --host=0.0.0.0 --port=8080
```
- WebSocket at: `ws://your-server-ip:8080`

---

## 📊 Verify Setup

### Check Laravel Health
```bash
php artisan health
```

### Check Database Connection
```bash
php artisan tinker
> DB::connection()->getPdo()
```

### Check Reverb Status
Terminal should show:
```
⏱  Server running on 0.0.0.0:8080
🔌 Application server started
```

---

## 🔍 Test Live Bookings

### 1. Create Test Booking
```bash
php artisan tinker
> php complete-live-test.php
```

### 2. Watch Admin Dashboard
- Go to: `http://your-server-ip:8000`
- Login with your credentials
- New bookings appear **LIVE without refresh** ✨

### 3. Create 7PM-9PM Test Booking
```bash
php test-7pm-booking.php
```

---

## 🛡️ Production Setup (Optional)

### Use Nginx (instead of `php artisan serve`)

**nginx.conf:**
```nginx
server {
    listen 8000;
    server_name your-server-ip;
    root /path/to/indoor/public;
    index index.php;

    location ~ \.php$ {
        fastcgi_pass 127.0.0.1:9000;
        fastcgi_index index.php;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }
}
```

### Use Supervisor for Services

**Create `/etc/supervisor/conf.d/reverb.conf`:**
```ini
[program:reverb]
process_name=%(program_name)s
command=php /path/to/indoor/artisan reverb:start --host=0.0.0.0 --port=8080
autostart=true
autorestart=true
user=www-data
```

Start:
```bash
sudo supervisorctl reread
sudo supervisorctl update
sudo supervisorctl start reverb
```

---

## 🐛 Troubleshooting

### WebSocket Not Connecting
```bash
# Check Reverb is running
netstat -tulpn | grep 8080

# Check firewall allows port 8080
sudo ufw allow 8080/tcp
```

### Database Migration Fails
```bash
# Reset migrations (CAUTION: Deletes data)
php artisan migrate:refresh --force

# Or rollback one
php artisan migrate:rollback
```

### Bookings Not Broadcasting
```bash
# Check queue is running (if using queue driver)
php artisan queue:work

# Check logs
tail -f storage/logs/laravel.log
```

### Echo Not Initialized
- Check browser console for errors (F12)
- Verify `REVERB_HOST` and `REVERB_PORT` in `.env`
- Clear browser cache: `Ctrl+Shift+Delete`

---

## 📱 Access Points

| Service | URL | Purpose |
|---------|-----|---------|
| Admin Dashboard | `http://server-ip:8000` | Booking management, real-time updates |
| API | `http://server-ip:8000/api` | Mobile app bookings |
| WebSocket | `ws://server-ip:8080` | Real-time notifications |

---

## ✅ Deployment Checklist

- [ ] PHP 8.2+ installed
- [ ] PostgreSQL 12+ running
- [ ] Node.js 18+ installed
- [ ] Code cloned/pulled
- [ ] `composer install` completed
- [ ] `npm install` completed
- [ ] `.env` configured
- [ ] `APP_KEY` generated
- [ ] Database created
- [ ] Migrations run
- [ ] Assets built: `npm run build`
- [ ] Laravel server running on port 8000
- [ ] Reverb WebSocket running on port 8080
- [ ] Admin dashboard accessible
- [ ] Test booking created and appears live

---

## 🚀 You're Live!

Your booking system is now running with:
- ✅ Real-time booking updates via WebSocket
- ✅ Multi-admin support without polling
- ✅ Automatic calendar refresh
- ✅ Live notifications
- ✅ Zero page refresh needed

**Start receiving bookings!** 📊✨
