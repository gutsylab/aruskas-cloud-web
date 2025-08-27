# Multi-Tenant Laravel Application Setup

Aplikasi Laravel ini telah dikonfigurasi untuk mendukung multi-tenancy dengan arsitektur 1 merchant = 1 database, plus 1 database global untuk data subscription dan merchant.

## Arsitektur

### Database Global
- `subscription_plans`: Rencana berlangganan yang tersedia
- `merchants`: Data merchant/tenant
- `merchant_subscriptions`: Langganan merchant 
- `merchant_users`: User yang memiliki akses ke merchant

### Database Tenant (Per Merchant)
- `users`: User aplikasi untuk tenant
- `cache`: Cache data tenant
- `jobs`: Queue jobs tenant
- `api_clients`: API clients untuk tenant
- `email_messages` & `email_providers`: Data email untuk tenant

## Setup Awal

### 1. Konfigurasi Database

Copy file `.env.example.multitenant` ke `.env` dan sesuaikan konfigurasi database:

```bash
cp .env.example.multitenant .env
```

Pastikan konfigurasi database sudah benar:
- `DB_GLOBAL_*`: untuk database global
- `DB_TENANT_*`: untuk database tenant
- `DB_TENANT_PREFIX`: prefix untuk nama database tenant (default: `tenant_`)
- `DB_TENANT_SEPARATOR`: separator antara prefix dan slug (default: `_`)

### 2. Install Dependencies

```bash
composer install
npm install
```

### 3. Generate Application Key

```bash
php artisan key:generate
```

### 4. Setup Database Global

```bash
# Migrate database global
php artisan migrate:global

# Atau dengan fresh (drop all tables)
php artisan migrate:global --fresh
```

### 5. Buat Subscription Plans

```bash
# Buat plan basic
php artisan plan:create "Basic Plan" 29.99 --cycle=monthly --trial=14

# Buat plan professional  
php artisan plan:create "Professional Plan" 59.99 --cycle=monthly --trial=14

# Buat plan enterprise
php artisan plan:create "Enterprise Plan" 149.99 --cycle=monthly --trial=30
```

## Penggunaan

### 1. Membuat Tenant Baru

```bash
php artisan tenant:create "Nama Merchant" admin@merchant.com --plan=basic
```

Command ini akan:
- Membuat record merchant di database global
- Membuat database tenant baru
- Menjalankan migrations di database tenant
- Membuat subscription untuk merchant
- Membuat user admin untuk merchant

### 2. Mengakses Aplikasi

#### Admin Global
Akses melalui URL utama atau path `/admin`:
- `http://localhost/admin` - Dashboard admin global
- `http://localhost/admin/merchants` - Kelola merchants
- `http://localhost/admin/plans` - Kelola subscription plans

#### Tenant/Merchant
Akses melalui subdomain atau parameter:
- `http://merchant-slug.localhost` - Dashboard merchant
- `http://localhost?tenant=merchant-slug` - Alternative access

### 3. API Endpoints

#### Global API
```
GET /api/global/plans - List subscription plans
POST /api/global/register-merchant - Register new merchant
```

#### Admin API
```
GET /admin/merchants - List merchants
POST /admin/merchants - Create merchant
PUT /admin/merchants/{id} - Update merchant
DELETE /admin/merchants/{id} - Delete merchant

GET /admin/plans - List plans
POST /admin/plans - Create plan
PUT /admin/plans/{id} - Update plan

GET /admin/subscriptions - List subscriptions
PUT /admin/subscriptions/{id} - Update subscription
```

#### Tenant API
Semua API endpoint yang menggunakan middleware `tenant` akan secara otomatis terhubung ke database tenant yang sesuai.

## Pengembangan

### 1. Menambah Model Tenant

Untuk model yang akan menggunakan database tenant:

```php
<?php

namespace App\Models;

use App\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use BelongsToTenant;
    
    // Model akan otomatis menggunakan koneksi tenant
}
```

### 2. Menambah Migration Tenant

Letakkan migrations tenant di folder `database/migrations/tenant/`:

```bash
php artisan make:migration create_products_table
# Pindahkan file migration ke database/migrations/tenant/
```

### 3. Middleware Custom

Untuk route yang perlu akses khusus:

```php
Route::middleware(['tenant', 'auth'])->group(function () {
    // Routes yang membutuhkan autentikasi di level tenant
});
```

### 4. Mengakses Tenant Data

Dalam controller atau middleware:

```php
public function index(Request $request)
{
    $tenant = $request->attributes->get('tenant');
    // $tenant adalah instance dari App\Models\Global\Merchant
    
    // Data akan otomatis menggunakan database tenant
    $users = User::all();
}
```

## Commands Available

```bash
# Tenant management
php artisan tenant:create "Merchant Name" "admin@email.com" --plan=basic
php artisan tenant:drop merchant-slug

# Tenant prefix management
php artisan tenant:prefix show                    # Show current prefix configuration
php artisan tenant:prefix list                    # List all tenant databases
php artisan tenant:prefix change --new-prefix=company_ --dry-run

# Global database
php artisan migrate:global
php artisan migrate:global --fresh

# Subscription plans
php artisan plan:create "Plan Name" 29.99 --cycle=monthly --trial=14

# Tenant migrations
php artisan migrate --database=tenant_merchant-slug --path=database/migrations/tenant
```

## Struktur File

```
app/
├── Models/
│   ├── Global/           # Models untuk database global
│   │   ├── Merchant.php
│   │   ├── SubscriptionPlan.php
│   │   ├── MerchantSubscription.php
│   │   └── MerchantUser.php
│   └── User.php          # Model tenant (menggunakan BelongsToTenant)
├── Services/
│   └── TenantService.php # Service untuk mengelola tenant
├── Http/Middleware/
│   └── TenantResolver.php # Middleware untuk resolve tenant
├── Traits/
│   └── BelongsToTenant.php # Trait untuk model tenant
└── Console/Commands/     # Commands untuk management
    ├── CreateTenant.php
    ├── CreateSubscriptionPlan.php
    └── MigrateGlobal.php

database/
├── migrations/
│   ├── global/          # Migrations untuk database global
│   └── tenant/          # Migrations untuk database tenant
└── seeders/
    └── SubscriptionPlanSeeder.php

routes/
├── admin.php           # Routes untuk admin global
├── web.php            # Routes untuk tenant (dengan middleware)
└── api.php            # API routes
```

## Troubleshooting

### Database Connection Issues
1. Pastikan konfigurasi database di `.env` sudah benar
2. Periksa koneksi database global dan tenant
3. Jalankan `php artisan config:clear` setelah mengubah konfigurasi

### Tenant Not Found
1. Periksa apakah merchant sudah dibuat di database global
2. Pastikan subdomain atau parameter tenant sudah benar
3. Periksa status merchant (harus aktif)

### Migration Issues
1. Untuk global: `php artisan migrate:global`
2. Untuk tenant: `php artisan migrate --database=tenant_slug --path=database/migrations/tenant`

## Security Considerations

1. **Database Isolation**: Setiap tenant memiliki database terpisah
2. **Connection Security**: Pastikan kredensial database aman
3. **Tenant Validation**: Middleware memvalidasi tenant sebelum mengakses data
4. **Admin Access**: Route admin hanya dapat diakses melalui global connection

## Performance Tips

1. Gunakan Redis untuk session dan cache
2. Implementasikan database connection pooling
3. Monitor jumlah koneksi database
4. Pertimbangkan read replicas untuk tenant database
5. Implementasikan queue untuk operasi berat
