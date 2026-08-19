# Bangladesh Division & District Delivery System - Checkout Implementation

## Objective

Implement a Division → District selection system in the checkout process.

The existing checkout, cart, payment, order, customer, and other business logic must remain **untouched** unless changes are strictly required to support this delivery-location feature.

The customer will:

1. Select a **Division** first.
2. After selecting a Division, the checkout will display only the **Districts/Zilas** belonging to that Division.
3. The customer will then select a District.
4. Delivery charge will be calculated automatically based on the selected District.
5. Admin will be able to manage delivery charges and configure a **Free Delivery Threshold**.

---

## 1. Divisions and Districts

Use the following exact Bangladesh administrative structure.

### Dhaka Division - 13 Districts

- Dhaka
- Faridpur
- Gazipur
- Gopalganj
- Kishoreganj
- Madaripur
- Manikganj
- Munshiganj
- Narayanganj
- Narsingdi
- Rajbari
- Shariatpur
- Tangail

### Chattogram Division - 11 Districts

- Chattogram
- Cumilla
- Bandarban
- Brahmanbaria
- Chandpur
- Cox's Bazar
- Feni
- Khagrachari
- Lakshmipur
- Noakhali
- Rangamati

### Barishal Division - 6 Districts

- Barguna
- Barishal
- Bhola
- Jhalokati
- Patuakhali
- Pirojpur

### Khulna Division - 10 Districts

- Bagerhat
- Chuadanga
- Jashore
- Jhenaidah
- Khulna
- Kushtia
- Magura
- Meherpur
- Narail
- Satkhira

### Mymensingh Division - 4 Districts

- Jamalpur
- Mymensingh
- Netrokona
- Sherpur

### Rajshahi Division - 8 Districts

- Bogura
- Joypurhat
- Naogaon
- Natore
- Chapainawabganj
- Pabna
- Rajshahi
- Sirajganj

### Rangpur Division - 8 Districts

- Dinajpur
- Gaibandha
- Kurigram
- Lalmonirhat
- Nilphamari
- Panchagarh
- Rangpur
- Thakurgaon

### Sylhet Division - 4 Districts

- Habiganj
- Maulvibazar
- Sunamganj
- Sylhet

---

# 2. Checkout UX

The checkout location fields should work in this order:

```text
Division
   ↓
District
```

### Division

Display all 8 divisions in a dropdown/select field.

Example:

```text
Select Division
- Dhaka
- Chattogram
- Barishal
- Khulna
- Mymensingh
- Rajshahi
- Rangpur
- Sylhet
```

### District

Initially, the District field should be disabled or empty.

After the customer selects a Division, populate the District dropdown dynamically with only the districts belonging to that Division.

Example:

```text
Division: Dhaka

District:
- Select District
- Dhaka
- Faridpur
- Gazipur
- Gopalganj
- Kishoreganj
- Madaripur
- Manikganj
- Munshiganj
- Narayanganj
- Narsingdi
- Rajbari
- Shariatpur
- Tangail
```

If the customer changes the Division, reset the District selection and load the new Division's districts.

Do not display all districts at once.

---

# 3. Initial Delivery Charge Rules

Default delivery pricing:

### Inside Dhaka and Cumilla

If the selected District is:

- Dhaka
- Cumilla

Delivery charge:

```text
80 BDT
```

### All Other Districts

Every other district should have:

```text
130 BDT
```

Therefore, the initial/default configuration is:

```text
Dhaka     → 80 BDT
Cumilla   → 80 BDT
Others    → 130 BDT
```

Do not determine the charge based on Division alone.

The charge must ultimately be associated with the selected District so that the admin can manage individual district pricing later.

---

# 4. Free Delivery Threshold

Add an admin-configurable **Free Delivery Threshold**.

Example:

```text
Free Delivery Threshold: 2000 BDT
```

If the order subtotal is equal to or greater than the configured threshold:

```text
Delivery Charge = 0 BDT
```

If the order subtotal is below the threshold:

```text
Delivery Charge = District's configured delivery charge
```

Example:

```text
Subtotal: 1500
District: Dhaka
Delivery: 80
Total: 1580
```

If:

```text
Free Delivery Threshold: 2000
Subtotal: 2500
District: Dhaka
Delivery: 0
Total: 2500
```

The threshold must be configurable from the admin panel.

If free delivery is disabled, the normal district delivery charge should apply regardless of subtotal.

---

# 5. Admin Management

Create an admin configuration/interface for delivery management.

Admin should be able to:

### Manage Divisions

View the 8 divisions.

### Manage Districts

View all districts grouped under their respective divisions.

### Manage District Delivery Charge

Admin should be able to change the delivery charge for individual districts.

Example:

```text
Dhaka       80 BDT
Cumilla     80 BDT
Faridpur    130 BDT
Gazipur     130 BDT
...
```

If admin changes:

```text
Gazipur → 100 BDT
```

the checkout should automatically use:

```text
Gazipur = 100 BDT
```

without requiring code changes.

### Free Delivery Settings

Admin should be able to:

- Enable/disable free delivery.
- Set the free delivery threshold.
- Change the threshold at any time.

Example:

```text
Free Delivery: ON
Threshold: 2000 BDT
```

or:

```text
Free Delivery: OFF
```

---

# 6. Recommended Database Structure

Use the project's existing database conventions, ORM, migrations, naming conventions, authentication and admin architecture.

Do not introduce unnecessary tables if equivalent location tables already exist.

If location tables do not exist, use a normalized structure similar to:

### divisions

```text
id
name
status
created_at
updated_at
```

### districts

```text
id
division_id
name
delivery_charge
status
created_at
updated_at
```

### delivery_settings

```text
id
free_delivery_enabled
free_delivery_threshold
created_at
updated_at
```

The exact implementation should follow the existing project's architecture.

Avoid duplicating location data in orders.

---

# 7. Order Data

When an order is placed, preserve the selected:

```text
Division
District
Delivery Charge
```

The delivery charge used at the time of order creation should be stored with the order.

This is important because an admin may later change a district's delivery charge.

Example:

```text
Order #1001
District: Dhaka
Delivery Charge: 80 BDT
```

If the admin later changes Dhaka's delivery charge to 100 BDT, existing Order #1001 must continue showing:

```text
80 BDT
```

Do not dynamically recalculate delivery charges for already-created orders.

---

# 8. Checkout Calculation

The final checkout calculation should follow this logic:

```text
1. Get cart subtotal.

2. Get selected Division.

3. Get selected District.

4. Validate that the District belongs to the selected Division.

5. Check whether Free Delivery is enabled.

6. If Free Delivery is enabled:
      If subtotal >= free_delivery_threshold:
          delivery_charge = 0
      Else:
          delivery_charge = district.delivery_charge

7. If Free Delivery is disabled:
      delivery_charge = district.delivery_charge

8. Calculate final total:
      total = subtotal + delivery_charge
```

Never trust the delivery charge sent directly by the browser.

The backend must recalculate and validate the delivery charge before creating the order.

---

# 9. AJAX / Dynamic District Loading

If the existing checkout uses AJAX, implement the Division → District dependency using the existing project's preferred approach.

Preferred behavior:

```text
User selects Division
        ↓
Request/lookup districts
        ↓
Return districts belonging to Division
        ↓
Populate District dropdown
```

Do not reload the entire checkout page unless the current architecture requires it.

If the project already has an API/AJAX structure, reuse it.

---

# 10. Validation

Backend validation is mandatory.

Validate:

- Division exists.
- Division is active.
- District exists.
- District is active.
- District belongs to the selected Division.
- Delivery charge is calculated server-side.
- Free delivery threshold is calculated server-side.

A user must not be able to manipulate the browser request and select:

```text
Division: Dhaka
District: Sylhet
```

The backend must reject this combination.

---

# 11. Existing Logic Protection

**Do not modify unrelated checkout functionality.**

Keep all existing functionality untouched, including where applicable:

- Cart
- Product pricing
- Product quantity
- Coupon/discount system
- Payment methods
- Customer information
- Billing information
- Shipping address
- Order creation
- Inventory
- Stock management
- Authentication
- Existing admin functionality
- Existing UI/UX
- Existing payment gateway integration

Only integrate the new Division → District → Delivery Charge functionality into the existing checkout.

Before making changes, inspect the existing project architecture and identify:

- Current checkout controller/service
- Checkout frontend
- Order model
- Order creation logic
- Existing address fields
- Existing delivery/shipping logic
- Existing admin panel
- Existing migrations
- Existing API/AJAX endpoints

Reuse existing components wherever possible.

---

# 12. Important Implementation Rule

Do not hard-code delivery charges directly inside checkout logic.

Bad:

```php
if ($district === 'Dhaka') {
    $delivery = 80;
} else {
    $delivery = 130;
}
```

Instead, seed the initial database values:

```text
Dhaka      → 80
Cumilla    → 80
All others → 130
```

Then retrieve the charge from the district configuration.

This allows the admin to change pricing later without modifying source code.

---

# 13. Initial Seed Data

Create seed/default data for all 8 divisions and 64 districts.

Initial district pricing:

```text
Dhaka       80
Cumilla     80

All remaining districts:
130
```

Use proper database seeding/migrations according to the existing project's architecture.

The seed operation should be safe and should not unnecessarily overwrite manually modified production delivery charges.

---

# 14. Acceptance Criteria

The implementation is complete only when all of the following work:

- [ ] Checkout displays Division first.
- [ ] District is initially unavailable until Division is selected.
- [ ] Selecting a Division displays only its districts.
- [ ] Dhaka displays all 13 Dhaka districts.
- [ ] Chattogram displays all 11 Chattogram districts.
- [ ] Barishal displays all 6 Barishal districts.
- [ ] Khulna displays all 10 Khulna districts.
- [ ] Mymensingh displays all 4 Mymensingh districts.
- [ ] Rajshahi displays all 8 Rajshahi districts.
- [ ] Rangpur displays all 8 Rangpur districts.
- [ ] Sylhet displays all 4 Sylhet districts.
- [ ] Dhaka delivery charge defaults to 80 BDT.
- [ ] Cumilla delivery charge defaults to 80 BDT.
- [ ] Every other district defaults to 130 BDT.
- [ ] Admin can change individual district delivery charges.
- [ ] Admin can enable/disable free delivery.
- [ ] Admin can configure the free delivery threshold.
- [ ] Free delivery correctly applies when subtotal reaches the threshold.
- [ ] Backend recalculates delivery charge securely.
- [ ] District must belong to the selected Division.
- [ ] The delivery charge used in an order is preserved after order creation.
- [ ] Existing checkout/payment/order logic remains functional.
- [ ] Existing unrelated functionality is not changed.

---

# 15. Development Approach

Before coding:

1. Inspect the existing project structure.
2. Identify the current checkout implementation.
3. Identify existing location/address/shipping tables.
4. Identify the existing order creation flow.
5. Identify the existing admin architecture.
6. Reuse existing architecture where possible.
7. Implement only the required database changes.
8. Add seed data.
9. Implement dependent Division → District selection.
10. Implement server-side delivery calculation.
11. Add admin management.
12. Test checkout thoroughly.
13. Test free delivery threshold.
14. Test invalid Division/District combinations.
15. Test that existing checkout functionality remains untouched.

**Do not rewrite or refactor unrelated code.**

The goal is to add a robust, database-driven Bangladesh Division/District delivery system while preserving the existing application's behavior and business logic.