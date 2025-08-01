# WebGL Application API Documentation

## Overview
This document provides the API endpoints for the WebGL 360° Virtual Store application. The frontend developer should use these endpoints to implement the splash screen, login system, and product catalog functionality.

## Base URL
```
http://127.0.0.1:8000/api/v1
```

## Authentication Flow

### 1. Validate Secure Link
**Endpoint:** `POST /webgl/validate-link`

**Purpose:** Validate the secure link and get store information for splash screen.

**Request Body:**
```json
{
    "secure_link": "abc123def456..."
}
```

**Response (Success - 200):**
```json
{
    "success": true,
    "message": "Secure link is valid",
    "data": {
        "user": {
            "id": 1,
            "name": "John Doe",
            "email": "john@example.com",
            "company": "Tech Solutions Inc.",
            "expires_at": "2025-08-02T10:00:00.000000Z",
            "remaining_hours": 24
        },
        "company": {
            "id": 2,
            "business_name": "Tech Solutions Inc.",
            "logo_url": "http://127.0.0.1:8000/storage/business-logos/logo.png",
            "contact_email": "contact@techsolutions.com"
        },
        "store_config": {
            "store_name": "Tech Solutions Inc.",
            "logo_url": "http://127.0.0.1:8000/storage/business-logos/logo.png",
            "theme_color": "#0d6efd",
            "splash_duration": 3000
        }
    }
}
```

**Response (Error - 401):**
```json
{
    "success": false,
    "message": "Secure link has expired",
    "expired_at": "2025-07-31T10:00:00.000000Z"
}
```

### 2. User Login
**Endpoint:** `POST /webgl/login`

**Purpose:** Authenticate user and create session for WebGL application. Supports both business users and end users.

**Request Body:**
```json
{
    "secure_link": "abc123def456...",
    "email": "john@example.com",
    "password": "webgl123"
}
```

**Note:** 
- **End Users:** Use email associated with secure link and password `webgl123`
- **Business Users:** Use business contact email and actual business password

**Response (Success - 200):**
```json
{
    "success": true,
    "message": "Login successful",
    "data": {
        "session_token": "xyz789abc123...",
        "user_type": "end_user",
        "user": {
            "id": 1,
            "name": "John Doe",
            "email": "john@example.com",
            "company": "Tech Solutions Inc."
        },
        "company": {
            "id": 2,
            "business_name": "Tech Solutions Inc.",
            "logo_url": "http://127.0.0.1:8000/storage/business-logos/logo.png",
            "contact_email": "contact@techsolutions.com"
        },
        "store_config": {
            "store_name": "Tech Solutions Inc.",
            "logo_url": "http://127.0.0.1:8000/storage/business-logos/logo.png",
            "theme_color": "#0d6efd",
            "splash_duration": 3000
        },
        "session_expires_at": "2025-08-02T10:00:00.000000Z",
        "access_level": "user"
    }
}
```

**Business User Response:**
```json
{
    "success": true,
    "message": "Login successful",
    "data": {
        "session_token": "xyz789abc123...",
        "user_type": "business",
        "user": {
            "id": 2,
            "name": "Tech Solutions Inc.",
            "email": "contact@techsolutions.com",
            "company": "Tech Solutions Inc."
        },
        "company": {
            "id": 2,
            "business_name": "Tech Solutions Inc.",
            "logo_url": "http://127.0.0.1:8000/storage/business-logos/logo.png",
            "contact_email": "contact@techsolutions.com"
        },
        "store_config": {
            "store_name": "Tech Solutions Inc.",
            "logo_url": "http://127.0.0.1:8000/storage/business-logos/logo.png",
            "theme_color": "#0d6efd",
            "splash_duration": 3000
        },
        "session_expires_at": "2025-08-02T10:00:00.000000Z",
        "access_level": "admin"
    }
}
```

**Response (Error - 401):**
```json
{
    "success": false,
    "message": "Invalid credentials"
}
```

### 3. Validate Session
**Endpoint:** `POST /webgl/validate-session`

**Purpose:** Check if the current session is still valid.

**Request Body:**
```json
{
    "session_token": "xyz789abc123..."
}
```

**Response (Success - 200):**
```json
{
    "success": true,
    "message": "Session is valid",
    "data": {
        "user": {
            "id": 1,
            "name": "John Doe",
            "email": "john@example.com",
            "company": "Tech Solutions Inc."
        },
        "company": {
            "id": 2,
            "business_name": "Tech Solutions Inc.",
            "logo_url": "http://127.0.0.1:8000/storage/business-logos/logo.png"
        },
        "session_expires_at": "2025-08-02T10:00:00.000000Z"
    }
}
```

### 4. Logout
**Endpoint:** `POST /webgl/logout`

**Purpose:** Invalidate the current session.

**Request Body:**
```json
{
    "session_token": "xyz789abc123..."
}
```

**Response (Success - 200):**
```json
{
    "success": true,
    "message": "Logged out successfully"
}
```

## Product Data

### 5. Get Products
**Endpoint:** `GET /webgl/products`

**Purpose:** Retrieve all products for the company (including global products).

**Headers:**
```
Content-Type: application/json
```

**Query Parameters:**
- `session_token` (required): The session token from login

**Response (Success - 200):**
```json
{
    "success": true,
    "message": "Products retrieved successfully",
    "data": {
        "products": [
            {
                "id": 1,
                "title": "Gaming Laptop",
                "description": "High-performance gaming laptop with RTX graphics",
                "price": 1299.99,
                "formatted_price": "$1,299.99",
                "sku": "LAPTOP-001",
                "stock_quantity": 15,
                "stock_status": "In Stock",
                "category": {
                    "id": 1,
                    "name": "Electronics"
                },
                "subcategory": {
                    "id": 1,
                    "name": "Laptops"
                },
                "images": [
                    {
                        "id": 1,
                        "url": "http://127.0.0.1:8000/storage/products/laptop1.jpg",
                        "thumbnail_url": "http://127.0.0.1:8000/storage/products/thumbnails/laptop1.jpg",
                        "is_primary": true,
                        "alt_text": "Gaming Laptop Front View"
                    }
                ],
                "main_image": "http://127.0.0.1:8000/storage/products/laptop1.jpg",
                "specifications": {
                    "processor": "Intel i7-12700H",
                    "ram": "16GB DDR4",
                    "storage": "512GB SSD"
                },
                "is_global": false,
                "created_at": "2025-07-31T10:00:00.000000Z"
            }
        ],
        "total_count": 1,
        "company_name": "Tech Solutions Inc."
    }
}
```

### 6. Get Categories
**Endpoint:** `GET /webgl/categories`

**Purpose:** Retrieve all categories and subcategories for product organization.

**Headers:**
```
Content-Type: application/json
```

**Query Parameters:**
- `session_token` (required): The session token from login

**Response (Success - 200):**
```json
{
    "success": true,
    "message": "Categories retrieved successfully",
    "data": {
        "categories": [
            {
                "id": 1,
                "name": "Electronics",
                "slug": "electronics",
                "subcategories": [
                    {
                        "id": 1,
                        "name": "Laptops",
                        "slug": "laptops",
                        "description": "Portable computers and laptops"
                    },
                    {
                        "id": 2,
                        "name": "Smartphones",
                        "slug": "smartphones",
                        "description": "Mobile phones and accessories"
                    }
                ]
            }
        ],
        "total_count": 1
    }
}
```

## Implementation Guide

### Splash Screen Implementation
1. **Extract secure link** from the URL (e.g., `?link=abc123def456`)
2. **Call `/webgl/validate-link`** with the secure link
3. **Display company logo** from `data.company.logo_url`
4. **Show loading animation** for `data.store_config.splash_duration` milliseconds
5. **Transition to login screen** after splash duration

### Login Screen Implementation
1. **Pre-fill email** from the validation response
2. **Use default password** `webgl123` (or implement password input)
3. **Call `/webgl/login`** with secure link, email, and password
4. **Store session token** for subsequent API calls
5. **Transition to 360° store** on successful login

### Session Management
1. **Store session token** in local storage or memory
2. **Include session token** in all subsequent API calls
3. **Call `/webgl/validate-session`** periodically to check session validity
4. **Redirect to login** if session expires

### Product Display
1. **Call `/webgl/products`** to get all products
2. **Call `/webgl/categories`** to get category structure
3. **Display products** in 3D space with images and details
4. **Implement category filtering** using the category data

## Error Handling

### Common Error Responses
- **400 Bad Request:** Invalid request parameters
- **401 Unauthorized:** Invalid credentials or expired session
- **403 Forbidden:** User account inactive
- **404 Not Found:** Resource not found
- **500 Internal Server Error:** Server error

### Error Response Format
```json
{
    "success": false,
    "message": "Error description",
    "errors": {
        "field_name": ["Error message"]
    }
}
```

## Testing

### Test Credentials

#### End User Testing:
- **Secure Link:** Use any valid secure link from the admin panel
- **Email:** Use the email associated with the secure link
- **Password:** `webgl123` (default WebGL password)

#### Business User Testing:
- **Secure Link:** Use business user's secure link
- **Email:** Use business contact email
- **Password:** Use actual business password (not `webgl123`)

### Test Flow

#### End User Flow:
1. Create a user in admin panel with secure link
2. Use the secure link in WebGL application
3. Login with user's email and password `webgl123`
4. Test product and category retrieval

#### Business User Flow:
1. Use business user's secure link in WebGL application
2. Login with business contact email and actual password
3. Test product and category retrieval with admin access

## Notes for Frontend Developer

1. **Session Token:** Always include the session token in API calls after login
2. **Image URLs:** All image URLs are absolute and ready to use
3. **Error Messages:** Display user-friendly error messages from the API
4. **Loading States:** Implement loading states for all API calls
5. **Offline Handling:** Consider caching product data for offline access
6. **Responsive Design:** Ensure the WebGL application works on different screen sizes

## Security Considerations

1. **HTTPS:** Use HTTPS in production for secure communication
2. **Token Storage:** Store session tokens securely
3. **Token Expiry:** Handle token expiration gracefully
4. **Input Validation:** Validate all user inputs on frontend
5. **Error Logging:** Log errors for debugging without exposing sensitive data 
