# Farmer's Basket — Hostinger Deployment Guide

---

## 1. Prerequisites (Local Machine)

Before uploading, run these commands on your local machine:

```bash
# Install production dependencies only
composer install --no-dev --optimize-autoloader

# Build frontend assets
npm run build
```

Make sure the `public/build/` folder is generated.

---

## 2. Hostinger Panel — Before Upload

### 2a. Create MySQL Database

1. Log in to **Hostinger hPanel**
2. Go to **Databases → MySQL Databases**
3. Create a new database, e.g. `farmersbasket`
4. Create a database user and assign it to the database
5. Note down:
   - Database name
   - Database username
   - Database password
   - Host (usually `127.0.0.1` or `localhost`)

### 2b. Set Document Root to `/public`

> **This is the most important step.** Laravel must serve from the `public/` folder, not the root.

**Option A — Subdomain (recommended):**
1. Go to **Domains → Subdomains** (or **Websites → Manage**)
2. Create a subdomain, e.g. `pos.yourdomain.com`
3. Set its document root to: `public_html/farmersbasket/public`

**Option B — Main domain:**
1. Go to **Hosting → Manage → Website → File Manager**
2. Upload everything to a folder like `public_html/farmersbasket/`
3. Go to **Advanced → Custom PHP Configuration** or **Hosting Settings**
4. Set the webroot / public_html to point at `public_html/farmersbasket/public`

> On Hostinger Business/Premium shared hosting you can change the domain's webroot under:  
> **hPanel → Websites → Manage → PHP Configuration → Document Root**

---

## 3. Upload Files

### Option A — File Manager (Hostinger)
1. Zip your entire project folder (exclude `node_modules/` and `.git/`)
2. Open **File Manager** in hPanel
3. Navigate to `public_html/`
4. Upload and extract the zip into a folder named `farmersbasket`

### Option B — FTP (FileZilla)
```
Host:     your-domain.com
Username: Your FTP username (from hPanel → FTP Accounts)
Password: Your FTP password
Port:     21
```
Upload the project to `public_html/farmersbasket/`

### What to exclude from upload:
```
node_modules/
.git/
.env              ← You will create this manually on the server
storage/logs/*
```

---

## 4. Configure `.env` on Server

In **File Manager**, navigate to `public_html/farmersbasket/` and create a new file named `.env`.

Paste and fill in the values below:

```env
APP_NAME="Farmer's Basket"
APP_ENV=production
APP_KEY=                         # Generate this — see Step 5
APP_DEBUG=false
APP_URL=https://yourdomain.com

APP_LOCALE=en
APP_FALLBACK_LOCALE=en
APP_FAKER_LOCALE=en_US

LOG_CHANNEL=stack
LOG_LEVEL=error

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=your_database_name
DB_USERNAME=your_database_user
DB_PASSWORD=your_database_password
DB_COLLATION=utf8mb4_unicode_ci

SESSION_DRIVER=database
SESSION_LIFETIME=120
SESSION_PATH=/
SESSION_DOMAIN=null

QUEUE_CONNECTION=database
CACHE_STORE=database
FILESYSTEM_DISK=local

MAIL_MAILER=smtp
MAIL_HOST=smtp.hostinger.com
MAIL_PORT=465
MAIL_USERNAME=it@speedtrackauto.com
MAIL_PASSWORD=Saudi@78600!
MAIL_ENCRYPTION=ssl
MAIL_FROM_ADDRESS="it@speedtrackauto.com"
MAIL_FROM_NAME="Farmer's Basket"
MAIL_NOTIFICATIONS_ENABLED=true

WHATSAPP_ENABLED=true
WHATSAPP_API_URL=https://7107.api.greenapi.com
WHATSAPP_INSTANCE_ID=7107581920
WHATSAPP_API_TOKEN=640315cee1e94f038569d889b11f2c55ce3175c27a5248528e
```

> **Note:** On live server, `MAIL_ENCRYPTION` should be `ssl` for port 465.  
> `APP_DEBUG=false` is mandatory on production — never expose debug info publicly.

---

## 5. Run Artisan Commands via SSH

### Connect via SSH
```
Host:     your-domain.com
Port:     22
Username: Your SSH username (from hPanel → SSH Access)
```

Enable SSH in hPanel: **Advanced → SSH Access → Enable**

```bash
# Navigate to your project
cd public_html/farmersbasket

# Generate app key
php artisan key:generate

# Run all database migrations
php artisan migrate --force

# Optimize for production
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Set storage permissions
chmod -R 775 storage bootstrap/cache
```

---

## 6. If SSH is Not Available (Shared Hosting Workaround)

Create a temporary file `artisan_run.php` in the project root:

```php
<?php
// IMPORTANT: Delete this file immediately after use!
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);

$command = $_GET['cmd'] ?? 'list';
$kernel->call($command);
echo '<pre>' . htmlspecialchars(implode("\n", $kernel->output() ? [$kernel->output()] : [])) . '</pre>';
```

Access via browser (replace with your actual URL):
```
https://yourdomain.com/artisan_run.php?cmd=key:generate
https://yourdomain.com/artisan_run.php?cmd=migrate --force
https://yourdomain.com/artisan_run.php?cmd=config:cache
https://yourdomain.com/artisan_run.php?cmd=route:cache
https://yourdomain.com/artisan_run.php?cmd=view:cache
```

> ⚠️ **Delete `artisan_run.php` immediately after running all commands.**

---

## 7. Storage Link

Run this once to link `storage/app/public` to `public/storage`:

```bash
# Via SSH
php artisan storage:link

# OR via artisan_run.php
https://yourdomain.com/artisan_run.php?cmd=storage:link
```

---

## 8. File Permissions

```bash
# Via SSH
find storage -type f -exec chmod 664 {} \;
find storage -type d -exec chmod 775 {} \;
find bootstrap/cache -type f -exec chmod 664 {} \;
find bootstrap/cache -type d -exec chmod 775 {} \;
```

---

## 9. Queue Worker (for background jobs)

The project uses `QUEUE_CONNECTION=database`. Set up a cron job in Hostinger:

**hPanel → Advanced → Cron Jobs → Add New Cron Job**

```
Command:  php /home/username/public_html/farmersbasket/artisan queue:work --sleep=3 --tries=3 --max-time=3600
Schedule: Every minute  (* * * * *)
```

> If cron jobs are not available on your plan, notifications will still work synchronously — they just run inline with each request instead of in the background.

---

## 10. Test Email on Live Server

Create a temporary file `test_mail.php` in the project root:

```php
<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\Mail;

try {
    Mail::raw(
        "Test email from Farmer's Basket live server.",
        fn($m) => $m->to('it@speedtrackauto.com')->subject('Live Server Test — Farmer\'s Basket')
    );
    echo "SUCCESS: Email sent!";
} catch (\Exception $e) {
    echo "FAILED: " . $e->getMessage();
}
```

Visit: `https://yourdomain.com/test_mail.php`

> ⚠️ **Delete `test_mail.php` immediately after testing.**

---

## 11. Test WhatsApp on Live Server

Create a temporary file `test_whatsapp.php` in the project root:

```php
<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\Http;

$apiUrl   = config('services.whatsapp.api_url');
$instance = config('services.whatsapp.instance');
$token    = config('services.whatsapp.token');

$url = "{$apiUrl}/waInstance{$instance}/sendMessage/{$token}";

$response = Http::post($url, [
    'chatId'  => '923017147110@c.us',   // ← Change to your test number
    'message' => "Test WhatsApp message from Farmer's Basket live server. ✅",
]);

echo "Status: " . $response->status() . "\n";
echo "Response: " . $response->body();
```

Visit: `https://yourdomain.com/test_whatsapp.php`

> ⚠️ **Delete `test_whatsapp.php` immediately after testing.**

---

## 12. Post-Deployment Checklist

| Step | Done |
|------|------|
| Database created in hPanel | ☐ |
| `.env` file created with correct DB credentials | ☐ |
| `APP_KEY` generated (`php artisan key:generate`) | ☐ |
| Migrations ran (`php artisan migrate --force`) | ☐ |
| `APP_DEBUG=false` in `.env` | ☐ |
| Document root pointing to `/public` | ☐ |
| Config/route/view cache cleared | ☐ |
| Storage permissions set (775) | ☐ |
| Test email received | ☐ |
| Test WhatsApp received | ☐ |
| All temporary test files deleted | ☐ |
| Admin login works | ☐ |
| POS login works | ☐ |
| Place a test order | ☐ |

---

## 13. Common Issues

| Problem | Fix |
|---------|-----|
| White screen / 500 error | Set `APP_DEBUG=true` temporarily, check `storage/logs/laravel.log` |
| `No application encryption key` | Run `php artisan key:generate` |
| Database connection refused | Check `DB_HOST`, `DB_USERNAME`, `DB_PASSWORD` in `.env` |
| CSS/JS not loading | Make sure `npm run build` was run locally and `public/build/` was uploaded |
| Email not sending | Check port 465 uses `MAIL_ENCRYPTION=ssl` (not `tls`) |
| Images not showing | Check `public/assets/` and `public/images/` were uploaded |
| Session expired on every request | Check `SESSION_DRIVER=database` and sessions table exists |
| `chmod` permission errors | Contact Hostinger support or use File Manager to set folder permissions |

---

*Generated for Farmer's Basket — Hostinger Deployment*
