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

# Cek status migration global
php artisan migrate:global-status

# Rollback migration global
php artisan migrate:global-rollback

# Rollback migration global dengan jumlah step tertentu
php artisan migrate:global-rollback --step=3

# Reset semua migration global (rollback all)
php artisan migrate:global-reset

# Seed database global dengan default seeder (SubscriptionPlanSeeder)
php artisan db:seed:global

# Seed dengan seeder class tertentu
php artisan db:seed:global --class=SubscriptionPlanSeeder
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

Gunakan command khusus untuk model tenant:

```bash
# Buat model tenant dengan migration
php artisan make:model-tenant Product -m

# Model akan otomatis:
# - Menggunakan trait BelongsToTenant
# - Migration disimpan di database/migrations/tenant/
# - Menggunakan koneksi database tenant
```

Model yang dihasilkan:
```php
<?php

namespace App\Models\Tenant;

use App\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use BelongsToTenant;
    
    // Model akan otomatis menggunakan koneksi tenant
}
```

### 2. Menambah Model Global

Gunakan command khusus untuk model global:

```bash
# Buat model global dengan migration
php artisan make:model-global GlobalConfig -m

# Model akan otomatis:
# - Disimpan di namespace App\Models\Global\
# - Migration disimpan di database/migrations/global/
# - Menggunakan koneksi database global
```

Model yang dihasilkan:
```php
<?php

namespace App\Models\Global;

use Illuminate\Database\Eloquent\Model;

class GlobalConfig extends Model
{
    // Model menggunakan koneksi database global
}
```

### 3. Menambah Migration Manual

Jika hanya ingin membuat migration tanpa model:

```bash
# Migration global
php artisan make:migration-global CreateGlobalSettingsTable --create=global_settings

# Migration tenant
php artisan make:migration-tenant CreateProductCategoriesTable --create=product_categories
```

### 4. Middleware Custom

Untuk route yang perlu akses khusus:

```php
Route::middleware(['tenant', 'auth'])->group(function () {
    // Routes yang membutuhkan autentikasi di level tenant
});
```

### 5. Mengakses Tenant Data

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

## ✅ Migration & Model Management Lengkap

### 🎯 **Membuat Migration & Model**

| Command | Deskripsi | Contoh |
|---------|-----------|---------|
| `make:migration-global` | Buat migration global | `php artisan make:migration-global CreateGlobalSettingsTable --create=global_settings` |
| `make:migration-tenant` | Buat migration tenant | `php artisan make:migration-tenant CreateProductsTable --create=products` |
| `make:model-global` | Buat model global + migration | `php artisan make:model-global GlobalSetting -m` |
| `make:model-tenant` | Buat model tenant + migration | `php artisan make:model-tenant Product -mcr` |

### 🎯 **Migration Database Global**

| Command | Deskripsi | Contoh |
|---------|-----------|---------|
| `migrate:global` | Jalankan migration global | `php artisan migrate:global` |
| `migrate:global --fresh` | Drop tables + migrate global | `php artisan migrate:global --fresh` |
| `migrate:global-fresh` | Drop tables + migrate global | `php artisan migrate:global-fresh` |
| `migrate:global-fresh --seed` | Fresh migrate + seed | `php artisan migrate:global-fresh --seed` |
| `migrate:global-status` | Status migration global | `php artisan migrate:global-status` |
| `migrate:global-rollback` | Rollback migration global | `php artisan migrate:global-rollback` |
| `migrate:global-rollback --step=N` | Rollback N migration | `php artisan migrate:global-rollback --step=3` |
| `migrate:global-reset` | Reset semua migration | `php artisan migrate:global-reset` |

### 🎯 **Migration Database Tenant**

| Command | Deskripsi | Contoh |
|---------|-----------|---------|
| `tenant:migrate TENANT_ID` | Migrate satu tenant | `php artisan tenant:migrate 2B1GWBXL` |
| `tenant:migrate --all` | Migrate semua tenant | `php artisan tenant:migrate --all` |
| `tenant:migrate-fresh TENANT_ID` | Fresh migrate satu tenant | `php artisan tenant:migrate-fresh 2B1GWBXL` |
| `tenant:migrate-fresh --all` | Fresh migrate semua tenant | `php artisan tenant:migrate-fresh --all` |
| `tenant:migrate-fresh --all --seed` | Fresh migrate + seed | `php artisan tenant:migrate-fresh --all --seed` |
| `tenant:migrate-status TENANT_ID` | Status satu tenant | `php artisan tenant:migrate-status 2B1GWBXL` |
| `tenant:migrate-status --all` | Status semua tenant | `php artisan tenant:migrate-status --all` |
| `tenant:migrate-rollback TENANT_ID` | Rollback satu tenant | `php artisan tenant:migrate-rollback 2B1GWBXL` |
| `tenant:migrate-rollback --all` | Rollback semua tenant | `php artisan tenant:migrate-rollback --all` |

### 🎯 **Database Seeding**

| Command | Deskripsi | Contoh |
|---------|-----------|---------|
| `db:seed:global` | Seed database global | `php artisan db:seed:global` |
| `db:seed:global --class=SeederClass` | Seed dengan seeder tertentu | `php artisan db:seed:global --class=SubscriptionPlanSeeder` |
| `db:seed:tenant TENANT_ID` | Seed satu tenant | `php artisan db:seed:tenant 2B1GWBXL` |
| `db:seed:tenant --all` | Seed semua tenant | `php artisan db:seed:tenant --all` |
| `db:seed:tenant TENANT_ID --class=SeederClass` | Seed tenant dengan seeder tertentu | `php artisan db:seed:tenant 2B1GWBXL --class=ProductSeeder` |

### 🎯 **Queue Worker Commands**

| Command | Deskripsi | Contoh |
|---------|-----------|---------|
| `queue:work-global` | Process queue jobs di global database | `php artisan queue:work-global` |
| `queue:work-global --once` | Process 1 job lalu stop | `php artisan queue:work-global --once` |
| `queue:work-global --stop-when-empty` | Stop ketika queue kosong | `php artisan queue:work-global --stop-when-empty` |
| `queue:work-tenant TENANT_ID` | Process queue untuk satu tenant | `php artisan queue:work-tenant 2B1GWBXL` |
| `queue:work-tenant --all` | Process queue semua tenant | `php artisan queue:work-tenant --all` |
| `queue:work-tenant --all --once` | Process 1 job per tenant | `php artisan queue:work-tenant --all --once` |

### 🎯 **Membuat Seeder**

| Command | Deskripsi | Contoh |
|---------|-----------|---------|
| `make:seeder:global SeederName` | Buat seeder global | `php artisan make:seeder:global SubscriptionPlanSeeder` |
| `make:seeder:tenant SeederName` | Buat seeder tenant | `php artisan make:seeder:tenant ProductSeeder` |

### 🚀 **Workflow Development**

#### Membuat Model & Migration Global
```bash
# Buat model global dengan migration
php artisan make:model-global GlobalSetting -m

# Buat model global dengan controller dan resource
php artisan make:model-global User -mcr

# Buat model global dengan semua file pendukung
php artisan make:model-global Config --all

# Jalankan migration global
php artisan migrate:global

# Seed database global
php artisan db:seed:global
```

#### Membuat Model & Migration Tenant
```bash
# Buat model tenant dengan migration
php artisan make:model-tenant Product -m

# Buat model tenant dengan controller dan resource  
php artisan make:model-tenant Order -mcr

# Buat model tenant dengan semua file pendukung
php artisan make:model-tenant Invoice --all

# Jalankan migration untuk semua tenant
php artisan tenant:migrate --all

# Seed semua tenant
php artisan db:seed:tenant --all
```

#### Membuat Seeder
```bash
# Buat seeder untuk database global
php artisan make:seeder:global SubscriptionPlanSeeder

# Buat seeder untuk database tenant
php artisan make:seeder:tenant ProductSeeder
php artisan make:seeder:tenant CategorySeeder

# Jalankan seeder global
php artisan db:seed:global --class=SubscriptionPlanSeeder

# Jalankan seeder tenant untuk satu tenant
php artisan db:seed:tenant 2B1GWBXL --class=ProductSeeder

# Jalankan seeder tenant untuk semua tenant
php artisan db:seed:tenant --all --class=ProductSeeder
```

#### Fitur Otomatis
- **Model Global**: Otomatis disimpan di namespace `App\Models\Global\`
- **Model Tenant**: Otomatis disimpan di namespace `App\Models\Tenant\` dengan trait `BelongsToTenant`
- **Controller Global**: Otomatis disimpan di namespace `App\Http\Controllers\Global\`
- **Controller Tenant**: Otomatis disimpan di namespace `App\Http\Controllers\Tenant\`
- **Migration Global**: Otomatis ke folder `database/migrations/global/`
- **Migration Tenant**: Otomatis ke folder `database/migrations/tenant/`

### 🚀 **Workflow Contoh**

```bash
# Setup aplikasi baru
php artisan migrate:global --fresh --seed
php artisan tenant:create "My Company" admin@test.com --plan=free

# Cek status semua database
php artisan migrate:global-status
php artisan tenant:migrate-status --all

# Update aplikasi (ada migration baru)
php artisan migrate:global
php artisan tenant:migrate --all

# Rollback jika ada masalah
php artisan migrate:global-rollback --step=1
php artisan tenant:migrate-rollback --all --step=1
```

## Struktur File

```
app/
├── Http/
│   └── Controllers/
│       ├── Global/           # Controllers untuk admin global
│       │   ├── MerchantController.php
│       │   ├── SubscriptionController.php
│       │   └── AdminDashboardController.php
│       ├── Tenant/           # Controllers untuk aplikasi tenant
│       │   ├── DashboardController.php
│       │   ├── ProductController.php
│       │   └── OrderController.php
│       └── Controller.php    # Base controller
├── Models/
│   ├── Global/           # Models untuk database global
│   │   ├── Merchant.php
│   │   ├── SubscriptionPlan.php
│   │   ├── MerchantSubscription.php
│   │   └── MerchantUser.php
│   └── Tenant/           # Models untuk database tenant  
│       ├── User.php      # Model tenant (menggunakan BelongsToTenant)
│       ├── ApiClient.php
│       ├── EmailMessage.php
│       └── EmailProvider.php
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
    ├── DatabaseSeeder.php
    ├── SubscriptionPlanSeeder.php
    ├── Global/          # Seeders untuk database global
    │   └── SubscriptionPlanSeeder.php
    └── Tenant/          # Seeders untuk database tenant
        ├── ProductSeeder.php
        └── CategorySeeder.php

routes/
├── admin.php           # Routes untuk admin global
├── web.php            # Routes untuk tenant (dengan middleware)
└── api.php            # API routes
```

## Commands Available

### Development Commands
```bash
# Membuat migration & model global
php artisan make:migration-global CreateGlobalSettingsTable --create=global_settings
php artisan make:model-global GlobalSetting -m

# Membuat migration & model tenant  
php artisan make:migration-tenant CreateProductsTable --create=products
php artisan make:model-tenant Product -mcr

# Membuat controller global (untuk admin)
php artisan make:controller-global AdminDashboardController -r

# Membuat controller tenant (untuk aplikasi tenant)
php artisan make:controller-tenant DashboardController -r

# Opsi yang tersedia untuk make:model-*:
# -m, --migration    : Buat migration file
# -c, --controller   : Buat controller  
# -r, --resource     : Buat resource controller
# -f, --factory      : Buat factory
# -s, --seeder       : Buat seeder
# --requests         : Buat form request classes
# -a, --all          : Buat semua file di atas

# Opsi yang tersedia untuk make:controller-*:
# -r, --resource     : Buat resource controller
# --api              : Buat API resource controller
# --invokable        : Buat single method controller
# --model=ModelName  : Buat resource controller untuk model tertentu
# --parent=ParentModel : Buat nested resource controller
```

### Tenant Management
```bash
# Membuat tenant baru
php artisan tenant:create "Merchant Name" "admin@email.com" --plan=basic

# Drop tenant (belum tersedia - manual delete dari database)
```

### Global Database
```bash
php artisan migrate:global                      # Migrate global database
php artisan migrate:global --fresh              # Fresh migrate global
php artisan migrate:global-status               # Status migration global
php artisan migrate:global-rollback             # Rollback global migration
php artisan migrate:global-rollback --step=3    # Rollback dengan jumlah step
php artisan migrate:global-reset                # Reset semua migration global
```

### Tenant Database
```bash
php artisan tenant:migrate TENANT_ID            # Migrate satu tenant
php artisan tenant:migrate --all                # Migrate semua tenant
php artisan tenant:migrate TENANT_ID --fresh    # Fresh migrate tenant
php artisan tenant:migrate-status TENANT_ID     # Status migration tenant
php artisan tenant:migrate-status --all         # Status semua tenant
php artisan tenant:migrate-rollback TENANT_ID   # Rollback tenant migration
php artisan tenant:migrate-rollback --all       # Rollback semua tenant
```

### Database Seeding
```bash
# Global Database
php artisan db:seed:global                         # Seed dengan default seeder
php artisan db:seed:global --class=SubscriptionPlanSeeder  # Seed dengan seeder tertentu
php artisan db:seed:global --force                 # Force seed di production

# Tenant Database
php artisan db:seed:tenant TENANT_ID               # Seed satu tenant
php artisan db:seed:tenant --all                   # Seed semua tenant
php artisan db:seed:tenant TENANT_ID --class=ProductSeeder  # Seed dengan seeder tertentu
php artisan db:seed:tenant --all --class=ProductSeeder --force  # Seed semua dengan force

# Membuat Seeder
php artisan make:seeder:global SubscriptionPlanSeeder  # Buat seeder global
php artisan make:seeder:tenant ProductSeeder       # Buat seeder tenant
```

### Queue Worker Commands

#### Global Queue Worker
Process queue jobs untuk database global:
```bash
# Process queue jobs di global database
php artisan queue:work-global

# Process hanya 1 job lalu stop
php artisan queue:work-global --once

# Stop ketika queue kosong
php artisan queue:work-global --stop-when-empty

# Limit jumlah jobs yang diproses
php artisan queue:work-global --max-jobs=100

# Limit waktu running (dalam detik)
php artisan queue:work-global --max-time=3600

# Custom sleep time (detik)
php artisan queue:work-global --sleep=5

# Custom timeout per job (detik)
php artisan queue:work-global --timeout=120

# Jumlah retry attempts
php artisan queue:work-global --tries=3
```

#### Tenant Queue Worker
Process queue jobs untuk database tenant:
```bash
# Process queue jobs untuk satu tenant
php artisan queue:work-tenant TENANT_ID

# Process queue jobs untuk semua tenant
php artisan queue:work-tenant --all

# Process 1 job per tenant lalu stop
php artisan queue:work-tenant TENANT_ID --once
php artisan queue:work-tenant --all --once

# Stop ketika queue kosong
php artisan queue:work-tenant TENANT_ID --stop-when-empty

# Dengan opsi tambahan
php artisan queue:work-tenant TENANT_ID --max-jobs=50 --sleep=3 --tries=3
php artisan queue:work-tenant --all --stop-when-empty --timeout=120
```

**Contoh Penggunaan:**
```bash
# Development: Process queue untuk tenant tertentu
php artisan queue:work-tenant 2B1GWBXL --once

# Production: Process semua tenant queues
php artisan queue:work-tenant --all --sleep=3 --tries=3

# Process global queue (untuk email verifikasi, dll)
php artisan queue:work-global --stop-when-empty
```

### Development Utilities
```bash
php artisan tenant:prefix show                  # Show current prefix configuration
php artisan tenant:prefix list                  # List all tenant databases
```

## Troubleshooting

### Migration Issues

#### Global Database
```bash
# Cek status migration global
php artisan migrate:global-status

# Jika ada migration yang belum berjalan
php artisan migrate:global

# Jika perlu rollback
php artisan migrate:global-rollback --step=1

# Jika database corrupt, reset dan migrate ulang
php artisan migrate:global-reset
php artisan migrate:global --seed
```

#### Tenant Database
```bash
# Cek status migration semua tenant
php artisan tenant:migrate-status --all

# Migrate tenant yang belum up-to-date
php artisan tenant:migrate TENANT_ID

# Jika ada masalah dengan satu tenant
php artisan tenant:migrate-rollback TENANT_ID --step=1
php artisan tenant:migrate TENANT_ID

# Migrate semua tenant sekaligus
php artisan tenant:migrate --all

# Seed tenant setelah migration
php artisan db:seed:tenant TENANT_ID
php artisan db:seed:tenant --all
```

#### Seeding Issues
```bash
# Jika seeder tidak berjalan, pastikan seeder class sudah dibuat
php artisan make:seeder:global SubscriptionPlanSeeder
php artisan make:seeder:tenant ProductSeeder

# Jalankan seeder dengan class tertentu
php artisan db:seed:global --class=SubscriptionPlanSeeder
php artisan db:seed:tenant TENANT_ID --class=ProductSeeder

# Force seeding di production
php artisan db:seed:global --force
php artisan db:seed:tenant --all --force
```

#### Error: "Database does not exist"
```bash
# Pastikan tenant sudah dibuat dengan benar
php artisan tenant:create "Company Name" admin@test.com --plan=free

# Atau cek daftar tenant yang ada
php artisan tinker
>>> App\Models\Global\Merchant::all(['tenant_id', 'name', 'database_name']);
```

### Database Connection Issues
1. Pastikan konfigurasi database di `.env` sudah benar
2. Periksa koneksi database global dan tenant
3. Jalankan `php artisan config:clear` setelah mengubah konfigurasi

### Tenant Not Found
1. Periksa apakah merchant sudah dibuat di database global
2. Pastikan subdomain atau parameter tenant sudah benar
3. Periksa status merchant (harus aktif)

### Migration Issues (Legacy)
1. Untuk global: `php artisan migrate:global`
2. Untuk tenant: `php artisan tenant:migrate TENANT_ID` atau `php artisan tenant:migrate --all`

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
