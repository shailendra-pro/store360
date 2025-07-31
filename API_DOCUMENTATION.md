# Store 360 Business API Documentation

## Overview
The Store 360 Business API provides authentication and management endpoints for business accounts. All API responses follow a consistent JSON format.

**Base URL:** `http://127.0.0.1:8000/api/v1`

## Authentication
The API uses Laravel Sanctum for token-based authentication. Include the Bearer token in the Authorization header for protected endpoints.

```
Authorization: Bearer {your-token}
```

## Response Format
All API responses follow this structure:
```json
{
    "success": true|false,
    "message": "Response message",
    "data": {
        // Response data
    },
    "errors": {
        // Validation errors (if any)
    }
}
```

---

## Authentication Endpoints

### 1. Business Login
**POST** `/business/login`

Authenticate a business account and receive an access token.

**Request Body:**
```json
{
    "username": "business_username",
    "password": "business_password"
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
            "business_name": "Tech Solutions Inc.",
            "contact_email": "contact@techsolutions.com",
            "username": "techsolutions",
            "logo_url": "http://127.0.0.1:8000/storage/business-logos/logo.png",
            "is_active": true,
            "expires_at": "2025-12-31T23:59:59.000000Z",
            "business_description": "Leading technology solutions provider",
            "phone": "+1-555-0123",
            "address": "123 Tech Street",
            "city": "San Francisco",
            "state": "CA",
            "postal_code": "94105",
            "country": "USA"
        },
        "token": "1|abc123def456...",
        "token_type": "Bearer",
        "expires_in": 3600
    }
}
```

**Error Response (401):**
```json
{
    "success": false,
    "message": "Invalid credentials",
    "errors": {
        "username": ["Invalid username or password."]
    }
}
```

### 2. Business Logout
**POST** `/business/logout`

Logout and invalidate the current access token.

**Headers:**
```
Authorization: Bearer {token}
```

**Success Response (200):**
```json
{
    "success": true,
    "message": "Logged out successfully"
}
```

### 3. Get Profile
**GET** `/business/profile`

Get the authenticated business profile information.

**Headers:**
```
Authorization: Bearer {token}
```

**Success Response (200):**
```json
{
    "success": true,
    "data": {
        "user": {
            "id": 1,
            "business_name": "Tech Solutions Inc.",
            "contact_email": "contact@techsolutions.com",
            "username": "techsolutions",
            "logo_url": "http://127.0.0.1:8000/storage/business-logos/logo.png",
            "is_active": true,
            "expires_at": "2025-12-31T23:59:59.000000Z",
            "business_description": "Leading technology solutions provider",
            "phone": "+1-555-0123",
            "address": "123 Tech Street",
            "city": "San Francisco",
            "state": "CA",
            "postal_code": "94105",
            "country": "USA",
            "created_at": "2025-07-30T10:00:00.000000Z",
            "updated_at": "2025-07-30T10:00:00.000000Z"
        }
    }
}
```

### 4. Update Profile
**PUT** `/business/profile`

Update business profile information.

**Headers:**
```
Authorization: Bearer {token}
```

**Request Body:**
```json
{
    "business_name": "Updated Business Name",
    "contact_email": "newemail@business.com",
    "business_description": "Updated description",
    "phone": "+1-555-9999",
    "address": "456 New Street",
    "city": "New York",
    "state": "NY",
    "postal_code": "10001",
    "country": "USA"
}
```

**Success Response (200):**
```json
{
    "success": true,
    "message": "Profile updated successfully",
    "data": {
        "user": {
            // Updated user data
        }
    }
}
```

### 5. Change Password
**POST** `/business/change-password`

Change the business account password.

**Headers:**
```
Authorization: Bearer {token}
```

**Request Body:**
```json
{
    "current_password": "oldpassword",
    "new_password": "newpassword",
    "new_password_confirmation": "newpassword"
}
```

**Success Response (200):**
```json
{
    "success": true,
    "message": "Password changed successfully"
}
```

### 6. Refresh Token
**POST** `/business/refresh-token`

Generate a new access token and invalidate the current one.

**Headers:**
```
Authorization: Bearer {token}
```

**Success Response (200):**
```json
{
    "success": true,
    "message": "Token refreshed successfully",
    "data": {
        "token": "2|xyz789abc123...",
        "token_type": "Bearer",
        "expires_in": 3600
    }
}
```

### 7. Check Authentication
**POST** `/business/check-auth`

Check if the current token is valid and the account is active.

**Headers:**
```
Authorization: Bearer {token}
```

**Success Response (200):**
```json
{
    "success": true,
    "message": "Authenticated",
    "data": {
        "user": {
            "id": 1,
            "business_name": "Tech Solutions Inc.",
            "username": "techsolutions",
            "is_active": true,
            "expires_at": "2025-12-31T23:59:59.000000Z"
        }
    }
}
```

---

## Business Operations Endpoints

### 1. Dashboard
**GET** `/business/dashboard`

Get dashboard data including statistics and recent activity.

**Headers:**
```
Authorization: Bearer {token}
```

**Success Response (200):**
```json
{
    "success": true,
    "data": {
        "business": {
            // Business profile data
        },
        "statistics": {
            "total_users": 25,
            "active_users": 20,
            "users_with_secure_links": 15,
            "valid_secure_links": 12
        },
        "recent_users": [
            {
                "id": 1,
                "name": "John Smith",
                "email": "john@example.com",
                "company": "Tech Solutions Inc.",
                "created_at": "2025-07-30T10:00:00.000000Z"
            }
        ],
        "expiring_links": [
            {
                "id": 2,
                "name": "Sarah Johnson",
                "email": "sarah@example.com",
                "secure_link_expires_at": "2025-07-31T10:00:00.000000Z"
            }
        ]
    }
}
```

### 2. Statistics
**GET** `/business/statistics`

Get detailed business statistics.

**Headers:**
```
Authorization: Bearer {token}
```

**Success Response (200):**
```json
{
    "success": true,
    "data": {
        "total_users": 25,
        "active_users": 20,
        "inactive_users": 5,
        "users_with_secure_links": 15,
        "valid_secure_links": 12,
        "expired_secure_links": 3,
        "users_by_company": [
            {
                "company": "Tech Solutions Inc.",
                "count": 10
            },
            {
                "company": "Global Retail Corp.",
                "count": 8
            }
        ]
    }
}
```

### 3. Get Users
**GET** `/business/users`

Get paginated list of users with optional filtering.

**Headers:**
```
Authorization: Bearer {token}
```

**Query Parameters:**
- `page` (optional): Page number (default: 1)
- `per_page` (optional): Items per page (default: 15)
- `company` (optional): Filter by company name
- `status` (optional): Filter by status (active/inactive)
- `has_secure_link` (optional): Filter by secure link status (true/false)
- `search` (optional): Search in name, email, or company

**Success Response (200):**
```json
{
    "success": true,
    "data": {
        "users": [
            {
                "id": 1,
                "name": "John Smith",
                "email": "john@example.com",
                "company": "Tech Solutions Inc.",
                "role": "user",
                "is_active": true,
                "created_at": "2025-07-30T10:00:00.000000Z",
                "updated_at": "2025-07-30T10:00:00.000000Z"
            }
        ],
        "pagination": {
            "current_page": 1,
            "last_page": 2,
            "per_page": 15,
            "total": 25,
            "from": 1,
            "to": 15
        }
    }
}
```

### 4. Get User Details
**GET** `/business/users/{userId}`

Get detailed information about a specific user.

**Headers:**
```
Authorization: Bearer {token}
```

**Success Response (200):**
```json
{
    "success": true,
    "data": {
        "user": {
            "id": 1,
            "name": "John Smith",
            "email": "john@example.com",
            "company": "Tech Solutions Inc.",
            "role": "user",
            "is_active": true,
            "secure_link": true,
            "secure_link_expires_at": "2025-08-01T10:00:00.000000Z",
            "remaining_hours": 24,
            "custom_hours": 48,
            "created_at": "2025-07-30T10:00:00.000000Z",
            "updated_at": "2025-07-30T10:00:00.000000Z"
        }
    }
}
```

### 5. Get Companies
**GET** `/business/companies`

Get list of all companies.

**Headers:**
```
Authorization: Bearer {token}
```

**Success Response (200):**
```json
{
    "success": true,
    "data": {
        "companies": [
            "Tech Solutions Inc.",
            "Global Retail Corp.",
            "Creative Design Studio",
            "Green Energy Solutions"
        ]
    }
}
```

### 6. Get Expiring Links
**GET** `/business/expiring-links`

Get users with secure links expiring within specified hours.

**Headers:**
```
Authorization: Bearer {token}
```

**Query Parameters:**
- `hours` (optional): Hours threshold (default: 24)

**Success Response (200):**
```json
{
    "success": true,
    "data": {
        "expiring_users": [
            {
                "id": 2,
                "name": "Sarah Johnson",
                "email": "sarah@example.com",
                "company": "Global Retail Corp.",
                "secure_link_expires_at": "2025-07-31T10:00:00.000000Z",
                "remaining_hours": 12
            }
        ],
        "hours_threshold": 24
    }
}
```

### 7. Update Logo
**POST** `/business/update-logo`

Upload and update business logo.

**Headers:**
```
Authorization: Bearer {token}
Content-Type: multipart/form-data
```

**Request Body:**
```
logo: [image file] (jpeg, png, jpg, gif, max 2MB)
```

**Success Response (200):**
```json
{
    "success": true,
    "message": "Logo updated successfully",
    "data": {
        "logo_url": "http://127.0.0.1:8000/storage/business-logos/new-logo.png"
    }
}
```

---

## Error Codes

| Code | Description |
|------|-------------|
| 200 | Success |
| 400 | Bad Request |
| 401 | Unauthorized (Invalid or missing token) |
| 403 | Forbidden (Insufficient permissions or inactive account) |
| 404 | Not Found |
| 422 | Validation Error |
| 500 | Internal Server Error |

## Rate Limiting

API endpoints are subject to rate limiting. The default limit is 60 requests per minute per IP address.

## Testing the API

### Using cURL

**Login:**
```bash
curl -X POST http://127.0.0.1:8000/api/v1/business/login \
  -H "Content-Type: application/json" \
  -d '{
    "username": "techsolutions",
    "password": "password123"
  }'
```

**Get Profile (with token):**
```bash
curl -X GET http://127.0.0.1:8000/api/v1/business/profile \
  -H "Authorization: Bearer YOUR_TOKEN_HERE"
```

### Using Postman

1. Set the base URL to `http://127.0.0.1:8000/api/v1`
2. For protected endpoints, add the Authorization header:
   - Type: Bearer Token
   - Token: Your access token from login

### Sample Business Credentials

Use these credentials for testing:
- **Username:** `techsolutions`
- **Password:** `password123`

---

## Support

For API support or questions, please contact the development team. 
