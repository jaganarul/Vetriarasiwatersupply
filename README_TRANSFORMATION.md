# Vetriarasi Water Supply — Project Overview

This project is a compact PHP/MySQL storefront tailored for a local water supply business called "Vetriarasi Water Supply". It provides a simple product catalog, product detail pages with image gallery and zoom, a cart and checkout flow, and an admin area for product and order management.

What this repo contains
- Core public pages: `index.php`, `product.php`, `cart.php`, `checkout.php`
- Admin panel: `/admin/` for product CRUD, image uploads, and order viewing
- Database schema: `db.sql` (includes users, products, orders, order_items, messages, payments)
- Templates: shared header/footer in `templates/`
- Static assets: CSS and images under `assets/` and `uploads/`

Quick setup
1. Place the project folder in your webserver root (for XAMPP on macOS: `/Applications/XAMPP/xamppfiles/htdocs/Vetriarasiwatersupply`).
2. Create the database and import `db.sql`:

```bash
mysql -u root -p < db.sql
```

3. Configure DB credentials in `config.php` if necessary.
4. Ensure `uploads/` exists and is writable by the webserver.
5. Open `http://localhost/Vetriarasiwatersupply/` in your browser.

Key features
- Product gallery with thumbnail switching and click-to-zoom modal
- Stock status badges (in-stock / low-stock / out-of-stock)
- Add-to-cart and basic checkout flow
- Admin area for product and order management
- Contact/messages table to capture user inquiries
- Payments table to record payment attempts/transactions

Notes for operators and developers
- The project uses PDO prepared statements and an `esc()` helper for output escaping.
- Image uploads are handled from the admin panel — verify acceptable file types and storage limits before production.
- For production, update `config.php` with secure credentials, enable HTTPS, and tighten file permissions.

Contact
- For local support, contact the developer or administrator responsible for the deployment.

