
#!/bin/bash

# Display a beautiful setup guide
clear

cat << 'EOF'

╔══════════════════════════════════════════════════════════════════════════════╗
║                                                                              ║
║                  🎉 WELCOME TO YOUR LIVE BOOKING SYSTEM 🎉                  ║
║                                                                              ║
║                   Real-Time Indoor Sports Booking Platform                  ║
║                          Production Ready • WebSocket Enabled                ║
║                                                                              ║
╚══════════════════════════════════════════════════════════════════════════════╝


┌──────────────────────────────────────────────────────────────────────────────┐
│  🚀 QUICK START (Choose Your Path)                                           │
└──────────────────────────────────────────────────────────────────────────────┘

  PATH A: QUICK START (5 minutes)
  ────────────────────────────────
  Just the essential commands:
  
  $ cd /var/www/ib_laravel
  $ composer install && npm install
  $ php artisan migrate --force
  $ npm run build
  
  Terminal 1: $ php artisan serve --host=0.0.0.0 --port=8000
  Terminal 2: $ php artisan reverb:start --host=0.0.0.0 --port=8080
  
  Browser: http://your-server-ip:8000


  PATH B: AUTOMATED (3 minutes)
  ────────────────────────────
  Let the script do everything:
  
  Linux/Mac:
  $ bash deploy-live.sh
  
  Windows:
  $ .\deploy-live.ps1


  PATH C: DETAILED GUIDE (30 minutes)
  ──────────────────────────────────
  Step-by-step with explanations:
  
  📖 Read: LIVE_DEPLOYMENT_GUIDE.md
  ⚡ Follow every step


┌──────────────────────────────────────────────────────────────────────────────┐
│  📊 YOUR SYSTEM ARCHITECTURE                                                 │
└──────────────────────────────────────────────────────────────────────────────┘

                          USER BROWSER
                               │
                               ↓
                    ┌──────────────────┐
                    │  Web Interface   │
                    │  (Livewire UI)   │
                    │   Port 8000      │
                    └────────┬─────────┘
                             │
                ┌────────────┴────────────┐
                │                         │
                ↓                         ↓
        ┌──────────────┐        ┌─────────────────┐
        │   Laravel    │        │  Echo JS Lib    │
        │  Framework   │        │  (WebSocket)    │
        │              │        │                 │
        │ • Routes     │        │ • Listens to    │
        │ • Controllers│        │   real-time     │
        │ • Models     │        │   events        │
        │ • Observers  │        │                 │
        └──────┬───────┘        └────────┬────────┘
               │                         │
               └────────────┬────────────┘
                            ↓
                  ┌──────────────────────┐
                  │  Reverb WebSocket    │
                  │  Server              │
                  │  Port 8080           │
                  │                      │
                  │ • Broadcasting       │
                  │ • Real-time events   │
                  │ • Multi-user sync    │
                  └──────────┬───────────┘
                             │
                             ↓
                  ┌──────────────────────┐
                  │  PostgreSQL          │
                  │  Database            │
                  │  Port 5432           │
                  │                      │
                  │ • Booking data       │
                  │ • User info          │
                  │ • Analytics          │
                  └──────────────────────┘


┌──────────────────────────────────────────────────────────────────────────────┐
│  ✨ HOW LIVE UPDATES WORK                                                     │
└──────────────────────────────────────────────────────────────────────────────┘

  1. Admin Creates Booking
     └─→ BookingBooking model created
  
  2. Observer Triggered
     └─→ BookingCreated event dispatched
  
  3. Broadcast Event
     └─→ Reverb sends via WebSocket
  
  4. All Connected Clients Receive
     └─→ Browser receives real-time data
  
  5. UI Updates INSTANTLY
     └─→ No page refresh needed!
  
  ⚡ Result: All admins see new booking LIVE! 🎉


┌──────────────────────────────────────────────────────────────────────────────┐
│  📚 NEW DOCUMENTATION FILES                                                   │
└──────────────────────────────────────────────────────────────────────────────┘

  ⭐ START_HERE_LIVE.md
     ├─ Quick reference card
     ├─ 5-minute TL;DR
     ├─ Critical settings
     └─ Quick troubleshooting

  📖 README_LIVE.md
     ├─ System overview
     ├─ What you have
     ├─ Architecture
     └─ Next steps

  🚀 LIVE_DEPLOYMENT_GUIDE.md
     ├─ Step-by-step guide
     ├─ All 3 deployment paths
     ├─ Detailed explanations
     └─ Common issues & solutions

  🔧 deploy-live.sh
     ├─ Automated Linux/Mac
     ├─ Checks prerequisites
     ├─ Installs dependencies
     └─ Optional auto-start

  🔧 deploy-live.ps1
     ├─ Automated Windows
     ├─ Color output
     └─ Opens terminals

  🏥 health-check.sh
     ├─ System diagnostics
     ├─ Verifies services
     ├─ Checks configuration
     └─ Identifies problems

  📍 LIVE_INDEX.md
     ├─ Navigation guide
     ├─ Document index
     └─ Quick links


┌──────────────────────────────────────────────────────────────────────────────┐
│  ✅ VERIFICATION CHECKLIST                                                    │
└──────────────────────────────────────────────────────────────────────────────┘

  After deploying:

  ☐ Laravel Server Running (port 8000)
    $ curl http://127.0.0.1:8000

  ☐ Reverb WebSocket Running (port 8080)
    $ curl http://127.0.0.1:8080

  ☐ Database Connected
    $ psql -h 127.0.0.1 -U webxkey -d indoor_booking

  ☐ System Healthy
    $ bash health-check.sh

  ☐ Test Live Updates
    $ php complete-live-test.php
    (Check dashboard - should see booking INSTANTLY)


┌──────────────────────────────────────────────────────────────────────────────┐
│  🎯 PORTS TO ALLOW IN FIREWALL                                               │
└──────────────────────────────────────────────────────────────────────────────┘

  $ sudo ufw allow 8000/tcp    # Laravel Application
  $ sudo ufw allow 8080/tcp    # WebSocket Server
  $ sudo ufw allow 5432/tcp    # PostgreSQL (if remote)

  Verify:
  $ netstat -tulpn | grep -E '8000|8080|5432'


┌──────────────────────────────────────────────────────────────────────────────┐
│  📱 ACCESS YOUR SYSTEM                                                        │
└──────────────────────────────────────────────────────────────────────────────┘

  After starting services:

  🏠 Admin Dashboard
     http://your-server-ip:8000/admin

  📅 Bookings Management
     http://your-server-ip:8000/admin/bookings

  🏢 Venues Management
     http://your-server-ip:8000/admin/indoors

  👥 Staff Dashboard
     http://your-server-ip:8000/staff

  📡 API Endpoints
     http://your-server-ip:8000/api/bookings


┌──────────────────────────────────────────────────────────────────────────────┐
│  🆘 TROUBLESHOOTING                                                           │
└──────────────────────────────────────────────────────────────────────────────┘

  Problem: Don't know where to start
  Solution: Read START_HERE_LIVE.md

  Problem: Want step-by-step instructions
  Solution: Read LIVE_DEPLOYMENT_GUIDE.md

  Problem: Services won't start
  Solution: Run health-check.sh for diagnostics

  Problem: Bookings not updating live
  Solution: Check SERVER_TROUBLESHOOTING.md

  Problem: Something is broken
  Solution: 
    1. Run: bash health-check.sh
    2. Check: tail -f storage/logs/laravel.log
    3. Check: Browser console (F12)
    4. Read: SERVER_TROUBLESHOOTING.md


┌──────────────────────────────────────────────────────────────────────────────┐
│  🎁 FEATURES INCLUDED                                                        │
└──────────────────────────────────────────────────────────────────────────────┘

  ✅ Real-time Booking Dashboard
  ✅ Live Updates (no page refresh)
  ✅ Multi-Admin Support
  ✅ WebSocket Broadcasting
  ✅ PostgreSQL Database
  ✅ Professional UI
  ✅ Responsive Design
  ✅ Mobile API
  ✅ User Authentication
  ✅ Booking History
  ✅ Analytics & Reports
  ✅ Payment Processing
  ✅ Email Notifications
  ✅ Rating System


┌──────────────────────────────────────────────────────────────────────────────┐
│  🚀 RECOMMENDED NEXT STEPS                                                    │
└──────────────────────────────────────────────────────────────────────────────┘

  1️⃣  Read START_HERE_LIVE.md (5 minutes)
  
  2️⃣  Run deployment script (3-5 minutes)
       Linux/Mac: bash deploy-live.sh
       Windows:   .\deploy-live.ps1
  
  3️⃣  Start services (2 terminals)
       Terminal 1: php artisan serve --host=0.0.0.0 --port=8000
       Terminal 2: php artisan reverb:start --host=0.0.0.0 --port=8080
  
  4️⃣  Open browser
       http://your-server-ip:8000
  
  5️⃣  Test with: php complete-live-test.php
  
  6️⃣  Watch live update on dashboard! ✨


┌──────────────────────────────────────────────────────────────────────────────┐
│  📞 DOCUMENTATION LINKS                                                       │
└──────────────────────────────────────────────────────────────────────────────┘

  📍 Navigation & Index
     → LIVE_INDEX.md

  ⭐ Quick Start (TL;DR)
     → START_HERE_LIVE.md

  📚 Complete Overview
     → README_LIVE.md

  🚀 Deployment Guide
     → LIVE_DEPLOYMENT_GUIDE.md

  🏥 Troubleshooting
     → SERVER_TROUBLESHOOTING.md

  📖 How it Works
     → LIVE_UPDATES_FIXED.md
     → IMPLEMENTATION_SUMMARY.md


╔══════════════════════════════════════════════════════════════════════════════╗
║                                                                              ║
║                        ✨ YOU'RE ALL SET! ✨                                 ║
║                                                                              ║
║                    Your booking system is ready to go live                  ║
║                                                                              ║
║              Follow the steps above and you'll be live in minutes!          ║
║                                                                              ║
║                           Happy Booking! 🎉                                  ║
║                          🏐 ⚽ 🏸 🎾 🏀                                         ║
║                                                                              ║
╚══════════════════════════════════════════════════════════════════════════════╝


                          Questions? Check the docs!
                     Every answer is in the files above 👆

EOF
