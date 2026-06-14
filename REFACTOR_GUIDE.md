# REFACTOR: Simple Role-Based Architecture

## What Changed

This refactor removes the entire permissions system and converts to a **simple role-based architecture** where:

- ✅ **Frontend handles all UI logic**
- ✅ **Backend only returns user info + role_id**
- ✅ **Firebase token verification**
- ✅ **Stateless, minimal, fast**

---

## Architecture Overview

```
React Frontend
    ↓ (Firebase token)
    ↓
Laravel Backend
    ↓ (verify Firebase token)
    ↓
Check user in database
    ↓
Return: { user, role_id, zone_id }
    ↓
React Frontend
    ↓ (decides UI based on role_id)
```

---

## What Was REMOVED ❌

### Models
- `app/Models/Permission.php`
- `app/Models/RolePermission.php`
- `app/Models/UserPermission.php`

### Services
- `app/Services/PermissionService.php`

### Middleware
- `app/Http/Middleware/CheckPermission.php`
- `app/Http/Middleware/CheckZone.php`
- (Keep `Authenticate.php` but not used)

### Controllers
- `app/Http/Controllers/PermissionController.php`
- `app/Http/Controllers/RoleController.php`
- `app/Http/Controllers/UserController.php`
- `app/Http/Controllers/ZoneController.php`

### Form Requests
- `app/Http/Requests/StoreRoleRequest.php`
- `app/Http/Requests/UpdateRoleRequest.php`
- `app/Http/Requests/StorePermissionRequest.php`
- `app/Http/Requests/StoreUserRequest.php`
- `app/Http/Requests/UpdateUserRequest.php`
- `app/Http/Requests/StoreZoneRequest.php`

### Resources
- `app/Http/Resources/RoleResource.php`
- `app/Http/Resources/PermissionResource.php`
- `app/Http/Resources/ZoneResource.php`
- `app/Http/Resources/UserResource.php`

### Seeders
- `database/seeders/PermissionSeeder.php`
- `database/seeders/RolePermissionSeeder.php`
- `database/seeders/UserSeeder.php`

### Helpers
- `app/Helpers/RbacHelper.php`

### Documentation
- Old permission documentation (replace with new guide)

---

## What Was UPDATED ✅

### User Model
```php
// OLD: permission relationships, getComputedPermissions()
// NEW: Only role and zone relationships
protected $fillable = [
    'firebase_uid',   // Firebase authentication ID
    'full_name',
    'email',
    'role_id',        // Just role reference
    'zone_id',        // Optional zone reference
    'status',
];
```

### Role Model
```php
// OLD: permission relationships, description
// NEW: Simple role definition
protected $fillable = [
    'code',           // 'ADMIN', 'CLIENT', etc
    'name',           // Display name
];
```

### Zone Model
```php
// OLD: description, is_active flags
// NEW: Simple zone definition
protected $fillable = [
    'code',
    'name',
];
```

### AuthController
```php
// OLD: login(), logout(), permission computation
// NEW: Only me() endpoint
public function me()
{
    // Returns user with role_id and zone_id
    // Frontend decides everything else
}
```

### Routes
```php
// OLD: 50+ endpoints for permissions, roles, users, zones
// NEW: Only 2 endpoints

GET  /api/health          # Health check
GET  /api/me              # Get current user (Firebase auth required)
```

### Middleware
```php
// OLD: CheckPermission, CheckZone
// NEW: FirebaseAuthMiddleware only

Registered as 'firebase.auth'
Used on protected routes
```

---

## Database Migration Changes

### Remove Tables (if migrations exist)
```sql
DROP TABLE IF EXISTS user_permissions;
DROP TABLE IF EXISTS role_permissions;
DROP TABLE IF EXISTS permissions;
```

### Keep Tables
- users (simplified)
- roles (simplified)
- zones (simplified)

### Users Table Structure
```sql
ALTER TABLE users MODIFY COLUMN name VARCHAR(255) NULLABLE;
ALTER TABLE users ADD COLUMN firebase_uid VARCHAR(255) UNIQUE;
ALTER TABLE users ADD COLUMN full_name VARCHAR(255);
ALTER TABLE users ADD COLUMN status VARCHAR(50) DEFAULT 'active';

-- Remove old columns
ALTER TABLE users DROP COLUMN password;
ALTER TABLE users DROP COLUMN remember_token;
ALTER TABLE users DROP COLUMN is_active;
ALTER TABLE users DROP COLUMN email_verified_at;
```

### Roles Table Structure
```sql
-- Already created, just ensure structure:
CREATE TABLE roles (
  id BIGINT UNSIGNED PRIMARY KEY,
  code VARCHAR(100) UNIQUE,
  name VARCHAR(255),
  timestamps NOT REQUIRED
);
```

### Zones Table Structure
```sql
CREATE TABLE zones (
  id BIGINT UNSIGNED PRIMARY KEY,
  code VARCHAR(100) UNIQUE,
  name VARCHAR(255),
  timestamps NOT REQUIRED
);
```

---

## Setup Instructions

### 1. Clean Up Old Files

Delete these directories/files manually:
```
app/Http/Controllers/PermissionController.php
app/Http/Controllers/RoleController.php
app/Http/Controllers/UserController.php
app/Http/Controllers/ZoneController.php

app/Http/Middleware/CheckPermission.php
app/Http/Middleware/CheckZone.php

app/Http/Requests/StoreRoleRequest.php
app/Http/Requests/UpdateRoleRequest.php
app/Http/Requests/StorePermissionRequest.php
app/Http/Requests/StoreUserRequest.php
app/Http/Requests/UpdateUserRequest.php
app/Http/Requests/StoreZoneRequest.php

app/Http/Resources/RoleResource.php
app/Http/Resources/PermissionResource.php
app/Http/Resources/ZoneResource.php
app/Http/Resources/UserResource.php

app/Models/Permission.php
app/Models/RolePermission.php
app/Models/UserPermission.php

app/Services/PermissionService.php

app/Helpers/RbacHelper.php

database/seeders/PermissionSeeder.php
database/seeders/RolePermissionSeeder.php
database/seeders/UserSeeder.php
```

### 2. Remove Old Migrations

If these exist, delete or keep them (they won't be run):
```
database/migrations/2024_01_01_000002_create_permissions_table.php
database/migrations/2024_01_01_000004_create_role_permissions_table.php
database/migrations/2024_01_01_000005_create_user_permissions_table.php
```

### 3. Create New User Migration

```bash
php artisan make:migration update_users_table_for_firebase
```

Update the migration:
```php
public function up()
{
    Schema::table('users', function (Blueprint $table) {
        $table->string('firebase_uid')->unique();
        $table->string('full_name');
        $table->string('status')->default('active');
        
        // Remove old columns if they exist
        // $table->dropColumn(['password', 'remember_token']);
    });
}
```

### 4. Run Migrations

```bash
php artisan migrate
```

### 5. Seed Database

```bash
php artisan db:seed
```

This creates:
- 7 roles (CLIENT, EMPLOYEE, ADMIN, etc.)
- 5 zones (ZONE_A, ZONE_B, etc.)

### 6. Test API

#### Health Check (No auth)
```bash
curl http://localhost:8000/api/health
```

#### Get Current User (Firebase auth required)
```bash
curl -X GET http://localhost:8000/api/me \
  -H "Authorization: Bearer YOUR_FIREBASE_TOKEN"
```

Expected response:
```json
{
  "status": "success",
  "data": {
    "user": {
      "id": 1,
      "firebase_uid": "uid_from_firebase",
      "full_name": "John Doe",
      "email": "john@example.com",
      "role_id": 7,
      "role": "Administrator",
      "zone_id": 1,
      "zone": "Zone A",
      "status": "active"
    }
  }
}
```

---

## Frontend Integration

### React Example

```typescript
// Get user and role
async function getUser(token: string) {
  const response = await fetch('/api/me', {
    headers: {
      'Authorization': `Bearer ${token}`
    }
  });
  
  const { data } = await response.json();
  const { user } = data;
  
  return user;
}

// Determine UI based on role_id
function renderUI(user) {
  switch(user.role_id) {
    case 1: // CLIENT
      return <ClientDashboard />;
    case 2: // EMPLOYEE
      return <EmployeeDashboard />;
    case 7: // ADMIN
      return <AdminDashboard />;
    default:
      return <NotFound />;
  }
}

// Check role
function hasRole(user, roleId) {
  return user.role_id === roleId;
}

// Filter by zone
function filterByZone(items, user) {
  if (!user.zone_id) return items;
  return items.filter(item => item.zone_id === user.zone_id);
}
```

---

## Role IDs Reference

| ID | Code | Name |
|----|------|------|
| 1 | CLIENT | Client |
| 2 | EMPLOYEE | Employee |
| 3 | RESPONSABLE_ZONE_A | Zone A Manager |
| 4 | RESPONSABLE_ZONE_B | Zone B Manager |
| 5 | DELIVERY | Delivery Person |
| 6 | TRAVELER | Traveler |
| 7 | ADMIN | Administrator |

---

## API Summary

### Endpoints

```
GET  /api/health                    (No auth)
     Response: { status: "ok" }

GET  /api/me                        (Firebase auth required)
     Response: {
       status: "success",
       data: {
         user: {
           id, firebase_uid, full_name, email, 
           role_id, role, zone_id, zone, status
         }
       }
     }
```

---

## Key Differences from Previous System

| Aspect | Before | After |
|--------|--------|-------|
| Permissions | ✅ Database-driven | ❌ Removed |
| Roles | Database with permissions | Simple reference |
| Auth | Sanctum (email/password) | Firebase tokens |
| Permission Check | Backend middleware | Frontend logic |
| UI Control | Backend | **Frontend** |
| Database Queries | Complex joins | Simple lookups |
| Response Size | Large (permissions array) | Small (just role_id) |
| Performance | Slower (caching needed) | Fast |
| Scalability | Complex | Simple |

---

## Important Notes

1. **Firebase Configuration**: Ensure Firebase is configured in `config/firebase.php`
2. **Frontend Validation**: Frontend MUST validate role_id before showing pages
3. **No Permission Checking**: Backend does NOT check permissions
4. **Stateless**: Each request is independent
5. **Zone Optional**: Zone filtering is optional, frontend can ignore if not needed
6. **Token Verification**: Only Firebase token is verified
7. **User Creation**: Must be created with firebase_uid from Firebase Auth

---

## Troubleshooting

### 401 Unauthorized
- Invalid or missing Firebase token
- User not found in database
- Check Authorization header format: `Bearer TOKEN`

### 404 User Not Found
- firebase_uid doesn't match in database
- Ensure user exists in database
- Sync with Firebase Auth

### Missing firebaseAuth Instance
- Configure Firebase in `config/firebase.php`
- Ensure kreait/firebase-php is installed

---

## Migration Checklist

- [ ] Delete old permission files
- [ ] Update users table with firebase_uid
- [ ] Run migrations
- [ ] Seed roles and zones
- [ ] Update React frontend to use role_id
- [ ] Test /api/me endpoint
- [ ] Update API documentation
- [ ] Remove old API client code
- [ ] Deploy to production

---

## Files Modified

✅ `app/Models/User.php` - Simplified
✅ `app/Models/Role.php` - Simplified
✅ `app/Models/Zone.php` - Simplified
✅ `app/Http/Controllers/AuthController.php` - Only me()
✅ `app/Http/Middleware/FirebaseAuthMiddleware.php` - New
✅ `app/Http/Kernel.php` - Updated middleware aliases
✅ `routes/api.php` - Minimal routes
✅ `database/seeders/RoleSeeder.php` - Updated
✅ `database/seeders/ZoneSeeder.php` - Updated
✅ `database/seeders/DatabaseSeeder.php` - Updated

---

## Performance Benefits

- ⚡ **No permission computation** - Removed complex caching logic
- ⚡ **Simple queries** - Direct foreign key lookups
- ⚡ **Small responses** - No permission arrays
- ⚡ **No middleware chain** - Direct Firebase verification
- ⚡ **Frontend rendering** - Reduces server load

---

## Security Notes

- ✅ Firebase token validation required
- ✅ User must exist in database
- ✅ No sensitive data in response
- ✅ Frontend responsible for showing correct UI
- ✅ Stateless: no session data

