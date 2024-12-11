# Truck App API Documentation

## Overview
API Documentation for Truck App Backend

- **Version:** 1.0.0
- **Contact:** dabeer445@gmail.com
- **Server:** http://localhost:8000

## Authentication
All protected endpoints require Bearer token authentication:
```
Authorization: Bearer <token>
```

## API Endpoints

### Authentication

#### Register a new user
```http
POST /api/v1/auth/register
```
Creates a new user account and returns access token.

**Request Body:**
```json
{
  "name": "John Doe",
  "email": "john@example.com",
  "password": "password123",
  "phone": "+1234567890"
}
```

**Responses:**
- `201`: User registered successfully
- `422`: Validation error

#### Login
```http
POST /api/v1/auth/login
```

**Request Body:**
```json
{
  "email": "john@example.com",
  "password": "password123"
}
```

**Responses:**
- `200`: Login successful
- `401`: Invalid credentials
- `422`: Validation error

#### Logout
```http
POST /api/v1/auth/logout
```

**Responses:**
- `200`: Logout successful
- `401`: Unauthenticated

### Orders

#### List Orders
```http
GET /api/v1/orders
```
Retrieve a paginated list of orders.

**Responses:**
- `200`: List of orders
- `401`: Unauthorized

#### Create Order
```http
POST /api/v1/orders
```

**Request Body:**
```json
{
  "pickup_location": "123 Pickup St",
  "delivery_location": "456 Delivery Ave",
  "cargo_details": {
    "weight": 100,
    "dimensions": {
      "length": 10,
      "width": 10,
      "height": 10
    }
  },
  "pickup_time": "2023-10-01T10:00:00Z",
  "delivery_time": "2023-10-02T10:00:00Z"
}
```

**Responses:**
- `201`: Order created successfully
- `422`: Validation error

#### Get Order Details
```http
GET /api/v1/orders/{id}
```

**Responses:**
- `200`: Order details
- `404`: Order not found

#### Cancel Order
```http
DELETE /api/v1/orders/{id}/cancel
```

**Responses:**
- `200`: Order cancelled successfully
- `422`: Only pending orders can be cancelled
- `404`: Order not found

### Messages

#### Get Messages
```http
GET /messages
```

**Query Parameters:**
- `order_id`: Filter messages by order ID (optional)

**Responses:**
- `200`: List of messages

#### Send Message
```http
POST /messages
```

**Request Body:**
```json
{
  "order_id": 1,
  "receiver_id": 1,
  "content": "Message content"
}
```

**Responses:**
- `201`: Message sent successfully
- `422`: Validation error

#### Mark Message as Read
```http
PUT /messages/{id}/read
```

**Responses:**
- `200`: Message marked as read
- `404`: Message not found

#### Get Unread Count
```http
GET /messages/unread-count
```

**Responses:**
- `200`: Count of unread messages

### Notifications

#### List Notifications
```http
GET /api/notifications
```

**Query Parameters:**
- `read`: Filter by read/unread status (boolean)
- `type`: Filter by notification type
- `from`: Filter from date (datetime)
- `to`: Filter to date (datetime)

**Responses:**
- `200`: List of notifications

#### Mark Notification as Read
```http
POST /api/notifications/{id}/read
```

**Responses:**
- `200`: Notification marked as read
- `404`: Notification not found

#### Mark All as Read
```http
POST /api/notifications/read
```

**Responses:**
- `200`: All notifications marked as read

#### Delete Notification
```http
DELETE /api/notifications/{id}
```

**Responses:**
- `200`: Notification deleted
- `404`: Notification not found

### User Profile

#### Get Profile
```http
GET /api/v1/user/profile
```

**Responses:**
- `200`: User profile information
- `401`: Unauthenticated

#### Update Profile
```http
PUT /api/v1/user/profile
```

**Request Body:**
```json
{
  "name": "John Doe",
  "email": "john@example.com",
  "phone": "+1234567890"
}
```

**Responses:**
- `200`: Profile updated successfully
- `422`: Validation error

### Admin Endpoints

#### List All Orders (Admin)
```http
GET /api/v1/admin/orders
```

**Query Parameters:**
- `status`: Filter by status (pending/in_progress/completed/cancelled)
- `from`: Filter from date (Y-m-d)
- `to`: Filter to date (Y-m-d)
- `per_page`: Items per page (default: 10)

**Responses:**
- `200`: List of all orders
- `401`: Unauthenticated
- `403`: Unauthorized access

#### Update Order Status (Admin)
```http
PUT /api/v1/admin/orders/{order}/status
```

**Request Body:**
```json
{
  "status": "in_progress"
}
```

**Responses:**
- `200`: Status updated successfully
- `404`: Order not found
- `422`: Validation error

## Models

### Order
```json
{
  "id": 1,
  "user_id": 1,
  "pickup_location": "123 Pickup St",
  "delivery_location": "456 Delivery Ave",
  "cargo_details": {
    "weight": "50kg",
    "dimensions": "100x50x75cm"
  },
  "pickup_time": "datetime",
  "delivery_time": "datetime",
  "status": "pending",
  "created_at": "datetime",
  "updated_at": "datetime",
  "deleted_at": null
}
```

### User
```json
{
  "id": 1,
  "name": "John Doe",
  "email": "john@example.com",
  "phone": "+1234567890",
  "email_verified_at": null,
  "created_at": "datetime",
  "updated_at": "datetime",
  "roles": [
    {
      "id": 1,
      "name": "admin",
      "guard_name": "web"
    }
  ]
}
```

### Message
```json
{
  "id": 1,
  "order_id": 1,
  "sender_id": 1,
  "receiver_id": 1,
  "content": "string",
  "is_read": false,
  "created_at": "datetime",
  "updated_at": "datetime"
}
```

### Notification
```json
{
  "id": 1,
  "type": "string",
  "data": {},
  "read_at": "datetime",
  "created_at": "datetime",
  "updated_at": "datetime"
}
```