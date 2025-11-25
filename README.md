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
