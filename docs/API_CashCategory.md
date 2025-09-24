# Cash Category API Documentation

## Base URL
```
/{tenant_id}/cash/categories
```

## Authentication
All endpoints require tenant authentication (`tenant.auth` middleware).

## Endpoints

### 1. List All Categories
```http
GET /{tenant_id}/cash/categories
```

**Query Parameters:**
- `type` (optional): Filter by category type (`income` or `expense`)
- `search` (optional): Search by category name
- `page` (optional): Page number for pagination

**Example:**
```bash
curl -X GET "/{tenant_id}/cash/categories?type=income&search=salary"
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

### 2. Create Category
```http
POST /{tenant_id}/cash/categories
```

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
- `type`: required, must be `income` or `expense`
- `description`: optional, string, max 1000 chars

**Response:**
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

### 3. Show Category
```http
GET /{tenant_id}/cash/categories/{id}
```

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

### 4. Update Category
```http
PUT /{tenant_id}/cash/categories/{id}
```

**Request Body:**
```json
{
    "name": "Updated Category Name",
    "type": "expense",
    "description": "Updated description"
}
```

**Response:**
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

### 5. Delete Category (Soft Delete)
```http
DELETE /{tenant_id}/cash/categories/{id}
```

**Response:**
```json
{
    "success": true,
    "message": "Cash category deleted successfully"
}
```

### 6. Get Categories by Type
```http
GET /{tenant_id}/cash/categories/type/{type}
```

**Parameters:**
- `type`: `income` or `expense`

**Example:**
```bash
curl -X GET "/{tenant_id}/cash/categories/type/income"
```

### 7. Get Trashed Categories
```http
GET /{tenant_id}/cash/categories/trashed/list
```

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

### 8. Restore Category
```http
POST /{tenant_id}/cash/categories/{id}/restore
```

**Response:**
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

### 9. Force Delete Category
```http
DELETE /{tenant_id}/cash/categories/{id}/force
```

**Response:**
```json
{
    "success": true,
    "message": "Cash category permanently deleted"
}
```

## Features

### ✅ Implemented Features:
1. **CRUD Operations** - Create, Read, Update, Delete
2. **Soft Deletes** - Categories can be soft deleted and restored
3. **Audit Trail** - Tracks who created/updated/deleted each category
4. **Validation** - Comprehensive validation with custom messages
5. **Filtering** - Filter by type and search by name
6. **Pagination** - Paginated results for large datasets
7. **Unique Constraints** - Category name must be unique per type
8. **Observer Pattern** - Automatic audit field population

### 🔧 Technical Features:
1. **Form Request Validation** - Centralized validation logic
2. **Model Relationships** - Audit relationships to users
3. **Scopes** - Query scopes for income/expense filtering
4. **JSON Responses** - Consistent API response format
5. **Error Handling** - Proper exception handling
6. **Tenant Isolation** - Data isolated per tenant

## Error Responses

### Validation Error (422):
```json
{
    "message": "The given data was invalid.",
    "errors": {
        "name": [
            "A category with this name already exists for the selected type"
        ],
        "type": [
            "Category type must be either income or expense"
        ]
    }
}
```

### Server Error (500):
```json
{
    "success": false,
    "message": "Failed to create cash category",
    "error": "Database connection failed"
}
```

### Not Found (404):
```json
{
    "message": "No query results for model [App\\Models\\Tenant\\CashCategory] {id}"
}
```
