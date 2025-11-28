# Vetriarasi Water Supply — PHP + MySQL e-commerce demo

🔧 Live demo-style e-commerce application implemented in plain PHP with PDO, MySQL, and Bootstrap 5.

This project is a small, self-contained store demonstrating a complete flow: product CRUD, image uploads, cart, checkout, payments, user profiles, order tracking, admin area, and lightweight notifications.

---

## Table of Contents
- Overview
- Features
- Project layout (important files & folders)
- Quick setup (MAMP / XAMPP)
- Database schema & migrations
- Configuration
- Admin area and credentials
- Important application flows
- Troubleshooting & tips
- Development notes & recommended improvements

---

## Overview
- Purpose: A learning/demo e-commerce site written in plain PHP (procedural) using MySQL as the backing store and Bootstrap 5 for frontend.
- Tech: PHP 8+ (or 7.4+), PDO for DB, MySQL / MariaDB, Bootstrap 5, vanilla JS.

## Features
- Public shopping features: product listing, product detail, categories, shopping cart, quantity updates, checkout, and order tracking.
- Admin features: product CRUD with thumbnail + multiple images, orders management (status update, delete, view), user/customer list, contact messages list.
- Lightweight notifications: "new" flags on orders (`is_new`) and unread messages (`is_read`) let admin see which items need attention.
- Security basics: password hashing for users, prepared statements for DB queries.

## Project layout (important files & folders)
- `config.php` — Application configuration (database connection, `$base_url`, `$upload_dir`). The `$base_url` is computed and supports the site living under a subdirectory.
- `init.php` — Bootstrapping: session start, DB access, helper functions, and category list population for templates.
- `router.php` — Lightweight routing for pretty URLs (routes like `/product/123`, `/category/<name>`, `/admin/*`).
- `db.sql` — Base MySQL schema for fresh installs.
- `migrations/` — Small migration scripts (idempotent), e.g. `migrations/add_columns.php`.
- `templates/` — `header.php`, `footer.php` for shared layout.
- `admin/` — Admin area: `index.php`, `login.php`, `products.php`, `product_save.php`, `product_delete.php`, `orders.php`, `order_view.php`, `analytics.php`, `user.php` (customers), `messages.php`.
- `assets/` — Static assets (CSS, images). `assets/images/logo.png` used by header and admin.
- `uploads/` — Uploaded images and media (must be writable by webserver).

## Quick setup (macOS / Linux — MAMP, XAMPP or similar)
1. Place the project folder inside your webserver htdocs (e.g. `/Applications/MAMP/htdocs/` or XAMPP `htdocs`).
2. Start your webserver and MySQL (MAMP/XAMPP control panels).
3. Import the database script (db.sql):
```bash
# macOS/XAMPP
/Applications/XAMPP/xamppfiles/bin/mysql -u root -p < db.sql
# MAMP users might use the built-in mysql: change the path accordingly
```
4. Edit `config.php` to ensure DB credentials and optionally change the base URL if necessary.
   - `db_host`, `db_name`, `db_user`, `db_pass`.
   - `$base_url` is detected automatically but you can override in `config.php` if you need to.
5. Ensure `uploads/` is writable by the webserver (owner and mode). Example on macOS (MAMP):
```bash
cd /Applications/MAMP/htdocs/Vetriarasiwatersupply
mkdir -p uploads
sudo chown -R _www:_www uploads
sudo chmod -R 755 uploads
```
6. Optionally run migrations if you're upgrading from an older version:
```bash
/Applications/XAMPP/xamppfiles/bin/php migrations/add_columns.php
```
7. Create an initial admin account if needed; there's a `setup.php` script that populates an admin row or you may use the fixed default credentials during development.

### Default admin credentials (development only!)
- Admin login uses a simple default email/password in `admin/login.php` for quick testing:
  - Email: `admin@Vetriarasiwatersupply.com`
  - Password: `Admin@104`
- **Important**: These are development credentials and should be replaced with a secure admin account (or switch to a proper admin authentication flow) before a production deployment.

## Database schema highlights
- Created by `db.sql` with tables: `users`, `admins`, `products`, `orders`, `order_items`, plus support tables added by scripts: `payments`, `messages`, etc.
- Notable columns:
  - `products.images` is JSON encoded to store multiple image filenames.
  - `orders.status` supports `Pending`, `Processing`, `Shipped`, `Delivered`, `Cancelled`.
  - `is_new` (orders) — used by the admin notification system. The app sets `is_new = 1` when an order is created and the admin clears it when viewing or processing orders.
  - `messages.is_read` — used to track admin read/unread state for contact messages.

## How routing works
- The `router.php` is a simple routing entrypoint that maps friendly routes to internal PHP files.
  - `/` → `index.php`
  - `/product/<id>` → `product.php?id=<id>`
  - `/category/<name>` → `index.php?category=<name>`
  - `/products` → `index.php`
  - `/cart`, `/checkout`, `/payments`, `/login`, `/register` → the corresponding files
  - `/admin/*` → `/admin/` sub-pages (index, products, orders, etc.)

## Important flows
- Public: add to cart, update quantities, remove from cart — `add_to_cart.php`, `update_cart.php`, `remove_from_cart.php`.
- Checkout: `checkout.php` initiates and moves to `payments.php`. The `payments.php` file inserts `orders`, `order_items` and `payments` then clears the session cart.
- Orders & admin notification: `payments.php` now ensures `orders.is_new = 1` on insertion; admin sidebar shows a red badge with count of new orders; admin viewing or updating an order clears the `is_new` flag.
- Messages: public contact form writes to `messages` table (the app creates table/column if missing); messages can be marked read/unread and deleted from admin panel.

## Admin area
- `admin/products.php` – list, edit (modal), and add products, including thumbnail and multiple images.
- `admin/product_save.php` – handles create/update and file uploads.
- `admin/product_delete.php` – deletes a product row and removes uploaded images.
- `admin/orders.php` – view, filter and update order statuses (status update automatically clears `is_new`).
- `admin/order_view.php` – view order details (viewing marks the order read, `is_new=0`).
- `admin/user.php` (and `admin/users.php`) – list customers and view/delete them.
- `admin/messages.php` – new page that lists messages, allows mark read/unread and delete. Unread messages appear in the sidebar with a badge.

## Scripts & utilities
- `setup.php` — create an initial admin record (run once) or manual SQL to insert `admins`.
- `migrations/add_columns.php` — migration script to add `users.phone`, `users.address`, `orders.delivery_phone`, `orders.delivery_address` for older databases.

## Security & production advice
1. Remove or disable development default admin credentials; create a secure admin user.
2. Use HTTPS and set secure cookie flags for sessions.
3. Add CSRF tokens to POST forms and verify them server-side.
4. Add server-side validation to uploads (MIME check, limit size, sanitize filenames).
5. Sanitize all user input and use prepared statements (already used in the repo) for DB queries.

## Troubleshooting & common issues
- Fatal PDO errors (e.g., missing table/column): run `db.sql` to create base tables and use `migrations/add_columns.php` to add columns introduced in later versions.
- Uploads failing: check `uploads/` permission and `php.ini` (`upload_max_filesize`, `post_max_size`).
- Wrong paths / images 404: check `config.php` ` $base_url` and confirm templates are referencing `<?php echo $base_url; ?>` correctly.
- Admin logo not showing: ensure assets path uses `$base_url` and `uploads` contains correct files.

## Useful CLI commands
- Import DB:
```bash
mysql -u root -p < db.sql
```
- Run migrations:
```bash
php migrations/add_columns.php
```
- Backup DB:
```bash
mysqldump -u root -p Vetriarasiwatersupply > backup.sql
```

## Development & Contributing
- Branch workflow: create feature branches for changes and file PRs.
- If you need to change the config or DB schema, update `db.sql` and add a migration in `migrations/`.
- Add tests or small scripts covering critical flows: add-to-cart, checkout, and admin order processing.

---

If you'd like, I can also:
- Add a `README_TROUBLESHOOTING.md` with screenshots and common log excerpts.
- Add a GitHub Actions script to run PHP lint and basic unit tests where applicable.
- Add a minimal `docker-compose` for a reproducible local dev environment.

If this README looks good, I can save it to the repo (I have already replaced the previous README file).

---
Happy developing! — Let me know if you want screenshots, a migration history, or a step-by-step video-style checklist.
Vetriarasi Water Supply — PHP + MySQL e-commerce demo

**Overview**
- **Purpose:** Small, self-contained e-commerce demo implemented in plain PHP (procedural) with a MySQL backend and Bootstrap 5 frontend. It demonstrates product management, image uploads, cart/checkout, order management, and a lightweight admin area.
- **Main features:**
  - Admin product CRUD (thumbnail + multiple secondary images), delete, and analytics.
  - User registration/login/profile, cart, checkout and order tracking.
  - PDO-based DB access, prepared statements, and password hashing.

**Project layout (important files & folders)**
- `config.php` : application configuration and PDO creation (`$db_name`, `$base_url`, `$upload_dir`).
- `init.php` : session helpers and small utilities (e.g. `esc()`) and category injection for templates.
- `db.sql` : schema for fresh installs (creates the `Vetriarasiwatersupply` database and core tables).
- `migrations/add_columns.php` : safe PHP migration that adds missing columns (`users.phone`, `users.address`, `orders.delivery_*`) if they are absent.
- `setup.php` : creates an initial admin account (run once).
- `uploads/` : uploaded product images and media (ensure writable by the webserver).
- `assets/` : CSS, images and client assets (`assets/css/custom.css`, `assets/images/logo.png`).
- `templates/header.php` & `templates/footer.php` : site header and footer shared across pages.
- `admin/` : admin area files (login, index, products, product_save.php, product_delete.php, orders, order_view, analytics, user management).
- public pages: `index.php`, `product.php`, `cart.php`, `checkout.php`, `payments.php`, `register.php`, `login.php`, `profile.php`, `track.php`.

**Quick setup (macOS, XAMPP)**
1. Place the project folder inside your XAMPP `htdocs` (example already located at `/Applications/XAMPP/xamppfiles/htdocs/Vetriarasiwatersupply`).
2. Start XAMPP Apache and MySQL using the XAMPP Control Panel.
3. Import the database schema (the `db.sql` script contains a `CREATE DATABASE` and `USE` statement):
```bash
/Applications/XAMPP/xamppfiles/bin/mysql -u root -p < db.sql
```
If you prefer to run the statement against a specific database name (and your `config.php` uses that name), adjust accordingly.
4. Edit `config.php` and confirm the following values match your environment:
  - ` $db_host` — usually `127.0.0.1` or `localhost`.
  - ` $db_name` — default in this repo is `Vetriarasiwatersupply` (ensure it matches the DB you created/imported).
  - ` $db_user` / `$db_pass` — your MySQL credentials.
  - ` $base_url` — path where the app lives (e.g. `/Vetriarasiwatersupply`).
5. Ensure `uploads/` directory is writable by the webserver user. Example (macOS/XAMPP):
```bash
cd /Applications/XAMPP/xamppfiles/htdocs/Vetriarasiwatersupply
mkdir -p uploads
sudo chown -R _www:_www uploads   # XAMPP's Apache user on macOS is usually `_www`; adjust if different
sudo chmod -R 755 uploads
```
6. Create initial admin account (one-time):
```bash
/Applications/XAMPP/xamppfiles/bin/php setup.php
```
7. Visit the site in your browser: `http://localhost/Vetriarasiwatersupply/` (or the `base_url` you configured).

**Running migrations (safe check-and-add which is idempotent)**
- Use the bundled PHP migration to add missing columns when upgrading from older installs:
```bash
/Applications/XAMPP/xamppfiles/bin/php migrations/add_columns.php
```
The script checks `information_schema` and only runs `ALTER TABLE` statements if columns are missing.

**Recommended backup before schema changes**
```bash
/Applications/XAMPP/xamppfiles/bin/mysqldump -u root -p Vetriarasiwatersupply > backup_before_migration.sql
```
If you use a different DB name in `config.php`, substitute it in the command.

**Permissions & PHP settings to check**
- File uploads may fail if `upload_max_filesize` or `post_max_size` in `php.ini` are too small — increase them for large images and restart Apache.
- Ensure `file_uploads = On` in `php.ini`.
- Set correct ownership for `uploads/` to the webserver user so `move_uploaded_file()` succeeds.

**How the upload flow works (admin/product_save.php)**
- Admin form allows a single `thumbnail` and multiple `images[]`.
- Files are written to the `$upload_dir` (configured in `config.php`). The app uses `move_uploaded_file()` and falls back to `copy()` where necessary.
- Filenames are stored in the `products.thumbnail` and `products.images` (JSON) columns.

**Important code locations**
- `admin/product_save.php` — product create/update handling and uploads.
- `admin/products.php` — admin product list and edit modal (builds image preview URLs using `$base_url`).
- `admin/product_delete.php` — removes DB row and unlinks files from `uploads/`.
- `payments.php` — creates orders and records payments (stores delivery details into `orders` when available).
- `migrations/add_columns.php` — adds `users.phone`, `users.address`, `orders.delivery_phone`, `orders.delivery_address` when necessary.

**Troubleshooting**
- If uploads show broken images:
  - Confirm files exist: `ls -l uploads`.
  - Confirm `config.php` ` $base_url` is correct and templates use `<?php echo $base_url; ?>` when building src URLs.
  - Check `php_error.log` and Apache error logs for upload/move errors.
- If you see SQL errors about missing columns (e.g., `delivery_phone`), run the migration script or apply the `ALTER TABLE` statements manually.

**Typical commands (quick reference)**
- Import schema: `/Applications/XAMPP/xamppfiles/bin/mysql -u root -p < db.sql`
- Backup DB: `/Applications/XAMPP/xamppfiles/bin/mysqldump -u root -p Vetriarasiwatersupply > backup.sql`
- Run migration: `/Applications/XAMPP/xamppfiles/bin/php migrations/add_columns.php`
- Create admin: `/Applications/XAMPP/xamppfiles/bin/php setup.php`

**Development notes & safety**
- This project is intended as a demo and learning resource. For production:
  - Use HTTPS and a hardened PHP configuration.
  - Add CSRF protection to forms and rate-limit sensitive endpoints.
  - Sanitize and validate uploads (size, mime type, virus scan where appropriate).
  - Configure logging rather than exposing errors to users.

If you want, I can also:
- Generate a small troubleshooting script that verifies DB connectivity and folder permissions.
- Harden upload handling further (restrict mime-types, image resizing, add thumbnail generation).

---
Project structure (quick tree)
```
./
├─ admin/                  # admin area (products, orders, users, analytics)
├─ assets/                 # css and images
├─ templates/              # header/footer
├─ uploads/                # product images (writable)
├─ migrations/             # small PHP migration utilities
├─ db.sql                  # DB schema for fresh installs
├─ config.php              # DB credentials and base URL
├─ init.php                # app init + helpers
├─ setup.php               # create initial admin
├─ README.md               # this file
```

Enjoy working on the project — tell me if you want the README to include screenshots, ER diagrams, or a step-by-step video-style checklist.
