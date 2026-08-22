# Invoice System Setup Guide

## Overview

Your Vetriarasiwatersupply admin panel now includes a complete invoice management system that allows admins to:
- ✅ View all invoices for customer orders
- ✅ Search and filter invoices by number, customer, status
- ✅ Download invoices as PDF
- ✅ View customer invoices from customer details page
- ✅ Track invoice status (Draft, Sent, Viewed, Paid, Overdue, Cancelled)

---

## Error Fix

**Error encountered:**
```
Fatal error: Uncaught PDOException: SQLSTATE[42S02]: Base table or view not found: 
1146 Table 'if0_40473744_vetriarasiwatersupply.invoices' doesn't exist
```

**Root Cause:** The `invoices` table was missing from your database.

**Solution:** Run the database setup to create the table.

---

## Step 1: Create the Invoices Table

### Option A: Using the Setup Page (Recommended)

1. Open your browser and go to:
   ```
   http://localhost/Vetriarasiwatersupply/setup_database.php
   ```

2. You'll see a page showing all database tables

3. Click the button: **"Create Missing Tables"**

4. The invoices table will be created automatically

### Option B: Manual Database Setup

If Option A doesn't work, use phpMyAdmin:

1. Open phpMyAdmin
2. Select your database: `if0_40473744_vetriarasiwatersupply`
3. Click the **SQL** tab
4. Paste this code:

```sql
CREATE TABLE IF NOT EXISTS `invoices` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `order_id` INT NOT NULL UNIQUE,
  `invoice_number` VARCHAR(100) NOT NULL UNIQUE,
  `invoice_date` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `due_date` DATE,
  `subtotal` DECIMAL(10,2) DEFAULT 0,
  `tax` DECIMAL(10,2) DEFAULT 0,
  `total` DECIMAL(10,2) NOT NULL,
  `status` ENUM('Draft','Sent','Viewed','Paid','Overdue','Cancelled') DEFAULT 'Draft',
  `notes` TEXT,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  INDEX (`order_id`),
  INDEX (`invoice_number`),
  INDEX (`status`),
  CONSTRAINT `fk_invoices_order` FOREIGN KEY (`order_id`) REFERENCES `orders`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
```

5. Click **Go** to execute

---

## Step 2: Verify the Installation

After creating the table:

1. Go to your admin panel: `http://localhost/Vetriarasiwatersupply/admin/`
2. Log in with your admin account
3. You should now see **"Invoices"** in the sidebar menu
4. Click on it to access the invoices page

---

## Files Created/Modified

### New Files Created:
- **admin/invoices.php** - Main invoices management page
- **admin/download_invoice.php** - Invoice PDF download functionality
- **setup_database.php** - Database setup and verification tool
- **migrations/001_add_invoices_table.sql** - SQL migration file
- **db.sql** - Updated with invoices table definition

### Files Modified:
- **admin/user_view.php** - Added error handling for invoices table
- **db.sql** - Added invoices table definition

---

## Using the Invoice System

### As an Admin:

#### 1. View All Invoices
- Navigate to **Admin Panel → Invoices**
- See all invoices with customer names, amounts, and status

#### 2. Search Invoices
- Use the search box to find by:
  - Invoice number (e.g., INV-001)
  - Customer name (e.g., John Doe)
  - Customer email

#### 3. Filter by Status
- Click the Status dropdown
- Select: Draft, Sent, Viewed, Paid, Overdue, or Cancelled
- Click **Filter** to apply

#### 4. Sort Invoices
- Click on column headers to sort:
  - Invoice #
  - Amount
  - Status
  - Date
  
#### 5. Download Invoice as PDF
- Click the **Download** button on any invoice
- PDF will be generated with:
  - Customer details
  - Invoice items and quantities
  - Pricing breakdown
  - Payment status

#### 6. View Customer Invoices
- Go to **Admin Panel → Customers**
- Click on a customer name to view their details
- Scroll down to see their associated invoices

---

## Database Schema

### Invoices Table Structure

| Column | Type | Description |
|--------|------|-------------|
| id | INT | Primary key |
| order_id | INT | Foreign key to orders table (UNIQUE) |
| invoice_number | VARCHAR(100) | Unique invoice identifier (e.g., INV-001) |
| invoice_date | TIMESTAMP | When invoice was created |
| due_date | DATE | Payment due date (nullable) |
| subtotal | DECIMAL(10,2) | Amount before tax |
| tax | DECIMAL(10,2) | Tax amount |
| total | DECIMAL(10,2) | Final total amount |
| status | ENUM | Draft, Sent, Viewed, Paid, Overdue, Cancelled |
| notes | TEXT | Additional notes (nullable) |
| created_at | TIMESTAMP | Record creation time |
| updated_at | TIMESTAMP | Last update time |

### Relationships
- One invoice per order (UNIQUE constraint on order_id)
- Each invoice is linked to exactly one order
- Deleting an order cascades to delete its invoice

---

## How to Populate Invoices

### Automatic (When orders exist):
Currently, invoices are created when admins manually add them. Here's how to populate:

1. Go to Admin Panel
2. Check existing orders in the **Orders** page
3. Invoices can be created by inserting records into the invoices table

### Manual Creation (phpMyAdmin):

For each order, insert an invoice record:

```sql
INSERT INTO invoices (order_id, invoice_number, total, status, subtotal, tax)
VALUES (1, 'INV-001', 1000.00, 'Paid', 900.00, 100.00);
```

Where:
- `order_id` = The order's ID number
- `invoice_number` = Unique invoice reference
- `total` = Order total
- `status` = Current payment status
- `subtotal` = Amount before tax
- `tax` = Tax amount

---

## Troubleshooting

### Issue: "Table 'invoices' doesn't exist"

**Solution:**
1. Run the setup page: `setup_database.php`
2. Or manually run the SQL from Option B above
3. Refresh your browser

### Issue: "Invoice not found" when trying to download

**Solution:**
- Make sure the invoice ID exists in the invoices table
- Check the order_id is valid
- Verify the order has items in order_items table

### Issue: Admin can't see invoices menu

**Solution:**
1. Clear browser cache
2. Log out and log back in
3. Check if you're logged in as admin (not regular user)
4. Verify invoices table exists using phpMyAdmin

### Issue: PDF download doesn't work

**Solution:**
- The PDF opens in the browser for printing
- Use Ctrl+P (or Cmd+P) to save as PDF
- Or right-click and "Save as PDF"
- If the page is blank, check that:
  - The invoice ID is valid
  - The order has items
  - The customer data exists

---

## Next Steps

After setup is complete:

1. ✅ Verify the invoices table exists
2. ✅ Access the invoices management page
3. ✅ Create or import existing invoices
4. ✅ Test downloading a PDF
5. ✅ View customer invoices from customer details

---

## Support

If you encounter any issues:

1. Check the error message carefully
2. Run `setup_database.php` to verify database structure
3. Check phpMyAdmin to see if the invoices table exists
4. Verify all orders have the required data

---

## Summary

Your invoice system is now ready to:
- Track all customer invoices
- Generate printable PDFs
- Manage invoice statuses
- Search and filter invoices
- View customer invoices from customer details

**All invoices are linked to orders** - each order can have one invoice associated with it.

---

**Setup Complete!** 🎉

Your admin panel now has full invoice management capabilities.
