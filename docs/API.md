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

#### 3. Get Tenant Info
Mendapatkan informasi tenant berdasarkan criteria tertentu.

**Endpoint:** `POST /api/v1/tenant/info`

**Request Body:**
```json
{
  "tenant_id": "TNT123456",
  // atau
  "slug": "abc-company",
  // atau
  "email": "admin@example.com"
}
```

**Success Response (200):**
```json
{
  "success": true,
  "data": {
    "id": 1,
    "name": "ABC Company",
    "slug": "abc-company",
    "tenant_id": "TNT123456",
    "email": "admin@example.com",
    "status": true
  }
}
```

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

### Account (Chart of Accounts) Endpoints

Base URL: `/{tenant_id}/v1/account`

#### 17. List All Accounts
Get daftar semua accounts dalam chart of accounts.

**Endpoint:** `GET /{tenant_id}/v1/account`

**Headers:**
```
Authorization: Bearer {your_token}
```

**Response:**
```json
{
  "success": true,
  "data": [
    {
      "id": 1,
      "code": "1-10100",
      "name": "Cash on Hand",
      "type": "asset",
      "category": "current_asset",
      "parent_id": null,
      "is_active": true,
      "balance": 10000000
    }
  ]
}
```

---

#### 18. Show Account
Get detail satu account.

**Endpoint:** `GET /{tenant_id}/v1/account/{id}`

**Headers:**
```
Authorization: Bearer {your_token}
```

**Response:**
```json
{
  "success": true,
  "data": {
    "id": 1,
    "code": "1-10100",
    "name": "Cash on Hand",
    "type": "asset",
    "category": "current_asset",
    "parent_id": null,
    "is_active": true,
    "balance": 10000000,
    "created_at": "2025-11-20T10:00:00.000000Z",
    "updated_at": "2025-11-20T10:00:00.000000Z"
  }
}
```

---

#### 19. Create Account
Membuat account baru.

**Endpoint:** `POST /{tenant_id}/v1/account`

**Headers:**
```
Authorization: Bearer {your_token}
```

**Request Body:**
```json
{
  "code": "1-10500",
  "name": "Petty Cash",
  "type": "asset",
  "category": "current_asset",
  "parent_id": null,
  "is_active": true
}
```

**Success Response (201):**
```json
{
  "success": true,
  "message": "Account created successfully",
  "data": {
    "id": 2,
    "code": "1-10500",
    "name": "Petty Cash",
    "type": "asset",
    "category": "current_asset"
  }
}
```

---

#### 20. Update Account
Update account yang ada.

**Endpoint:** `PUT /{tenant_id}/v1/account/{id}`

**Headers:**
```
Authorization: Bearer {your_token}
```

**Request Body:**
```json
{
  "name": "Updated Account Name",
  "is_active": true
}
```

**Success Response (200):**
```json
{
  "success": true,
  "message": "Account updated successfully",
  "data": {
    "id": 2,
    "name": "Updated Account Name"
  }
}
```

---

#### 21. Delete Account
Delete account.

**Endpoint:** `DELETE /{tenant_id}/v1/account/{id}`

**Headers:**
```
Authorization: Bearer {your_token}
```

**Success Response (200):**
```json
{
  "success": true,
  "message": "Account deleted successfully"
}
```

---

### Cash Flow Endpoints

Base URL: `/{tenant_id}/v1/cash-flow`

#### 22. List Cash Flows
Get daftar cash flows (in/out).

**Endpoint:** `GET /{tenant_id}/v1/cash-flow/{type}`

**Parameters:**
- `type`: `in` atau `out` (default: `in`)

**Headers:**
```
Authorization: Bearer {your_token}
```

**Response:**
```json
{
  "success": true,
  "data": [
    {
      "id": 1,
      "code": "CF-IN-001",
      "type": "in",
      "date": "2025-11-20",
      "account_id": 1,
      "account_name": "Cash on Hand",
      "category_id": 1,
      "category_name": "Sales",
      "amount": 1000000,
      "description": "Sales payment",
      "status": "posted"
    }
  ]
}
```

---

#### 23. Show Cash Flow
Get detail satu cash flow.

**Endpoint:** `GET /{tenant_id}/v1/cash-flow/show/{id}`

**Headers:**
```
Authorization: Bearer {your_token}
```

**Response:**
```json
{
  "success": true,
  "data": {
    "id": 1,
    "code": "CF-IN-001",
    "type": "in",
    "date": "2025-11-20",
    "account": {
      "id": 1,
      "name": "Cash on Hand"
    },
    "category": {
      "id": 1,
      "name": "Sales"
    },
    "amount": 1000000,
    "description": "Sales payment",
    "status": "posted",
    "posted_at": "2025-11-20T10:00:00.000000Z"
  }
}
```

---

#### 24. Create Cash Flow
Membuat cash flow baru (in/out).

**Endpoint:** `POST /{tenant_id}/v1/cash-flow`

**Headers:**
```
Authorization: Bearer {your_token}
```

**Request Body:**
```json
{
  "type": "in",
  "date": "2025-11-20",
  "account_id": 1,
  "category_id": 1,
  "amount": 1000000,
  "description": "Sales payment",
  "reference": "INV-001"
}
```

**Success Response (201):**
```json
{
  "success": true,
  "message": "Cash flow created successfully",
  "data": {
    "id": 1,
    "code": "CF-IN-001",
    "type": "in",
    "amount": 1000000,
    "status": "draft"
  }
}
```

---

#### 25. Update Cash Flow
Update cash flow yang ada.

**Endpoint:** `PUT /{tenant_id}/v1/cash-flow/{id}`

**Headers:**
```
Authorization: Bearer {your_token}
```

**Request Body:**
```json
{
  "amount": 1500000,
  "description": "Updated description"
}
```

**Success Response (200):**
```json
{
  "success": true,
  "message": "Cash flow updated successfully"
}
```

---

#### 26. Set Cash Flow to Draft
Mengubah status cash flow menjadi draft.

**Endpoint:** `PATCH /{tenant_id}/v1/cash-flow/{id}/set-draft`

**Headers:**
```
Authorization: Bearer {your_token}
```

**Success Response (200):**
```json
{
  "success": true,
  "message": "Cash flow set to draft"
}
```

---

#### 27. Set Cash Flow to Posted
Memposting cash flow (final).

**Endpoint:** `PATCH /{tenant_id}/v1/cash-flow/{id}/set-posted`

**Headers:**
```
Authorization: Bearer {your_token}
```

**Success Response (200):**
```json
{
  "success": true,
  "message": "Cash flow posted successfully"
}
```

---

#### 28. Delete Cash Flow
Delete cash flow.

**Endpoint:** `DELETE /{tenant_id}/v1/cash-flow/{id}`

**Headers:**
```
Authorization: Bearer {your_token}
```

**Success Response (200):**
```json
{
  "success": true,
  "message": "Cash flow deleted successfully"
}
```

---

### Cash Transfer Endpoints

Base URL: `/{tenant_id}/v1/cash-transfer`

#### 29. List Cash Transfers
Get daftar cash transfers.

**Endpoint:** `GET /{tenant_id}/v1/cash-transfer`

**Headers:**
```
Authorization: Bearer {your_token}
```

**Response:**
```json
{
  "success": true,
  "data": [
    {
      "id": 1,
      "code": "CT-001",
      "date": "2025-11-20",
      "from_account_id": 1,
      "from_account_name": "Cash on Hand",
      "to_account_id": 2,
      "to_account_name": "Bank Account",
      "amount": 5000000,
      "description": "Transfer to bank",
      "status": "posted"
    }
  ]
}
```

---

#### 30. Show Cash Transfer
Get detail satu cash transfer.

**Endpoint:** `GET /{tenant_id}/v1/cash-transfer/show/{id}`

**Headers:**
```
Authorization: Bearer {your_token}
```

**Response:**
```json
{
  "success": true,
  "data": {
    "id": 1,
    "code": "CT-001",
    "date": "2025-11-20",
    "from_account": {
      "id": 1,
      "name": "Cash on Hand"
    },
    "to_account": {
      "id": 2,
      "name": "Bank Account"
    },
    "amount": 5000000,
    "description": "Transfer to bank",
    "status": "posted",
    "posted_at": "2025-11-20T10:00:00.000000Z"
  }
}
```

---

#### 31. Create Cash Transfer
Membuat cash transfer baru.

**Endpoint:** `POST /{tenant_id}/v1/cash-transfer`

**Headers:**
```
Authorization: Bearer {your_token}
```

**Request Body:**
```json
{
  "date": "2025-11-20",
  "from_account_id": 1,
  "to_account_id": 2,
  "amount": 5000000,
  "description": "Transfer to bank"
}
```

**Success Response (201):**
```json
{
  "success": true,
  "message": "Cash transfer created successfully",
  "data": {
    "id": 1,
    "code": "CT-001",
    "amount": 5000000,
    "status": "draft"
  }
}
```

---

#### 32. Update Cash Transfer
Update cash transfer yang ada.

**Endpoint:** `PUT /{tenant_id}/v1/cash-transfer/{id}`

**Headers:**
```
Authorization: Bearer {your_token}
```

**Request Body:**
```json
{
  "amount": 6000000,
  "description": "Updated description"
}
```

**Success Response (200):**
```json
{
  "success": true,
  "message": "Cash transfer updated successfully"
}
```

---

#### 33. Set Cash Transfer to Draft
Mengubah status cash transfer menjadi draft.

**Endpoint:** `PATCH /{tenant_id}/v1/cash-transfer/{id}/set-draft`

**Headers:**
```
Authorization: Bearer {your_token}
```

**Success Response (200):**
```json
{
  "success": true,
  "message": "Cash transfer set to draft"
}
```

---

#### 34. Set Cash Transfer to Posted
Memposting cash transfer (final).

**Endpoint:** `PATCH /{tenant_id}/v1/cash-transfer/{id}/set-posted`

**Headers:**
```
Authorization: Bearer {your_token}
```

**Success Response (200):**
```json
{
  "success": true,
  "message": "Cash transfer posted successfully"
}
```

---

#### 35. Delete Cash Transfer
Delete cash transfer.

**Endpoint:** `DELETE /{tenant_id}/v1/cash-transfer/{id}`

**Headers:**
```
Authorization: Bearer {your_token}
```

**Success Response (200):**
```json
{
  "success": true,
  "message": "Cash transfer deleted successfully"
}
```

---

### Accounting Report Endpoints

Base URL: `/{tenant_id}/v1/report/accounting`

#### 36. Profit & Loss Report
Mendapatkan laporan laba rugi.

**Endpoint:** `GET /{tenant_id}/v1/report/accounting/profit-loss`

**Headers:**
```
Authorization: Bearer {your_token}
```

**Query Parameters:**
- `start_date`: YYYY-MM-DD (required)
- `end_date`: YYYY-MM-DD (required)

**Example:**
```
GET /{tenant_id}/v1/report/accounting/profit-loss?start_date=2025-01-01&end_date=2025-12-31
```

**Response:**
```json
{
  "success": true,
  "data": {
    "period": {
      "start_date": "2025-01-01",
      "end_date": "2025-12-31"
    },
    "revenue": {
      "total": 50000000,
      "details": [
        {
          "account_code": "4-10100",
          "account_name": "Sales Revenue",
          "amount": 50000000
        }
      ]
    },
    "expenses": {
      "total": 30000000,
      "details": [
        {
          "account_code": "5-10100",
          "account_name": "Operating Expenses",
          "amount": 30000000
        }
      ]
    },
    "net_income": 20000000
  }
}
```

---

#### 37. Bank Statement Report
Mendapatkan laporan mutasi bank/kas.

**Endpoint:** `GET /{tenant_id}/v1/report/accounting/bank-statement`

**Headers:**
```
Authorization: Bearer {your_token}
```

**Query Parameters:**
- `account_id`: ID akun (required)
- `start_date`: YYYY-MM-DD (required)
- `end_date`: YYYY-MM-DD (required)

**Example:**
```
GET /{tenant_id}/v1/report/accounting/bank-statement?account_id=1&start_date=2025-01-01&end_date=2025-12-31
```

**Response:**
```json
{
  "success": true,
  "data": {
    "account": {
      "id": 1,
      "code": "1-10100",
      "name": "Cash on Hand"
    },
    "period": {
      "start_date": "2025-01-01",
      "end_date": "2025-12-31"
    },
    "opening_balance": 10000000,
    "transactions": [
      {
        "date": "2025-01-15",
        "description": "Sales payment",
        "reference": "CF-IN-001",
        "debit": 5000000,
        "credit": 0,
        "balance": 15000000
      }
    ],
    "closing_balance": 15000000
  }
}
```

---

#### 38. Cash Flow Summary Report
Mendapatkan ringkasan arus kas.

**Endpoint:** `GET /{tenant_id}/v1/report/accounting/cash-flow-summary`

**Headers:**
```
Authorization: Bearer {your_token}
```

**Query Parameters:**
- `start_date`: YYYY-MM-DD (required)
- `end_date`: YYYY-MM-DD (required)

**Example:**
```
GET /{tenant_id}/v1/report/accounting/cash-flow-summary?start_date=2025-01-01&end_date=2025-12-31
```

**Response:**
```json
{
  "success": true,
  "data": {
    "period": {
      "start_date": "2025-01-01",
      "end_date": "2025-12-31"
    },
    "cash_in": {
      "total": 50000000,
      "by_category": [
        {
          "category_name": "Sales",
          "amount": 50000000
        }
      ]
    },
    "cash_out": {
      "total": 30000000,
      "by_category": [
        {
          "category_name": "Operating Expenses",
          "amount": 30000000
        }
      ]
    },
    "net_cash_flow": 20000000
  }
}
```

---

#### 39. Cash Flow Detail Report
Mendapatkan detail arus kas per transaksi.

**Endpoint:** `GET /{tenant_id}/v1/report/accounting/cash-flow-detail`

**Headers:**
```
Authorization: Bearer {your_token}
```

**Query Parameters:**
- `start_date`: YYYY-MM-DD (required)
- `end_date`: YYYY-MM-DD (required)
- `type`: `in` atau `out` (optional)

**Example:**
```
GET /{tenant_id}/v1/report/accounting/cash-flow-detail?start_date=2025-01-01&end_date=2025-12-31&type=in
```

**Response:**
```json
{
  "success": true,
  "data": {
    "period": {
      "start_date": "2025-01-01",
      "end_date": "2025-12-31"
    },
    "transactions": [
      {
        "date": "2025-01-15",
        "code": "CF-IN-001",
        "type": "in",
        "account_name": "Cash on Hand",
        "category_name": "Sales",
        "amount": 5000000,
        "description": "Sales payment",
        "status": "posted"
      }
    ],
    "total": 5000000
  }
}
```

---

### Dashboard & Profile Endpoints

#### 40. Get Dashboard Data
Mendapatkan data untuk dashboard.

**Endpoint:** `GET /{tenant_id}/v1/dashboard`

**Headers:**
```
Authorization: Bearer {your_token}
```

**Response:**
```json
{
  "success": true,
  "data": {
    "summary": {
      "total_cash_in": 50000000,
      "total_cash_out": 30000000,
      "net_cash_flow": 20000000
    },
    "recent_transactions": [
      {
        "date": "2025-11-20",
        "description": "Sales payment",
        "amount": 1000000,
        "type": "in"
      }
    ]
  }
}
```

---

#### 41. Get User Profile
Mendapatkan profile user yang sedang login.

**Endpoint:** `GET /{tenant_id}/v1/profile`

**Headers:**
```
Authorization: Bearer {your_token}
```

**Response:**
```json
{
  "success": true,
  "data": {
    "id": 1,
    "name": "John Doe",
    "email": "admin@example.com",
    "email_verified_at": "2025-11-03T10:00:00.000000Z"
  }
}
```

---

#### 42. Update User Profile
Update profile user.

**Endpoint:** `PUT /{tenant_id}/v1/profile`

**Headers:**
```
Authorization: Bearer {your_token}
```

**Request Body:**
```json
{
  "name": "John Updated",
  "email": "john.updated@example.com"
}
```

**Success Response (200):**
```json
{
  "success": true,
  "message": "Profile updated successfully",
  "data": {
    "id": 1,
    "name": "John Updated",
    "email": "john.updated@example.com"
  }
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
├── Cash Categories
│   ├── List Categories
│   ├── Create Category
│   ├── Show Category
│   ├── Update Category
│   └── Delete Category
├── Accounts
│   ├── List Accounts
│   ├── Create Account
│   ├── Show Account
│   ├── Update Account
│   └── Delete Account
├── Cash Flow
│   ├── List Cash Flows
│   ├── Create Cash Flow
│   ├── Show Cash Flow
│   ├── Update Cash Flow
│   ├── Set to Draft
│   ├── Set to Posted
│   └── Delete Cash Flow
├── Cash Transfer
│   ├── List Cash Transfers
│   ├── Create Cash Transfer
│   ├── Show Cash Transfer
│   ├── Update Cash Transfer
│   ├── Set to Draft
│   ├── Set to Posted
│   └── Delete Cash Transfer
├── Reports
│   ├── Profit & Loss
│   ├── Bank Statement
│   ├── Cash Flow Summary
│   └── Cash Flow Detail
└── Dashboard & Profile
    ├── Get Dashboard
    ├── Get Profile
    └── Update Profile
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

## 📊 API Response Format

**Note:** Semua response keys menggunakan camelCase format (bukan snake_case).

Contoh:
```json
{
  "success": true,
  "data": {
    "userId": 1,
    "userName": "John Doe",
    "emailVerifiedAt": "2025-11-03T10:00:00.000000Z",
    "createdAt": "2025-11-01T10:00:00.000000Z"
  }
}
```

---

**Last Updated:** November 20, 2025  
**API Version:** v1  
**Laravel Version:** 11.x
