# GutsyPOS API Documentation

## 📋 Overview

GutsyPOS Cloud menggunakan RESTful API architecture dengan multi-tenant support. Setiap tenant memiliki URL dan database tersendiri untuk isolasi data yang aman.

## 🌐 Base URLs

### Global Endpoints
```
http://your-domain.com/api/v1
```
Digunakan untuk operasi yang tidak memerlukan tenant context (registrasi, daftar subscription plans).

### Tenant-Specific Endpoints
```
http://your-domain.com/{tenant_id}/api/v1
```
Digunakan untuk operasi yang memerlukan tenant context (authentication, resource management).

---

## 🔐 Authentication

API menggunakan **Laravel Sanctum** untuk token-based authentication.

### Headers Required
```
Content-Type: application/json
Accept: application/json
Authorization: Bearer {token}  // Untuk protected endpoints
```

### Token Management
- Token diperoleh setelah login sukses
- Token dapat di-refresh untuk memperpanjang sesi
- Token dapat di-revoke untuk logout
- Support multiple device sessions

---

## 📚 API Endpoints

### Global Endpoints

#### 1. Get Subscription Plans
Mendapatkan daftar paket subscription yang tersedia.

**Endpoint:** `GET /api/v1/tenant/plans`

**Response:**
```json
{
  "success": true,
  "data": {
    "plans": [
      {
        "id": 1,
        "name": "Free Trial",
        "slug": "free-trial",
        "price": 0,
        "trial_days": 30,
        "features": "Basic features",
        "status": true
      }
    ]
  }
}
```

---

#### 2. Register New Tenant
Mendaftarkan tenant/merchant baru dengan free trial subscription.

**Endpoint:** `POST /api/v1/tenant/register`

**Request Body:**
```json
{
  "company_name": "ABC Company",
  "admin_name": "John Doe",
  "admin_email": "admin@example.com",
  "password": "password123",
  "password_confirmation": "password123",
  "terms": true
}
```

**Validation Rules:**
- `company_name`: required, string, max 255 chars
- `admin_name`: required, string, max 255 chars
- `admin_email`: required, email, unique
- `password`: required, min 8 chars, confirmed
- `terms`: required, must be true

**Success Response (201):**
```json
{
  "success": true,
  "message": "Tenant registered successfully. Please check your email to verify your account.",
  "data": {
    "merchant": {
      "id": 1,
      "name": "ABC Company",
      "slug": "abc-company",
      "tenant_id": "TNT123456",
      "email": "admin@example.com",
      "status": true
    },
    "subscription": {
      "plan": "Free Trial",
      "status": "active",
      "trial_ends_at": "2025-12-03T00:00:00.000000Z"
    },
    "tenant_url": "http://your-domain.com/TNT123456",
    "admin_email": "admin@example.com"
  }
}
```

**Error Response (422):**
```json
{
  "success": false,
  "message": "Validation failed",
  "errors": {
    "admin_email": ["This email address is already registered."]
  }
}
```

**What Happens Behind the Scenes:**
1. Generate unique `tenant_id`, `slug`, dan `database_name`
2. Create merchant record di global database
3. Create tenant-specific database
4. Run migrations untuk tenant database
5. Create subscription record
6. Create admin user di global dan tenant database
7. Seed tenant database dengan data awal
8. Send email verification
9. Commit transaction

---

### Tenant Authentication Endpoints

Base URL: `http://your-domain.com/{tenant_id}/api/v1`

#### 3. Tenant Login
Authenticate dan mendapatkan access token.

**Endpoint:** `POST /{tenant_id}/api/v1/auth/login`

**Example:** `POST /TNT123456/api/v1/auth/login`

**Request Body:**
```json
{
  "email": "admin@example.com",
  "password": "password123",
  "device_name": "iPhone 12"  // optional
}
```

**Success Response (200):**
```json
{
  "success": true,
  "message": "Login successful",
  "data": {
    "user": {
      "id": 1,
      "name": "John Doe",
      "email": "admin@example.com",
      "email_verified_at": "2025-11-03T10:00:00.000000Z"
    },
    "tenant": {
      "id": 1,
      "name": "ABC Company",
      "tenant_id": "TNT123456",
      "slug": "abc-company"
    },
    "token": "1|abcdefghijklmnopqrstuvwxyz123456789"
  }
}
```

**Error Responses:**

401 - Invalid credentials:
```json
{
  "success": false,
  "message": "These credentials do not match our records."
}
```

403 - Email not verified:
```json
{
  "success": false,
  "message": "Please verify your email address before logging in."
}
```

404 - Tenant not found:
```json
{
  "success": false,
  "message": "Tenant not found"
}
```

---

#### 4. Get Authenticated User
Mendapatkan informasi user yang sedang login.

**Endpoint:** `GET /{tenant_id}/api/v1/auth/me`

**Headers:**
```
Authorization: Bearer {your_token}
```

**Success Response (200):**
```json
{
  "success": true,
  "data": {
    "user": {
      "id": 1,
      "name": "John Doe",
      "email": "admin@example.com",
      "email_verified_at": "2025-11-03T10:00:00.000000Z"
    },
    "tenant": {
      "id": 1,
      "name": "ABC Company",
      "tenant_id": "TNT123456",
      "slug": "abc-company"
    }
  }
}
```

---

#### 5. Logout (Current Device)
Revoke token saat ini.

**Endpoint:** `POST /{tenant_id}/api/v1/auth/logout`

**Headers:**
```
Authorization: Bearer {your_token}
```

**Success Response (200):**
```json
{
  "success": true,
  "message": "Logout successful"
}
```

---

#### 6. Logout All Devices
Revoke semua token untuk user.

**Endpoint:** `POST /{tenant_id}/api/v1/auth/logout-all`

**Headers:**
```
Authorization: Bearer {your_token}
```

**Success Response (200):**
```json
{
  "success": true,
  "message": "All sessions revoked successfully"
}
```

---

#### 7. Refresh Token
Generate token baru (token lama akan di-revoke).

**Endpoint:** `POST /{tenant_id}/api/v1/auth/refresh`

**Headers:**
```
Authorization: Bearer {your_token}
```

**Request Body (Optional):**
```json
{
  "device_name": "iPhone 12"
}
```

**Success Response (200):**
```json
{
  "success": true,
  "message": "Token refreshed successfully",
  "data": {
    "token": "2|newtoken123456789abcdefghijklmnopqrstuvwxyz"
  }
}
```

---

### Cash Category Endpoints

Base URL: `/{tenant_id}/cash/categories`

#### 8. List All Categories
Get daftar cash categories dengan pagination dan filter.

**Endpoint:** `GET /{tenant_id}/cash/categories`

**Query Parameters:**
- `type` (optional): Filter by type (`income` or `expense`)
- `search` (optional): Search by name
- `page` (optional): Page number

**Example:**
```
GET /{tenant_id}/cash/categories?type=income&search=salary&page=1
```

**Response:**
```json
{
  "success": true,
  "data": {
    "data": [
      {
        "id": 1,
        "name": "Salary",
        "type": "income",
        "description": "Monthly salary income",
        "created_at": "2025-08-28T10:00:00.000000Z",
        "updated_at": "2025-08-28T10:00:00.000000Z",
        "created_by_id": 1,
        "updated_by_id": 1
      }
    ],
    "current_page": 1,
    "per_page": 15,
    "total": 1
  }
}
```

---

#### 9. Create Category
Membuat cash category baru.

**Endpoint:** `POST /{tenant_id}/cash/categories`

**Request Body:**
```json
{
  "name": "Freelance Work",
  "type": "income",
  "description": "Income from freelance projects"
}
```

**Validation Rules:**
- `name`: required, string, max 255 chars, unique per type
- `type`: required, enum (`income`, `expense`)
- `description`: optional, string, max 1000 chars

**Success Response (201):**
```json
{
  "success": true,
  "message": "Cash category created successfully",
  "data": {
    "id": 2,
    "name": "Freelance Work",
    "type": "income",
    "description": "Income from freelance projects",
    "created_by_id": 1,
    "updated_by_id": 1,
    "created_at": "2025-08-28T10:00:00.000000Z",
    "updated_at": "2025-08-28T10:00:00.000000Z"
  }
}
```

---

#### 10. Show Category
Get detail satu category.

**Endpoint:** `GET /{tenant_id}/cash/categories/{id}`

**Response:**
```json
{
  "success": true,
  "data": {
    "id": 1,
    "name": "Salary",
    "type": "income",
    "description": "Monthly salary income",
    "created_by_id": 1,
    "updated_by_id": 1,
    "created_at": "2025-08-28T10:00:00.000000Z",
    "updated_at": "2025-08-28T10:00:00.000000Z",
    "created_by": {
      "id": 1,
      "name": "John Doe",
      "email": "john@example.com"
    },
    "updated_by": {
      "id": 1,
      "name": "John Doe",
      "email": "john@example.com"
    }
  }
}
```

---

#### 11. Update Category
Update category yang ada.

**Endpoint:** `PUT /{tenant_id}/cash/categories/{id}`

**Request Body:**
```json
{
  "name": "Updated Category Name",
  "type": "expense",
  "description": "Updated description"
}
```

**Success Response (200):**
```json
{
  "success": true,
  "message": "Cash category updated successfully",
  "data": {
    "id": 1,
    "name": "Updated Category Name",
    "type": "expense",
    "description": "Updated description",
    "updated_by_id": 1,
    "updated_at": "2025-08-28T11:00:00.000000Z"
  }
}
```

---

#### 12. Delete Category (Soft Delete)
Soft delete category.

**Endpoint:** `DELETE /{tenant_id}/cash/categories/{id}`

**Success Response (200):**
```json
{
  "success": true,
  "message": "Cash category deleted successfully"
}
```

---

#### 13. Get Categories by Type
Get categories berdasarkan type tertentu.

**Endpoint:** `GET /{tenant_id}/cash/categories/type/{type}`

**Parameters:**
- `type`: `income` or `expense`

---

#### 14. Get Trashed Categories
Get daftar categories yang sudah di-delete.

**Endpoint:** `GET /{tenant_id}/cash/categories/trashed/list`

**Response:**
```json
{
  "success": true,
  "data": {
    "data": [
      {
        "id": 1,
        "name": "Deleted Category",
        "type": "expense",
        "deleted_at": "2025-08-28T12:00:00.000000Z"
      }
    ]
  }
}
```

---

#### 15. Restore Category
Restore category yang sudah di-delete.

**Endpoint:** `POST /{tenant_id}/cash/categories/{id}/restore`

**Success Response (200):**
```json
{
  "success": true,
  "message": "Cash category restored successfully",
  "data": {
    "id": 1,
    "name": "Restored Category",
    "deleted_at": null
  }
}
```

---

#### 16. Force Delete Category
Permanently delete category.

**Endpoint:** `DELETE /{tenant_id}/cash/categories/{id}/force`

**Success Response (200):**
```json
{
  "success": true,
  "message": "Cash category permanently deleted"
}
```

---

## 🧪 Testing Guide

### Quick Start Testing

#### 1. Register New Tenant
```bash
curl -X POST http://localhost:8000/api/v1/tenant/register \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d '{
    "company_name": "Test Company ABC",
    "admin_name": "Admin ABC",
    "admin_email": "admin.abc@example.com",
    "password": "password123",
    "password_confirmation": "password123",
    "terms": true
  }'
```

**⚠️ IMPORTANT: Save `tenant_id` dari response**

#### 2. Verify Email
Option 1: Check email inbox dan klik link verifikasi

Option 2: Manual via database (untuk testing):
```sql
-- Global database
UPDATE merchants SET email_verified_at = NOW() WHERE tenant_id = 'TNT123456';

-- Tenant database
USE tenant_TNT123456;
UPDATE users SET email_verified_at = NOW() WHERE email = 'admin.abc@example.com';
```

#### 3. Login
```bash
curl -X POST http://localhost:8000/TNT123456/api/v1/auth/login \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d '{
    "email": "admin.abc@example.com",
    "password": "password123",
    "device_name": "Test Device"
  }'
```

**⚠️ IMPORTANT: Save `token` dari response**

#### 4. Get User Info
```bash
curl -X GET http://localhost:8000/TNT123456/api/v1/auth/me \
  -H "Accept: application/json" \
  -H "Authorization: Bearer {your_token}"
```

---

### Testing Error Scenarios

#### Test 1: Register dengan Email yang Sudah Ada
```bash
curl -X POST http://localhost:8000/api/v1/tenant/register \
  -H "Content-Type: application/json" \
  -d '{
    "company_name": "Another Company",
    "admin_name": "Admin XYZ",
    "admin_email": "admin.abc@example.com",
    "password": "password123",
    "password_confirmation": "password123",
    "terms": true
  }'
```

**Expected:** 422 - Validation error

#### Test 2: Login dengan Password Salah
```bash
curl -X POST http://localhost:8000/TNT123456/api/v1/auth/login \
  -H "Content-Type: application/json" \
  -d '{
    "email": "admin.abc@example.com",
    "password": "wrongpassword"
  }'
```

**Expected:** 401 - Invalid credentials

#### Test 3: Access Protected Route Tanpa Token
```bash
curl -X GET http://localhost:8000/TNT123456/api/v1/auth/me \
  -H "Accept: application/json"
```

**Expected:** 401 - Unauthenticated

#### Test 4: Login dengan Tenant ID Invalid
```bash
curl -X POST http://localhost:8000/INVALID999/api/v1/auth/login \
  -H "Content-Type: application/json" \
  -d '{
    "email": "admin.abc@example.com",
    "password": "password123"
  }'
```

**Expected:** 404 - Tenant not found

---

### Testing dengan Postman

#### Setup
1. Import Postman Collection: `docs/GutsyPOS_Tenant_Auth_API.postman_collection.json`
2. Create environment variables:
   - `base_url`: `http://localhost:8000`
   - `tenant_id`: Akan auto-save setelah register
   - `token`: Akan auto-save setelah login

#### Collection Structure
```
GutsyPOS API
├── Global
│   ├── Get Plans
│   └── Register Tenant
├── Auth
│   ├── Login
│   ├── Get Me
│   ├── Logout
│   ├── Logout All
│   └── Refresh Token
└── Cash Categories
    ├── List Categories
    ├── Create Category
    ├── Show Category
    ├── Update Category
    ├── Delete Category
    ├── Get by Type
    ├── List Trashed
    ├── Restore Category
    └── Force Delete
```

---

## ❌ Error Codes

| Code | Description | Common Causes |
|------|-------------|---------------|
| 200  | Success | Request berhasil |
| 201  | Created | Resource berhasil dibuat |
| 400  | Bad Request | Request format salah |
| 401  | Unauthorized | Token invalid/missing |
| 403  | Forbidden | Email not verified / No permission |
| 404  | Not Found | Tenant/Resource tidak ditemukan |
| 422  | Validation Error | Input data tidak valid |
| 500  | Internal Server Error | Server error |

---

## 📝 Response Format Standard

### Success Response
```json
{
  "success": true,
  "message": "Operation successful",
  "data": {
    // response data
  }
}
```

### Error Response
```json
{
  "success": false,
  "message": "Error message",
  "errors": {
    // validation errors or details
  }
}
```

---

## 🔒 Security Best Practices

### 1. Token Storage
- ❌ **NEVER** store token di localStorage untuk production
- ✅ Store di httpOnly cookies atau secure storage
- ✅ Implement token refresh mechanism
- ✅ Handle token expiration gracefully

### 2. API Calls
- ✅ Always use HTTPS di production
- ✅ Validate SSL certificates
- ✅ Implement rate limiting
- ✅ Log failed authentication attempts

### 3. Multi-Tenant Isolation
- ✅ Always include `tenant_id` di URL
- ✅ Never trust client-side tenant selection
- ✅ Validate tenant access di server-side
- ✅ Use separate databases per tenant

---

## 💡 Tips & Best Practices

### 1. URL Pattern
```
✅ CORRECT: /{tenant_id}/api/v1/auth/login
❌ WRONG:   /api/v1/auth/login
```

### 2. Headers
Always include:
```
Content-Type: application/json
Accept: application/json
Authorization: Bearer {token}  // for protected routes
```

### 3. Device Names
Use descriptive device names untuk session management:
```
- "iPhone 12"
- "Web Browser"
- "Android App"
- "API Client"
```

### 4. Environment Variables
Di Postman atau development environment:
```
base_url: http://localhost:8000
tenant_id: TNT123456
token: 1|abc123...
```

---

## 🐛 Troubleshooting

### "Tenant not found"
**Solutions:**
- Pastikan `tenant_id` benar di URL
- Check database: `SELECT * FROM merchants WHERE tenant_id = 'XXX'`
- Verify tenant status is active

### "Unauthenticated"
**Solutions:**
- Check token included di Authorization header
- Verify token format: `Bearer {token}`
- Token mungkin sudah expired atau di-revoke
- Try refresh token atau login ulang

### "Email not verified"
**Solutions:**
- Check email untuk verification link
- Atau manual update database untuk testing
- Resend verification email

### "Validation failed"
**Solutions:**
- Check semua required fields ada
- Verify data types dan format
- Check unique constraints (email, name)

### Database connection error
**Solutions:**
- Verify tenant database exists
- Check database credentials
- Run migrations: `php artisan migrate:tenant {tenant_id}`

---

## 📖 Related Documentation

- `DEVELOPMENT.md` - Development guide dan implementation details
- `README.md` - Project overview dan setup instructions
- Postman Collection - `GutsyPOS_Tenant_Auth_API.postman_collection.json`

---

## 📞 Support

Untuk pertanyaan atau issue terkait API, silakan hubungi development team atau buat issue di repository.

---

**Last Updated:** November 2025  
**API Version:** v1  
**Laravel Version:** 11.x
