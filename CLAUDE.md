# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Commands

### Development
```bash
# Start both servers (run in separate terminals)
php artisan serve          # Laravel backend on http://127.0.0.1:8000
npm run dev                # Vite frontend asset bundler (hot reload)
```

### Building
```bash
npm run build              # Production JS/CSS bundle
```

### Testing
```bash
./vendor/bin/phpunit                          # Run all tests
./vendor/bin/phpunit tests/Unit/ExampleTest   # Run a single test file
./vendor/bin/phpunit --filter testMethodName  # Run a single test by name
```

### Linting / Formatting
```bash
./vendor/bin/pint          # Format PHP code (Laravel Pint)
./vendor/bin/pint --test   # Dry-run, reports issues without fixing
```

### Database
```bash
php artisan migrate              # Run pending migrations
php artisan migrate:fresh        # Drop all tables and re-run all migrations
php artisan migrate:fresh --seed # Fresh migration + seed data
php artisan db:seed              # Seed without resetting
php artisan tinker               # Interactive Eloquent REPL
```

### Initial Setup
```bash
composer install
npm install
cp .env.example .env
php artisan key:generate
php artisan migrate
```

## Architecture

**Farmers Basket** is a monolithic Laravel 11 MVC e-commerce application.

### Key Layers

- **Routes** — All web routes in `routes/web.php` (~99+ named routes). No API routes.
- **Controllers** — `app/Http/Controllers/`. Core controllers: `HomeController`, `ShopController`, `CartController`, `AdminController`, `UserController`, `WishlistController`. Auth controllers in `Auth/` subdirectory.
- **Models** — `app/Models/`. 13 Eloquent models: `Product`, `Category`, `Brand`, `Order`, `OrderItem`, `Transaction`, `User`, `Address`, `ProductSize`, `Coupon`, `Slide`, `Contacts`, `MonthName`.
- **Views** — `resources/views/` Blade templates. Layouts: `layouts/app.blade.php` (frontend) and `layouts/admin.blade.php` (admin panel). Admin views in `admin/` subdirectory, user views in `user/`.

### Domain Concepts

- **Products** have `Category`, `Brand`, and multiple `ProductSize` records (each size has its own price).
- **Cart** is handled by the `surfsidemedia/shoppingcart` package (session-based). Config at `config/cart.php`.
- **Orders** go through statuses: ordered → delivered / canceled. Each `Order` has many `OrderItem`s and one `Transaction`.
- **Admin middleware** — `app/Http/Middleware/AuthAdmin.php` gates the entire `/admin/*` route group; checks `users.utype === 'ADM'`.
- **Coupons** apply percentage discounts; validated at checkout in `CartController`.
- **Slides** are homepage carousel banners managed from the admin panel.
- **MonthName** and revenue aggregation are used for the admin dashboard analytics chart.

### Frontend

- Bootstrap 5.2 + custom SASS at `resources/sass/app.scss`.
- Vite bundles `resources/js/app.js` and `resources/sass/app.scss` → `public/build/`.
- Axios is available globally for AJAX; used in cart/wishlist interactions.
- A POS (Point of Sale) page exists at `resources/views/pos.blade.php`.

### Database

MySQL. Connection configured in `.env` (`DB_CONNECTION=mysql`). Migrations are in `database/migrations/`. The schema includes a `transections` table (note the typo — keep it as-is to match the migration and model).

### Image Storage

Product, brand, and slide images are stored under `public/assets/` (not `storage/`). Image processing uses `intervention/image` v1.x API.
