# ArusKAS Cloud - Documentation

![ArusKAS](https://img.shields.io/badge/ArusKAS-Cloud-blue)
![Laravel](https://img.shields.io/badge/Laravel-11.x-red)
![PHP](https://img.shields.io/badge/PHP-8.2+-purple)
![Multi-Tenant](https://img.shields.io/badge/Architecture-Multi--Tenant-green)

## 📖 Documentation Overview

Dokumentasi ArusKAS Cloud dirancang untuk membantu developer memahami dan menggunakan sistem dengan efektif.

### 📚 Available Documentation

#### 1. [API.md](./API.md) - API Reference
**Untuk:** Frontend Developers, API Consumers

Dokumentasi lengkap untuk semua API endpoints:
- 🔐 Authentication & Authorization
- 👤 Tenant Registration
- 💰 Cash Category Management
- 🧪 Testing Examples dengan cURL
- 📮 Postman Collection Guide
- ❌ Error Codes & Troubleshooting

**Quick Links:**
- [Authentication Endpoints](./API.md#tenant-authentication-endpoints)
- [Cash Category API](./API.md#cash-category-endpoints)
- [Testing Guide](./API.md#testing-guide)
- [Error Codes](./API.md#error-codes)

---

#### 2. [DEVELOPMENT.md](./DEVELOPMENT.md) - Development Guide
**Untuk:** Backend Developers, System Architects

Panduan development mendalam tentang:
- 🏗️ Multi-Tenant Architecture
- 🔧 Setup & Configuration
- 💾 Database Structure
- 🔄 Registration & Authentication Flow
- 🌱 Auto-Seeding Implementation
- 🛣️ Routing Configuration
- 🐛 Troubleshooting Guide
- 🔨 Development Workflow

**Quick Links:**
- [Architecture Overview](./DEVELOPMENT.md#architecture-overview)
- [Multi-Tenant Implementation](./DEVELOPMENT.md#multi-tenant-implementation)
- [Setup Guide](./DEVELOPMENT.md#setup--configuration)
- [Troubleshooting](./DEVELOPMENT.md#troubleshooting)

---

#### 3. [ArusKAS_Tenant_Auth_API.postman_collection.json](./ArusKAS_Tenant_Auth_API.postman_collection.json)
**Untuk:** API Testing, Integration Testing

Postman collection dengan:
- ✅ Pre-configured API requests
- 🔄 Auto-save tokens & tenant_id
- 🧪 Test scripts
- 📝 Request examples

**How to Use:**
1. Import file ke Postman
2. Create environment dengan variables:
   - `base_url`: `http://localhost:8000`
   - `tenant_id`: (auto-filled setelah register)
   - `token`: (auto-filled setelah login)
3. Run requests dari collection

---

## 🚀 Quick Start

### For API Consumers

1. **Read API Documentation**
   ```bash
   Start with: docs/API.md
   ```

2. **Import Postman Collection**
   ```bash
   File: docs/ArusKAS_Tenant_Auth_API.postman_collection.json
   ```

3. **Test Registration & Login**
   ```bash
   # Register tenant
   POST /api/v1/tenant/register
   
   # Login
   POST /{tenant_id}/api/v1/auth/login
   ```

4. **Start Building**
   Use the token untuk access protected endpoints

---

### For Backend Developers

1. **Setup Development Environment**
   ```bash
   # Follow setup guide
   See: docs/DEVELOPMENT.md#setup--configuration
   ```

2. **Understand Architecture**
   ```bash
   # Read architecture overview
   See: docs/DEVELOPMENT.md#architecture-overview
   ```

3. **Run Migrations & Seeds**
   ```bash
   php artisan migrate
   php artisan db:seed --class=SubscriptionPlanSeeder
   ```

4. **Start Development**
   ```bash
   php artisan serve
   ```

---

## 📂 Project Structure

```
aruskas-cloud/
├── app/
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── Api/
│   │   │   │   └── Global/
│   │   │   │       └── RegistrationController.php
│   │   │   └── Tenant/
│   │   │       ├── TenantAuthApiController.php
│   │   │       └── CashCategoryController.php
│   │   ├── Middleware/
│   │   └── Requests/
│   ├── Models/
│   │   ├── Global/
│   │   │   ├── Merchant.php
│   │   │   ├── MerchantUser.php
│   │   │   └── MerchantSubscription.php
│   │   └── Tenant/
│   │       ├── User.php
│   │       ├── Account.php
│   │       └── CashCategory.php
│   ├── Services/
│   │   └── TenantService.php
│   └── Traits/
│       └── BelongsToTenant.php
├── database/
│   ├── migrations/
│   │   ├── global/
│   │   └── tenant/
│   └── seeders/
│       ├── SubscriptionPlanSeeder.php
│       └── Tenant/
│           └── TenantSeeder.php
├── routes/
│   ├── api.php
│   └── web.php
└── docs/
    ├── README.md (this file)
    ├── API.md
    ├── DEVELOPMENT.md
    └── ArusKAS_Tenant_Auth_API.postman_collection.json
```

---

## 🔑 Key Concepts

### Multi-Tenant Architecture

ArusKAS menggunakan **database-per-tenant** approach:

```
Global Database          Tenant Databases
     (1)                    (Many)
      │
      ├─── tenant_TNT123456
      ├─── tenant_TNT789012
      ├─── tenant_TNT345678
      └─── ...
```

**Benefits:**
- ✅ Complete data isolation
- ✅ Independent backups & restore
- ✅ Scalability per tenant
- ✅ Customizable per tenant

### URL Pattern

```
# Global endpoints (no tenant required)
/api/v1/tenant/register
/api/v1/tenant/plans

# Tenant endpoints (tenant_id required)
/{tenant_id}/api/v1/auth/login
/{tenant_id}/api/v1/auth/me
/{tenant_id}/cash/categories
```

### Authentication Flow

```
Register → Verify Email → Login → Get Token → Use Token for API Calls
```

---

## 🛠️ Common Tasks

### Create New Tenant

```bash
curl -X POST http://localhost:8000/api/v1/tenant/register \
  -H "Content-Type: application/json" \
  -d '{
    "company_name": "My Company",
    "admin_name": "Admin Name",
    "admin_email": "admin@example.com",
    "password": "password123",
    "password_confirmation": "password123",
    "terms": true
  }'
```

### Login to Tenant

```bash
curl -X POST http://localhost:8000/TNT123456/api/v1/auth/login \
  -H "Content-Type: application/json" \
  -d '{
    "email": "admin@example.com",
    "password": "password123"
  }'
```

### Access Protected Endpoint

```bash
curl -X GET http://localhost:8000/TNT123456/api/v1/auth/me \
  -H "Authorization: Bearer YOUR_TOKEN"
```

---

## 🧪 Testing

### Manual Testing with cURL

See complete examples in: [API.md - Testing Guide](./API.md#testing-guide)

### Automated Testing with Postman

1. Import: `ArusKAS_Tenant_Auth_API.postman_collection.json`
2. Setup environment variables
3. Run collection

### Unit Testing

```bash
php artisan test
```

---

## 🐛 Troubleshooting

### Common Issues

| Issue | Solution | Details |
|-------|----------|---------|
| Tenant not found | Check tenant_id di URL | [Guide](./DEVELOPMENT.md#issue-tenant-not-found) |
| Email not verified | Manual verify atau klik link | [Guide](./DEVELOPMENT.md#issue-email-not-verified) |
| Route not found | Check URL pattern | [Guide](./DEVELOPMENT.md#issue-route-not-found) |
| Database error | Check connection & migrations | [Guide](./DEVELOPMENT.md#issue-database-connection-error) |

**Full troubleshooting guide:** [DEVELOPMENT.md - Troubleshooting](./DEVELOPMENT.md#troubleshooting)

---

## 📞 Support & Contributing

### Getting Help

1. Check documentation di folder `docs/`
2. Search existing issues di repository
3. Contact development team

### Contributing

1. Fork repository
2. Create feature branch
3. Make changes
4. Submit pull request

---

## 🔐 Security

### Best Practices

- ✅ Always use HTTPS di production
- ✅ Store tokens securely (httpOnly cookies)
- ✅ Implement rate limiting
- ✅ Regular security audits
- ✅ Keep dependencies updated

### Reporting Security Issues

Email: security@gutsylab.com

---

## 📋 Feature Status

### ✅ Implemented

- [x] Multi-tenant architecture
- [x] Tenant registration
- [x] Email verification
- [x] Sanctum authentication
- [x] Token management
- [x] Cash category CRUD
- [x] Auto-seeding
- [x] Audit trail

### 🚧 In Progress

- [ ] Password reset
- [ ] Two-factor authentication
- [ ] Advanced reporting

### 📝 Planned

- [ ] Mobile app API
- [ ] Webhook support
- [ ] API versioning
- [ ] GraphQL support

---

## 🔄 Version History

### Current Version: 1.0.0

**Features:**
- Multi-tenant support
- RESTful API
- Sanctum authentication
- Email verification
- Cash category management

**Tech Stack:**
- Laravel 11.x
- PHP 8.2+
- MySQL 8.0+
- Laravel Sanctum

---

## 📚 Additional Resources

### External Links

- [Laravel Documentation](https://laravel.com/docs)
- [Laravel Sanctum](https://laravel.com/docs/sanctum)
- [RESTful API Best Practices](https://restfulapi.net/)
- [Postman Learning Center](https://learning.postman.com/)

### Internal Documentation

- Architecture diagrams: Coming soon
- Database schema: [DEVELOPMENT.md](./DEVELOPMENT.md#database-structure)
- API changelog: Coming soon

---

## 📄 License

Proprietary - GutsyLab © 2025

---

## 👥 Team

Developed with ❤️ by GutsyLab Team

---

## 📝 Document Updates

- **Last Updated:** November 2025
- **Documentation Version:** 1.0.0
- **Maintained By:** Development Team

---

## 🎯 Quick Reference

| Need | Go To |
|------|-------|
| API Endpoints | [API.md](./API.md) |
| Setup Guide | [DEVELOPMENT.md](./DEVELOPMENT.md#setup--configuration) |
| Architecture | [DEVELOPMENT.md](./DEVELOPMENT.md#architecture-overview) |
| Testing | [API.md](./API.md#testing-guide) |
| Troubleshooting | [DEVELOPMENT.md](./DEVELOPMENT.md#troubleshooting) |
| Postman | [Postman Collection](./ArusKAS_Tenant_Auth_API.postman_collection.json) |

---

**Happy Coding! 🚀**
