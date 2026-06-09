# Sportynix Hub System — Complete Guide
> For Claude and future developers. Read this before touching any file.

---

## Table of Contents
1. [What This System Is](#1-what-this-system-is)
2. [Architecture Overview](#2-architecture-overview)
3. [Technology Stack](#3-technology-stack)
4. [User Roles & Access](#4-user-roles--access)
5. [Database Schema](#5-database-schema)
6. [Routes Reference](#6-routes-reference)
7. [Livewire Components](#7-livewire-components)
8. [Layouts](#8-layouts)
9. [Models & Relationships](#9-models--relationships)
10. [Critical Quirks & Gotchas](#10-critical-quirks--gotchas)
11. [Bugs Fixed (History)](#11-bugs-fixed-history)
12. [Future Enhancements & New Feature Suggestions](#12-future-enhancements--new-feature-suggestions)

---

## 1. What This System Is

**Sportynix Hub** is a dual-platform sports venue booking system:

- **Web App** — Laravel 10 + Livewire 3 (this project)
- **Mobile App** — Django REST API (separate repo)
- **Shared Database** — PostgreSQL (`indoor_booking_test`)

The web app has three portals:
1. **Public Landing Page** — Browse venues, register as a facility owner
2. **Staff/Facility Owner Dashboard** — Manage your venue's bookings, sports, reports
3. **Sportynix Hub Admin Dashboard** — Manage all venues, users, bookings system-wide

The mobile app's users book through Django; those bookings appear in the Laravel web admin in real-time via WebSocket (Laravel Reverb).

---

## 2. Architecture Overview

```
┌─────────────────────────────────────────────────────────┐
│                    PostgreSQL Database                   │
│  ┌─────────────────────┐  ┌────────────────────────┐    │
│  │  Laravel Tables     │  │  Django Tables          │    │
│  │  users              │  │  users_user             │    │
│  │  sessions           │  │  booking_booking        │    │
│  │  landing_pages      │  │  booking_venue          │    │
│  │  blogs              │  │  booking_sport          │    │
│  │  ...                │  │  booking_*              │    │
│  └─────────────────────┘  └────────────────────────┘    │
└─────────────────────────────────────────────────────────┘
         ▲                              ▲
         │ Eloquent ORM                 │ Django ORM
         │                              │
┌────────┴──────────┐        ┌──────────┴──────────┐
│  Laravel Web App  │        │  Django Mobile API   │
│  (this project)   │        │  (separate repo)     │
│  Port: 8000       │◄──────►│  Port: 8001(approx)  │
│                   │Reverb  │                      │
│  Livewire 3       │WS:8080 │  DRF REST API        │
└───────────────────┘        └─────────────────────┘
         ▲
         │ Browser
┌────────┴──────────┐
│  Admin / Staff    │
│  Web Dashboard    │
└───────────────────┘
```

**Real-time flow:** Mobile user books → Django writes to `booking_booking` → Laravel `BookingBookingObserver` fires → Reverb WebSocket broadcasts → Staff dashboard updates instantly.

---

## 3. Technology Stack

| Layer | Technology | Version |
|-------|-----------|---------|
| Backend Framework | Laravel | 10.10 |
| Component Framework | Livewire | 3.x |
| Authentication | Jetstream + Sanctum | 4.3 / 3.3 |
| Database | PostgreSQL | local 5432 |
| Real-time | Laravel Reverb | 1.6 |
| CSS Framework | Bootstrap | 5.3 |
| Utility CSS | Tailwind CSS | 3.4 (CDN) |
| Icons | Font Awesome | 6.4 |
| JS Framework | Alpine.js | 3.x (via Livewire) |
| PDF | DOMPDF | 3.1 |
| Date/Time | Carbon | 2.x |
| HTTP Client | Guzzle | 7.x |
| Asset Build | Vite | Latest |
| Mail | SMTP / Gmail | - |
| PHP | PHP | ^8.1 |

**Key `.env` settings:**
```
DB_CONNECTION=pgsql
DB_DATABASE=indoor_booking_test
BROADCAST_DRIVER=reverb
REVERB_PORT=8080
SESSION_DRIVER=database
```

---

## 4. User Roles & Access

Three roles stored in `users.role`:

| Role | Value | Access | URL Prefix |
|------|-------|--------|------------|
| Super Admin | `admin` | Full system | `/admin/` |
| Staff | `staff` | Their venue | `/staff/` |
| Facility Owner | `facility_owner` | Their venue | `/staff/` |

**Important:** `staff` and `facility_owner` share the **same URLs and route names** (`staff.*`). The `RoleMiddleware` accepts both: `role:staff,facility_owner`.

**Login → Redirect logic** (`CustomLogin.php`):
```
admin         → route('admin.dashboard')
staff         → route('staff.dashboard')
facility_owner → route('staff.dashboard')
```

**Registration** (`/register`) creates:
1. A `users` record with `role = 'facility_owner'`
2. A `users_user` record (for Django mobile app compatibility)
3. A `booking_venue` record
4. One or more `booking_sport` records
5. `complex_id` on user points to `booking_venue.id`

---

## 5. Database Schema

### Two Separate User Systems

```
users (Laravel)                  users_user (Django)
─────────────────                ─────────────────────
id                               id
name                             first_name, last_name
email ──────────────────────────► email
password                         password
role (admin/staff/facility_owner) is_superuser, is_staff
contact                          phone_number
complex_id → booking_venue.id    profile_picture
                                 points, referral_code
                                 sports_preferences (json)
                                 availability
                                 last_login
                                 NO timestamps
```

### Core Booking Tables (Django-style naming)

```
booking_venue                    booking_sport
────────────────                 ────────────────
id                               id
name                             venue_id → booking_venue.id
complex_type                     name
county, location                 price (decimal 10,2)
address, postal_code             image (string)
contact_number, email_address    game_type, rate_type
website, status                  maximum_court
opening_hours (json)             status ('Active'/'Inactive')
amenities (json)                 advance_required (boolean)
cover_image (path)               additional_charges (json)
gallery_images_json (json)       average_rating
image_url (full URL)             description
video_tour_url
social_links (json)
description, terms
rating, reviews

booking_booking
─────────────────────────────────
id
game_id_id → booking_sport.id    ← Django double-underscore FK
complex_id_id → booking_venue.id ← Django double-underscore FK
user_id_id → users_user.id       ← Django double-underscore FK
game_name, user_name, user_number
court_number
booking_date (date)
start_time, end_time (time)
duration (integer, minutes)
price (decimal)
status ('Confirmed'|'Pending'|'Cancelled'|'Completed'|'No-Show'|'Playing')
payment_status ('Pending'|'Paid'|'Refunded')
payment_method
qr_code
notes, admin_comments
is_challenge_booking (boolean)
permanent_source_id (for recurring bookings)
```

### Status Values (Case-sensitive — ALWAYS Title Case)

```php
// booking_booking.status
'Confirmed', 'Pending', 'Cancelled', 'Completed', 'No-Show', 'Playing'

// booking_venue.status
'Active', 'Inactive', 'Maintenance', 'New'

// booking_sport.status
'Active', 'Inactive'
```

**CRITICAL:** The database stores `'Confirmed'` not `'confirmed'`. Queries using lowercase will return 0 results. Always use Title case.

### Other Tables

| Table | Purpose |
|-------|---------|
| `booking_venuereview` | Customer reviews of venues |
| `booking_sportreview` | Customer reviews of specific sports/courts |
| `booking_galleryimage` | Gallery images linked to venue |
| `booking_facility` | Facilities/amenities per sport |
| `booking_discount` | Discounts per sport |
| `booking_notification` | In-app notifications |
| `booking_chat` | Chat rooms |
| `booking_chatmessage` | Chat messages |
| `booking_team` | Team management (league feature) |
| `booking_teamchallenge` | Team vs team challenges |
| `landing_pages` | CMS for landing page sections |
| `blogs` | Blog posts |
| `admin_sales` | Admin sales tracking |
| `sessions` | Database-backed Laravel sessions |

---

## 6. Routes Reference

### Public Routes (No Auth)
```
GET  /                  → Home
GET  /indoor            → Indoor venue listing
GET  /about             → About page
GET  /contact           → Contact form
GET  /register          → Facility owner registration (3-step wizard)
GET  /blog              → Blog listing
GET  /features          → Features page
GET  /terms-of-service  → Terms
GET  /security          → Security info
GET  /for-business      → B2B page
GET  /privacy           → Privacy policy
GET  /careers           → Careers page
GET  /login             → Login (guest only)
POST /logout            → Logout
```

### Admin Routes (`/admin/*`, role: admin)
```
GET  /admin/dashboard        → admin.dashboard
GET  /admin/indoors          → admin.indoors       (venue management)
GET  /admin/bookings         → admin.bookings      (booking overview)
GET  /admin/customers        → admin.customers     (Django users)
GET  /admin/indoor-admins    → admin.indoor-admins (facility owner accounts)
GET  /admin/landing-page     → admin.landing-page  (CMS)
GET  /admin/blogs-management → admin.blogs-management
```

### Staff Routes (`/staff/*`, role: staff OR facility_owner)
```
GET  /staff/dashboard  → staff.dashboard
GET  /staff/sports     → staff.sports
GET  /staff/bookings   → staff.bookings
GET  /staff/reports    → staff.reports
GET  /staff/feedbacks  → staff.feedbacks
GET  /staff/setting    → staff.setting
GET  /staff/help       → staff.help
```

---

## 7. Livewire Components

### Admin Panel (`app/Livewire/Admin/`)

| Component | Layout | Key Properties | Key Methods |
|-----------|--------|---------------|-------------|
| `AdminDashboard` | admin | totalRevenue, bookingsCount, pendingCount, cancelledCount | render() |
| `Indoors` | admin | search, statusFilter, addModalVisible, editingVenue | saveVenue(), deleteVenue(), showEditModal() |
| `Bookings` | admin | dateFilter, statusFilter, venueFilter, totalRevenue | applyFilters(), getRecentBookingsProperty() |
| `Customers` | admin | search, statusFilter, showModal | toggleStatus(), addUser() |
| `IndoorAdmins` | admin | search, venueFilter, editIndoorAdminId | saveIndoorAdmin(), updateIndoorAdmin(), resetPassword() |
| `BlogsManagement` | admin | - | CRUD blog posts |
| `LandingPageCMS` | admin | - | Manage CMS sections |

### Staff Panel (`app/Livewire/Staff/`)

| Component | Layout | Key Properties | Key Methods |
|-----------|--------|---------------|-------------|
| `StaffDashboard` | staff | complex_id, bookingsCount, todaybookingRevenue, analyticsSlots, sportsWithSlots, calendarDays | loadSlotAvailability(), saveQuickBooking(), generateCalendarDays() |
| `BookingsManagement` | staff | complex_id, bookingdetails[], sports, games, selectedGame/Date/Time/Court | addBooking(), cancelBooking(), loadSports() |
| `SportsManagement` | staff | complex_id, name, price, advance_required, maximum_court | addSport(), editSport(), deleteSport() |
| `StaffReport` | staff | complex_id, start_date, end_date, period | generateReport(), exportReport() |
| `StaffFeedbacks` | staff | complex_id, reviews, totalReviews, averageRating | loadFeedbacks() |
| `StaffSetting` | staff | complex_id, activeSection, isEditModalOpen, opening_hours[], social_links[] | saveChanges(), changePassword(), uploadGallery(), updateOpeningHours() |
| `StaffHelp` | staff | name, email, subject, message | sendContactMessage() |

### Landing Page (`app/Livewire/LandingPage/`)

| Component | Purpose |
|-----------|---------|
| `Home` | Homepage with venue listings |
| `Indoor` | Venue search/filter |
| `Register` | 3-step facility owner registration wizard |
| `Contact` | Contact form with email |
| `Blog` | Blog listing |
| Others | Static info pages |

### Shared
| Component | Purpose |
|-----------|---------|
| `CustomLogin` | Email/password login with role-based redirect |

---

## 8. Layouts

### `components/layouts/admin.blade.php`
- Fixed sidebar (280px), col-md-9 main content
- Sidebar uses `.sidebar` class (NOT `#sidebar` — was a bug)
- Main content: `<main id="main-content">{{ $slot }}</main>` inside col-md-9
- Scripts: Bootstrap 5.3, SweetAlert2, Alpine.js, Livewire
- Profile dropdown shows `auth()->user()->name` and `email`
- Notification/envelope icons link to `route('admin.bookings')`

### `components/layouts/staff.blade.php`
- Fixed sidebar (280px), margin-left on `.main-content`
- Sidebar uses `querySelector('.sidebar')` (NOT getElementById)
- Bell icon links to `route('staff.setting')?section=notifications`
- Profile dropdown links to `route('staff.setting')`

### `components/layouts/main.blade.php`
- Public landing page layout
- Responsive mobile sidebar
- No authentication required

### `components/layouts/app.blade.php`
- Generic layout (minimal use)

---

## 9. Models & Relationships

### `BookingBooking` — the central booking model
```php
// table: booking_booking
// FK columns use Django double-underscore naming:
game_id_id    → BookingSport (NOT sport_id)
complex_id_id → BookingVenue (NOT venue_id)
user_id_id    → UserUser    (NOT user_id)

// Relationships:
sport()  → belongsTo(BookingSport::class, 'game_id_id')
venue()  → belongsTo(BookingVenue::class, 'complex_id_id')
user()   → belongsTo(User::class, 'user_id_id')
```

### `BookingVenue`
```php
// table: booking_venue
// JSON columns (auto-cast as array):
opening_hours, amenities, gallery_images_json, social_links

// Cover image stored as:
cover_image  = relative path (e.g. 'venues/cover_123.jpg')
image_url    = full URL (asset('storage/' . cover_image))

// Relationships:
sports()   → hasMany(BookingSport, 'venue_id')
gallery()  → hasMany(BookingGalleryImage, 'venue_id')
reviews()  → hasMany(BookingVenueReview, 'venue_id')
bookings() → hasMany(BookingBooking, 'complex_id_id')
owner()    → hasOne(User, 'complex_id')
```

### `BookingSport`
```php
// table: booking_sport
// image field: 'image' (NOT 'image_url')
// status: 'Active' / 'Inactive' (Title case)

// Relationships:
venue() → belongsTo(BookingVenue, 'venue_id')
```

### `User` (Laravel users table)
```php
// role values: 'admin', 'staff', 'facility_owner'
// complex_id links to booking_venue.id (NOT a separate complexes table)

complex() → belongsTo(BookingVenue, 'complex_id')
```

### `UserUser` (Django's users_user table)
```php
// $timestamps = false (NO created_at/updated_at)
// Password is Django-hashed (pbkdf2) for Django; Laravel stores bcrypt separately

reviews() → hasMany(BookingVenueReview, 'user_id')
```

---

## 10. Critical Quirks & Gotchas

### 1. Status Strings Are Case-Sensitive Title Case
```php
// WRONG (returns 0 results):
BookingBooking::where('status', 'confirmed')
BookingVenue::where('status', 'active')

// CORRECT:
BookingBooking::where('status', 'Confirmed')
BookingVenue::where('status', 'Active')
```

### 2. Django Double-Underscore Foreign Keys
The `booking_booking` table uses Django ORM naming for foreign keys:
```php
$booking->game_id_id    // BookingSport ID
$booking->complex_id_id // BookingVenue ID
$booking->user_id_id    // UserUser ID
```

### 3. Two Cover Image Fields on BookingVenue
```php
$venue->cover_image  // Storage path: 'venues/cover_123.jpg'
$venue->image_url    // Full URL: 'https://example.com/storage/venues/cover_123.jpg'
// Always update BOTH when changing cover image
```

### 4. Gallery Images Stored in JSON Column
```php
$venue->gallery_images_json  // Cast as array, contains relative paths
// NOT using the booking_galleryimage table for the venue's own gallery
// booking_galleryimage is used for admin-uploaded gallery entries
```

### 5. Sport Name Case-Insensitive Search
The `bookingdetails` array in `BookingsManagement` stores game names as lowercase:
```php
$game = strtolower($booking->game_name); // e.g. 'badminton'
```
But `BookingSport.name` stores `'Badminton'`. Always use `whereRaw('LOWER(name) = ?', [strtolower(...)])` when searching sports by name.

### 6. No `$timestamps` on UserUser
```php
// UserUser model has: public $timestamps = false;
// There is NO created_at/updated_at on users_user table
// "New this week" stats use last_login as proxy — it's imperfect but it's all we have
```

### 7. Livewire v3 Dispatch (NOT dispatchBrowserEvent)
```php
// WRONG (Livewire v2):
$this->dispatchBrowserEvent('eventName', [...]);

// CORRECT (Livewire v3):
$this->dispatch('eventName', data: [...]);
```

### 8. Sidebar querySelector vs getElementById
Both admin and staff layouts use a CSS **class** `.sidebar`, not an ID.
```js
// WRONG:
document.getElementById('sidebar') // returns null — breaks ALL Bootstrap JS

// CORRECT:
document.querySelector('.sidebar')
```

### 9. Route Middleware Accepts Multiple Roles
```php
// RoleMiddleware now supports comma-separated roles:
Route::middleware('role:staff,facility_owner')
// This is why staff and facility_owner share /staff/* URLs
```

### 10. Registration Creates Records in Two User Tables
When a facility owner registers:
- `users` table gets a record with `role = 'facility_owner'`
- `users_user` table gets a record (for Django mobile app)
- `booking_venue` gets the venue record
- `users.complex_id = booking_venue.id`

Admin-created facility owners (via IndoorAdmins) do NOT create a `users_user` record. `user_id_id` on bookings they create will be null.

### 11. Mail/SMTP Can Fail — Always Wrap in try/catch
```php
// Always wrap Mail::send() in try/catch
// Handler.php catches TransportException for forgot-password flow
// StaffHelp::sendContactMessage() has its own try/catch
```

### 12. Opening Hours Format
Stored as JSON with structure:
```json
{
  "monday": {"open": "09:00", "close": "17:00", "closed": false},
  "tuesday": {"open": "09:00", "close": "17:00", "closed": false},
  ...
}
```
Day keys are always **lowercase**. Both `StaffSetting` and `BookingsManagement` have a `parseOpeningHoursFromDb()` method to normalize variations.

---

## 11. Bugs Fixed (History)

This section documents all bugs that were found and fixed so future developers don't re-introduce them.

| Area | Bug | Fix |
|------|-----|-----|
| Admin Layout | `getElementById('sidebar')` returned null, breaking ALL Bootstrap dropdowns | Changed to `querySelector('.sidebar')` with null checks |
| Admin Layout | `{{ $slot }}` rendered outside Bootstrap column (behind fixed sidebar) | Moved inside `col-md-9` as `<main id="main-content">` |
| Staff Layout | Same `getElementById` null pointer as admin | Changed to `querySelector('.sidebar')` |
| Login | Failed login showed no error, just redirected | Changed to `$this->addError('email', ...)` |
| Bookings | Badminton/Cricket "game not available" error | Case-insensitive sport search with `whereRaw('LOWER(name) = ?', ...)` |
| Bookings | Status dropdown only showed "Pending" | Added all 6 status options to dropdown |
| Bookings | Revenue = 0 | Was querying `'completed'` (lowercase); fixed to `'Completed'` |
| Sports | Edit Sport modal missing "Advance Payment Required" checkbox | Added checkbox to edit modal |
| Reports | `$booking->booking_id` doesn't exist | Changed to `$booking->id` |
| Reports | `$complex->complex_name` doesn't exist | Changed to `$complex->name` |
| Reports | `$complex->game_image` doesn't exist | Changed to `$complex->cover_image` |
| Reports | Sport name shows "N/A" | Fixed `sport()` relationship FK: `sport_id` → `game_id_id` |
| Reports | Export button throws MethodNotFoundException | Added `exportReport()` method with CSV download |
| Reports | Customer Reports / Performance buttons did nothing | Added modal triggers (`data-bs-toggle`, `data-bs-target`) |
| Settings | Gallery images not showing | View used `$complexes->gallery_images` instead of `$existing_gallery_images` |
| Settings | Last login showed hardcoded fake date | Changed to `auth()->user()->updated_at->format(...)` |
| Settings | Upcoming bookings in notifications was empty | Added real DB query with `$upcomingNotifBookings` |
| Settings | Social Links remove button closed modal | Missing `type="button"` on button, defaulted to submit |
| Settings | Add Staff modal not accessible | Modal was inside wrong `@elseif` block; moved outside all conditionals |
| Settings | Broken staff card images | URL strings were cut off with HTML mixed inside `src` attribute |
| Settings | Missing `@endif` for `$activeSection` block | Added closing `@endif` at end of file |
| Admin Bookings | Status filters used lowercase strings | Fixed to Title case throughout |
| Admin Bookings | `$booking->total` column doesn't exist | Changed to `$booking->price` |
| Admin Bookings | Currency shows `$` instead of LKR | Fixed to `LKR` throughout |
| Admin Indoors | Status values lowercase (`'active'`) inconsistent with DB | Fixed to Title case (`'Active'`) |
| Admin Indoors | `exportData()` called `dd()` crashing page | Replaced with flash message |
| Register | Single sport type only (radio) | Changed to multiple checkboxes bound to `$sport_types[]` |
| Register | Amenities count showed "0 selected" | Fixed count expression |
| Register | "County" label confusing for Sri Lanka | Changed to "District / City" |
| Help | Symfony TransportException crashed page | Wrapped `Mail::send()` in try/catch |
| Help | `ContactMessageMail` used `->markdown()` for HTML view | Changed to `->view()` |
| Forgot Password | TransportException crashed forgot-password flow | Added `Handler.php` renderable handler |
| Staff Dashboard | `dispatchBrowserEvent()` — Livewire v2 syntax | Changed to `$this->dispatch()` |
| Staff Dashboard | `markAllAsRead()` updated all venues' bookings | Added `complex_id_id` filter |
| Staff Dashboard | Duplicate `sportsCount` key in render array | Removed duplicate |
| Bookings Mgmt | Staff with no `users_user` record couldn't create bookings | Made `user_id_id` nullable with `?->id` |
| Routing | `staff` and `facility_owner` had duplicate route names → `facility_owner` overwrote `staff` routes | Consolidated into single group with `role:staff,facility_owner` |
| Middleware | `RoleMiddleware` only accepted one role | Updated to accept variadic `...$roles` |
| Admin Dashboard | Total Revenue card was missing | Added `$totalRevenue` property and card |

---

## 12. Future Enhancements & New Feature Suggestions

### HIGH PRIORITY (Core Functionality Gaps)

#### 1. Online Payment Integration
**Current state:** Payment status is tracked (`Pending`, `Paid`) but no actual payment gateway exists. All payments are cash/manual.  
**Suggestion:** Integrate a payment gateway.
- **Sri Lanka options:** PayHere, Genie, FriMi, Sampath Bank payment gateway
- **International fallback:** Stripe (test mode available globally)
- Flow: Booking created → Payment page → Webhook confirms → Status → `Paid`
- Store: `payment_reference`, `payment_gateway`, `paid_at` columns on `booking_booking`

#### 2. Real Email Notifications to Customers
**Current state:** No emails sent to customers who book via the mobile app. The contact form sends to the admin email only.  
**Suggestion:**
- Booking confirmation email to `user_number` phone (SMS via local gateway) or email
- Reminder email 24 hours before booking
- Cancellation notification
- Use Laravel Queues (`QUEUE_CONNECTION=database`) with scheduled jobs

#### 3. Two-Factor Authentication (2FA)
**Current state:** The 2FA section in Staff Settings has a toggle switch and "Configure" button that do nothing.  
**Suggestion:** Laravel Jetstream already includes 2FA (`two_factor_secret`, `two_factor_recovery_codes` columns on `users`). Just enable it:
```php
// In JetstreamServiceProvider or config/jetstream.php:
Features::twoFactorAuthentication(['confirm' => true])
```
Then wire the Configure button to Jetstream's built-in 2FA management routes.

#### 4. Booking Cancellation by Customer (Self-Service)
**Current state:** Only staff can cancel bookings via the dashboard.  
**Suggestion:** Allow customers to cancel via:
- A cancellation link in their booking confirmation email/SMS
- A cancel token stored on the booking (signed URL)
- Cancellation policy: Allow if > N hours before booking

#### 5. Recurring Bookings UI
**Current state:** The `permanent_source_id` column and `$permanent` toggle exist in `BookingsManagement` but there's no UI to view/manage all recurring bookings or cancel a series.  
**Suggestion:**
- Recurring bookings list view grouped by `permanent_source_id`
- "Cancel entire series" or "Cancel this instance only" option

---

### MEDIUM PRIORITY (Business Value)

#### 6. Customer Booking Portal (Web)
**Current state:** The landing page shows venues but has no booking flow for web customers — bookings only come from the mobile app.  
**Suggestion:** Add a web booking flow:
- `/indoor/{venue}/book` → Select sport → Select date/time → Confirm → Payment
- Stores in `booking_booking` same as mobile
- Login optional (guest checkout with email)

#### 7. Dashboard Analytics Charts
**Current state:** Stats are shown as numbers only. No charts or visual analytics.  
**Suggestion:** Add Chart.js (already referenced in admin layout) charts:
- Revenue trend (last 30 days line chart)
- Booking status distribution (pie chart)
- Busiest hours heatmap
- Sport popularity bar chart
- Occupancy rate per day

#### 8. Dynamic Pricing
**Current state:** Each sport has a flat `price` per hour.  
**Suggestion:**
- Peak/off-peak pricing (weekday vs weekend, morning vs evening)
- Seasonal pricing
- Group discount (more courts = lower price per court)
- Advance booking discount
- Store as `booking_sport.pricing_rules (json)`

#### 9. Court/Slot Blocking
**Current state:** `booking_venue.blocked_slots (json)` column exists but is never used.  
**Suggestion:** Allow staff to block specific time slots for:
- Maintenance
- Private events
- Tournaments
- Show as "Unavailable" in calendar — not bookable by mobile app

#### 10. Staff Member Management (Real Backend)
**Current state:** Team Member section in Settings shows 4 hardcoded demo staff cards with fake names.  
**Suggestion:** Create a real staff management system:
- New table: `venue_staff` (`id, venue_id, name, role, phone, shift, status`)
- CRUD via `StaffSetting` or a new `StaffTeam` Livewire component
- Link to `users` table for login access
- Display real staff on the settings page

#### 11. CCTV / Camera Management (Real Backend)
**Current state:** CCTV section in Settings is static mockup — camera cards are hardcoded HTML.  
**Suggestion:**
- If cameras support RTSP/HLS, embed stream URLs
- Simple table: `venue_cameras` (`id, venue_id, name, stream_url, location, status`)
- Allow staff to add camera stream URLs
- Embed HLS.js player for live preview

#### 12. Waitlist / Queue System
**Current state:** When a slot is booked, it's just unavailable. No notification if it opens up.  
**Suggestion:**
- Allow customers to join a waitlist for a fully-booked slot
- When booking is cancelled, notify the next person on the waitlist
- Table: `booking_waitlist` (`id, sport_id, date, time_slot, user_id, notified_at`)

---

### LOW PRIORITY (Polish & UX)

#### 13. Booking QR Code Generation & Scanning
**Current state:** `qr_code` column exists on bookings and a code is generated (`'QR' . random(6)`), but no actual QR image is created and no scanner exists.  
**Suggestion:**
- Use `simplesoftwareio/simple-qrcode` package to generate a real QR image
- Store the image path in `qr_code` column
- Allow staff to scan QR with phone camera to validate booking at venue entry
- Add a `/validate/{token}` public route

#### 14. Feedback Response System
**Current state:** Staff can see reviews in Feedbacks page but cannot reply to them.  
**Suggestion:**
- Add `booking_venuereview.owner_reply` (text, nullable) column
- Allow facility owners to type a public reply
- Display reply in mobile app under the review

#### 15. Blog / News System (Full CMS)
**Current state:** `BlogsManagement` component and `blogs` table exist but are minimal.  
**Suggestion:**
- Rich text editor (Quill.js or TinyMCE)
- Blog categories/tags
- SEO meta fields
- Featured image with crop/resize
- Publish scheduling

#### 16. Multi-Language Support (i18n)
**Current state:** All text is hardcoded in English.  
**Suggestion:**
- Add Sinhala and Tamil language support (Sri Lanka official languages)
- Laravel's built-in `lang/` directory
- Language switcher in navbar
- Store user preference in session or DB

#### 17. Mobile-Responsive Staff Dashboard
**Current state:** Staff and admin dashboards work on desktop but the sidebar toggle on mobile has a known issue — it's hidden by `d-none d-md-block` in some areas.  
**Suggestion:**
- Full mobile audit of both dashboards
- Bottom navigation bar for mobile (like mobile apps)
- Touch-friendly calendar/slot UI

#### 18. Booking History for Customers (Web)
**Current state:** No way for a customer to view their booking history on the web.  
**Suggestion:**
- Add customer login (separate from staff/admin)
- Customer portal: `/my-bookings`
- Show past and upcoming bookings
- Download receipt as PDF (DOMPDF already installed)

#### 19. Notification System (In-App)
**Current state:** `booking_notification` table exists with correct structure but is never written to or read from in the web app.  
**Suggestion:**
- Write notifications when bookings are created/cancelled/updated
- Display in a notification dropdown in the staff header (bell icon)
- Mark as read functionality
- Real-time delivery via Reverb WebSocket

#### 20. Advanced Report Export
**Current state:** Report export generates a basic CSV file.  
**Suggestion:**
- Excel export with formatting (PhpSpreadsheet / Laravel Excel)
- PDF export with charts (DOMPDF already installed)
- Scheduled monthly reports emailed automatically
- Custom date range picker

---

### TECHNICAL DEBT & INFRASTRUCTURE

#### 21. Queue System
**Current state:** `QUEUE_CONNECTION=sync` (all jobs run synchronously in-request).  
**Suggestion:**
- Switch to `database` queue driver (migration already exists: `jobs` table)
- Move email sending to queued jobs
- Move WebSocket broadcasts to queued jobs
- Add `php artisan queue:work` to startup scripts

#### 22. Image Optimization
**Current state:** Images are stored as-is from user uploads. No resizing or compression.  
**Suggestion:**
- Use `intervention/image` to resize cover images on upload (e.g. max 1200×800)
- Generate thumbnails for gallery (400×300)
- Strip EXIF data for privacy
- Consider Cloudinary or S3 for production

#### 23. API Layer for Mobile App
**Current state:** Mobile app (Django) talks directly to the shared PostgreSQL database — not via Laravel API.  
**Suggestion:** Create a proper Laravel API layer:
- `routes/api.php` with Sanctum-authenticated endpoints
- `GET /api/venues` → list venues
- `POST /api/bookings` → create booking
- `GET /api/bookings/{id}` → booking detail
- This would decouple Django from direct DB access

#### 24. Automated Tests
**Current state:** No tests exist (`/tests` directory is default Laravel scaffold only).  
**Suggestion:**
- Feature tests for booking creation flow
- Unit tests for `RoleMiddleware`
- Livewire component tests
- Database seeder for test data

#### 25. Error Monitoring
**Current state:** Errors go to `storage/logs/laravel.log`. No alerting.  
**Suggestion:**
- Sentry.io free tier (Laravel SDK available)
- Alerts on 500 errors
- Performance monitoring

---

## Quick Reference Card

```
# Start the app
php artisan serve          # Web server on :8000
php artisan reverb:start   # WebSocket server on :8080
npm run dev                # Vite asset watcher

# Clear all caches (do this after any config/view change)
php artisan view:clear && php artisan cache:clear && php artisan config:clear

# Database
php artisan migrate
php artisan migrate:rollback

# Check routes
php artisan route:list --name=staff
php artisan route:list --name=admin

# Logs
tail -f storage/logs/laravel.log
```

---

*Last updated: 2026-04-08 | Maintained by: Claude (Sonnet 4.6)*  
*This file is the single source of truth for system understanding.*
