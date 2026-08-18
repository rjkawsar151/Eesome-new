Below is a complete implementation prompt you can give to your developer or coding AI. I’ve written it so the existing order/email functionality stays intact while the email design and order-tracking experience are upgraded.

# EESOME — Update Existing Order Email Form + Real-Time Order Tracking

Update the **existing EESOME order-status email template and order-tracking user interface**.

This is an update to the existing system, **not a rebuild**.

## CRITICAL REQUIREMENT — DO NOT BREAK EXISTING FUNCTIONS

Do **not** change, remove, rewrite, or interfere with any existing backend/business logic that is already working.

This includes, but is not limited to:

* order creation
* checkout
* payment handling
* order status updates
* admin order management
* inventory
* customer data
* email sending triggers
* order status hooks
* database structure unless absolutely required for tracking
* existing APIs
* existing authentication
* existing age verification / age function
* existing security logic
* existing customer-account functionality
* existing webhook functionality

If there is an existing **age-verification function**, it must remain completely untouched.

The objective is mainly to:

1. update the existing order-status email form/design;
2. correctly display content based on the current order status;
3. add a secure no-login order-tracking experience;
4. add an order tracker section/page to the website;
5. connect both experiences to the existing live order data.

Do not create duplicate order-status logic if the website already has a source of truth for order status.

---

# 1. UPDATE THE EXISTING ORDER EMAIL DESIGN

Use the existing EESOME branding and upgraded logo.

Design theme:

* white
* very light pink
* soft pink
* elegant luxury appearance
* clean spacing
* responsive/mobile friendly
* professional ecommerce email layout
* simple typography
* strong visual hierarchy

Use the updated EESOME logo at the top of the email.

Use SVG where supported, but provide a PNG fallback for email clients such as Outlook that may not reliably display SVG.

Do not embed an unnecessarily large logo.

---

# 2. USE ONE DYNAMIC EMAIL TEMPLATE

Do not create completely separate duplicated HTML templates for every status unless the existing system requires it.

Prefer one reusable order-status email template that dynamically changes according to the order status.

Possible statuses include:

* Confirmed
* Pending
* Processing
* On Hold
* Shipped
* Out for Delivery
* Delivered
* Cancelled
* Refunded
* Partially Refunded
* Failed
* Completed
* Returned

Also gracefully support custom/new statuses added later.

The email should automatically display the correct:

* headline
* message
* icon
* status badge
* timeline state
* action button
* tracking information

based on the actual current order status.

---

# 3. CANCELLATION MESSAGE MUST ONLY SHOW FOR CANCELLED ORDERS

The following cancellation alert must appear **ONLY when the order status is actually cancelled**.

It must never appear for:

* confirmed
* pending
* processing
* on hold
* shipped
* out for delivery
* delivered
* completed
* refunded
* any other non-cancelled status

For a cancelled order show a red alert section with a red warning/error icon.

Use wording similar to:

**We're sorry — your order has been cancelled.**

If you have any questions or would like help placing a new order, please call us at **{{support_phone}}** or email us at **{{support_email}}**.

Use the actual website support phone/email already configured in the system.

Do not hardcode fake contact information.

Where appropriate:

* phone should use `tel:`
* email should use `mailto:`

The entire cancellation alert component must be conditionally rendered.

For example conceptually:

`if order.status == cancelled`

show cancellation alert.

Otherwise:

do not render the cancellation alert at all.

Do not merely hide it visually with CSS.

It should not exist in the rendered HTML for non-cancelled emails.

---

# 4. ORDER INFORMATION IN THE EMAIL

Show a professional order summary including available data such as:

* Order number
* Order date
* Current order status
* Customer name
* Product image
* Product name
* Variant
* Size
* Quantity
* Item price
* Subtotal
* Discount
* Shipping
* Tax
* Refund amount, when applicable
* Total
* Payment method, if appropriate
* Shipping method
* Shipping destination
* Tracking number, when available
* Carrier, when available

Do not expose unnecessary sensitive information.

---

# 5. EMAIL ORDER STATUS TIMELINE

Include a clean visual status/progress section where appropriate.

Example normal fulfillment timeline:

**Order Confirmed → Processing → Shipped → Out for Delivery → Delivered**

The current status should be visually highlighted.

Completed stages should appear completed.

Future stages should appear inactive.

The timeline shown in the email can represent the status at the moment the email was generated.

However, the **Track Order** button must open the website's live tracker, which retrieves the latest order status.

For On Hold, Cancelled, Refunded, Returned, Failed, or other exceptional states, display an appropriate status presentation instead of falsely progressing through the normal fulfillment path.

---

# 6. TRACK ORDER BUTTON

Add a prominent **Track Order** button to relevant order emails.

Example text:

**Track Order**

The button should use the site's pink branding.

Do not require the customer to log in.

When the customer clicks **Track Order** from an email:

1. open the website's order-tracking page;
2. identify the correct order securely from the tracking URL;
3. automatically load that order;
4. immediately retrieve the current order information;
5. display the real-time order-status timeline;
6. do not ask the customer to type the order number again;
7. do not require account login.

Example concept:

`https://example.com/track-order/?token=SECURE_SIGNED_TOKEN`

or use the website's existing secure tracking mechanism if one already exists.

Do not rely on an easily predictable order ID in the URL if it exposes customer information.

---

# 7. SECURE NO-LOGIN TRACKING FROM EMAIL

Email tracking must work without login, but it still needs to be secure.

Preferred method:

Generate a secure signed tracking token associated with the order.

The token should:

* identify the order
* be difficult/impossible to guess
* not expose internal database IDs unnecessarily
* be validated server-side
* not expose payment credentials
* not expose sensitive customer data
* optionally support expiration if appropriate

If the existing ecommerce platform already provides a secure order-tracking key/token, reuse that rather than creating a new system.

Do not weaken existing security.

---

# 8. WEBSITE ORDER TRACKER SECTION

Create an attractive **Track Your Order** section/page on the website.

It should match EESOME branding:

* white
* light pink
* pink accents
* premium/luxury presentation
* responsive layout

Suggested heading:

# Track Your Order

Suggested supporting text:

Enter your order number below to check the latest status of your order.

Include an input such as:

**Order Number**

Example placeholder:

`#EESOME-12345`

Button:

**Track Order**

---

# 9. SECURITY FOR MANUAL ORDER-NUMBER LOOKUP

The customer wants to be able to enter an order number without logging in.

Do not reveal private customer/order information to anyone who simply guesses an order number.

If tracking uses **order number only**, restrict the result to safe non-sensitive information such as:

* order status
* status timeline
* dates associated with fulfillment updates
* carrier
* tracking number only if appropriate
* delivery status

Do not expose:

* full customer name
* full email
* full phone number
* billing information
* payment information
* full billing address
* private order notes
* admin notes
* internal IDs
* fraud/security information

For stronger privacy, preferably support verification using:

**Order number + email address**

or:

**Order number + phone/email**

unless the project requirement specifically mandates order-number-only tracking.

Another acceptable implementation is to use a secure order tracking key in addition to the visible order number.

Do not sacrifice customer privacy for convenience.

---

# 10. AUTOMATIC TRACKING WHEN OPENED FROM EMAIL

There should be two ways to use the same tracker.

## Method A — Customer visits tracker manually

Customer opens:

`/track-order`

They see the tracking form.

They enter their order number.

After submitting, load the timeline.

## Method B — Customer clicks Track Order from an email

Customer clicks the email button.

The URL contains the appropriate secure tracking reference/token.

For example:

`/track-order/?token={{secure_tracking_token}}`

The tracking page detects the token automatically.

It should then:

* validate the token
* find the correct order
* immediately display the order timeline
* bypass the manual lookup form or place it above/below the result as appropriate

The customer should **not have to enter their order number manually after clicking the email button.**

---

# 11. REAL-TIME ORDER STATUS

The website tracking page must retrieve data from the actual existing order system.

Do not create a fake/static progress timeline.

The tracker should reflect the latest order status available from the backend.

For example:

Admin changes:

`Processing → Shipped`

The tracking page should then show:

**Shipped**

without requiring any manual duplicate status update elsewhere.

Use the existing order database/order-status system as the single source of truth.

If the page is already open, it can refresh status periodically or provide a refresh function if appropriate.

Avoid unnecessary aggressive polling.

---

# 12. TRACKING TIMELINE DESIGN

Create a professional vertical timeline on mobile and an appropriate horizontal or vertical layout on desktop.

Example:

✓ Order Confirmed
August 18, 2026 · 10:42 AM

│

✓ Processing
August 18, 2026 · 2:15 PM

│

● Shipped
August 19, 2026 · 9:20 AM

│

○ Out for Delivery

│

○ Delivered

Use:

* checkmarks for completed stages
* highlighted pink/current-state icon for active status
* muted/light styling for future stages
* red styling for cancellation
* appropriate styling for hold/refund/return statuses

When timestamps exist in the backend, show the real timestamps.

Do not invent timestamps.

---

# 13. SPECIAL STATUS BEHAVIOR

## Confirmed

Show something similar to:

**Your order is confirmed**

We've received your order and will begin preparing it shortly.

---

## Processing

Show:

**We're preparing your order**

Your order is currently being prepared for shipment.

---

## On Hold

Show an amber/neutral information component.

Example:

**Your order is currently on hold**

We'll update you as soon as processing resumes.

Do not show the red cancellation message.

---

## Shipped

Show:

**Your order is on the way**

Include when available:

* carrier
* tracking number
* tracking button/link
* shipping date

---

## Out for Delivery

Show:

**Your order is out for delivery**

Make the current status prominent.

---

## Delivered

Show:

**Your order has been delivered**

Use a positive success indicator.

---

## Cancelled

Use a red warning icon and red alert styling.

Show:

**We're sorry — your order has been cancelled.**

Then show the support contact information.

Do not display normal future fulfillment steps as though the order will continue to ship.

---

## Refunded

Clearly show that the order/refund has been processed.

If the backend provides refund amount/date, display it appropriately.

Do not treat a refund as a cancellation unless the actual status is cancelled.

---

# 14. TRACKING DETAILS CARD

When shipping information is available, include a tracking information card.

Possible fields:

**Carrier:** {{carrier}}

**Tracking Number:** {{tracking_number}}

**Shipment Status:** {{shipment_status}}

Button:

**Track Shipment**

If the carrier provides an external tracking URL, it can be available in addition to the EESOME internal order timeline.

The main EESOME **Track Order** button should still open the EESOME website tracker.

---

# 15. URL BEHAVIOR

Use a clean route such as:

`/track-order`

Manual:

`/track-order`

Email deep link:

`/track-order/?token={{secure_order_tracking_token}}`

If the current website framework has a preferred routing convention, follow it.

Preserve any existing tracking routes where possible to avoid broken historical email links.

---

# 16. INVALID ORDER

If no matching order is found, do not expose technical/database errors.

Show:

**We couldn't find that order.**

Please check your order number and try again.

Also provide a way to contact support using the website's existing contact details.

---

# 17. INVALID OR EXPIRED TRACKING LINK

If an email tracking token is:

* invalid
* modified
* expired
* deleted
* no longer valid

show a safe message such as:

**This tracking link is no longer valid.**

Please enter your order information below or contact our support team for assistance.

Never expose stack traces or debugging information.

---

# 18. MOBILE RESPONSIVENESS

Both the email and website tracker must be fully responsive.

Test common widths including approximately:

* 320px
* 375px
* 390px
* 430px
* tablet
* laptop
* desktop

On mobile:

* buttons should be easy to tap
* typography should remain readable
* order information should stack cleanly
* timeline should preferably be vertical
* no horizontal scrolling
* product images should resize correctly

---

# 19. EMAIL CLIENT COMPATIBILITY

The email must work reasonably across common clients including:

* Gmail
* Gmail mobile
* Apple Mail
* Outlook
* iPhone Mail
* Android mail clients

Use email-safe HTML/CSS.

Prefer:

* table-based layout where needed
* inline CSS for important presentation
* safe fonts
* proper image dimensions
* absolute HTTPS image URLs in production

Do not depend on JavaScript inside email.

The real-time functionality belongs on the website, not inside the email itself.

---

# 20. LOGO

Use the updated EESOME logo.

Preferred implementation:

* SVG for supported web usage
* PNG fallback for email clients

Maintain correct aspect ratio.

Do not stretch, crop, or distort the logo.

Ensure it remains sharp on high-density/mobile displays.

---

# 21. ACCESSIBILITY

Use semantic and accessible UI where practical.

Requirements:

* sufficient contrast
* meaningful button labels
* readable font sizes
* alt text for the logo/product images
* status information cannot rely entirely on color
* keyboard-accessible website tracker
* proper form labels
* meaningful error messages

---

# 22. PERFORMANCE

Do not load unnecessary JavaScript libraries just for the tracker.

Reuse the existing framework/component system.

Keep:

* CSS lightweight
* API calls minimal
* images optimized
* tracking requests efficient

---

# 23. DO NOT DUPLICATE DATA

There must be one source of truth for order status.

The following should all read from the same actual order data:

* admin order view
* email status
* website tracker
* customer account order view, if one exists

Do not create a separate manually maintained tracking-status database unless technically necessary.

---

# 24. STATUS HISTORY

If the existing platform records order-status history, use it.

Example data:

* Confirmed — date/time
* Processing — date/time
* Shipped — date/time
* Out for Delivery — date/time
* Delivered — date/time

If historic timestamps are not currently recorded, do not fabricate them.

If implementation requires storing future status timestamps, add this in the least invasive way possible without changing existing order functionality.

---

# 25. ADMIN WORKFLOW MUST REMAIN SIMPLE

The admin should continue changing the order status from the existing order-management interface.

Example:

Admin changes:

`Processing → Shipped`

That existing change should automatically:

1. update the order;
2. trigger the existing order-update email if configured;
3. make the website tracker show Shipped;
4. update the timeline.

Do not require admins to update the same status in two different places.

---

# 26. EMAIL TRIGGERS

Keep existing email-trigger functionality intact.

When the system already sends an email for a particular status change, update the appearance/content of that email instead of creating duplicate notifications.

Avoid accidentally sending two status-update emails for a single event.

---

# 27. TRACK ORDER BUTTON RULES

Display the Track Order button for statuses where tracking is useful.

Examples:

* confirmed
* processing
* on hold
* shipped
* out for delivery
* delivered

For cancelled/refunded orders, the button may instead say:

**View Order Status**

if this gives a clearer experience.

The tracker should still display the correct final status.

---

# 28. EMAIL FOOTER

Use the real website information already stored/configured in the system.

Include appropriate items such as:

* customer support email
* customer support phone
* website
* copyright/company name

Do not hardcode dummy information.

Use dynamic site configuration whenever available.

---

# 29. PRIVACY

The no-login tracker must not become an order-information enumeration vulnerability.

Protect against someone trying sequential numbers such as:

`10001`

`10002`

`10003`

and accessing private customer information.

Implement appropriate protections such as:

* signed tracking tokens
* limited manual lookup response
* order number + secondary verifier
* rate limiting
* generic not-found responses
* server-side validation

Never expose payment credentials or sensitive customer information.

---

# 30. RATE LIMITING

Apply sensible rate limiting to the public manual tracking endpoint.

Prevent:

* brute-force order enumeration
* scraping
* excessive automated lookups

Do not unnecessarily block legitimate customers.

---

# 31. EXPECTED USER EXPERIENCE

## Email flow

Customer receives:

**Your order has shipped**

↓

Clicks:

**Track Order**

↓

Website opens:

`EESOME → Track Your Order`

↓

Correct order loads automatically.

↓

Customer sees:

**Order #XXXXX**

**Shipped**

and the live timeline.

No login.

No need to re-enter the order number.

---

# 32. MANUAL WEBSITE FLOW

Customer visits the website.

↓

Opens:

**Track Order**

↓

Enters order number/required verification.

↓

Clicks:

**Track Order**

↓

Current order timeline appears immediately.

---

# 33. DO NOT MODIFY UNRELATED WEBSITE FEATURES

Do not redesign or alter unrelated pages or functionality.

Specifically leave existing:

* checkout
* cart
* login
* account
* payment
* age verification
* product pages
* inventory
* shipping logic
* tax calculation
* discount logic
* admin workflow

unchanged unless a very small integration change is strictly necessary for the tracker.

The scope is the **order email UI and order tracking UI/integration**.

---

# 34. BEFORE MAKING CHANGES

First inspect the existing codebase and determine:

* framework/CMS
* ecommerce/order system
* existing order model
* status values
* email template location
* email trigger logic
* tracking fields
* status history
* existing tracking URLs
* current site contact information
* existing logo assets
* authentication/order security mechanisms

Reuse existing functionality whenever possible.

Do not make assumptions about status field names.

---

# 35. IMPLEMENTATION QUALITY

Code should be:

* production-ready
* maintainable
* readable
* responsive
* secure
* minimally invasive
* compatible with the existing architecture

Avoid unnecessary rewrites.

Comment only where helpful.

Follow the project's existing naming conventions and code style.

---

# 36. TESTING REQUIREMENTS

Before considering the work complete, test at least:

### Confirmed order

* correct confirmed message
* no cancellation warning
* Track Order works

### Processing order

* correct processing message
* no cancellation warning

### On Hold order

* correct hold message
* no cancellation warning
* no cancelled wording

### Shipped order

* tracking information appears
* timeline shows shipped as current
* no cancellation warning

### Out for Delivery

* correct timeline stage

### Delivered

* delivered status appears correctly

### Cancelled

* red warning icon
* cancellation message appears
* support phone/email appear
* future shipping steps are not presented as active

### Refunded

* refund information appears correctly
* cancellation text does not appear unless the order is also genuinely cancelled

### Email tracking link

* no login required
* automatically loads correct order
* correct live status displayed

### Manual tracker

* valid order works
* invalid order handled safely
* privacy protections work

### Security

* invalid token rejected
* modified token rejected
* unauthorized sensitive information not exposed
* brute-force/manual enumeration mitigated

---

# 37. FINAL RESULT

The completed implementation should provide a polished EESOME order experience where:

**Email notification → Track Order → Live order timeline**

works smoothly without requiring login.

Customers should also be able to visit the website's **Track Your Order** section and check their order status manually.

The status shown must always come from the site's existing live order information.

Most importantly:

**Do not change or break existing business logic, order processing, payment logic, age-verification functionality, or existing working site features. Upgrade only the email presentation and tracking experience plus the minimum backend integration needed to support secure real-time tracking.**
