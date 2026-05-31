# Deploying Farmers Basket (Laravel 11) on Hostinger Business Shared Hosting

---

## Prerequisites
- Hostinger Business plan (includes SSH access)
- Domain pointed to Hostinger nameservers
- Local project working and fully tested

---

## Step 1 — Prepare the Project Locally

**1.1 Build production frontend assets**
```bash
npm run build
```
This generates `public/build/` — make sure this folder exists before uploading.

**1.2 Clear all local caches**
```bash
php artisan config:clear
php artisan cache:clear
php artisan view:clear
php artisan route:clear
```

**1.3 Create a deployment ZIP**

Zip the entire project folder and **exclude** the following:
```
vendor/
node_modules/
.git/
.env
storage/logs/*.log
```
Include `public/build/` since you just ran `npm run build`.

---

## Step 2 — Configure Hostinger hPanel

**2.1 Log in to hPanel**

Go to [hpanel.hostinger.com](https://hpanel.hostinger.com) → click **Manage** next to your hosting plan.

**2.2 Set PHP version to 8.2 or higher**
```
hPanel → Advanced → PHP Configuration
→ Select PHP 8.2 (or 8.3)
→ Ensure these extensions are enabled:
   ✓ pdo_mysql
   ✓ mbstring
   ✓ openssl
   ✓ tokenizer
   ✓ xml
   ✓ ctype
   ✓ json
   ✓ bcmath
   ✓ fileinfo
   ✓ gd
→ Save
```

**2.3 Create a MySQL database**
```
hPanel → Databases → MySQL Databases
→ Create new database
   Database name:  farmersbasket_db
   Username:       farmersbasket_user
   Password:       (generate a strong password and save it)
→ Create
```

> Hostinger automatically prefixes names with your account ID.
> Example results:
>   Database: u123456789_farmersbasket_db
>   Username: u123456789_farmersbasket_user
>
> Write these down — you will need them in your .env file.

**2.4 Enable SSH access**
```
hPanel → Advanced → SSH Access
→ Enable SSH
→ Note down:
   Host:     your-domain.com
   Port:     22  (or 65002 on some Hostinger plans)
   Username: u123456789
   Password: your hPanel account password
```

---

## Step 3 — Upload Project Files

**3.1 Understand the server directory layout**

```
/home/u123456789/
├── public_html/          ← default web root (what the browser sees)
└── farmersbasket/        ← create this folder (NOT inside public_html)
    ├── app/
    ├── bootstrap/
    ├── config/
    ├── database/
    ├── public/           ← this becomes the new web root
    ├── resources/
    ├── routes/
    ├── storage/
    └── ...
```

**3.2 Upload via File Manager**
```
hPanel → Files → File Manager
→ Navigate to /home/u123456789/  (one level above public_html)
→ Create new folder: farmersbasket
→ Upload your ZIP file into farmersbasket/
→ Right-click the ZIP → Extract
→ If the ZIP created a nested subfolder, move its contents up one level
  so that app/, public/, routes/ etc. are directly inside farmersbasket/
```

**3.3 Upload via FTP (FileZilla)**
```
Host:     ftp.your-domain.com
Username: u123456789
Password: your hPanel password
Port:     21
```
Connect and drag the project folder into `/home/u123456789/farmersbasket/`.

---

## Step 4 — Point the Domain Web Root to public/

This is the most critical step. Laravel must be served from `farmersbasket/public/`, not from the project root.

**4.1 Change the document root in hPanel**
```
hPanel → Websites → your domain → Manage
→ Look for "Document Root" or "Website Root"
→ Change from: public_html
→ Change to:   farmersbasket/public
→ Save
```

**4.2 Alternative — if document root cannot be changed**

If hPanel does not allow changing the document root, use this workaround instead:

1. Leave your Laravel files in `farmersbasket/` (outside `public_html`)
2. Delete the default contents of `public_html/`
3. Copy **only the contents** of `farmersbasket/public/` into `public_html/`
   (not the folder itself — just what is inside it)
4. Edit `public_html/index.php` and update the two require lines:

```php
// BEFORE (default Laravel paths):
require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';

// AFTER (corrected paths for this shared hosting layout):
require __DIR__.'/../../farmersbasket/vendor/autoload.php';
$app = require_once __DIR__.'/../../farmersbasket/bootstrap/app.php';
```

5. The `public_html/.htaccess` copied from Laravel is already correct — do not change it.

---

## Step 5 — Install Composer Dependencies via SSH

```bash
# 1. Connect to the server
ssh u123456789@your-domain.com -p 22

# 2. Navigate to the project directory
cd ~/farmersbasket

# 3. Confirm the PHP version is 8.2 or higher
php -v

# 4. Download Composer
curl -sS https://getcomposer.org/installer | php

# 5. Install all production dependencies
php composer.phar install --no-dev --optimize-autoloader
```

> If Composer is already available globally on the server, skip step 4 and
> use `composer install` instead of `php composer.phar install`.
> Check with: `which composer`

---

## Step 6 — Configure the .env File

```bash
# In the SSH session, still inside ~/farmersbasket
cp .env.example .env
nano .env
```

Replace all values with your production configuration:

```dotenv
APP_NAME="Farmers Basket"
APP_ENV=production
APP_KEY=
APP_DEBUG=false
APP_URL=https://your-domain.com

LOG_CHANNEL=stack
LOG_LEVEL=error

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=u123456789_farmersbasket_db
DB_USERNAME=u123456789_farmersbasket_user
DB_PASSWORD=your_strong_db_password_here

CACHE_DRIVER=file
SESSION_DRIVER=file
QUEUE_CONNECTION=sync

MAIL_MAILER=smtp
MAIL_HOST=smtp.hostinger.com
MAIL_PORT=465
MAIL_USERNAME=no-reply@your-domain.com
MAIL_PASSWORD=your_email_password
MAIL_ENCRYPTION=ssl
MAIL_FROM_ADDRESS=no-reply@your-domain.com
MAIL_FROM_NAME="Farmers Basket"
```

Save and exit: press `Ctrl+X`, then `Y`, then `Enter`.

**Generate the application encryption key:**
```bash
php artisan key:generate
```

> This fills in APP_KEY automatically in your .env file.

---

## Step 7 — Set Directory Permissions

```bash
cd ~/farmersbasket

# Allow the web server to write to storage and cache
chmod -R 775 storage
chmod -R 775 bootstrap/cache

# Set correct ownership
chown -R u123456789:u123456789 storage
chown -R u123456789:u123456789 bootstrap/cache
```

---

## Step 8 — Run Migrations

```bash
cd ~/farmersbasket

# Create all database tables
php artisan migrate --force

# Optional: seed the database with initial data
php artisan db:seed --force
```

> The `--force` flag is required when APP_ENV=production to confirm
> you want to modify the live database.

---

## Step 9 — Create the Storage Symlink

Laravel stores uploaded files (product images, payment receipts, etc.) in
`storage/app/public` and serves them through `public/storage`. You must
create this symlink once:

```bash
cd ~/farmersbasket
php artisan storage:link
```

If the command fails due to shared hosting restrictions, create it manually:

```bash
ln -s ~/farmersbasket/storage/app/public ~/farmersbasket/public/storage
```

---

## Step 10 — Cache Configuration for Performance

```bash
cd ~/farmersbasket

php artisan config:cache
php artisan route:cache
php artisan view:cache
```

---

## Step 11 — Enable SSL / HTTPS

```
hPanel → Websites → your domain → Manage
→ Security → SSL
→ Install Free SSL (Let's Encrypt)
→ Force HTTPS redirect: Enable
```

After SSL is active, update your .env:
```dotenv
APP_URL=https://your-domain.com
```

Re-run config cache to apply the change:
```bash
cd ~/farmersbasket
php artisan config:cache
```

---

## Step 12 — Upload Existing Images and Assets

If your local project already has uploaded images, transfer them to the
server via FTP so they appear on the live site.

| What                          | Local path                      | Remote path                                    |
|-------------------------------|---------------------------------|------------------------------------------------|
| Product / brand images        | `public/assets/`                | `~/farmersbasket/public/assets/`               |
| Category / slide images       | `public/uploads/`               | `~/farmersbasket/public/uploads/`              |
| Payment receipts / user files | `storage/app/public/`           | `~/farmersbasket/storage/app/public/`          |

---

## Step 13 — Set Up a Cron Job (Queue Worker)

The application uses database queues for background jobs. Set up a cron job
in hPanel to keep the queue worker running:

```
hPanel → Advanced → Cron Jobs → Add New Cron Job

Command:
php /home/u123456789/farmersbasket/artisan queue:work --sleep=3 --tries=3 --max-time=3600

Schedule: Every minute  (* * * * *)
```

> If your plan does not support cron jobs, set QUEUE_CONNECTION=sync in
> your .env — jobs will run inline with each request instead.

---

## Step 14 — Verify the Deployment

Open `https://your-domain.com` in your browser and test each area:

- [ ] Homepage loads and displays products
- [ ] Product images display correctly
- [ ] Customer registration works
- [ ] Customer login and logout work
- [ ] Add to cart and checkout work
- [ ] Admin login works at `/admin`
- [ ] Admin can add/edit products and categories
- [ ] POS system loads at `/pos`
- [ ] POS order placement works
- [ ] Receipt prints correctly
- [ ] Payment receipts upload correctly
- [ ] Courier selection appears on POS delivery orders

---

## Future Deployments (Updating the Live Site)

After the initial setup, use this workflow for every code update:

```bash
# 1. On your local machine — build new assets if frontend changed
npm run build

# 2. SSH into the server
ssh u123456789@your-domain.com -p 22
cd ~/farmersbasket

# 3a. If using Git — pull latest code
git pull origin main

# 3b. If uploading manually — FTP the changed files to the server

# 4. If composer.json changed — update dependencies
php composer.phar install --no-dev --optimize-autoloader

# 5. If new migration files exist — run them
php artisan migrate --force

# 6. If frontend assets changed — re-upload public/build/ via FTP

# 7. Always clear and re-cache after any code change
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

---

## Common Issues and Fixes

| Problem | Fix |
|---|---|
| White screen / 500 error | Temporarily set `APP_DEBUG=true`, reload the page, read the error, then set back to `false` |
| "No application encryption key" | Run `php artisan key:generate` |
| Images not showing | Run `php artisan storage:link` or create the symlink manually |
| Database connection refused | Double-check DB credentials in `.env` — Hostinger prefixes names with your account ID |
| "Class not found" | Run `php composer.phar dump-autoload` |
| Migrations fail | Confirm the database exists in hPanel and credentials match exactly |
| CSS and JS not loading | Run `npm run build` locally and re-upload `public/build/` via FTP |
| Sessions reset on every page | Set `SESSION_DRIVER=file` and verify `storage/framework/sessions/` has `775` permissions |
| `.htaccess` not working | In hPanel → Advanced → enable `mod_rewrite` support |
| 403 Forbidden | Files should be `644`, directories `755` — fix via File Manager |
| Admin redirects to login repeatedly | Clear browser cookies and ensure `APP_URL` matches your domain exactly (with or without www) |
| Uploaded images give 404 | Re-run `php artisan storage:link` — the symlink may be missing |

---

## Security Checklist Before Going Live

- [ ] `APP_DEBUG=false` in `.env`
- [ ] `APP_ENV=production` in `.env`
- [ ] `.env` is NOT accessible from the browser — test this: `https://your-domain.com/.env` must return a 404 or blank page, never the file contents
- [ ] `APP_KEY` is set to a generated value and kept secret
- [ ] SSL certificate is active and HTTPS is enforced for all pages
- [ ] Strong unique password set for the database user
- [ ] Default admin credentials have been changed
- [ ] `storage/` directory is not directly browseable from the web
- [ ] All temporary test files (`test_mail.php`, `artisan_run.php`, etc.) have been deleted
