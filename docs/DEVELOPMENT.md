# ArusKAS Development Guide

## 📋 Table of Contents

1. [Architecture Overview](#architecture-overview)
2. [Multi-Tenant Implementation](#multi-tenant-implementation)
3. [Features Implemented](#features-implemented)
4. [Setup & Configuration](#setup--configuration)
5. [Database Structure](#database-structure)
6. [Registration Flow](#registration-flow)
7. [Authentication Flow](#authentication-flow)
8. [Tenant Auto-Seeding](#tenant-auto-seeding)
9. [Routing Configuration](#routing-configuration)
10. [Troubleshooting](#troubleshooting)
11. [Development Workflow](#development-workflow)

---

## 🏗️ Architecture Overview

### Multi-Tenant Architecture

ArusKAS menggunakan **database-per-tenant** approach untuk isolasi data yang maksimal:

```
┌─────────────────────────────────────────────────────────┐
│                    Global Database                       │
│  - merchants (tenant info)                              │
│  - merchant_users (admin accounts)                      │
│  - merchant_subscriptions                               │
│  - subscription_plans                                   │
└─────────────────────────────────────────────────────────┘
                           │
                           │ Creates
                           ▼
         ┌─────────────────────────────────────────┐
         │         Tenant Database (per tenant)     │
         │  - users                                 │
         │  - accounts                              │
         │  - journals                              │
         │  - cash_categories                       │
         │  - sequences                             │
         │  - ... (business data)                   │
         └─────────────────────────────────────────┘
```

### Key Components

#### 1. Controllers
- **Global Controllers** (`app/Http/Controllers/Api/Global/`)
  - `RegistrationController.php` - Tenant registration
  
- **Tenant Controllers** (context-aware)
  - `TenantAuthApiController.php` - Tenant authentication
  - `CashCategoryController.php` - Cash management
  - etc.

#### 2. Services
- **TenantService** (`app/Services/TenantService.php`)
  - Handle tenant database creation
  - Manage database connections
  - Tenant identification from URLs

#### 3. Middleware
- **TenantMiddleware** - Identify and set tenant context
- **Sanctum Auth** - Token-based authentication

#### 4. Models
- **Global Models** (`app/Models/Global/`)
  - `Merchant` - Tenant information
  - `MerchantUser` - Global admin users
  - `MerchantSubscription` - Subscription management
  
- **Tenant Models** (`app/Models/Tenant/`)
  - `User` - Tenant-specific users
  - `Account` - Chart of accounts
  - `CashCategory` - Cash categories
  - etc.

---

## 🔧 Multi-Tenant Implementation

### Tenant Identification

Tenant diidentifikasi dari URL pattern:

```php
// API Routes
/{tenant_id}/api/v1/...
// Example: /TNT123456/api/v1/auth/login

// Web Routes
/{tenant_id}/...
// Example: /TNT123456/dashboard
```

### Dynamic Database Connection

```php
// TenantService.php
public function setTenantConnection($merchant)
{
    $databaseName = $merchant->database_name;
    
    config(['database.connections.tenant' => [
        'driver' => 'mysql',
        'host' => config('database.connections.mysql.host'),
        'database' => $databaseName,
        'username' => config('database.connections.mysql.username'),
        'password' => config('database.connections.mysql.password'),
        // ... other settings
    ]]);
    
    DB::purge('tenant');
    DB::reconnect('tenant');
    DB::setDefaultConnection('tenant');
}
```

### Tenant Isolation

Setiap tenant memiliki:
1. **Unique tenant_id** - ID unik untuk URL routing
2. **Separate database** - Database terpisah untuk isolasi data
3. **Own users** - User management terpisah
4. **Independent subscription** - Subscription dan billing terpisah

---

## ✅ Features Implemented

### 1. Tenant Registration

**Features:**
- ✅ Automatic tenant_id generation
- ✅ Database creation & migration
- ✅ Free trial subscription setup
- ✅ Admin user creation (global & tenant)
- ✅ Email verification system
- ✅ Transaction rollback on failure
- ✅ Auto-seeding initial data

**File:** `app/Http/Controllers/Api/Global/RegistrationController.php`

### 2. Tenant Authentication

**Features:**
- ✅ Sanctum token authentication
- ✅ Multi-device session support
- ✅ Token refresh mechanism
- ✅ Logout (single & all devices)
- ✅ Email verification check
- ✅ Tenant context validation

**File:** `app/Http/Controllers/Api/TenantAuthApiController.php`

### 3. Cash Category Management

**Features:**
- ✅ CRUD operations
- ✅ Soft deletes & restore
- ✅ Audit trail (created_by, updated_by)
- ✅ Type filtering (income/expense)
- ✅ Search functionality
- ✅ Pagination support

**File:** `app/Http/Controllers/Tenant/CashCategoryController.php`

### 4. Database Seeding

**Features:**
- ✅ Automatic seeding on registration
- ✅ Error handling (non-blocking)
- ✅ Manual seeding command
- ✅ Configurable seeders

**Command:** `php artisan db:seed:tenant {tenant_id}`

---

## 🛠️ Setup & Configuration

### Prerequisites

- PHP 8.2+
- MySQL 8.0+
- Composer
- Node.js & NPM (untuk frontend assets)

### Installation Steps

1. **Clone Repository**
```bash
git clone <repository-url>
cd aruskas-cloud
```

2. **Install Dependencies**
```bash
composer install
npm install
```

3. **Environment Configuration**
```bash
cp .env.example .env
php artisan key:generate
```

4. **Configure Database**
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=aruskas_cloud_global
DB_USERNAME=your_username
DB_PASSWORD=your_password
```

5. **Configure Mail**
```env
MAIL_MAILER=smtp
MAIL_HOST=your_smtp_host
MAIL_PORT=587
MAIL_USERNAME=your_email
MAIL_PASSWORD=your_password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=noreply@example.com
```

6. **Run Migrations**
```bash
# Global database
php artisan migrate

# Seed subscription plans
php artisan db:seed --class=SubscriptionPlanSeeder
```

7. **Build Assets**
```bash
npm run build
```

8. **Start Server**
```bash
php artisan serve
```

---

## 💾 Database Structure

### Global Database Tables

```sql
merchants
├── id (PK)
├── name
├── slug (unique)
├── tenant_id (unique)
├── database_name
├── email
├── email_verified_at
├── status
├── settings (JSON)
└── timestamps

merchant_users
├── id (PK)
├── merchant_id (FK)
├── name
├── email
├── password
├── role
├── is_active
└── timestamps

merchant_subscriptions
├── id (PK)
├── merchant_id (FK)
├── subscription_plan_id (FK)
├── status
├── starts_at
├── ends_at
├── trial_ends_at
└── timestamps

subscription_plans
├── id (PK)
├── name
├── slug
├── price
├── trial_days
├── features (JSON)
├── status
└── timestamps
```

### Tenant Database Tables (per tenant)

```sql
users
├── id (PK)
├── name
├── email (unique)
├── password
├── email_verified_at
└── timestamps

accounts
├── id (PK)
├── code (unique)
├── name
├── type (enum)
├── category
├── parent_id (FK)
├── is_active
├── audit fields (created_by, updated_by, deleted_by)
└── timestamps (with soft deletes)

cash_categories
├── id (PK)
├── name
├── type (income/expense)
├── description
├── audit fields
└── timestamps (with soft deletes)

journals
├── id (PK)
├── code
├── date
├── description
├── reference
├── status (draft/posted/cancelled)
├── posted_at
├── audit fields
└── timestamps (with soft deletes)

sequences
├── id (PK)
├── module
├── prefix
├── current_number
├── padding
└── timestamps
```

---

## 🔄 Registration Flow

### Step-by-Step Process

```
1. User submits registration form
   ↓
2. Validate input data
   ↓
3. Begin database transaction
   ↓
4. Generate unique identifiers:
   - tenant_id (e.g., TNT123456)
   - slug (e.g., abc-company)
   - database_name (e.g., tenant_TNT123456)
   ↓
5. Get free trial subscription plan
   ↓
6. Create merchant record in global DB
   ↓
7. Create tenant database
   ↓
8. Run migrations on tenant database
   ↓
9. Create subscription record
   ↓
10. Create admin user in global DB
    ↓
11. Switch to tenant connection
    ↓
12. Create admin user in tenant DB
    ↓
13. Reset to global connection
    ↓
14. Seed tenant database (non-blocking)
    ↓
15. Send email verification
    ↓
16. Commit transaction
    ↓
17. Return success response with tenant_id
```

### Code Implementation

```php
public function register(TenantRegistrationRequest $request)
{
    DB::beginTransaction();
    
    try {
        // Generate identifiers
        $slug = Merchant::generateSlug($request->company_name);
        $tenantId = Merchant::generateTenantId();
        $databaseName = Merchant::generateDatabaseName($tenantId);
        
        // Create merchant
        $merchant = Merchant::create([
            'name' => $request->company_name,
            'slug' => $slug,
            'tenant_id' => $tenantId,
            'database_name' => $databaseName,
            'email' => $request->admin_email,
            'status' => true,
        ]);
        
        // Create tenant database & run migrations
        $this->tenantService->createTenant($merchant);
        
        // Create subscription
        $subscription = MerchantSubscription::create([...]);
        
        // Create users (global & tenant)
        $adminUser = MerchantUser::create([...]);
        
        $this->tenantService->setTenantConnection($merchant);
        $tenantUser = \App\Models\Tenant\User::create([...]);
        $this->tenantService->resetToGlobalConnection();
        
        // Seed tenant database
        try {
            Artisan::call('db:seed:tenant', [
                'tenant_id' => $tenantId,
                '--force' => true,
            ]);
        } catch (\Exception $e) {
            Log::warning("Tenant seeding failed: " . $e->getMessage());
        }
        
        // Send email verification
        Mail::to($merchant->email)->send(new TenantEmailVerification($merchant));
        
        DB::commit();
        
        return response()->json([...], 201);
        
    } catch (\Exception $e) {
        DB::rollBack();
        return response()->json([...], 500);
    }
}
```

---

## 🔐 Authentication Flow

### Login Process

```
1. User submits login request to /{tenant_id}/api/v1/auth/login
   ↓
2. Middleware identifies tenant from URL
   ↓
3. Validate tenant exists and is active
   ↓
4. Switch to tenant database connection
   ↓
5. Validate user credentials
   ↓
6. Check email verification status
   ↓
7. Generate Sanctum token
   ↓
8. Return user info + tenant info + token
```

### Code Implementation

```php
public function login(Request $request)
{
    // Get tenant from middleware
    $tenant = $request->tenant;
    
    // Set tenant connection
    $this->tenantService->setTenantConnection($tenant);
    
    // Validate credentials
    $user = User::where('email', $request->email)->first();
    
    if (!$user || !Hash::check($request->password, $user->password)) {
        return response()->json([
            'success' => false,
            'message' => 'These credentials do not match our records.'
        ], 401);
    }
    
    // Check email verification
    if (!$user->hasVerifiedEmail()) {
        return response()->json([
            'success' => false,
            'message' => 'Please verify your email address before logging in.'
        ], 403);
    }
    
    // Generate token
    $deviceName = $request->device_name ?? 'API Client';
    $token = $user->createToken($deviceName)->plainTextToken;
    
    return response()->json([
        'success' => true,
        'message' => 'Login successful',
        'data' => [
            'user' => $user,
            'tenant' => $tenant,
            'token' => $token
        ]
    ]);
}
```

---

## 🌱 Tenant Auto-Seeding

### Implementation

Setiap tenant baru secara otomatis di-seed dengan data awal setelah database dibuat.

### What Gets Seeded

1. **Sequences** - Auto-increment counters untuk document numbering
2. **Accounts** - Chart of accounts dasar
3. **Additional seeders** - Dapat ditambahkan sesuai kebutuhan

### Seeding Process

```php
// In registration process
try {
    Artisan::call('db:seed:tenant', [
        'tenant_id' => $tenantId,
        '--force' => true,
    ]);
} catch (\Exception $e) {
    // Log error but don't fail registration
    Log::warning("Tenant seeding failed for {$tenantId}: " . $e->getMessage());
}
```

### Manual Seeding

```bash
# Seed specific tenant
php artisan db:seed:tenant TNT123456

# Seed with specific seeder class
php artisan db:seed:tenant TNT123456 --class=AccountSeeder

# Seed all tenants
php artisan db:seed:tenant --all
```

### Features

- ✅ Non-blocking (registration succeeds even if seeding fails)
- ✅ Error logging
- ✅ Manual retry capability
- ✅ Configurable seeders
- ✅ Force flag for production

---

## 🛣️ Routing Configuration

### URL Patterns

#### Global Routes (No Tenant Context)
```php
// routes/api.php
Route::prefix('api/v1')->group(function () {
    Route::prefix('tenant')->group(function () {
        Route::get('/plans', [RegistrationController::class, 'getPlans']);
        Route::post('/register', [RegistrationController::class, 'register']);
    });
});
```

**URLs:**
- `GET /api/v1/tenant/plans`
- `POST /api/v1/tenant/register`

#### Tenant Routes (Require Tenant ID)
```php
// routes/api.php
Route::prefix('{tenant_id}/api/v1')->middleware(['tenant'])->group(function () {
    // Public tenant routes
    Route::prefix('auth')->group(function () {
        Route::post('/login', [TenantAuthApiController::class, 'login']);
    });
    
    // Protected tenant routes
    Route::middleware('auth:sanctum')->group(function () {
        Route::get('/auth/me', [TenantAuthApiController::class, 'me']);
        Route::post('/auth/logout', [TenantAuthApiController::class, 'logout']);
        Route::post('/auth/logout-all', [TenantAuthApiController::class, 'logoutAll']);
        Route::post('/auth/refresh', [TenantAuthApiController::class, 'refresh']);
    });
});
```

**URLs:**
- `POST /{tenant_id}/api/v1/auth/login`
- `GET /{tenant_id}/api/v1/auth/me` (protected)
- `POST /{tenant_id}/api/v1/auth/logout` (protected)

### Common Routing Mistakes

❌ **WRONG:**
```php
// Missing {tenant_id} in prefix
Route::prefix('api/v1')->middleware(['tenant'])->group(function () {
    Route::post('/auth/login', ...);
});
// Results in: /api/v1/auth/login (tenant_id missing!)
```

✅ **CORRECT:**
```php
// Include {tenant_id} in prefix
Route::prefix('{tenant_id}/api/v1')->middleware(['tenant'])->group(function () {
    Route::post('/auth/login', ...);
});
// Results in: /{tenant_id}/api/v1/auth/login
```

---

## 🐛 Troubleshooting

### Issue: Tenant Not Found

**Symptoms:**
```json
{
  "success": false,
  "message": "Tenant not found"
}
```

**Solutions:**

1. **Check tenant exists in database:**
```sql
SELECT id, name, tenant_id, status, database_name
FROM merchants
WHERE tenant_id = 'TNT123456';
```

2. **Verify tenant is active:**
```sql
UPDATE merchants SET status = 1 WHERE tenant_id = 'TNT123456';
```

3. **Check database exists:**
```sql
SHOW DATABASES LIKE 'tenant_TNT123456';
```

### Issue: Email Not Verified

**Symptoms:**
```json
{
  "success": false,
  "message": "Please verify your email address before logging in."
}
```

**Solutions:**

1. **Manual verification for testing:**
```sql
-- Global database
UPDATE merchants 
SET email_verified_at = NOW() 
WHERE tenant_id = 'TNT123456';

-- Tenant database
USE tenant_TNT123456;
UPDATE users 
SET email_verified_at = NOW() 
WHERE email = 'admin@example.com';
```

2. **Resend verification email:**
```php
Mail::to($merchant->email)->send(new TenantEmailVerification($merchant));
```

### Issue: Route Not Found

**Symptoms:**
```
The route TNT123456/api/v1/auth/login could not be found.
```

**Solutions:**

1. **Check route list:**
```bash
php artisan route:list --path=api
```

2. **Verify URL pattern:**
```
✅ CORRECT: http://localhost:8000/TNT123456/api/v1/auth/login
❌ WRONG:   http://localhost:8000/api/v1/auth/login
```

3. **Clear route cache:**
```bash
php artisan route:clear
php artisan route:cache
```

### Issue: Database Connection Error

**Symptoms:**
```
SQLSTATE[HY000] [1049] Unknown database 'tenant_TNT123456'
```

**Solutions:**

1. **Check tenant database exists:**
```sql
SHOW DATABASES LIKE 'tenant_%';
```

2. **Recreate tenant database:**
```php
// In tinker
$merchant = App\Models\Global\Merchant::where('tenant_id', 'TNT123456')->first();
$tenantService = app(App\Services\TenantService::class);
$tenantService->createTenant($merchant);
```

3. **Run migrations:**
```bash
php artisan migrate:tenant TNT123456
```

### Issue: SQL Syntax Error pada Migration

**Symptoms:**
```
SQLSTATE[42000]: Syntax error or access violation: 1064 
You have an error in your SQL syntax... near 'after `reference`'
```

**Cause:**
Penggunaan klausa `after` dalam `CREATE TABLE` (hanya valid di `ALTER TABLE`)

**Solution:**
Remove `after()` calls dari migration file:

```php
// ❌ WRONG
$table->enum('status', ['draft', 'posted'])->after('reference');

// ✅ CORRECT
$table->enum('status', ['draft', 'posted']);
```

---

## 🔨 Development Workflow

### 1. Adding New Feature

```bash
# 1. Create migration for tenant database
php artisan make:migration create_features_table --path=database/migrations/tenant

# 2. Create model
php artisan make:model Models/Tenant/Feature

# 3. Create controller
php artisan make:controller Tenant/FeatureController --api

# 4. Add routes
# Edit routes/api.php

# 5. Run migrations on existing tenants
php artisan migrate:tenant --all

# 6. Test
php artisan test
```

### 2. Testing Multi-Tenancy

```bash
# 1. Create test tenant
curl -X POST http://localhost:8000/api/v1/tenant/register \
  -H "Content-Type: application/json" \
  -d '{"company_name":"Test","admin_name":"Admin","admin_email":"test@example.com","password":"password123","password_confirmation":"password123","terms":true}'

# 2. Verify email manually
mysql -u root -p -e "UPDATE aruskas_cloud_global.merchants SET email_verified_at = NOW() WHERE email = 'test@example.com'"
mysql -u root -p -e "USE tenant_XXXXX; UPDATE users SET email_verified_at = NOW() WHERE email = 'test@example.com'"

# 3. Login
curl -X POST http://localhost:8000/XXXXX/api/v1/auth/login \
  -H "Content-Type: application/json" \
  -d '{"email":"test@example.com","password":"password123"}'

# 4. Test feature with token
curl -X GET http://localhost:8000/XXXXX/api/v1/features \
  -H "Authorization: Bearer TOKEN"
```

### 3. Database Commands

```bash
# Show all routes
php artisan route:list

# Clear caches
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear

# Run migrations
php artisan migrate                    # Global DB
php artisan migrate:tenant TNT123456   # Specific tenant
php artisan migrate:tenant --all       # All tenants

# Seed database
php artisan db:seed                    # Global DB
php artisan db:seed:tenant TNT123456   # Specific tenant
```

### 4. Debugging

```bash
# Enable query logging
DB::enableQueryLog();
// ... run queries
dd(DB::getQueryLog());

# Check current connection
dd(DB::connection()->getDatabaseName());

# Laravel logs
tail -f storage/logs/laravel.log

# MySQL logs
tail -f /var/log/mysql/error.log
```

---

## 📝 Code Standards

### Model Traits

```php
// Tenant models should use
use App\Traits\BelongsToTenant;

class Feature extends Model
{
    use BelongsToTenant;
    
    protected $connection = 'tenant';
    protected $fillable = [...];
}
```

### Audit Fields

```php
// Add audit fields to tenant models
protected $fillable = [
    'name',
    'description',
    'created_by',
    'updated_by',
    'deleted_by',
];

public function createdBy()
{
    return $this->belongsTo(User::class, 'created_by');
}
```

### API Response Format

```php
// Success response
return response()->json([
    'success' => true,
    'message' => 'Operation successful',
    'data' => $data
], 200);

// Error response
return response()->json([
    'success' => false,
    'message' => 'Error message',
    'errors' => $errors
], 422);
```

---

## 📚 Related Documentation

- **API Documentation**: `API.md` - Complete API reference
- **Project README**: `../README.md` - Project overview and setup
- **Postman Collection**: `ArusKAS_Tenant_Auth_API.postman_collection.json`

---

## 🎯 Next Steps

### Recommended Improvements

1. **Performance**
   - [ ] Implement caching for tenant data
   - [ ] Add database indexing optimization
   - [ ] Queue email sending
   - [ ] Background job for tenant seeding

2. **Security**
   - [ ] Add rate limiting
   - [ ] Implement API key authentication
   - [ ] Add request logging
   - [ ] CSRF protection for web routes

3. **Features**
   - [ ] Password reset API
   - [ ] Two-factor authentication
   - [ ] Tenant settings management
   - [ ] Subscription upgrade/downgrade
   - [ ] Usage tracking & analytics

4. **DevOps**
   - [ ] CI/CD pipeline
   - [ ] Automated testing
   - [ ] Docker containerization
   - [ ] Monitoring & alerting

---

**Last Updated:** November 2025  
**Laravel Version:** 11.x  
**PHP Version:** 8.2+
