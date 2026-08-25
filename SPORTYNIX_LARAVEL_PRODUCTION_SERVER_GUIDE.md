# Sportynix Laravel Admin — Production Server Operations Guide

> **Purpose**
>
> This document is for Laravel-side developers who need to maintain, update, inspect, or troubleshoot the Sportynix Laravel admin application on the production Hostinger VPS.
>
> The current production deployment is **Docker-based**, while PostgreSQL runs directly on the VPS host.
>
> **Important:** This is currently a **manual deployment workflow**. CI/CD is not required at this time.

---

# 1. Production Architecture

## Main components

- **VPS:** Hostinger KVM2
- **OS:** Ubuntu 24.04 LTS
- **Application:** Laravel 10
- **PHP:** PHP 8.3
- **Web server inside Docker:** Nginx
- **PHP runtime:** PHP-FPM
- **Realtime server:** Laravel Reverb
- **Frontend realtime client:** Laravel Echo
- **Database:** PostgreSQL 16 running directly on the VPS host
- **Host reverse proxy:** Host Nginx
- **Public admin URL:** `https://admin.sportynix.com`
- **Cloudflare:** DNS proxy + TLS
- **Broadcasting:** Reverb
- **Queue:** sync
- **Session:** database
- **Cache:** file

---

# 2. Production Project Path

Laravel project:

```bash
cd ~/apps/laravel/ib_laravel
```

Current production branch:

```text
minhaj-new
```

Check:

```bash
git branch --show-current
```

---

# 3. Important Production Files

## Environment

Real production env:

```text
.env.production
```

This file contains production secrets and **must never be committed**.

Verify Git ignores it:

```bash
git check-ignore -v .env.production
```

Expected output should show `.env.production` matched by `.gitignore`.

## Production example env

Safe template:

```text
.env.production.example
```

This file should contain variable names and placeholders only.

Never put real secrets inside it.

## Docker files

```text
Dockerfile
compose.yaml
.dockerignore

docker/
├── nginx/
│   ├── Dockerfile
│   └── default.conf
└── php/
    └── php.ini
```

---

# 4. Current Docker Services

The Laravel stack currently contains:

- `app`
- `reverb`
- `nginx`

Check:

```bash
cd ~/apps/laravel/ib_laravel
docker compose ps
```

Expected:

```text
app       running / healthy
reverb    running
nginx     running
```

---

# 5. Current Docker Network

Docker Compose network:

```text
sportynix_laravel_net
```

Inspect:

```bash
docker network inspect sportynix_laravel_net
```

---

# 6. Current Image Tag Workflow

Production images use immutable tags.

Example historical tags:

```text
c4b9a15
c4b9a15-reverb1
c4b9a15-reverb2
c4b9a15-reverb3
c4b9a15-reverb4
```

Do not hard-code old deployment tags permanently into source.

Current Compose pattern:

```text
sportynix-laravel:${IMAGE_TAG:-local}
sportynix-laravel-nginx:${IMAGE_TAG:-local}
```

If `IMAGE_TAG` is not exported, Compose may fall back to:

```text
:local
```

and Docker may attempt to pull a nonexistent image.

Always verify the tag before deploying.

---

# 7. Standard Manual Deployment Workflow

Use this when Laravel developers have pushed new code to GitHub.

## Step 1 — Enter project

```bash
cd ~/apps/laravel/ib_laravel
```

## Step 2 — Check Git status

```bash
git status
git branch --show-current
git log -1 --oneline
```

Production should ideally show:

```text
nothing to commit, working tree clean
```

If local changes exist, inspect first:

```bash
git diff
```

Do not pull blindly over local production changes.

---

# 8. Fetch Latest GitHub Changes

```bash
git fetch --prune origin
```

Check incoming commits:

```bash
git log --oneline --decorate --graph HEAD..origin/$(git branch --show-current)
```

Check changed files:

```bash
git diff --stat HEAD..origin/$(git branch --show-current)
```

---

# 9. Pull Safely

Preferred:

```bash
git pull --ff-only origin $(git branch --show-current)
```

Using `--ff-only` avoids accidental merge commits on production.

---

# 10. GitHub SSH Authentication

The production VPS normally uses a **read-only deploy key**.

Current read-only SSH alias:

```text
github-sportynix-laravel
```

Typical remote:

```text
git@github-sportynix-laravel:webxkey/IB-indoor.git
```

Check:

```bash
git remote -v
```

Test SSH:

```bash
ssh -T git@github-sportynix-laravel
```

---

# 11. Temporary Write Access From Production

A separate write-capable deploy key may exist for exceptional cases.

Write alias:

```text
github-sportynix-laravel-write
```

Typical temporary remote:

```text
git@github-sportynix-laravel-write:webxkey/IB-indoor.git
```

If an intentional server-side push is required:

```bash
git remote set-url origin \
git@github-sportynix-laravel-write:webxkey/IB-indoor.git
```

Verify:

```bash
git remote -v
```

Push:

```bash
git push origin minhaj-new
```

Then immediately restore read-only remote:

```bash
git remote set-url origin \
git@github-sportynix-laravel:webxkey/IB-indoor.git
```

Verify again:

```bash
git remote -v
```

Do not leave production permanently configured with write access unless explicitly required.

---

# 12. Production Reverb Architecture

Realtime communication has two distinct paths.

## Laravel server → Reverb inside Docker

```text
Laravel app
   ↓
reverb:8081
```

Production runtime values:

```dotenv
REVERB_HOST=reverb
REVERB_PORT=8081
REVERB_SCHEME=http
```

## Reverb server bind

```dotenv
REVERB_SERVER_HOST=0.0.0.0
REVERB_SERVER_PORT=8081
```

## Browser → public Reverb endpoint

Frontend build values:

```text
VITE_REVERB_HOST=admin.sportynix.com
VITE_REVERB_PORT=443
VITE_REVERB_SCHEME=https
VITE_REVERB_APP_KEY=<public Reverb app key>
```

Expected browser WebSocket URL:

```text
wss://admin.sportynix.com/app/<reverb-app-key>
```

The browser should **not** connect to:

```text
ws-.pusher.com
```

and the Laravel server should **not** broadcast to:

```text
0.0.0.0:8081
```

---

# 13. Important Reverb Configuration

`config/broadcasting.php` should use:

```php
'host' => env('REVERB_HOST', 'reverb'),
'port' => env('REVERB_PORT', 8081),
'scheme' => env('REVERB_SCHEME', 'http'),
'useTLS' => env('REVERB_SCHEME', 'http') === 'https',
```

`REVERB_SERVER_HOST` is for the Reverb server bind address, not Laravel's broadcast destination.

---

# 14. Before Building — Set Frontend Reverb Variables

Before building an image, ensure Vite values exist in the shell.

Safe public values:

```bash
export VITE_REVERB_HOST=admin.sportynix.com
export VITE_REVERB_PORT=443
export VITE_REVERB_SCHEME=https
```

Load the public Reverb app key from `.env.production` without printing it:

```bash
export VITE_REVERB_APP_KEY="$(
  grep '^REVERB_APP_KEY=' .env.production \
  | head -1 \
  | cut -d= -f2-
)"
```

Verify:

```bash
printf 'VITE_REVERB_HOST=%s\n' "$VITE_REVERB_HOST"
printf 'VITE_REVERB_PORT=%s\n' "$VITE_REVERB_PORT"
printf 'VITE_REVERB_SCHEME=%s\n' "$VITE_REVERB_SCHEME"

if [ -n "$VITE_REVERB_APP_KEY" ]; then
    echo "VITE_REVERB_APP_KEY=SET"
else
    echo "VITE_REVERB_APP_KEY=MISSING"
fi
```

Expected:

```text
VITE_REVERB_HOST=admin.sportynix.com
VITE_REVERB_PORT=443
VITE_REVERB_SCHEME=https
VITE_REVERB_APP_KEY=SET
```

Do not print the actual app key unnecessarily.

---

# 15. Choose a New Image Tag

Recommended:

```bash
export NEW_TAG=$(git rev-parse --short HEAD)
```

If additional suffixes are needed during migration/debugging, use a clear immutable suffix.

Example:

```bash
export NEW_TAG=$(git rev-parse --short HEAD)-reverb5
```

Check:

```bash
echo "$NEW_TAG"
```

---

# 16. Build Laravel Application Image

```bash
cd ~/apps/laravel/ib_laravel

docker build \
  --build-arg VITE_REVERB_APP_KEY="$VITE_REVERB_APP_KEY" \
  --build-arg VITE_REVERB_HOST="$VITE_REVERB_HOST" \
  --build-arg VITE_REVERB_PORT="$VITE_REVERB_PORT" \
  --build-arg VITE_REVERB_SCHEME="$VITE_REVERB_SCHEME" \
  -t sportynix-laravel:$NEW_TAG \
  .
```

Verify:

```bash
docker images | grep 'sportynix-laravel'
```

---

# 17. Build Laravel Nginx Image

Important: the build context must be:

```text
docker/nginx
```

because `docker/nginx/Dockerfile` contains:

```dockerfile
COPY default.conf /etc/nginx/conf.d/default.conf
```

Correct command:

```bash
docker build \
  --build-arg LARAVEL_IMAGE=sportynix-laravel:$NEW_TAG \
  -f docker/nginx/Dockerfile \
  -t sportynix-laravel-nginx:$NEW_TAG \
  docker/nginx
```

Do not use project root `.` as the build context for this Nginx image unless the Dockerfile is changed accordingly.

Verify:

```bash
docker images | grep "$NEW_TAG"
```

Expected both:

```text
sportynix-laravel:$NEW_TAG
sportynix-laravel-nginx:$NEW_TAG
```

---

# 18. Deploy New Image

Set the deployment tag:

```bash
export IMAGE_TAG="$NEW_TAG"
```

Verify Compose resolves the correct images:

```bash
docker compose config --images
```

Make sure it does **not** show:

```text
sportynix-laravel:local
```

Then recreate:

```bash
docker compose up -d --force-recreate app reverb
```

Then:

```bash
docker compose up -d --force-recreate nginx
```

---

# 19. Verify Containers

```bash
docker compose ps
```

Expected:

```text
app       healthy
reverb    running
nginx     running
```

Check resource usage:

```bash
docker stats --no-stream
```

---

# 20. Verify Laravel Runtime Reverb Configuration

```bash
docker compose exec app php -r '
require "vendor/autoload.php";
$app = require "bootstrap/app.php";
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "Broadcast driver: ".config("broadcasting.default").PHP_EOL;
echo "Reverb host: ".config("broadcasting.connections.reverb.options.host").PHP_EOL;
echo "Reverb port: ".config("broadcasting.connections.reverb.options.port").PHP_EOL;
echo "Reverb scheme: ".config("broadcasting.connections.reverb.options.scheme").PHP_EOL;
'
```

Expected:

```text
Broadcast driver: reverb
Reverb host: reverb
Reverb port: 8081
Reverb scheme: http
```

---

# 21. Test App → Reverb TCP Connectivity

```bash
docker compose exec app php -r '
$fp = @fsockopen("reverb", 8081, $errno, $errstr, 3);
echo $fp
    ? "Reverb TCP: CONNECTED\n"
    : "Reverb TCP: FAILED - $errno $errstr\n";
if ($fp) fclose($fp);
'
```

Expected:

```text
Reverb TCP: CONNECTED
```

---

# 22. Check Public Admin Site

```bash
curl -I https://admin.sportynix.com
```

Expected:

```text
HTTP/2 200
```

or expected redirect.

---

# 23. Browser Reverb Verification

Open browser developer console.

Expected:

```text
Laravel Echo initialized for real-time updates
Subscribing to public channel: bookings.complex.<id>
Subscribing to sport channel: slots.venue.<id>.sport.<id>
```

Expected WebSocket:

```text
wss://admin.sportynix.com/app/<key>
```

Successful WebSocket upgrade should appear as:

```text
101 Switching Protocols
```

---

# 24. View Laravel Logs

## App logs

```bash
cd ~/apps/laravel/ib_laravel

docker compose logs -f --tail=100 app
```

## Reverb logs

```bash
docker compose logs -f --tail=100 reverb
```

## Nginx logs

```bash
docker compose logs -f --tail=100 nginx
```

## All relevant services

```bash
docker compose logs -f --tail=100 app nginx reverb
```

Press:

```text
Ctrl + C
```

to stop following logs.

---

# 25. Laravel Application Log File

Inside the app container:

```bash
docker compose exec app sh -lc '
tail -n 150 storage/logs/laravel.log
'
```

Filter errors:

```bash
docker compose exec app sh -lc '
grep -Ei -A20 -B5 \
"ERROR|exception|webhook|broadcast|reverb|pusher" \
storage/logs/laravel.log | tail -n 200
'
```

---

# 26. Django → Laravel Webhook Troubleshooting

Webhook endpoint:

```text
POST /api/integration/webhooks/django-events
```

Expected public URL:

```text
https://admin.sportynix.com/api/integration/webhooks/django-events
```

Authentication uses:

```text
DJANGO_LARAVEL_WEBHOOK_SECRET
```

Laravel receives the secret through:

```text
config('services.django_webhook.secret')
```

and the environment variable:

```text
DJANGO_LARAVEL_WEBHOOK_SECRET
```

---

# 27. Verify Laravel Webhook Secret Without Printing It

```bash
docker compose exec app php -r '
require "vendor/autoload.php";
$app = require "bootstrap/app.php";
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$secret = config("services.django_webhook.secret", "");

echo "Laravel webhook secret configured: ".
     ($secret ? "YES" : "NO").PHP_EOL;

echo "Laravel webhook secret SHA256: ".
     ($secret ? hash("sha256", $secret) : "MISSING").
     PHP_EOL;
'
```

Do not paste the raw secret into logs or chat.

---

# 28. Webhook HTTP Status Meaning

## `401`

Likely:

- missing shared secret
- mismatched secret
- invalid signature
- invalid timestamp
- invalid source header

## `422`

Likely:

- invalid JSON
- missing required event fields
- unsupported event type
- invalid payload schema

## `500`

Likely:

- database issue
- Laravel processing issue
- Reverb broadcasting failure
- application exception

## `2xx`

Webhook accepted successfully.

---

# 29. Known Reverb Failure Pattern

If logs show:

```text
Pusher error: cURL error 7:
Failed to connect to 0.0.0.0 port 8081
```

then Laravel is incorrectly using:

```text
REVERB_SERVER_HOST
```

as the broadcast destination.

Correct runtime destination:

```text
reverb:8081
```

---

# 30. Known Frontend Reverb Failure Pattern

If browser attempts:

```text
wss://ws-.pusher.com/app/
```

then the frontend build likely did not receive valid Vite Reverb variables.

Check shell variables before rebuilding:

```bash
printf 'VITE_REVERB_HOST=%s\n' "$VITE_REVERB_HOST"
printf 'VITE_REVERB_PORT=%s\n' "$VITE_REVERB_PORT"
printf 'VITE_REVERB_SCHEME=%s\n' "$VITE_REVERB_SCHEME"

if [ -n "$VITE_REVERB_APP_KEY" ]; then
    echo "VITE_REVERB_APP_KEY=SET"
else
    echo "VITE_REVERB_APP_KEY=MISSING"
fi
```

Then rebuild frontend assets through the Docker image build.

---

# 31. Browser Cache After Frontend Deployment

After deploying a new frontend bundle, use:

```text
Ctrl + Shift + R
```

or an Incognito window.

A service worker may otherwise continue using an older JS bundle.

---

# 32. Host Nginx

The VPS host Nginx proxies:

```text
admin.sportynix.com
    ↓
127.0.0.1:8082
    ↓
Laravel Docker Nginx
```

Check:

```bash
sudo nginx -t
```

Status:

```bash
sudo systemctl status nginx --no-pager
```

Reload:

```bash
sudo systemctl reload nginx
```

Errors:

```bash
sudo tail -f /var/log/nginx/error.log
```

Access:

```bash
sudo tail -f /var/log/nginx/access.log
```

---

# 33. Laravel Docker Nginx Routing

The Docker Nginx currently proxies:

```text
/app
```

and:

```text
/apps
```

to:

```text
http://reverb:8081
```

and PHP requests to:

```text
app:9000
```

This routing is required for browser WebSocket connections and Reverb API events.

---

# 34. PostgreSQL

PostgreSQL runs on the Hostinger VPS, not inside Laravel Docker.

Database:

```text
indoor_booking
```

Laravel connects using:

```text
DB_CONNECTION=pgsql
DB_HOST=host.docker.internal
DB_PORT=5432
```

Do not expose PostgreSQL publicly as a quick troubleshooting step.

---

# 35. Verify Database Connectivity

Inside Laravel:

```bash
docker compose exec app php artisan about
```

For a lightweight database test:

```bash
docker compose exec app php artisan tinker
```

Then:

```php
DB::select('select current_database(), now()');
```

Exit:

```php
exit
```

---

# 36. Database Migrations

Before running:

```bash
docker compose exec app php artisan migrate
```

first inspect:

```bash
docker compose exec app php artisan migrate:status
```

Do not run production migrations blindly.

Before risky schema migrations, create a PostgreSQL backup.

Example:

```bash
sudo -u postgres pg_dump \
  -Fc \
  -d indoor_booking \
  -f /var/backups/indoor_booking_predeploy_$(date +%Y%m%d_%H%M%S).dump
```

---

# 37. Laravel Cache Commands

Inspect:

```bash
docker compose exec app php artisan about
```

If configuration must be cleared intentionally:

```bash
docker compose exec app php artisan config:clear
```

Clear general optimization caches:

```bash
docker compose exec app php artisan optimize:clear
```

Do not run cache-clearing commands casually if the deployment process intentionally relies on cached configuration.

---

# 38. Verify Effective Container Environment

Do not print secrets.

Example:

```bash
docker compose exec app sh -c '
env | grep -E "^REVERB_(HOST|PORT|SCHEME|SERVER_HOST|SERVER_PORT)="
'
```

Expected:

```text
REVERB_HOST=reverb
REVERB_PORT=8081
REVERB_SCHEME=http
REVERB_SERVER_HOST=0.0.0.0
REVERB_SERVER_PORT=8081
```

---

# 39. Verify Effective Compose Configuration

```bash
export IMAGE_TAG=<current-tag>

docker compose config --images
```

Inspect Reverb env:

```bash
docker compose config | grep -n -A8 -B8 'REVERB_HOST'
```

This is useful when `.env.production` appears correct but the running container behaves differently.

---

# 40. Restart Individual Services

App:

```bash
docker compose restart app
```

Reverb:

```bash
docker compose restart reverb
```

Nginx:

```bash
docker compose restart nginx
```

---

# 41. Recreate Individual Services

App:

```bash
docker compose up -d --no-deps --force-recreate app
```

Reverb:

```bash
docker compose up -d --no-deps --force-recreate reverb
```

Nginx:

```bash
docker compose up -d --no-deps --force-recreate nginx
```

---

# 42. Do Not Use `down -v`

Never casually run:

```bash
docker compose down -v
```

because `-v` removes named volumes.

Current named volumes include:

```text
laravel_storage
laravel_framework
laravel_logs
```

---

# 43. Laravel Storage

Current Compose uses named volumes for:

```text
storage/app/public
storage/framework
storage/logs
```

The Nginx container mounts:

```text
laravel_storage
```

read-only for `/storage/` serving.

Be careful with volume deletion or recreation.

---

# 44. Cloudflare

Public admin:

```text
admin.sportynix.com
```

Cloudflare SSL mode should remain:

```text
Full (strict)
```

The browser connects through Cloudflare, while host Nginx handles origin traffic.

Do not expose the Cloudflare Origin CA private key.

---

# 45. WebSocket Health Check

Browser:

```text
wss://admin.sportynix.com/app/<key>
```

Host Nginx:

```text
admin.sportynix.com
    ↓
127.0.0.1:8082
```

Docker Nginx:

```text
/app
    ↓
reverb:8081
```

Expected successful handshake:

```text
101 Switching Protocols
```

---

# 46. Common 502 Troubleshooting

If admin returns `502`:

Check:

```bash
docker compose ps
```

Then:

```bash
docker compose logs --tail=100 app nginx reverb
```

Check host Nginx:

```bash
sudo tail -n 100 /var/log/nginx/error.log
```

If the `app` container was recreated, Nginx may need recreation/reload if upstream resolution became stale.

---

# 47. Common `sportynix-laravel:local` Error

If Docker shows:

```text
pull access denied for sportynix-laravel
```

and mentions:

```text
sportynix-laravel:local
```

then `IMAGE_TAG` was not exported.

Fix:

```bash
export IMAGE_TAG=<known-existing-tag>
```

Verify:

```bash
docker compose config --images
```

Then deploy.

---

# 48. Docker Build Context Error

If Nginx build fails with:

```text
"/default.conf": not found
```

the build context is wrong.

Correct:

```bash
docker build \
  --build-arg LARAVEL_IMAGE=sportynix-laravel:$NEW_TAG \
  -f docker/nginx/Dockerfile \
  -t sportynix-laravel-nginx:$NEW_TAG \
  docker/nginx
```

The final argument must be:

```text
docker/nginx
```

---

# 49. Docker Commands

List running containers:

```bash
docker ps
```

All containers:

```bash
docker ps -a
```

Images:

```bash
docker images
```

Disk usage:

```bash
docker system df
```

Inspect:

```bash
docker inspect <container>
```

---

# 50. Docker Cleanup

Inspect before cleaning:

```bash
docker system df
```

Do not casually run:

```bash
docker system prune -a
```

Keep:

- current production image
- at least one known-good rollback image

---

# 51. Rollback Strategy

If a new release fails:

1. Identify the previous working image tag.
2. Export it.
3. Recreate containers with the previous image.
4. Evaluate database migration compatibility separately.
5. Re-test admin, Reverb, and webhook flow.

Example:

```bash
export IMAGE_TAG=<previous-working-tag>

docker compose up -d --force-recreate app reverb
docker compose up -d --force-recreate nginx
```

Never blindly roll back database schema changes.

---

# 52. Git Safety Rules

Do not run these casually on production:

```bash
git reset --hard
git clean -fd
git restore .
git checkout .
```

Never:

```bash
git push --force
```

Preferred pull:

```bash
git pull --ff-only
```

---

# 53. Safe Secret Handling

Never commit or print:

```text
.env.production
APP_KEY
DB_PASSWORD
REVERB_APP_SECRET
DJANGO_LARAVEL_WEBHOOK_SECRET
mail credentials
payment credentials
private SSH keys
Cloudflare Origin private key
GitHub PATs
```

The Reverb **app key** is browser-visible and used by the frontend, but the Reverb **app secret** must remain private.

---

# 54. Check Repository for Secrets Before Commit

Example:

```bash
grep -RniE \
'(DB_PASSWORD|APP_KEY|REVERB_APP_SECRET|DJANGO_LARAVEL_WEBHOOK_SECRET|MAIL_PASSWORD|API_KEY|TOKEN)[=:][^$<{[:space:]]+' \
Dockerfile compose.yaml docker .env.production.example config/broadcasting.php \
2>/dev/null || true
```

Review matches manually.

---

# 55. Recommended Commit Files

Normally commit:

```text
application source
config/broadcasting.php
Dockerfile
compose.yaml
docker/
.dockerignore
.env.production.example
```

Do not commit:

```text
.env.production
real secrets
runtime logs
private keys
database dumps
temporary deployment files
```

---

# 56. GitHub Actions — Current Status

CI/CD is currently not required.

Deployment is manual.

Future CI/CD could automate:

```text
push
  ↓
tests
  ↓
build Laravel image
  ↓
build frontend assets
  ↓
build Nginx image
  ↓
push images to registry
  ↓
deploy to VPS
  ↓
health checks
```

Until then, use the manual commands in this document.

---

# 57. If GitHub Actions Is Added Later

Workflow location:

```text
.github/workflows/
```

List:

```bash
find .github/workflows -maxdepth 1 -type f -print 2>/dev/null
```

Never hard-code production secrets in YAML.

Use GitHub repository/environment secrets.

---

# 58. Browser Verification Checklist

After deployment:

```text
[ ] admin.sportynix.com loads
[ ] login works
[ ] booking pages load
[ ] Livewire works
[ ] WebSocket connects to admin.sportynix.com
[ ] Reverb returns 101 Switching Protocols
[ ] bookings.complex.* subscription succeeds
[ ] slots.venue.*.sport.* subscription succeeds
[ ] slot hold updates in real time
[ ] slot release updates in real time
[ ] booking creation updates in real time
```

---

# 59. Django Integration Verification Checklist

```text
[ ] Django webhook reaches Laravel
[ ] webhook returns 2xx
[ ] no 401 secret mismatch
[ ] no 422 payload validation issue
[ ] no 500 Reverb/broadcast exception
[ ] Laravel broadcasts event
[ ] browser receives event
[ ] admin UI updates immediately
```

---

# 60. Production Release Checklist

Before deployment:

```text
[ ] Git working tree clean
[ ] Correct branch
[ ] Incoming commits reviewed
[ ] .env.production present
[ ] IMAGE_TAG / NEW_TAG selected
[ ] VITE_REVERB_* variables set
[ ] Reverb app key loaded safely
[ ] Laravel image builds
[ ] Nginx image builds
[ ] Docker images verified
[ ] migrations reviewed
[ ] DB backup created if migration is risky
```

After deployment:

```text
[ ] app healthy
[ ] reverb running
[ ] nginx running
[ ] admin site returns 200
[ ] Laravel runtime host = reverb
[ ] Reverb TCP = CONNECTED
[ ] browser WebSocket uses admin.sportynix.com
[ ] WebSocket returns 101
[ ] Django webhook returns 2xx
[ ] real-time booking/slot updates work
[ ] Laravel logs clean
```

---

# 61. Quick Deployment Command Reference

```bash
cd ~/apps/laravel/ib_laravel

git status
git fetch --prune origin
git pull --ff-only origin $(git branch --show-current)

export VITE_REVERB_HOST=admin.sportynix.com
export VITE_REVERB_PORT=443
export VITE_REVERB_SCHEME=https

export VITE_REVERB_APP_KEY="$(
  grep '^REVERB_APP_KEY=' .env.production \
  | head -1 \
  | cut -d= -f2-
)"

export NEW_TAG=$(git rev-parse --short HEAD)

docker build \
  --build-arg VITE_REVERB_APP_KEY="$VITE_REVERB_APP_KEY" \
  --build-arg VITE_REVERB_HOST="$VITE_REVERB_HOST" \
  --build-arg VITE_REVERB_PORT="$VITE_REVERB_PORT" \
  --build-arg VITE_REVERB_SCHEME="$VITE_REVERB_SCHEME" \
  -t sportynix-laravel:$NEW_TAG \
  .

docker build \
  --build-arg LARAVEL_IMAGE=sportynix-laravel:$NEW_TAG \
  -f docker/nginx/Dockerfile \
  -t sportynix-laravel-nginx:$NEW_TAG \
  docker/nginx

export IMAGE_TAG="$NEW_TAG"

docker compose config --images

docker compose up -d --force-recreate app reverb
docker compose up -d --force-recreate nginx

docker compose ps

curl -I https://admin.sportynix.com
```

---

# 62. Quick Log Reference

App:

```bash
docker compose logs -f --tail=100 app
```

Reverb:

```bash
docker compose logs -f --tail=100 reverb
```

Nginx:

```bash
docker compose logs -f --tail=100 nginx
```

All:

```bash
docker compose logs -f --tail=100 app nginx reverb
```

Laravel file log:

```bash
docker compose exec app sh -lc '
tail -n 150 storage/logs/laravel.log
'
```

---

# 63. Final Notes for Developers

The production design intentionally separates:

```text
Browser → Cloudflare → host Nginx → Docker Nginx → Reverb
```

from:

```text
Laravel app → reverb:8081
```

and:

```text
Django → Laravel webhook → Reverb → browser
```

When troubleshooting, verify one layer at a time.

Recommended order:

```text
Git
  ↓
Docker image
  ↓
Container environment
  ↓
Laravel config
  ↓
Reverb TCP
  ↓
Docker Nginx
  ↓
Host Nginx
  ↓
Cloudflare
  ↓
Browser WebSocket
  ↓
Django webhook
```

Do not change multiple infrastructure layers at once unless the failure clearly requires it.
