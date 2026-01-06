╔══════════════════════════════════════════════════════════════╗
║         🛠️ SERVER DEPLOYMENT - TROUBLESHOOTING GUIDE          ║
╚══════════════════════════════════════════════════════════════╝

PROBLEM 1: "composer: command not found"
─────────────────────────────────────────
SOLUTION:
  1. Install Composer:
     $ curl -sS https://getcomposer.org/installer | php
     $ sudo mv composer.phar /usr/local/bin/composer
  2. Verify:
     $ composer --version

─────────────────────────────────────────────────────────────────

PROBLEM 2: "php: command not found"
────────────────────────────────────
SOLUTION:
  1. Install PHP:
     Ubuntu/Debian: $ sudo apt-get install php8.2-cli php8.2-fpm
     CentOS: $ sudo dnf install php82-cli
  2. Install extensions:
     $ sudo apt-get install php8.2-pdo php8.2-pgsql php8.2-openssl
  3. Verify:
     $ php -v

─────────────────────────────────────────────────────────────────

PROBLEM 3: "PostgreSQL connection refused"
──────────────────────────────────────────
SOLUTION:
  1. Check if PostgreSQL is running:
     $ sudo systemctl status postgresql
  2. Start PostgreSQL:
     $ sudo systemctl start postgresql
  3. Check credentials in .env:
     DB_HOST=localhost
     DB_PORT=5432
     DB_USERNAME=postgres
     DB_PASSWORD=correct_password
  4. Test connection:
     $ psql -U postgres -h localhost

─────────────────────────────────────────────────────────────────

PROBLEM 4: "Laravel server won't start"
───────────────────────────────────────
ERROR: "Address already in use"
SOLUTION:
  1. Kill process on port 8000:
     $ lsof -i :8000
     $ kill -9 <PID>
  2. Or use different port:
     $ php artisan serve --port=8001

─────────────────────────────────────────────────────────────────

PROBLEM 5: "Reverb WebSocket won't start"
──────────────────────────────────────────
SOLUTION:
  1. Check if port 8080 is available:
     $ netstat -tulpn | grep 8080
  2. Kill any process on port 8080:
     $ lsof -i :8080
     $ kill -9 <PID>
  3. Try starting Reverb:
     $ php artisan reverb:start --host=0.0.0.0 --port=8080
  4. Check firewall:
     $ sudo ufw allow 8080/tcp

─────────────────────────────────────────────────────────────────

PROBLEM 6: "WebSocket connection fails in browser"
──────────────────────────────────────────────────
ERROR: "Failed to connect to WebSocket"
SOLUTION:
  1. Check .env has correct IP:
     REVERB_HOST=your-actual-server-ip  (NOT localhost!)
     VITE_REVERB_HOST=your-actual-server-ip
  2. Verify Reverb is running:
     $ php artisan reverb:start --host=0.0.0.0 --port=8080
  3. Check browser console (F12):
     • Clear cache: Ctrl+Shift+Delete
     • Refresh: F5
     • Check for errors in Console tab
  4. Test connection:
     $ curl http://your-server-ip:8080

─────────────────────────────────────────────────────────────────

PROBLEM 7: "Bookings not appearing live"
────────────────────────────────────────
SOLUTION:
  1. Check WebSocket listener:
     Browser F12 → Console → Look for:
     "✅ Real-time WebSocket listener connected"
  2. Create test booking:
     $ php complete-live-test.php
  3. Check if notification appears
  4. Check browser console for errors
  5. If still not working:
     a. Hard refresh: Ctrl+Shift+Delete + F5
     b. Check Laravel logs: tail -f storage/logs/laravel.log
     c. Check network tab in browser (F12)

─────────────────────────────────────────────────────────────────

QUICK VERIFICATION COMMANDS
────────────────────────────

Check all services running:
  $ netstat -tulpn | grep -E '8000|8080|5432'

View Laravel logs:
  $ tail -f storage/logs/laravel.log

Test database:
  $ php artisan tinker
  > DB::connection()->getPdo()
  > exit

═══════════════════════════════════════════════════════════════════
