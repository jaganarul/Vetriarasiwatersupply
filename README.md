Ecommerce PHP/MySQL demo

Overview
- Simple e-commerce application built with PHP (procedural), MySQL and Bootstrap 5.
- Admin portal with product CRUD, image uploads, order management and sales analytics (Chart.js).
- User portal with registration, login, profile, cart, checkout, order tracking.
- Uses PDO prepared statements and password_hash / password_verify for security.

Quick setup (Windows, using XAMPP/WAMP)
1. Place the `ecommerce` folder inside your web server root (e.g., C:\xampp\htdocs\ecommerce).
2. Create a MySQL database (e.g., `ecommerce_db`) and import `db.sql` provided.
   - You can import with phpMyAdmin or:
     mysql -u root -p ecommerce_db < db.sql
3. Edit `config.php` and set DB credentials.
4. Ensure `uploads/` directory is writable by the webserver (on Windows usually OK).
5. Visit http://localhost/ecommerce/

Admin login
- Email: admin@example.com
- Password: Admin@123

Notes & Security
- This is a demo. For production, use more hardened security, HTTPS, CSRF tokens, stricter file upload checks and size limits, proper error handling and logging.

Files of interest
- `config.php` - DB connection variables
- `db.sql` - database schema and sample data
- `admin/` - admin portal files
- `index.php`, `product.php`, `cart.php`, `checkout.php` - public pages

How to zip
- From PowerShell in project parent directory:
  Compress-Archive -Path "ecommerce\*" -DestinationPath ecommerce.zip

Enjoy!
