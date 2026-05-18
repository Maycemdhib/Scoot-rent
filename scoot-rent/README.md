# 🛴 ScootRent

A PHP MVC scooter rental web application.

## Setup

### 1. Requirements
- PHP 8.0+
- MySQL 5.7+
- Apache / Nginx with mod_rewrite

### 2. Database
Run the included schema in MySQL:
```bash
mysql -u root -p < database.sql
```

### 3. Configure the database
Edit `config/Database.php`:
```php
private $host     = "localhost";
private $db_name  = "scoot_rent";
private $username = "root";
private $password = "your_password";
```

### 4. Deploy
Place the project in your web server root (e.g. `htdocs/scoot-rent/`).

### 5. Default admin account
- **Email:** admin@scootrent.com
- **Password:** admin123
> ⚠️ Change this immediately after first login.

---

## Project Structure
```
scoot-rent/
├── config/
│   └── Database.php         # PDO connection
├── controllers/
│   ├── AuthController.php       # Login / Register / Logout
│   ├── TrottinetteController.php
│   └── ReservationController.php
├── helpers/
│   └── auth.php             # requireLogin(), requireAdmin(), e()
├── models/
│   ├── User.php
│   ├── Trottinette.php
│   └── Reservation.php
├── views/
│   ├── login.php
│   ├── register.php
│   ├── dashboard.php
│   ├── trottinettes.php
│   ├── admin/
│   │   ├── dashboard.php
│   │   ├── trottinettes.php
│   │   └── reservations.php
│   └── user/
│       └── my_reservations.php
├── public/
│   └── uploads/             # Scooter images (writable)
├── database.sql
└── index.php
```

## Security fixes applied
- ✅ All output escaped with `htmlspecialchars()` via `e()` helper
- ✅ Auth guards on every protected page (`requireLogin`, `requireAdmin`)
- ✅ Image upload validates MIME type (not just extension) and file size
- ✅ Session regenerated on login (session fixation prevention)
- ✅ All SQL uses parameterized queries (no injection risk)
- ✅ Status values validated against allowlist before DB update
- ✅ Reservation dates validated server-side (end > start)
- ✅ Removed duplicate `session_start()` calls
