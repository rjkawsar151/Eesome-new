Use this expanded prompt for your coding agent:

---

# Build a Complete Laravel Admin Dashboard and E-commerce Management System

I have an existing **Laravel + Tailwind CSS e-commerce project for a bag store**. Extend the current project with a secure, production-ready admin dashboard where nearly all store content, products, orders, payments, shipping, users, reviews, blog posts, homepage sections, and global settings can be managed without editing code.

Before making changes, inspect the existing:

* Laravel and PHP versions
* Authentication system
* User and product models
* Database schema and migrations
* Admin dashboard, if one already exists
* Frontend build setup
* Tailwind configuration
* JavaScript libraries
* Mail configuration
* File-storage system
* Payment implementation
* Order-processing logic
* Existing roles and permissions

Reuse the current project structure and conventions. Do not rebuild existing features unnecessarily or introduce duplicate models, tables, fields, routes, or packages.

## Main objective

Create a modern, responsive admin dashboard with the following modules:

1. Dashboard overview
2. User management
3. Roles and permissions
4. Hero carousel management
5. Product and category management
6. Order management and order pipeline
7. Customer email notifications
8. Admin new-order alerts
9. Reviews management
10. Blog management
11. Store settings
12. Payment settings
13. Shipping settings
14. Media and image management
15. Site-wide SEO and content settings
16. Admin activity logs

All important store operations should be manageable from the admin dashboard.

---

# 1. Admin Authentication and Authorization

Create a protected admin area, for example:

```text
/admin
```

Only authorized administrators and staff should be able to access it.

Implement role-based access control with roles such as:

* Super Admin
* Admin
* Store Manager
* Content Editor

Permissions should be granular, for example:

* View dashboard
* Manage users
* Manage roles
* Manage products
* Manage categories
* Manage orders
* Change order status
* Manage reviews
* Manage blog posts
* Manage homepage hero
* Manage payments
* Manage shipping
* Manage store settings
* View reports
* View activity logs

Prefer the project’s existing permissions system. If none exists, use a well-maintained Laravel-compatible package such as Spatie Laravel Permission.

The Super Admin must have full access.

Prevent administrators from accidentally removing their own final Super Admin access.

---

# 2. Dashboard Overview

Create a clean dashboard homepage containing useful store statistics.

Show cards for:

* Total orders
* Orders today
* Pending or awaiting orders
* Processing orders
* Shipped orders
* Delivered orders
* Cancelled orders
* Total customers
* Total products
* Low-stock products
* Pending reviews
* Published blog posts
* Revenue today
* Revenue this month
* Total revenue

Add sections for:

* Recent orders
* Latest customers
* Low-stock products
* Recent reviews
* Recent admin activity
* Order-status summary
* Sales chart where suitable

Use efficient database queries and avoid N+1 queries.

---

# 3. User Management

Create complete user CRUD functionality.

Administrators should be able to:

* View users
* Search users
* Filter by status or role
* Add users
* Edit users
* Disable or enable accounts
* Assign roles
* Reset or update passwords securely
* View customer order history
* View customer reviews
* View customer contact information
* Delete users when permitted
* Soft-delete users where appropriate

Suggested user fields:

```text
name
email
phone
password
status
email_verified_at
last_login_at
```

Do not expose passwords or sensitive authentication data.

Prevent deletion of users whose records are required for historical orders. In those cases, disable or anonymize the user instead.

---

# 4. Hero Carousel Management

Create a Hero Management section for the homepage product carousel.

Administrators should be able to:

* Add carousel items
* Edit carousel items
* Delete carousel items
* Enable or disable items
* Change display order
* Upload transparent product PNG or WebP images
* Set product name
* Set subtitle
* Set CTA label
* Set CTA URL
* Link a carousel item to a product
* Set optional starting and ending dates
* Preview the carousel item
* Reorder slides using drag and drop when practical

The storefront carousel should show three bags at once:

* Active bag in the center
* Center bag larger, fully visible, and sharp
* Previous and next bags smaller
* Side bags slightly transparent and softly blurred
* Active product name and CTA displayed
* Smooth transitions
* Mobile swipe support
* Keyboard navigation
* Reduced-motion support

Do not hardcode carousel products.

---

# 5. Product Management

Create a complete product-management module.

Administrators should be able to:

* View all products
* Search products
* Filter by category, brand, status, stock, and featured state
* Add products
* Edit products
* Duplicate products
* Delete or archive products
* Enable or disable products
* Mark products as featured
* Manage product images
* Manage stock
* Manage pricing
* Manage sale pricing
* Manage SEO fields
* Manage product variants
* Set product display order
* Preview the product page

Suggested product fields:

```text
name
slug
sku
short_description
description
price
sale_price
cost_price
stock_quantity
low_stock_threshold
category_id
brand_id
status
is_featured
is_active
weight
dimensions
meta_title
meta_description
```

Reuse existing equivalent fields.

## Product editor

For the product description field, integrate a rich-text editor.

Preferred options:

* CKEditor 5
* Tiptap
* TinyMCE
* The rich-text editor already used by the project

The editor should support:

* Headings
* Bold and italic text
* Links
* Lists
* Tables
* Block quotes
* Image insertion if permitted
* Source-safe HTML output
* Sanitized content

Do not store unsafe JavaScript or unsanitized HTML.

## Product images

Support:

* Main product image
* Additional gallery images
* Transparent PNG or WebP files
* Image preview
* Image removal
* Image ordering
* Alternative text
* File validation
* Reasonable upload-size limits

Use the existing Laravel storage configuration.

## Product variants

Where suitable, support:

* Color
* Size
* Material
* SKU
* Additional price
* Stock quantity
* Variant image
* Enabled or disabled state

Only add variant support if the project’s product structure can support it cleanly.

---

# 6. Categories, Brands, and Attributes

Create CRUD modules for:

* Product categories
* Product brands
* Product tags
* Product attributes
* Product variants or options

Category fields may include:

```text
name
slug
parent_id
description
image
status
sort_order
meta_title
meta_description
```

Support nested categories where appropriate.

---

# 7. Order Management

Create a complete order-management section.

Administrators should be able to:

* View all orders
* Search by order number, name, email, or phone
* Filter by status, payment status, date, or delivery method
* View order details
* View customer details
* View shipping and billing addresses
* View ordered products
* View totals, discounts, taxes, shipping, and payment details
* Add internal notes
* Add customer-visible notes
* Change order status
* Change payment status
* Print invoice
* Download invoice
* Cancel an order
* Create refunds where supported
* View order-status history
* Resend order notifications

## Order pipeline

Implement an order-status workflow using consistent internal values.

Use statuses such as:

```text
awaiting
processing
shipped
in_transit
delivered
cancelled
```

Displayed labels should be:

* Awaiting
* Processing
* Shipped
* In Transit
* Delivered
* Cancelled

Optionally support:

```text
pending_payment
confirmed
returned
refunded
failed
```

Do not use misspelled database status values.

Store every status change in an order-status history table.

Suggested fields:

```text
order_id
previous_status
new_status
changed_by
note
created_at
```

The order details page should display a timeline showing the full order history.

## Order-status rules

Add safe workflow validation. For example:

* Delivered orders should not move back to Awaiting without special permission.
* Cancelled orders should not be shipped.
* Refund status should be separate from shipping status.
* Payment status should be separate from order fulfillment status.

Suggested payment statuses:

```text
unpaid
pending
paid
partially_paid
failed
refunded
partially_refunded
```

---

# 8. Customer Email Notifications

Each order-status change should send an email to the customer when the order contains a valid email address.

Create notification emails for:

* New order confirmation
* Awaiting confirmation
* Processing
* Shipped
* In transit
* Delivered
* Cancelled
* Payment received
* Payment failed
* Refund processed

Each email should include:

* Customer name
* Order number
* New status
* Order summary
* Shipping address where appropriate
* Tracking number where available
* Store name
* Support contact details
* Link to view the order where supported

Use Laravel Mail or Notifications.

Send emails through queues rather than blocking the request.

If email delivery fails:

* Do not fail the order-status update
* Log the error
* Display a warning to the administrator
* Allow the email to be resent

Do not send a status email when the status has not actually changed.

Add an optional setting that lets administrators enable or disable specific email types.

---

# 9. New Order Alerts for Administrators

When a new order is placed, send an alert to administrator email addresses configured in `.env`.

Example:

```env
ADMIN_ORDER_ALERT_EMAILS=admin@example.com,manager@example.com
```

Support multiple comma-separated addresses.

Use a config file rather than reading `.env` directly throughout the application.

Example:

```php
'order_alert_emails' => array_filter(
    array_map(
        'trim',
        explode(',', env('ADMIN_ORDER_ALERT_EMAILS', ''))
    )
),
```

The admin notification should include:

* Order number
* Customer name
* Customer phone
* Customer email
* Ordered products
* Order total
* Payment method
* Shipping method
* Delivery address
* Link to the admin order page

This email should also be queued.

Optionally show an in-dashboard notification for new orders.

---

# 10. Shipping and Tracking

Allow administrators to manage shipping information for an order.

Fields may include:

```text
shipping_provider
tracking_number
tracking_url
shipped_at
estimated_delivery_at
delivered_at
```

When an order is marked as Shipped or In Transit:

* Allow entry of the tracking number
* Include tracking details in the customer email
* Save the update in the order history

---

# 11. Shipping Charge Management

Create a Shipping Settings module.

Administrators should be able to manage:

* Default shipping fee
* Free-shipping threshold
* Shipping zones
* City-based fees
* State or region-based fees
* Country-based fees
* Weight-based fees
* Order-total-based fees
* Delivery method
* Estimated delivery time
* Cash-on-delivery availability
* Shipping-method status
* Minimum order amount
* Maximum order weight where applicable

Suggested shipping-method fields:

```text
name
code
description
charge_type
base_charge
minimum_order_amount
free_shipping_threshold
estimated_delivery_days
is_active
sort_order
```

Suggested charge types:

```text
flat
free
weight_based
order_total_based
location_based
```

Shipping calculations must also be validated on the server. Never trust totals submitted by the browser.

---

# 12. Payment Settings

Create a Payment Settings module.

Administrators should be able to configure payment options such as:

* Cash on delivery
* Bank transfer
* Mobile payment
* Manual payment
* Online payment gateways already supported by the project

For payment numbers and accounts, support fields such as:

```text
payment_method_name
account_name
account_number
instructions
logo
is_active
sort_order
```

For mobile payment, allow management of values such as:

* Provider name
* Merchant or personal number
* Account type
* Payment instructions
* Transaction ID requirement

Sensitive gateway credentials must not be displayed as plain text.

Gateway API secrets should remain in environment variables or an encrypted secure-storage mechanism. The dashboard may display whether a gateway is configured, but must not reveal secrets.

Payment settings stored in the database should be validated and encrypted when sensitive.

---

# 13. Store Settings Management

Create a central Store Settings section using grouped key-value settings or structured settings tables.

Administrators should be able to manage:

## General settings

* Store name
* Store tagline
* Store logo
* Favicon
* Store email
* Support email
* Store phone
* WhatsApp number
* Business address
* Currency
* Currency symbol
* Time zone
* Date format
* Default language
* Maintenance mode message

## Homepage settings

* Hero heading
* Hero subtitle
* Hero visibility
* Featured-products section
* New-arrivals section
* Best-sellers section
* Promotional banners
* Newsletter section
* Testimonials section
* Homepage section ordering

## Contact and social settings

* Facebook
* Instagram
* YouTube
* TikTok
* X or Twitter
* LinkedIn
* WhatsApp
* Google Maps URL

## Checkout settings

* Guest checkout
* Account registration requirement
* Minimum order amount
* Maximum order amount
* Cash-on-delivery availability
* Customer order notes
* Address requirements
* Phone-number requirement

## Email settings

* Sender name
* Sender email
* Support email
* Admin order-alert recipients
* Email notification toggles
* Email footer content

Mail server passwords should remain outside normal editable settings unless they are securely encrypted and access-controlled.

## SEO settings

* Default meta title
* Default meta description
* Default social image
* Robots settings
* Google verification code
* Analytics ID
* Tag manager ID
* Structured-data details

## Legal and policy content

* Terms and conditions
* Privacy policy
* Refund policy
* Shipping policy
* Return policy
* Cancellation policy

Use a rich-text editor for policy content.

## Settings architecture

Implement a reusable settings service, for example:

```php
setting('store.name')
setting('shipping.default_charge')
```

Cache settings to avoid repeated database queries.

Clear the settings cache automatically when an administrator updates settings.

Do not load every setting with a new query.

---

# 14. Reviews Management

Create a review-management section.

Administrators should be able to:

* View all reviews
* Search reviews
* Filter by product, rating, customer, and status
* Approve reviews
* Reject reviews
* Mark reviews as spam
* Edit inappropriate text when policy permits
* Delete reviews
* Reply to reviews
* Feature selected reviews

Suggested review statuses:

```text
pending
approved
rejected
spam
```

Display:

* Product
* Customer
* Rating
* Review title
* Review body
* Submission date
* Status
* Admin reply

Only approved reviews should be publicly visible.

Implement moderation checks and escape user-generated output.

---

# 15. Blog Management

Create a complete blog-writing section.

Administrators and content editors should be able to:

* Create posts
* Edit posts
* Delete posts
* Save drafts
* Publish posts
* Schedule posts
* Add featured images
* Assign categories
* Assign tags
* Write excerpts
* Manage SEO metadata
* Preview posts
* Change author
* Set related products
* Set publication date

Suggested blog fields:

```text
title
slug
excerpt
content
featured_image
author_id
status
published_at
meta_title
meta_description
canonical_url
```

Suggested statuses:

```text
draft
scheduled
published
archived
```

Use CKEditor 5, Tiptap, TinyMCE, or an existing project editor.

Sanitize stored HTML.

The blog editor should support:

* Headings
* Lists
* Links
* Images
* Tables
* Quotes
* Embeds where safely supported
* Product links

---

# 16. Media Management

Create a reusable media picker or media library when practical.

Administrators should be able to:

* Upload images
* Preview images
* Search files
* Delete unused files where safe
* Copy file URLs
* Add alternative text
* Reuse existing media

Prevent deletion of files that are actively used, or clearly warn the administrator.

Validate MIME types rather than relying only on file extensions.

---

# 17. Site Navigation and Footer Management

Allow administrators to manage:

* Header menu items
* Footer menu items
* Dropdown links
* Link labels
* Link URLs
* Display order
* Visibility
* External-link behavior

Support linking to:

* Products
* Categories
* Blog posts
* Policy pages
* Custom URLs

---

# 18. Discounts and Coupons

Add coupon management if the current application supports checkout discounts.

Administrators should be able to configure:

* Coupon code
* Fixed or percentage discount
* Start date
* Expiration date
* Usage limit
* Per-user limit
* Minimum order amount
* Maximum discount
* Eligible products
* Eligible categories
* Active status

All coupon calculations must be performed and validated on the server.

---

# 19. Inventory Management

Support:

* Product stock
* Variant stock
* Low-stock threshold
* Out-of-stock status
* Stock adjustment
* Stock history
* Optional backorders

Inventory should be reduced safely when an order is confirmed according to the project’s business rules.

Prevent race conditions and negative stock where possible.

Use database transactions for critical stock operations.

---

# 20. Admin Activity Logs

Record important administrative actions.

Log activities such as:

* User created or updated
* Role changed
* Product created, updated, or deleted
* Order status changed
* Payment status changed
* Review moderated
* Blog post published
* Settings changed
* Shipping method changed
* Payment method changed

Suggested activity fields:

```text
admin_id
action
subject_type
subject_id
description
old_values
new_values
ip_address
user_agent
created_at
```

Sensitive values such as passwords, API secrets, tokens, and payment credentials must never be stored in activity logs.

---

# 21. Notifications Inside the Dashboard

Add a notification area for administrators.

Possible notifications:

* New order
* Payment failure
* Low-stock product
* New review awaiting approval
* Contact request
* Refund request
* Failed queued email

Allow notifications to be marked as read.

---

# 22. Search, Filters, Pagination, and Bulk Actions

For all large admin tables:

* Use server-side pagination
* Support search
* Support filters
* Support sorting
* Preserve filters during pagination
* Add clear empty states
* Add bulk actions where safe

Possible bulk actions:

* Enable products
* Disable products
* Delete products
* Approve reviews
* Reject reviews
* Publish blog posts
* Archive blog posts
* Update order assignment

Require confirmation for destructive bulk actions.

---

# 23. Validation and Security

Use Laravel Form Request classes for validation.

Implement:

* CSRF protection
* Authorization policies
* Route middleware
* Server-side validation
* Output escaping
* Safe rich-text sanitization
* Secure file uploads
* Rate limiting where appropriate
* Encrypted sensitive settings
* Database transactions for critical operations
* Protection from mass assignment
* Protection from IDOR vulnerabilities
* Secure password handling
* Safe error logging

Never trust:

* Product prices submitted from the browser
* Shipping totals submitted from the browser
* Discount values submitted from the browser
* Payment status submitted by customers
* Order totals submitted from JavaScript

Recalculate order totals on the server.

---

# 24. UI and UX Requirements

Use the existing Tailwind CSS design system.

The admin dashboard should have:

* Responsive sidebar
* Mobile navigation
* Dashboard header
* Breadcrumbs
* Search
* User menu
* Notification menu
* Confirmation modals
* Toast messages
* Validation errors
* Loading states
* Empty states
* Accessible forms
* Accessible tables
* Clear status badges

Keep the interface modern, clean, and consistent.

Do not add a second CSS framework.

Use Blade components for repeated elements such as:

```text
form inputs
buttons
status badges
tables
pagination
modals
alerts
file uploaders
rich-text editors
```

---

# 25. Recommended Route Structure

Use route prefixes and names similar to:

```php
Route::prefix('admin')
    ->name('admin.')
    ->middleware(['auth', 'admin'])
    ->group(function () {
        // Dashboard
        // Users
        // Roles
        // Hero slides
        // Products
        // Categories
        // Orders
        // Reviews
        // Blog
        // Shipping
        // Payments
        // Settings
        // Activity logs
    });
```

Use resource controllers where appropriate.

Do not place all admin logic in one controller.

---

# 26. Suggested Application Structure

Use a clean structure, adapted to the current project:

```text
app/
  Http/
    Controllers/
      Admin/
    Requests/
      Admin/
    Middleware/
  Models/
  Notifications/
  Mail/
  Services/
  Policies/
  Enums/
  Jobs/
  Events/
  Listeners/

resources/
  views/
    admin/
    components/
  js/
    admin/

routes/
  admin.php

database/
  migrations/
  seeders/
```

Register a separate `admin.php` route file only if it fits the project architecture.

---

# 27. Enums and Status Handling

Use PHP enums where supported by the project’s PHP and Laravel versions.

Example:

```php
enum OrderStatus: string
{
    case Awaiting = 'awaiting';
    case Processing = 'processing';
    case Shipped = 'shipped';
    case InTransit = 'in_transit';
    case Delivered = 'delivered';
    case Cancelled = 'cancelled';
}
```

Centralize status labels, colors, transition rules, and notification behavior.

Do not scatter raw status strings across controllers and Blade files.

---

# 28. Queues and Background Jobs

Use Laravel queues for:

* Customer emails
* Administrator order alerts
* Bulk emails
* Image processing
* Slow notification tasks

Provide the required queue configuration and worker instructions.

Example development command:

```bash
php artisan queue:work
```

Use retry logic and failed-job logging.

---

# 29. Tests

Add tests for critical functionality.

At minimum, test:

* Unauthorized users cannot access admin routes
* Role permissions work
* Product creation and update
* Product image validation
* Order status changes
* Invalid order-status transitions
* Customer notification dispatch
* Administrator new-order alerts
* Shipping-charge calculations
* Payment-method settings
* Review approval
* Blog publishing
* Settings updates
* Sensitive values are not exposed
* Server-side total calculations

Use feature tests and unit tests where appropriate.

---

# 30. Seeders and Initial Setup

Create seeders for:

* Super Admin role
* Initial Super Admin user
* Default permissions
* Default store settings
* Default order statuses
* Default payment methods
* Default shipping method

Do not hardcode production passwords.

Read the initial administrator credentials from environment variables or provide a secure command to create the first administrator.

---

# 31. Required Deliverables

Provide all necessary code and clearly identify each file.

Include:

1. Migrations
2. Models and relationships
3. Enums
4. Controllers
5. Form Requests
6. Policies and permissions
7. Routes
8. Blade views
9. Blade components
10. Tailwind classes
11. JavaScript components
12. Rich-text editor integration
13. Mail classes or notifications
14. Queue jobs
15. Order-status history
16. Settings service
17. Shipping calculation service
18. Admin alert configuration
19. Seeders
20. Tests
21. Required Composer or npm packages
22. Installation and build commands
23. Environment-variable examples
24. Deployment notes
25. A summary of assumptions

Do not provide only high-level examples. Implement the feature in production-ready code that fits the existing application.

---

# 32. Implementation Process

Work in phases to reduce risk.

## Phase 1

* Inspect project
* Document existing architecture
* Add admin authentication
* Add roles and permissions
* Create dashboard layout

## Phase 2

* User management
* Product management
* Categories and media
* Hero carousel management

## Phase 3

* Order management
* Order-status pipeline
* Status history
* Customer emails
* Administrator alerts
* Shipping and tracking

## Phase 4

* Store settings
* Payment settings
* Shipping settings
* Reviews
* Blog

## Phase 5

* Activity logs
* Dashboard notifications
* Reports
* Tests
* Performance improvements
* Security review

After each phase:

* Run migrations
* Run tests
* Run code formatting
* Check authorization
* Verify responsive behavior
* Report changed files

Do not make unrelated destructive changes.

---

# Acceptance Criteria

The work is complete when:

* Admin routes are securely protected.
* Roles and granular permissions work.
* Administrators can manage users.
* Hero carousel products can be managed from the dashboard.
* Products can be added, edited, archived, and deleted.
* Product descriptions use a secure rich-text editor.
* Product images and galleries can be managed.
* Orders can move through Awaiting, Processing, Shipped, In Transit, Delivered, and Cancelled.
* Every real status change is stored in order history.
* Customers receive status-update emails when an email is available.
* Administrator addresses from `.env` receive new-order alerts.
* Mail is queued and failure-safe.
* Payment options and payment numbers can be managed securely.
* Shipping charges and delivery methods can be managed.
* Store-wide settings can be changed without code edits.
* Reviews can be moderated.
* Blog posts can be drafted, scheduled, published, and archived.
* Critical admin actions are logged.
* The dashboard works on desktop and mobile.
* Validation, authorization, and security protections are applied.
* Tests cover critical workflows.
* Existing store functionality remains intact.

Use the current application as the source of truth. Where an equivalent feature already exists, improve and extend it instead of building a duplicate.
