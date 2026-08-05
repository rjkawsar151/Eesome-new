<div align="center">

# EESOME — Women's Handbags E‑Commerce

**A full-featured Laravel 10 storefront and admin panel for a premium women's handbag boutique (Bangladesh).**

![PHP](https://img.shields.io/badge/PHP-%5E8.1-777BB4?logo=php&logoColor=white)
![Laravel](https://img.shields.io/badge/Laravel-10-FF2D20?logo=laravel&logoColor=white)
![MySQL](https://img.shields.io/badge/MySQL-8-4479A1?logo=mysql&logoColor=white)
![Tailwind CSS](https://img.shields.io/badge/Tailwind_CSS-3-06B6D4?logo=tailwindcss&logoColor=white)
![License](https://img.shields.io/badge/license-MIT-blue)

</div>

---

## Table of Contents

1. [Overview](#overview)
2. [Features](#features)
3. [Tech Stack](#tech-stack)
4. [Project Structure](#project-structure)
5. [Requirements](#requirements)
6. [Installation (XAMPP / Local)](#installation-xampp--local)
7. [Environment Configuration](#environment-configuration)
8. [Database Setup & Seeding](#database-setup--seeding)
9. [Frontend Assets (Vite)](#frontend-assets-vite)
10. [Queue & Mail](#queue--mail)
11. [Admin Panel & Roles](#admin-panel--roles)
12. [Testing](#testing)
13. [Deployment Notes](#deployment-notes)
14. [Key Workflows](#key-workflows)
15. [Security](#security)
16. [License](#license)

---

## Overview

EESOME is a production-grade e-commerce platform built with **Laravel 10** for a women's handbag brand in Bangladesh. It ships with a fully responsive storefront, a complete role-based admin panel, order fulfillment tooling, email notifications, marketing analytics, and shared-hosting friendly storage fallbacks.

The store is live at **eesomebd.store** and serves prices in **Bangladeshi Taka (৳ / BDT)** with Cash on Delivery (COD) as the primary payment method.

---

## Features

### Storefront

- **Product catalog** — browsable product listings with search, filtering, and pagination
- **Category / brand / tag pages** with dedicated routing
- **Product detail pages** — pricing, sale badges, stock levels, description, and color **variants**
- **Shopping cart** — works for both guests and logged-in users, with AJAX add-to-cart and a live cart badge
- **Checkout** — address capture, shipping method selection, coupon discount, and COD order placement
- **Wishlist** — authenticated users can save products
- **Product reviews** — customers can review and rate products (throttled, moderated in admin)
- **Order tracking** — logged-in customers see active orders with a visual progress tracker and status history
- **Google login** and **email verification via OTP code**
- **WhatsApp chat button** — floating chat with a pre-filled message (phone configured in admin settings)
- **Mobile bottom navigation** bar and fully responsive layout
- **Meta Pixel** (Facebook) tracking on all storefront pages

### Admin Panel (`/admin`)

- **Dashboard** — revenue, order, product, customer, and traffic snapshots with 14-day traffic chart and top pages
- **Orders** — searchable/filterable list, order detail with items, status history, and payment transactions
- **Fulfillment** — flexible "next status" workflow (awaiting, processing, confirmed, waiting for confirmation, shipped, in transit, delivered, cancelled) with shipping provider, tracking number/URL, and estimated delivery
- **Payments** — update payment status (unpaid, pending, paid, partially paid, failed, refunded, partially refunded)
- **Products** — full CRUD, image management, sale pricing, pre-order flags, stock, and color **variants**
- **Hero products** — curate products shown on the homepage
- **Inventory** — low-stock view and stock adjustments with full movement history
- **Catalog** — categories, brands, and tags
- **Reviews** — approve, reject, or delete customer reviews
- **Blog** — manage blog posts
- **Users & Roles** — manage staff accounts with granular role permissions
- **Marketing** — coupons (percent/fixed, validity windows), shipping methods, payment methods
- **Site settings** — store name, logo, contact/WhatsApp numbers, and more
- **Navigation** — manage header/footer menu links
- **Media library** — upload and manage assets
- **Activity logs** — full security audit trail of admin changes (passwords are never logged)
- **Visitor stats** — unique visitors and page views for the last 1/3/7/30 days, plus source breakdown (Google, Facebook, Instagram, TikTok, direct, etc.), top pages, and a detailed visitor log (IP, URL, referrer, user agent)

### Notifications (HTML Emails)

- **Customer order confirmation** and **status update emails** — fully designed pink-themed HTML templates with order items, pricing breakdown, shipping address, and tracking details
- **Admin order alerts** — new-order and status-change emails to configured admin inboxes
- **OTP verification codes** for email verification

### Analytics & Tracking

- **Page-view tracking** middleware capturing IP, URL, referrer, user-agent, and marketing source
- **Meta Pixel** integration for Facebook advertising attribution

---

## Tech Stack

| Layer          | Technology |
|----------------|-----------|
| Backend        | PHP 8.1+, Laravel 10 |
| Frontend       | Blade, Tailwind CSS 3, Alpine.js, Axios |
| Build tool     | Vite |
| Database       | MySQL 8 |
| Auth           | Laravel Breeze, Laravel Sanctum, Laravel Socialite (Google OAuth) |
| Emails         | Laravel Mail (SMTP), HTML/Blade templates |
| Testing        | PHPUnit |

---

## Project Structure

```
app/
├── Enums/                  # OrderStatus, etc.
├── Http/
│   ├── Controllers/
│   │   ├── Admin/          # Admin panel controllers
│   │   ├── Storefront/     # Public-facing controllers
│   │   └── Auth/           # Breeze + Google OAuth + OTP verification
│   └── Middleware/         # TrackPageView, AdminMiddleware, LogAdminActivity, etc.
├── Models/                 # Eloquent models (Order, Product, PageView, ...)
├── Notifications/          # HTML email notifications
└── Services/               # Checkout, Cart, OrderStatus, Shipping, Permissions, etc.
config/                     # App configuration (order_alerts.php, etc.)
database/
├── migrations/             # Schema migrations
└── seeders/                # DemoCatalogSeeder (categories + products)
resources/views/
├── admin/                  # Admin panel views
├── storefront/             # Public store views
├── mail/                   # HTML email templates
├── layouts/                # app, admin, guest layouts
├── auth/                   # Authentication views
└── profile/                # Customer account views
routes/web.php              # All storefront + admin routes
tests/                      # PHPUnit feature tests (48 tests)
```

---

## Requirements

- **PHP** ≥ 8.1 with extensions: `pdo_mysql`, `mbstring`, `xml`, `curl`, `gd` (or `imagick`)
- **Composer** 2.x
- **Node.js** ≥ 18 and **npm** (for Vite assets)
- **MySQL** 5.7+ / 8.0
- A web server (Apache via XAMPP, Nginx, or Laravel Valet/Herd)

> The project is developed and tested on **XAMPP for Windows**.

---

## Installation (XAMPP / Local)

1. **Clone the repository**

   ```bash
   git clone <your-repository-url> Eesome
   cd Eesome
   ```

2. **Install PHP dependencies**

   ```bash
   composer install
   ```

3. **Install frontend dependencies**

   ```bash
   npm install
   ```

4. **Create the environment file**

   ```bash
   cp .env.example .env
   ```

5. **Generate the application key**

   ```bash
   php artisan key:generate
   ```

6. **Configure `.env`** (see [Environment Configuration](#environment-configuration))

7. **Create the database** in phpMyAdmin/MySQL, then migrate:

   ```bash
   php artisan migrate
   ```

8. **(Optional) Seed demo categories and products**

   ```bash
   php artisan db:seed
   ```

9. **Build frontend assets**

   ```bash
   npm run build
   ```

   During development you can instead run `npm run dev` (Vite hot-reload).

10. **Start the server**

    ```bash
    php artisan serve
    ```

    Open `http://localhost:8000`. The storefront loads at `/` and the admin panel at `/admin`.

---

## Environment Configuration

Key values in `.env`:

| Variable | Description |
|----------|-------------|
| `APP_URL` | Public site URL (e.g. `http://localhost:8000` or `https://eesomebd.store`) |
| `DB_DATABASE` / `DB_USERNAME` / `DB_PASSWORD` | MySQL credentials (XAMPP default: root with empty password) |
| `MAIL_MAILER` | `smtp` |
| `MAIL_HOST` / `MAIL_PORT` | SMTP server (e.g. `smtp.host` / `465` with `MAIL_ENCRYPTION=ssl`) |
| `MAIL_USERNAME` / `MAIL_PASSWORD` | SMTP credentials |
| `MAIL_FROM_ADDRESS` / `MAIL_FROM_NAME` | Sender identity used in all outgoing emails |
| `ADMIN_EMAILS` | Comma-separated list of admin inboxes that receive new-order/status alerts |
| `QUEUE_CONNECTION` | `sync` (recommended) or `database` + worker (see Queue) |
| `GOOGLE_CLIENT_ID` / `GOOGLE_CLIENT_SECRET` | Google OAuth credentials for "Sign in with Google" |

---

## Database Setup & Seeding

```bash
# Run all migrations
php artisan migrate

# Seed demo catalog (6 categories + products)
php artisan db:seed

# Fresh install: migrate + seed in one step
php artisan migrate:fresh --seed
```

The seeder creates handbag categories and demo products only — **no admin user is created**. To access the admin panel:

1. Register an account through the normal register form.
2. Promote that account to an admin role:

   ```bash
   php artisan tinker
   ```

   ```php
   $u = App\Models\User::where('email', 'your@email.com')->first();
   $u->role = 'super admin';
   $u->save();
   ```

   Or update the `role` column directly in MySQL.

---

## Frontend Assets (Vite)

- **Development:** `npm run dev` — Vite dev server with HMR
- **Production:** `npm run build` — compiled assets output to `public/build`

The admin panel styles are inlined in `resources/views/layouts/admin.blade.php`; the storefront uses `resources/css/app.css` and `resources/js/app.js` compiled through Vite.

---

## Queue & Mail

**Emails are sent synchronously** — the order-status and admin-alert notifications do **not** use queued jobs, so status updates deliver immediately without requiring a queue worker.

If you later re-enable queued mail (`QUEUE_CONNECTION=database`), run a queue worker:

```bash
php artisan queue:work
```

Or use a process manager / cron entry:

```bash
php artisan schedule:work   # if scheduled tasks are configured
```

The `jobs` table is created by the `2026_07_28_000003_create_queue_jobs_table` migration.

---

## Admin Panel & Roles

The admin panel lives at `/admin` and is protected by auth + role-based middleware.

### Roles

| Role            | Access                                                                 |
|-----------------|------------------------------------------------------------------------|
| **Super Admin** | Everything (`*`)                                                        |
| **Admin**       | Everything (`*`)                                                        |
| **Manager**     | Dashboard, Orders, Products, Categories, Reviews                        |
| **Content Editor** | Dashboard, Products, Categories, Reviews, Blog                       |
| **User**        | Storefront + account only, no admin access                              |

Permissions are defined centrally in `app/Services/AdminPermissionService.php` (route → permission mapping) and enforced by `app/Http/Middleware/AdminMiddleware.php`.

### Creating your first admin

See [Database Setup & Seeding](#database-setup--seeding).

---

## Testing

The project includes a PHPUnit suite covering admin management, order workflows, storefront auditing, variant commerce, authentication, profile, and more (48 tests, ~150 assertions).

```bash
# Run the full test suite
php artisan test

# Run a single file
php artisan test --filter=AdminOrderWorkflowTest
```

Tests use a separate database (see `phpunit.xml`).

---

## Deployment Notes

### Shared hosting without a storage symlink

The project includes fallback controllers for environments where `storage` cannot be symlinked into `public`:

- `GET /storage/{path}` → serves managed files (products, categories, blog, media, etc.)
- `GET /uploads/products/{filename}` → legacy product images

This means uploads work out of the box on cPanel/shared hosts.

### Production checklist

```bash
php artisan migrate --force
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan optimize
npm run build
```

Ensure the `storage` and `bootstrap/cache` directories are writable, and set `APP_ENV=production` with `APP_DEBUG=false`.

---

## Key Workflows

### Order lifecycle

```
awaiting → processing → confirmed → waiting for confirmation → shipped → in transit → delivered
                                                                          ↘ cancelled
```

Admin can move an order to **any** status at any time via the "Update fulfillment" form (the workflow is not enforced). Shipping info (provider, tracking number, URL, estimated delivery) can be attached to any transition. Cancelling an order automatically restores inventory stock and records an inventory movement.

### Notifications on order changes

- **Customer** receives an HTML email on order placement ("Order Confirmation") and on every status change, including items, price breakdown, shipping address, and tracking links.
- **Admins** (from `ADMIN_EMAILS` plus registered staff with an email) receive a new-order alert and a status-change alert.

### Visitor tracking

Every storefront GET request records a page view (IP, URL, referrer, user-agent, marketing source). Source attribution maps referrers to channels (Google, Facebook, Instagram, TikTok, WhatsApp, Twitter/X, Bing, direct, internal, other). Reports are available in **Admin → Visitor stats**.

---

## Security

- Passwords are hashed; **never logged** in the activity audit trail
- CSRF protection enabled on all forms
- Admin routes enforce role-based permissions
- Email OTP verification with throttling
- Sanitized/validated user input (role, status, URL, and file validations)
- `X-Requested-With` + JSON expectations on AJAX endpoints
- Sensitive configuration lives in `.env` (never committed)

---

## License

This project is open-sourced under the [MIT license](https://opensource.org/licenses/MIT).

---

<div align="center">
  <sub>Crafted with care by <strong>KAWSAR</strong> — EESOME © 2026. All rights reserved.</sub>
</div>
