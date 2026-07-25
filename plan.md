# EEsome Women's Handbag E-Commerce Rebuild
## Legacy-Database-Compatible Laravel Master Implementation Plan

**Document purpose:** Build a production-ready, high-converting Women's Handbag e-commerce application in Laravel and MySQL while preserving and loading all usable content from the supplied legacy database without primary-key remapping, relationship breakage, missing-column errors, or accidental data loss.

**Preferred platform:** Laravel 13 with PHP 8.3+ and MySQL 8.0+. If the production server cannot run PHP 8.3, complete a hosting compatibility audit before selecting Laravel 12/PHP 8.2. Do not begin application development until the actual production PHP, MySQL/MariaDB, Composer, Node.js, queue, cron, and storage capabilities are confirmed.

---

# 1. Non-Negotiable Implementation Rules

1. **Database first:** Inspect the real exported production database with `SHOW CREATE TABLE`, row counts, indexes, collations, and sample records before finalizing migrations, models, validation, or queries.
2. **Preserve legacy IDs:** Existing integer IDs must remain unchanged. Never re-seed, remap, truncate, or regenerate IDs during import.
3. **Never run destructive production commands:** Do not use `migrate:fresh`, `db:wipe`, destructive seeders, or migrations that drop legacy tables or columns.
4. **Use additive compatibility migrations:** Import the old database first, then add new columns/tables/indexes through idempotent Laravel migrations.
5. **Match legacy key types exactly:** The supplied schema uses signed MySQL `INT` keys. New foreign keys referencing legacy IDs must also use signed `INT`, not Laravel's default unsigned `BIGINT` or `foreignId()`.
6. **Do not trust default Eloquent timestamp behavior:** Most legacy tables do not contain both `created_at` and `updated_at`. Resolve this before models are used.
7. **Keep legacy columns:** New functionality must extend the schema. Do not rename or remove old columns during the initial rebuild.
8. **No secrets in the database or repository:** SMTP, database, payment, application, and third-party credentials must be moved to `.env`. Rotate the SMTP password exposed in the legacy SQL before launch.
9. **All checkout pricing is server-authoritative:** Never trust price, discount, shipping charge, stock, coupon value, payment status, or totals submitted by the browser.
10. **Stock changes must be transactional and locked:** Use a database transaction plus `lockForUpdate()` during checkout to prevent overselling.
11. **Order status notifications must be dispatched after commit:** Do not send email from inside an uncommitted transaction.
12. **Production deletion policy:** Products used by historical orders must not be hard-deleted. Prefer deactivation/soft deletion and preserve order-item snapshots.
13. **No performance promises without measurement:** `paginate(12)` controls result size but does not guarantee a sub-100 ms response. Indexing, eager loading, caching, image delivery, hosting, and query profiling are required.

---

# 2. Legacy Schema Audit Summary

## 2.1 Critical compatibility findings

### A. Signed integer key mismatch risk

The schema declares IDs and foreign keys as regular signed `INT`. Laravel's common migration helpers such as `$table->id()` and `$table->foreignId()` create unsigned `BIGINT` columns. Mixing them will cause foreign-key creation failures or type inconsistencies.

**Required approach:**

- Keep all existing primary keys as signed `INT`.
- New tables referencing `users.id`, `products.id`, `orders.id`, or `categories.id` must use `$table->integer(...)`.
- New standalone tables may use `bigIncrements()` only when they do not need to be type-compatible with a legacy parent key.

### B. Timestamp incompatibility

Laravel expects both `created_at` and `updated_at` by default. The legacy schema is inconsistent:

- `categories`, `products`, `users`, `orders`, `coupons`, and `blog_posts` have only `created_at`.
- `wishlist` has only `created_at`.
- `cart_items` has only `updated_at`.
- `site_settings` has both timestamps.
- `order_items`, `testimonials`, and `otp_codes` have neither.

**Preferred resolution:** Add nullable missing timestamps through compatibility migrations before enabling normal application traffic. Do not fabricate unknown historical dates. Only backfill where the source is defensible, such as `cart_items.created_at = cart_items.updated_at`.

### C. Required storefront fields are absent

The requested design depends on fields or entities that do not exist:

- Product SKU
- Product slug
- Discount price
- Flexible badge text
- Product image gallery
- Reviews and ratings
- Order status history
- Product snapshot details in order items
- Order number
- Explicit subtotal/shipping breakdown
- Secure payment transaction records

These must be introduced additively.

### D. Legacy cart conflicts with the requested session cart

`cart_items.user_id` is mandatory, so the table supports authenticated carts only. Guest/session carts must be implemented independently, then merged into `cart_items` when a customer logs in.

### E. Historical order integrity risk

`order_items.product_id` is `NOT NULL` with `ON DELETE CASCADE`. Deleting a product would delete historical order lines, which is unacceptable for accounting and customer records.

**Required policy:** Never hard-delete products in application code. Add order-item product snapshots and later migrate `product_id` to nullable with `ON DELETE SET NULL` after validating hosting/database support and backups.

### F. Coupon relation is text-only

`orders.coupon_code` stores a snapshot string but has no foreign key to `coupons`. Keep this field for history. Add optional `coupon_id` for reporting while continuing to store the code and discount snapshot.

### G. Exposed SMTP secret

The legacy `site_settings` data includes live-looking SMTP credentials. This is a credential leak and a deployment risk.

**Immediate action:** Rotate the mail password, remove secret rows from the database, store mail credentials only in environment variables, and make sure `.env` is ignored by Git.

### H. User authentication compatibility must be verified

The `users.password` values may be Laravel-compatible bcrypt/Argon hashes, or they may come from another system.

**Audit rule:**

- Bcrypt usually begins with `$2y$` or `$2b$`.
- Argon hashes usually begin with `$argon2`.
- Plaintext, MD5, SHA-1, or unknown custom hashes must not be silently accepted.
- Unsupported hashes require a secure forced password-reset flow or a carefully isolated one-time legacy verifier that immediately rehashes the password after successful verification.

---

# 3. Exact Legacy Table-to-Model Mapping

## 3.1 `categories` -> `App\Models\Category`

| Database column | Model handling |
|---|---|
| `id` | Integer primary key, auto-incrementing |
| `name` | Fillable string |
| `image` | Nullable string; resolve as remote URL or local storage path |
| `created_at` | Date cast |
| `updated_at` | Add nullable compatibility column |

**Relationships:**

- `Category hasMany Product` using `products.category_id`.

**New additive fields:** `slug`, `is_active`, `sort_order`, `meta_title`, `meta_description`, `updated_at`.

**Data rule:** Generate unique slugs without changing category IDs or names.

---

## 3.2 `products` -> `App\Models\Product`

| Database column | Model handling |
|---|---|
| `id` | Integer primary key |
| `category_id` | Nullable signed integer foreign key |
| `name` | Fillable string |
| `description` | Nullable text |
| `price` | Decimal cast with two places; treat as a decimal string, not a PHP float |
| `stock` | Integer; must never become negative |
| `image` | Legacy primary image field; keep permanently |
| `is_featured` | Boolean cast |
| `is_new` | Boolean cast |
| `is_sold_out` | Boolean cast, but computed stock state remains authoritative |
| `is_preorder` | Boolean cast |
| `created_at` | Date cast |
| `updated_at` | Add nullable compatibility column |

**Relationships:**

- `Product belongsTo Category` using nullable `category_id`.
- `Product hasMany OrderItem`.
- `Product hasMany CartItem`.
- `Product hasMany Wishlist`.
- `Product hasMany ProductImage` after extension.
- `Product hasMany Review` after extension.
- Optional `Product hasMany InventoryMovement` after extension.

**New additive fields:**

- `sku` nullable initially, then populated and made unique.
- `slug` nullable initially, then populated and made unique.
- `discount_price` nullable decimal(10,2).
- `badge_text` nullable varchar(30).
- `is_active` boolean default true.
- `sort_order` integer default 0.
- `meta_title`, `meta_description` nullable.
- `updated_at` nullable timestamp.
- Optional `deleted_at` only after the entire application is designed for soft deletes.

**Legacy SKU generation:** Use a stable value such as `LEGACY-{id}` for imported rows when no real SKU exists. Admins may later replace it, but uniqueness must be maintained.

**Badge priority:**

1. `SOLD OUT` when stock is zero and preorder is false.
2. `PREORDER` when `is_preorder = true`.
3. Percentage badge when `discount_price` is valid and lower than `price`.
4. Custom `badge_text` such as `HOT` or `LIMITED`.
5. `NEW` when `is_new = true`.

Do not let `is_sold_out` contradict stock. During cleanup, synchronize it from the business rule or treat it as a display override only.

---

## 3.3 `users` -> `App\Models\User`

| Database column | Model handling |
|---|---|
| `id` | Integer primary key |
| `name` | Fillable string |
| `email` | Unique, normalized for login |
| `phone` | Nullable string; normalize Bangladesh numbers without destroying original data |
| `password` | Hidden; hash compatibility audit required |
| `role` | Cast to an application enum or validated string: `user`, `admin` |
| `detailed_role` | Nullable/display role; do not use as the only authorization control |
| `profile_pic` | Nullable image path/URL |
| `is_verified` | Boolean cast |
| `created_at` | Date cast |
| `updated_at` | Add nullable compatibility column |

**Relationships:**

- `User hasMany Order`.
- `User hasMany CartItem`.
- `User hasMany Wishlist`.
- `User hasMany OtpCode`.
- `User hasMany Review` after extension.
- `User hasMany Address` if address book is added.

**Authentication extensions:** Add nullable `remember_token`, `email_verified_at`, and standard password reset/session support if the selected starter kit requires them. Preserve `is_verified` and define exactly how it differs from email verification.

**Authorization:** Use policies/gates or dedicated middleware. Never rely solely on a client-supplied role or `detailed_role` text.

---

## 3.4 `orders` -> `App\Models\Order`

| Database column | Model handling |
|---|---|
| `id` | Integer primary key |
| `user_id` | Nullable signed integer; supports guest checkout |
| `customer_name` | Order snapshot, required |
| `email` | Order snapshot, required |
| `phone` | Order snapshot, required |
| `shipping_address` | Order snapshot, required |
| `total_amount` | Preserve as the final payable/grand total for legacy compatibility |
| `discount_amount` | Decimal, default zero |
| `coupon_code` | Nullable coupon snapshot |
| `payment_method` | Exact legacy values: `COD`, `bKash` |
| `payment_status` | Exact legacy values: `Pending`, `Paid` |
| `order_status` | Legacy free-text status; validate through a controlled transition service |
| `transaction_id` | Nullable legacy payment reference |
| `created_at` | Date cast |
| `updated_at` | Add nullable compatibility column |

**Relationships:**

- `Order belongsTo User`, nullable.
- `Order hasMany OrderItem`.
- `Order hasMany OrderStatusHistory` after extension.
- `Order hasMany PaymentTransaction` after extension.
- Optional `Order belongsTo Coupon` through new nullable `coupon_id`; retain `coupon_code` snapshot.

**New additive fields:**

- `order_number` unique.
- `subtotal_amount` decimal.
- `shipping_charge` decimal.
- `payment_fee` decimal default zero.
- `coupon_id` nullable signed integer.
- `notes` nullable text.
- `status_changed_at` nullable timestamp.
- `placed_from` nullable string, e.g. web/admin.
- `updated_at` nullable timestamp.

**Legacy total backfill:** For imported orders where the exact historic shipping calculation is unknown:

- Keep `total_amount` unchanged.
- Set `subtotal_amount` to `SUM(order_items.price * quantity)` when item rows are complete.
- Derive `shipping_charge = total_amount + discount_amount - subtotal_amount` only if the result is non-negative and mathematically credible.
- Otherwise leave new breakdown fields nullable and label the order as legacy in internal reporting.

Never alter old `total_amount` values merely to make a new formula balance.

---

## 3.5 `order_items` -> `App\Models\OrderItem`

| Database column | Model handling |
|---|---|
| `id` | Integer primary key |
| `order_id` | Required signed integer foreign key |
| `product_id` | Legacy required signed integer; target nullable for historical safety |
| `price` | Unit price snapshot |
| `quantity` | Required positive integer |

**Relationships:**

- `OrderItem belongsTo Order`.
- `OrderItem belongsTo Product`, eventually nullable.
- Optional `OrderItem hasMany Review` or `hasOne Review` for verified purchase logic.

**New snapshot fields:**

- `product_name`
- `product_sku`
- `product_image`
- `line_total`
- `discount_amount`
- `created_at`
- `updated_at`

**Backfill:** Copy name/image/SKU from the current product only once. These become immutable order snapshots. Compute `line_total = price * quantity` using decimal-safe arithmetic.

---

## 3.6 `wishlist` -> `App\Models\Wishlist`

Laravel would normally pluralize `Wishlist` to `wishlists`, but the real table is singular.

**Required model declaration:** `protected $table = 'wishlist';`

| Database column | Model handling |
|---|---|
| `id` | Integer primary key |
| `user_id` | Required signed integer |
| `product_id` | Required signed integer |
| `created_at` | Date cast |
| `updated_at` | Add nullable compatibility column or explicitly disable it |

**Relationships:**

- `Wishlist belongsTo User`.
- `Wishlist belongsTo Product`.

**Required index:** Add unique composite index on `(user_id, product_id)` after removing duplicates deterministically. Keep the oldest row and archive/delete duplicate rows during the migration report.

---

## 3.7 `cart_items` -> `App\Models\CartItem`

| Database column | Model handling |
|---|---|
| `id` | Integer primary key |
| `user_id` | Required signed integer |
| `product_id` | Required signed integer |
| `quantity` | Positive integer |
| `updated_at` | Existing auto-update timestamp |
| `is_abandoned_notified` | Boolean cast |
| `created_at` | Add nullable; backfill from `updated_at` where appropriate |

**Relationships:**

- `CartItem belongsTo User`.
- `CartItem belongsTo Product`.

**Existing unique key:** `(user_id, product_id)` is correct and must be retained.

**Cart architecture:**

- Guest cart: session-based associative structure keyed by product ID.
- Authenticated cart: persisted in `cart_items`.
- Login/register: merge session cart into database cart with stock-safe quantity limits.
- Logout: keep the database cart and clear only the browser session representation.
- Cart totals: always recalculate from current product records.

---

## 3.8 `coupons` -> `App\Models\Coupon`

| Database column | Model handling |
|---|---|
| `id` | Integer primary key |
| `code` | Unique, store normalized uppercase for new records |
| `discount_type` | Enum/string: `fixed`, `percentage` |
| `discount_value` | Decimal |
| `min_order_amount` | Decimal |
| `expiry_date` | Date cast |
| `usage_limit` | Nullable integer |
| `used_count` | Integer |
| `status` | Boolean cast |
| `created_at` | Date cast |
| `updated_at` | Add nullable compatibility column |

**Relationships:**

- Optional `Coupon hasMany Order` through new `orders.coupon_id`.

**Validation rules:** Active, not expired, usage limit not reached, minimum subtotal reached, percentage range valid, fixed discount not greater than allowed business limit. Coupon usage increment must occur in the checkout transaction with a row lock.

---

## 3.9 `testimonials` -> `App\Models\Testimonial`

| Database column | Model handling |
|---|---|
| `id` | Integer primary key |
| `name` | Required string |
| `content` | Required text |
| `image` | Nullable image path/URL |
| `rating` | Integer constrained to 1-5 |

No relationships in the legacy schema.

**Extension:** Add `is_active`, `sort_order`, `created_at`, and `updated_at` if testimonials are managed through admin.

---

## 3.10 `blog_posts` -> `App\Models\BlogPost`

| Database column | Model handling |
|---|---|
| `id` | Integer primary key |
| `title` | Required string |
| `content` | Required text; sanitize admin HTML |
| `image` | Nullable path/URL |
| `created_at` | Date cast |
| `updated_at` | Add nullable compatibility column |

**Extension:** Add `slug`, `excerpt`, `status`, `published_at`, `meta_title`, and `meta_description` without modifying old titles/content.

---

## 3.11 `otp_codes` -> `App\Models\OtpCode`

| Database column | Model handling |
|---|---|
| `id` | Integer primary key |
| `user_id` | Nullable signed integer |
| `code` | Do not expose; preferably store a hash in the redesigned flow |
| `expires_at` | Date-time cast |

**Relationships:** `OtpCode belongsTo User`, nullable.

**Security extension:** Add `purpose`, `attempts`, `consumed_at`, and `created_at`; rate-limit OTP requests and verification attempts. Purge expired codes with the scheduler. Do not log OTP values.

---

## 3.12 `site_settings` -> `App\Models\SiteSetting`

| Database column | Model handling |
|---|---|
| `id` | Integer primary key |
| `setting_key` | Unique string |
| `setting_value` | Nullable text |
| `created_at` | Date cast |
| `updated_at` | Date cast |

**Usage:** Implement a cached settings repository, not repeated direct queries from every Blade partial.

**Secret policy:** Database, SMTP, API, bKash, application key, queue, and cloud-storage credentials must never be read from this table. Remove/ignore legacy secret keys.

**HTML safety:** `hero_title` contains markup. Do not render arbitrary database HTML with `{!! !!}`. Either split it into structured text fields or sanitize through an allowlist that permits only the required safe span/class pattern.

---

# 4. Relationship Map

```text
Category 1 ---- * Product

User 1 -------- * Order
User 1 -------- * CartItem
User 1 -------- * Wishlist
User 1 -------- * OtpCode
User 1 -------- * Review (new)

Order 1 ------- * OrderItem
Order 1 ------- * OrderStatusHistory (new)
Order 1 ------- * PaymentTransaction (new)

Product 1 ----- * OrderItem
Product 1 ----- * CartItem
Product 1 ----- * Wishlist
Product 1 ----- * ProductImage (new)
Product 1 ----- * Review (new)
Product 1 ----- * InventoryMovement (recommended)

Coupon 1 ------ * Order through optional orders.coupon_id (new)
```

**Nullable relationships:** `products.category_id`, `orders.user_id`, `otp_codes.user_id`, and eventually `order_items.product_id`.

---

# 5. Safe Migration Architecture

## 5.1 Repository migration strategy

Create migrations in two categories.

### Category A: Legacy baseline migrations

These allow a clean development/test database to reproduce the old schema, but they must not fail when tables already exist.

- Each baseline migration checks `Schema::hasTable()` before creating a table.
- Use signed integer key definitions matching the old SQL.
- Do not include destructive `down()` logic for production-critical legacy tables; document that rollback is backup-based.
- Seed only in local/testing environments. Never insert dummy records into imported production data.

### Category B: Additive compatibility migrations

Each migration checks `Schema::hasColumn()` and index existence where practical, then adds only missing structures.

Recommended order:

1. Framework infrastructure tables.
2. Missing timestamp/auth columns.
3. Category/product compatibility fields.
4. Product images.
5. Review system.
6. Order compatibility fields.
7. Order-item snapshots and product deletion safety.
8. Order status history.
9. Payment transactions.
10. Wishlist uniqueness cleanup/index.
11. Performance indexes.
12. Data backfill command, separate from schema migration where the dataset may be large.

## 5.2 Framework infrastructure tables

Add as required by the chosen stack:

- `migrations`
- `password_reset_tokens`
- `sessions` if database sessions are selected
- `cache` and `cache_locks` if database cache is selected
- `jobs`, `job_batches`, `failed_jobs` if database queues are selected

Redis is preferred for cache/session/queues when available, but the application must have a documented database-driver fallback.

## 5.3 Avoid long data operations inside schema migrations

Large slug/SKU/snapshot backfills should run through resumable Artisan commands using `chunkById()`, progress output, logs, and dry-run mode. Schema migrations should add nullable columns first. After backfill verification, add unique/not-null constraints in later migrations.

## 5.4 Foreign-key alteration plan for `order_items.product_id`

Do this only after a verified backup:

1. Add product snapshot columns.
2. Backfill snapshots.
3. Drop the existing foreign key.
4. Make `product_id` nullable signed `INT`.
5. Recreate the foreign key with `ON DELETE SET NULL`.
6. Enforce application-level product deactivation/soft-delete policy.

If the host's database engine cannot safely alter the column online, keep the existing FK temporarily and prohibit hard deletion.

---

# 6. New Tables Required by the Requested Features

## 6.1 `product_images`

```text
id                  primary key
product_id          signed INT, FK products.id, cascade on delete only if product deletion is controlled
image_path          varchar(500)
alt_text             varchar(255), nullable
sort_order           integer default 0
is_primary           boolean default false
created_at
updated_at
```

The legacy `products.image` remains the fallback primary image. New gallery images live here.

## 6.2 `reviews`

```text
id                  primary key
product_id          signed INT, required
user_id             signed INT, nullable for approved guest strategy
order_item_id       signed INT, nullable
rating              tiny integer 1-5
title               varchar(255), nullable
content             text, required
status              varchar(20): pending/approved/rejected
is_verified_purchase boolean default false
created_at
updated_at
```

Indexes: `(product_id, status, created_at)`, `(user_id, product_id)`. Define duplicate-review policy clearly.

## 6.3 `order_status_histories`

```text
id                  primary key
order_id            signed INT
from_status         varchar(50), nullable
to_status           varchar(50)
changed_by_user_id  signed INT, nullable
note                text, nullable
created_at
updated_at
```

This drives the customer timeline and admin audit trail.

## 6.4 `payment_transactions`

```text
id                  primary key
order_id            signed INT
provider             varchar(30), e.g. bKash
provider_transaction_id varchar(150), nullable
merchant_invoice    varchar(150), nullable
amount               decimal(10,2)
status               varchar(30)
request_payload      JSON, nullable and sanitized
response_payload     JSON, nullable and sanitized
verified_at          timestamp, nullable
created_at
updated_at
```

Never store payment secrets. Mask sensitive payload values.

## 6.5 `inventory_movements` - recommended

```text
id                  primary key
product_id          signed INT
order_id            signed INT, nullable
type                 varchar(30): sale/restock/adjustment/cancel_return
quantity_delta       integer
stock_before         integer
stock_after          integer
reference            varchar(100), nullable
created_by_user_id   signed INT, nullable
created_at
updated_at
```

This creates an auditable stock trail and simplifies cancellation/restock rules.

## 6.6 Optional `addresses`

Add only if customers need reusable addresses. Existing `orders.shipping_address` must remain an immutable order snapshot.

## 6.7 Optional product variants

Handbags may later require colour/material/size variants. Do not force legacy products into a complex variant system during initial migration. Introduce `product_variants` only when business requirements are confirmed. Legacy products may represent a product with no variants or a generated default variant.

---

# 7. Data Import and Cutover Runbook

## Phase 1: Obtain and verify the real dump

1. Export a fresh production backup including routines only if actually required.
2. Store an encrypted copy outside the web root.
3. Record database server/version, collation, character set, timezone, and SQL mode.
4. Run `SHOW CREATE TABLE` for every table because the supplied SQL may not exactly match production.
5. Record row counts and maximum IDs for all legacy tables.
6. Record checksums or deterministic aggregates for critical tables.

## Phase 2: Security cleanup before use

1. Rotate the exposed SMTP credentials.
2. Remove secret setting rows from the migration copy.
3. Scan the dump for API keys, payment secrets, passwords, private URLs, and customer data exposure.
4. Restrict staging access and disable outbound customer email/SMS.
5. Replace production recipient addresses with a safe mail sink in staging.

## Phase 3: Import into isolated staging

1. Create a blank staging database with `utf8mb4`.
2. Import the dump without changing IDs.
3. Confirm all tables use InnoDB where foreign keys are expected.
4. Re-enable foreign-key checks and run integrity queries.
5. Do not run application seeders.

## Phase 4: Pre-migration data audit

Run and report at least the following:

- Orphan products with missing categories.
- Orphan orders with missing users, noting that `orders.user_id` may legitimately be null.
- Orphan order items.
- Duplicate wishlist entries.
- Duplicate cart entries despite the unique key.
- Duplicate emails under case-insensitive comparison.
- Invalid/empty names, emails, phones, addresses.
- Negative prices, discounts, totals, stock, or quantities.
- `discount_amount > total_amount`.
- Invalid ratings outside 1-5.
- Invalid coupon types/status/limits.
- Unknown payment methods/statuses/order statuses.
- Products with contradictory stock/sold-out/preorder flags.
- Missing/broken/local image paths and unreachable remote URLs.
- Unsupported password hash formats.

Every correction must be written to a migration report containing table, row ID, old value, new value, reason, and operator/date.

## Phase 5: Run additive migrations

1. Enable maintenance mode for a production cutover.
2. Take a final backup.
3. Run schema migrations.
4. Run data backfill commands in dry-run mode.
5. Review counts and planned changes.
6. Run backfills for real.
7. Add final unique/not-null constraints only after successful backfill.
8. Clear/rebuild application caches.

## Phase 6: Validate imported content

Validate at minimum:

- Every category loads with its products.
- Product images resolve correctly for both URL and local path formats.
- Featured/new/preorder/sold-out flags display correctly.
- Existing users can authenticate or receive a reset path.
- Guest and registered orders display correctly.
- Every order total matches the legacy value.
- Every order line remains present.
- Customer order history only shows the authenticated customer's orders.
- Admin totals and status filters work.
- Site settings load without exposing secret rows.

## Phase 7: Production cutover

1. Put old site into read-only/maintenance mode.
2. Export the final delta/final database.
3. Import into the new production database.
4. Run the same scripted audit, migrations, and backfills used in staging.
5. Start queue workers and scheduler.
6. Warm critical caches.
7. Run smoke tests.
8. Switch DNS/web root only after acceptance checks pass.
9. Keep the old application and backup available for rollback during the agreed stabilization window.

---

# 8. Application Architecture

## 8.1 Recommended layers

```text
app/
  Actions/
    Cart/
    Checkout/
    Orders/
    Products/
  Data/
  Enums/
  Events/
  Exceptions/
  Http/
    Controllers/
      Storefront/
      Account/
      Admin/
    Requests/
    Middleware/
  Mail/
  Models/
  Notifications/
  Observers/
  Policies/
  Repositories/
  Services/
    CartService.php
    CheckoutService.php
    CouponService.php
    OrderStatusService.php
    ProductImageResolver.php
    SiteSettingsRepository.php
  Support/
```

Controllers should coordinate requests and responses. Pricing, checkout, stock, coupon, status transition, image resolution, and settings caching belong in dedicated services/actions.

## 8.2 Core models

- Category
- Product
- ProductImage
- User
- Order
- OrderItem
- OrderStatusHistory
- PaymentTransaction
- CartItem
- Wishlist
- Coupon
- Review
- Testimonial
- BlogPost
- OtpCode
- SiteSetting
- InventoryMovement, recommended

## 8.3 Storefront controllers

- `HomeController@index`
- `ProductController@index`
- `ProductController@show`
- `CartController@index/store/update/destroy`
- `CheckoutController@show/store`
- `BuyNowController@store`
- `WishlistController@index/store/destroy`
- `ReviewController@store`
- `BlogController@index/show`

## 8.4 Account controllers

- `ProfileController@show/update`
- `CustomerOrderController@index/show`
- Authentication and password reset controllers from the selected Laravel starter approach.

## 8.5 Admin controllers

- Dashboard
- Category CRUD
- Product CRUD and image gallery management
- Order list/detail/status transition
- Coupon CRUD
- Review moderation
- Testimonial CRUD
- Blog CRUD
- Site settings CRUD with protected-key denylist
- Customer view with authorization and privacy controls

Admin product preview must open the public product page in a new tab and must not expose unpublished products without an authorized preview mechanism.

---

# 9. Storefront Query Plan

## 9.1 Home page query contract

Load only required columns and eager-load relations:

- Active categories ordered by `sort_order`, then name.
- Featured active products with category and primary image.
- Paginated active products using `paginate(12)`.
- Approved testimonials.
- Cached safe site settings.

Avoid loading full descriptions or all gallery images for product cards.

## 9.2 Product detail query contract

Load:

- Product and category.
- Ordered gallery.
- Approved review count and average rating.
- Paginated approved reviews.
- Similar active products from the same category, excluding current ID.

Use database aggregates such as `withAvg` and `withCount` instead of looping and calculating per product.

## 9.3 Image resolution compatibility

Create one `ProductImageResolver` used everywhere:

1. Empty value -> configured placeholder.
2. `http://` or `https://` -> return remote URL during transition.
3. `storage/...` -> resolve with `asset()`/Storage URL.
4. Other relative paths -> normalize against the legacy media base path.

Recommended migration: download owned legacy product images into managed storage, generate optimized variants, and preserve the original source URL in an audit field. Do not rely permanently on third-party hotlinks.

---

# 10. Product Pricing and Badge Rules

## 10.1 Effective price

```text
If discount_price is not null,
AND discount_price > 0,
AND discount_price < price,
then effective price = discount_price.
Otherwise effective price = price.
```

Never accept `discount_price >= price` as a valid discount.

## 10.2 Decimal safety

MySQL `DECIMAL(10,2)` is appropriate for existing monetary data. In PHP:

- Keep Eloquent decimal casts as strings.
- Use decimal-safe helpers/BCMath or integer minor-unit conversion for calculations.
- Do not use binary floating-point arithmetic for order totals.
- Round according to documented BDT business rules at one consistent layer.

## 10.3 Product card interaction

The full card should navigate to the product detail page, but Add to Cart and Buy Now must remain independent controls. Do not create invalid nested anchors/buttons. Use a stretched-link pattern and put action controls above it with appropriate `z-index`.

---

# 11. Cart and Buy Now Design

## 11.1 Guest session structure

Store minimal data only:

```php
[
    product_id => [
        'quantity' => 2,
    ],
]
```

Never store trusted price/name/stock in the session. Hydrate them from the database for every cart page and checkout.

## 11.2 Authenticated cart

Use `cart_items` with `updateOrCreate` semantics and the existing `(user_id, product_id)` unique key.

## 11.3 Cart merge on login

Within a transaction:

1. Load each session product.
2. Validate product is active and purchasable.
3. Combine quantities with existing database quantity.
4. Cap or reject according to stock/preorder policy.
5. Upsert cart rows.
6. Clear the session cart only after successful commit.

## 11.4 Buy Now

Buy Now must create a temporary checkout intent/session containing only product ID and requested quantity. It must not bypass stock, coupon, shipping, address, or server-side pricing validation.

---

# 12. Checkout and Overselling Protection

## 12.1 Required transaction sequence

`CheckoutService` must perform the following inside `DB::transaction()`:

1. Normalize and validate the submitted customer/shipping data with a Form Request.
2. Resolve cart/buy-now product IDs.
3. Sort product IDs ascending before locking to reduce deadlock risk.
4. Query active products with `lockForUpdate()`.
5. Revalidate product existence, availability, price, effective discount, and requested quantity.
6. Reject sold-out products unless preorder rules allow them.
7. Lock the coupon row when a coupon is used.
8. Revalidate coupon status, date, minimum amount, and usage limit.
9. Calculate subtotal, discount, shipping, payment fee, and final `total_amount` server-side.
10. Create the order with immutable customer/contact/address snapshots.
11. Create order items with product name/SKU/image/unit price/quantity/line total snapshots.
12. Decrement stock and record inventory movements.
13. Increment coupon usage safely.
14. Create initial order-status history.
15. Commit.
16. Clear cart and dispatch confirmation work only after commit.

## 12.2 Stock rules

- Normal product: quantity must be less than or equal to current stock.
- Preorder product: define whether stock is ignored, reserved, or separately limited.
- Sold-out flag: cannot override actual positive stock without a documented admin reason.
- Cancellation: restock only once and record an inventory movement.
- Delivered orders: cannot be moved backward without elevated authorization and an audit note.

## 12.3 Idempotency

Protect checkout against double-clicks, browser retries, and payment callbacks:

- Generate a checkout idempotency token.
- Store/validate it server-side.
- Enforce unique order number and provider transaction identifiers.
- Repeated payment callbacks must update the same transaction/order, not create a new order.

---

# 13. Order Status Pipeline and Email Notifications

## 13.1 Controlled statuses

Keep the legacy database column as `VARCHAR(50)` for compatibility, but define a PHP backed enum or domain constants:

- Pending
- Processing
- Shipped
- Delivered
- Cancelled

Optional future statuses: Confirmed, On Hold, Returned, Refunded, Failed.

## 13.2 Allowed transitions

```text
Pending -> Processing | Cancelled
Processing -> Shipped | Cancelled
Shipped -> Delivered | Returned (future)
Delivered -> Returned (future, privileged)
Cancelled -> no normal forward transition
```

All status changes must go through `OrderStatusService`. Do not permit arbitrary text from admin forms.

## 13.3 Observer/event design

Preferred flow:

1. Admin calls `OrderStatusService`.
2. Service validates transition and updates the order using model `save()`.
3. Record `order_status_histories` row.
4. Dispatch `OrderStatusChanged` after commit.
5. Queued listener sends a responsive customer email.
6. Failed jobs are retained and retried.

Do not use mass `update()` for order status because model events/observers may not fire for mass updates. The status service should also dispatch explicitly so the workflow is not dependent on implicit behavior alone.

## 13.4 Email content

Each status email should include:

- Customer name
- Order number
- New status
- Short contextual message
- Order summary
- Payable amount
- Tracking/profile link where applicable
- Support phone/email/WhatsApp from safe settings

Email templates must have plain-text fallbacks. Queue mail so admin/customer requests remain fast.

---

# 14. Reviews and Ratings

1. Display approved reviews only.
2. Average rating is calculated from approved reviews.
3. Restrict rating to integer 1-5.
4. Apply rate limiting and CSRF protection.
5. Prefer verified-purchase reviews by matching the user/email to a delivered order item.
6. Moderate new reviews as `pending` unless business policy permits instant publication.
7. Escape review content in Blade.
8. Prevent duplicate spam according to a documented policy.
9. Admin moderation actions must be audited.

---

# 15. Tailwind UI System

## 15.1 Colour tokens

Use Tailwind theme aliases rather than scattering raw values:

```js
brand: {
  50:  '#fdf2f8',
  600: '#db2777',
  700: '#be185d',
}
```

Primary surfaces: `#ffffff` and `#fdf2f8`.

## 15.2 Responsive grid

Required product grid:

```html
<div class="grid grid-cols-2 gap-3 sm:gap-4 md:grid-cols-4 lg:gap-6">
```

Do not use a one-column mobile product grid unless an accessibility exception is documented.

## 15.3 Page sections in exact order

1. Hero
2. Categories grid
3. Featured infinite marquee
4. Paginated all-products grid
5. Optional testimonials/blog/newsletter below the required commerce sections

## 15.4 Product card visual behavior

- Rounded, overflow-hidden image frame.
- Badge at top-left.
- Image uses `object-cover` and zooms subtly on group hover.
- Card receives a soft brand ring/shadow on hover/focus.
- Product name limited to a consistent line count.
- Original price crossed out only when a valid discount exists.
- Add to Cart and Buy Now remain visible/tappable on mobile.
- Disabled states communicate sold-out/preorder behavior.
- Minimum touch target approximately 44px.

## 15.5 Accessibility

- Semantic headings in order.
- Useful alt text from product name/category.
- Visible keyboard focus.
- Sufficient colour contrast.
- Buttons have accessible names.
- Marquee pauses or becomes static for `prefers-reduced-motion`.
- Do not communicate status by colour alone.

---

# 16. Pure CSS Featured Product Marquee

Use two identical product groups inside one track so the loop is seamless. Keep markup lightweight and avoid carousel libraries.

```css
.featured-marquee {
    overflow: hidden;
    mask-image: linear-gradient(to right, transparent, black 5%, black 95%, transparent);
}

.featured-marquee__track {
    display: flex;
    width: max-content;
    gap: 1rem;
    animation: featured-scroll 32s linear infinite;
    will-change: transform;
}

.featured-marquee:hover .featured-marquee__track,
.featured-marquee:focus-within .featured-marquee__track {
    animation-play-state: paused;
}

.featured-marquee__group {
    display: flex;
    flex-shrink: 0;
    gap: 1rem;
}

@keyframes featured-scroll {
    from { transform: translateX(0); }
    to   { transform: translateX(calc(-50% - 0.5rem)); }
}

@media (prefers-reduced-motion: reduce) {
    .featured-marquee {
        overflow-x: auto;
        mask-image: none;
    }

    .featured-marquee__track {
        animation: none;
    }

    .featured-marquee__group[aria-hidden="true"] {
        display: none;
    }
}
```

Implementation notes:

- Duplicate the same featured collection exactly once.
- Mark the duplicate group `aria-hidden="true"`.
- Do not duplicate focusable links for screen-reader/keyboard users unless the duplicate is removed from the tab order.
- Pause on hover and focus.
- Adjust duration based on item count, not with heavy JavaScript.

---

# 17. Blade View Plan

```text
resources/views/
  layouts/
    app.blade.php
    admin.blade.php
  components/
    storefront/
      product-card.blade.php
      price.blade.php
      badge.blade.php
      image.blade.php
      category-card.blade.php
      featured-marquee.blade.php
      rating-stars.blade.php
      status-badge.blade.php
      whatsapp-button.blade.php
  storefront/
    home.blade.php
    products/index.blade.php
    products/show.blade.php
    cart/index.blade.php
    checkout/show.blade.php
    checkout/success.blade.php
    wishlist/index.blade.php
    blog/index.blade.php
    blog/show.blade.php
  account/
    profile.blade.php
    orders/index.blade.php
    orders/show.blade.php
  admin/
    dashboard.blade.php
    categories/
    products/
    orders/
    coupons/
    reviews/
    testimonials/
    blog/
    settings/
  mail/
    orders/status-changed.blade.php
    orders/placed.blade.php
```

Use components to ensure product pricing, badges, image resolution, and status styling are consistent everywhere.

---

# 18. Floating WhatsApp Button

1. Read the public WhatsApp number from a safe, cached site setting.
2. Normalize it to digits with country code for `wa.me`.
3. Include a short prefilled message containing the current page/product when relevant.
4. Add `rel="noopener noreferrer"` and an accessible label.
5. Position fixed at bottom-right with spacing that does not cover mobile checkout controls or cookie notices.
6. Hide the button when the number is empty/invalid.

---

# 19. Admin Product and Order Rules

## Products

- CRUD with category, name, SKU, slug, prices, stock, flags, badge, descriptions, SEO, and gallery.
- Validate `discount_price < price`.
- Prevent duplicate SKU/slug.
- Product preview opens in a new page/tab.
- Use active/inactive instead of hard delete.
- Log stock adjustments.
- Optimize images asynchronously where possible.

## Orders

- Filter by order number, name, email, phone, order status, payment status, date, payment method.
- Detail page shows immutable order/customer/item snapshots.
- Status change uses allowed transitions and requires notes for exceptional changes.
- Payment status cannot be changed casually; provider verification or authorized manual action is required.
- Export actions must be authorized and protect customer data.

---

# 20. Performance Plan

## Database

Add or verify indexes for common queries:

- `products(category_id, is_active, id)`
- `products(is_featured, is_active, sort_order)`
- `products(is_new, is_active, created_at)`
- `orders(user_id, created_at)`
- `orders(order_status, created_at)`
- `orders(payment_status, created_at)`
- `orders(order_number)` unique
- `order_items(order_id)`
- `order_items(product_id)`
- `reviews(product_id, status, created_at)`
- `wishlist(user_id, product_id)` unique
- existing `cart_items(user_id, product_id)` unique
- `coupons(code)` unique
- `site_settings(setting_key)` unique

Avoid adding redundant indexes without reviewing `EXPLAIN` output and write overhead.

## Eloquent/querying

- Use eager loading to avoid N+1 queries.
- Select only card-required columns.
- Use aggregate subqueries for ratings.
- Use pagination, not `get()` for full product/order catalogs.
- Use chunking for admin exports/backfills.
- Prevent lazy loading in non-production development/test environments to catch N+1 issues.

## Cache

Cache:

- Safe site settings.
- Category navigation.
- Featured product IDs/cards for a short TTL.
- Homepage fragments only when invalidation is reliable.

Invalidate cache after relevant admin saves. Do not cache customer-specific carts/orders in shared keys.

## Frontend/assets

- Compile with Vite.
- Purge unused Tailwind classes through proper content configuration.
- Defer non-critical scripts.
- Use Alpine only for small interactions; do not add a heavy slider library.
- Serve appropriately sized WebP/AVIF variants when supported.
- Lazy-load below-the-fold images.
- Preload only the actual hero/LCP image.
- Set image width/height or aspect ratio to prevent layout shifts.

## Deployment optimization

During a controlled deployment/build:

- Install production Composer dependencies.
- Build minified frontend assets.
- Cache configuration/routes/views where compatible.
- Restart queue workers after deploy.
- Keep debug mode disabled in production.

Measure with Laravel query logging/Telescope in local or staging, browser performance tools, and real server response timings. Do not run heavy debug tooling publicly in production.

---

# 21. Security Plan

1. Rotate leaked credentials immediately.
2. Keep `.env`, SQL dumps, logs, backups, and uploaded private files out of Git/public web root.
3. Use Laravel validation/Form Requests for all writes.
4. Use CSRF protection for web forms.
5. Escape all customer-generated text in Blade.
6. Sanitize allowed admin-rich HTML.
7. Validate uploads by MIME, extension, size, and image decoding; generate new filenames.
8. Use authorization policies for every admin/customer resource.
9. Prevent IDOR by scoping customer orders to `auth()->id()`.
10. Rate-limit login, OTP, coupon attempts, checkout, review submission, and contact endpoints.
11. Use secure cookies, HTTPS, correct trusted proxy settings, and production session settings.
12. Never log passwords, OTPs, payment secrets, full payment payloads, or sensitive customer data unnecessarily.
13. Verify bKash callbacks/signatures/server-side status before marking paid.
14. Use database constraints plus application validation.
15. Protect admin routes with authentication, authorization, and preferably MFA.
16. Maintain dependency scanning and automated tests in CI.

---

# 22. Testing Strategy

## 22.1 Legacy migration tests

- Import a sanitized copy of the legacy dump in CI/staging.
- Run all additive migrations.
- Assert every original table row count is unchanged unless a documented duplicate cleanup applies.
- Assert primary keys remain unchanged.
- Assert no orphan foreign keys.
- Assert legacy totals remain unchanged.
- Assert images/settings resolve.
- Assert migrations are idempotent in the supported workflow.

## 22.2 Model tests

- Exact table names, especially `wishlist`.
- Casts and timestamp behavior.
- Nullable relationships.
- Price/effective price/badge logic.
- Slug/SKU uniqueness.
- Safe image URL resolver.

## 22.3 Checkout feature tests

- Guest checkout.
- Logged-in checkout.
- Cart merge.
- Coupon fixed/percentage/expired/minimum/limit cases.
- Shipping/free-shipping threshold.
- Sold-out and preorder cases.
- Price changed between cart and checkout.
- Product disabled after addition to cart.
- Duplicate checkout submission/idempotency.
- Transaction rollback leaves stock/coupon/cart/order unchanged.

## 22.4 Concurrency test

Create stock = 1 and submit two concurrent purchase attempts. Acceptance result:

- Exactly one order succeeds.
- Exactly one fails with an out-of-stock response.
- Final stock is zero, never negative.
- Only one sale inventory movement exists.

## 22.5 Order pipeline tests

- Valid transitions succeed.
- Invalid transitions fail.
- History row is created.
- Notification is queued after commit.
- Rollback sends no notification.
- Customer can only access own orders.

## 22.6 UI tests

- Exactly two products per row on mobile and four from `md` upward.
- Card links and action buttons do not conflict.
- Discount display is correct.
- Pagination retains filters/query strings.
- Marquee pauses and respects reduced motion.
- WhatsApp button does not cover checkout actions.
- Product gallery works with one or many images.
- Keyboard and screen-reader basics pass.

---

# 23. Acceptance Criteria

The rebuild is ready only when all conditions below are met:

## Data compatibility

- All valid legacy categories, products, users, orders, order items, wishlists, cart items, coupons, testimonials, blog posts, OTP records, and safe site settings are present.
- Legacy IDs and order totals are unchanged.
- No missing-column or timestamp exceptions occur.
- No foreign-key type mismatch exists.
- Existing local/remote images have a working fallback strategy.
- Unsupported password hashes have a documented secure migration path.

## Storefront

- Hero, category grid, featured marquee, and paginated product grid appear in the required order.
- Mobile grid has two products per row; desktop has four.
- Product card badges, discounts, Add to Cart, Buy Now, and detail navigation work.
- Product detail includes gallery, SKU, status, prices, reviews, and related products.
- WhatsApp contact works from mobile and desktop.

## Commerce

- Guest/session and authenticated carts work.
- Cart merge is deterministic.
- Checkout is transaction-safe and cannot oversell.
- Coupon and shipping calculations are server-side.
- Order item snapshots are preserved.
- Customer order history and status timeline work.
- Status emails are queued after commit.

## Admin

- Product/category/coupon/blog/testimonial/review management works.
- Order pipeline enforces allowed transitions.
- Product preview opens separately.
- Product deletion cannot destroy order history.
- Secrets cannot be edited or displayed through site settings.

## Quality

- Automated tests pass.
- Critical queries have verified indexes and no N+1 issue.
- Production build uses optimized assets/configuration.
- Backups, rollback, queue worker, scheduler, logging, and monitoring are documented.

---

# 24. Phased Delivery Plan

## Phase 0 - Discovery and production audit

- Inspect real codebase and database.
- Confirm hosting requirements.
- Inventory files/images.
- Identify password hashes and payment integration.
- Produce signed-off data audit and mapping.

## Phase 1 - Safe Laravel foundation

- Create Laravel project and environment structure.
- Add authentication/authorization foundation.
- Add baseline and compatibility migrations.
- Build exact Eloquent models/relationships/casts.
- Build settings and image resolver.

## Phase 2 - Legacy import and validation

- Import staging dump.
- Run audits, migrations, and backfills.
- Resolve data anomalies.
- Verify row counts, IDs, totals, images, and login path.

## Phase 3 - Storefront UI

- Layout/header/footer.
- Hero/category grid.
- Featured CSS marquee.
- Product grid/cards/pagination.
- Product detail/gallery/reviews/related products.
- WhatsApp button.

## Phase 4 - Cart and checkout

- Session cart.
- Auth cart and merge.
- Coupon/shipping service.
- Transactional checkout with locks.
- Order snapshots and inventory ledger.
- COD/bKash integration foundation.

## Phase 5 - Customer account and notifications

- Profile.
- Order history/detail/status timeline.
- Order placed/status mail templates.
- Queue workers/retry handling.

## Phase 6 - Admin

- Dashboard and CRUD modules.
- Product gallery/stock/preview.
- Order pipeline/history.
- Review moderation.
- Safe site settings.

## Phase 7 - Performance, security, QA

- Query profiling/index verification.
- Image optimization/caching.
- Full automated tests.
- Accessibility/responsive QA.
- Security review.
- Load/concurrency tests.

## Phase 8 - Cutover

- Rehearse migration.
- Freeze old writes.
- Final export/import.
- Run scripted verification.
- Deploy, warm cache, start workers.
- Smoke test and monitor.
- Maintain rollback readiness.

---

# 25. Developer/AI Execution Instruction

Use this section as the master instruction for implementation:

> Before generating any controller, model, migration, route, service, Blade view, test, or admin component, inspect this plan and the actual imported database. Use the exact legacy table names and signed integer keys. Preserve all legacy IDs and columns. Never assume standard Laravel timestamps exist. Build additive, idempotent compatibility migrations and run them on a staging clone before production. Keep `products.image`, `orders.total_amount`, `orders.coupon_code`, customer/address snapshots, and all legacy order records intact. Implement business rules in services, not Blade or controllers. Use database transactions and pessimistic row locks for checkout. Dispatch order notifications only after a successful commit. Do not hard-delete products referenced by orders. Do not store secrets in `site_settings` or source control. Every feature must include authorization, validation, failure handling, tests, and legacy-data acceptance checks.

---

# 26. Explicit Do-Not-Do List

- Do not replace signed legacy IDs with UUIDs/bigints during initial migration.
- Do not use `$table->id()`/`foreignId()` for references to legacy signed `INT` keys.
- Do not rename `wishlist` to `wishlists` without a controlled data migration.
- Do not enable default timestamps on a model before its table supports them.
- Do not overwrite legacy `total_amount`.
- Do not hard-delete products or users to “clean up” data.
- Do not cascade-delete historical order items.
- Do not use dummy seed data on production.
- Do not send status emails inside uncommitted transactions.
- Do not use mass order-status updates that bypass events/history.
- Do not trust browser totals, coupon discounts, or payment status.
- Do not expose or retain the SMTP password from the SQL dump.
- Do not render arbitrary setting/blog HTML unescaped.
- Do not permanently depend on Unsplash or other hotlinked images.
- Do not claim a response-time target is achieved until measured in production-like conditions.

---

# 27. Final Handover Deliverables

1. Sanitized database audit report.
2. Legacy-to-new schema mapping document.
3. Laravel migrations and reversible/backup-based rollback notes.
4. Backfill/import Artisan commands with dry-run mode.
5. Eloquent models and relationship tests.
6. Storefront controllers/services/routes.
7. Tailwind Blade component library.
8. Cart/checkout/coupon/stock services.
9. Order observer/event/listener/mail pipeline.
10. Customer account pages.
11. Admin CRUD/status pipeline.
12. Automated unit, feature, migration, and concurrency tests.
13. `.env.example` with placeholders only.
14. Deployment, queue, scheduler, backup, and rollback runbooks.
15. Final legacy-data verification report with row counts and exception list.

---

# 28. Official Framework References

- Laravel 13 Release Notes: https://laravel.com/docs/13.x/releases
- Eloquent Models and Events: https://laravel.com/docs/13.x/eloquent
- Eloquent Relationships: https://laravel.com/docs/13.x/eloquent-relationships
- Database Queries and Transactions: https://laravel.com/docs/13.x/queries
- Pagination: https://laravel.com/docs/13.x/pagination
- Validation: https://laravel.com/docs/13.x/validation
- Mail: https://laravel.com/docs/13.x/mail
- Notifications: https://laravel.com/docs/13.x/notifications
- Queues: https://laravel.com/docs/13.x/queues
- Authentication: https://laravel.com/docs/13.x/authentication
- CSRF Protection: https://laravel.com/docs/13.x/csrf
- Testing: https://laravel.com/docs/13.x/testing

