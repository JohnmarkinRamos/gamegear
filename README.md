# GameGear Shop

Laravel 13 storefront based on the original GameGear Shop demo. Accounts, product inventory, orders, order items, and tracking events are stored in MySQL. Shopping carts use the Laravel session.

## Requirements

- PHP 8.3 or newer with `pdo_mysql`, `mbstring`, `openssl`, and `fileinfo`
- Composer 2
- XAMPP MySQL/MariaDB

The XAMPP release installed on the development machine bundles PHP 8.0, which cannot run Laravel 13. XAMPP can still provide MySQL; use a compatible PHP installation to run the app, or upgrade XAMPP to a release that bundles PHP 8.3 or newer before serving it through Apache.

## Setup

1. Start MySQL from the XAMPP Control Panel.
2. In phpMyAdmin, create a database named `gamegear` with the `utf8mb4_unicode_ci` collation.
3. Copy `.env.example` to `.env` and set `DB_HOST`, `DB_PORT`, `DB_DATABASE`, `DB_USERNAME`, and `DB_PASSWORD` for your XAMPP MySQL account. The defaults assume `root` with no password.
4. From this project folder, run:

```powershell
composer install
php artisan key:generate
php artisan migrate --seed
php artisan storage:link
php artisan serve
```

5. Open `http://127.0.0.1:8000` and create an account.

Create a dedicated admin account with `php artisan gamegear:create-admin gearadmin`. The command prints a randomly generated password once. Sign in separately at `http://127.0.0.1:8000/admin/login`; admin accounts cannot sign in through the customer login or access the storefront. To revoke an existing admin account, run `php artisan gamegear:make-admin username --revoke`.

The seed command adds the eight products from the original demo. It is safe to run again; existing products are updated by name.

## XAMPP Apache

For Apache hosting, configure the virtual host's `DocumentRoot` to this project's `public` directory, enable `mod_rewrite`, and ensure Apache uses PHP 8.3 or newer. Do not set the document root to the Laravel project root. The included `.htaccess` in `public` handles Laravel's front controller.

## Notes

- COD, GCash, and card are recorded as order payment choices. No payment gateway is integrated.
- The order status progression includes demo controls, matching the original prototype; this is not a live shipping integration.
- Run the feature suite with `php artisan test`.
