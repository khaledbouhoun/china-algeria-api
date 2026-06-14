# CLEANUP CHECKLIST

## ✅ Updated & Keep

### Models (3 files - UPDATED)
- [ ] `app/Models/User.php` ✅ Updated
- [ ] `app/Models/Role.php` ✅ Updated  
- [ ] `app/Models/Zone.php` ✅ Updated

### Controllers (1 file - UPDATED)
- [ ] `app/Http/Controllers/AuthController.php` ✅ Updated (me() only)

### Middleware (2 files)
- [ ] `app/Http/Middleware/FirebaseAuthMiddleware.php` ✅ NEW
- [ ] `app/Http/Kernel.php` ✅ Updated

### Routes (1 file - UPDATED)
- [ ] `routes/api.php` ✅ Updated

### Seeders (3 files - UPDATED)
- [ ] `database/seeders/RoleSeeder.php` ✅ Updated
- [ ] `database/seeders/ZoneSeeder.php` ✅ Updated
- [ ] `database/seeders/DatabaseSeeder.php` ✅ Updated

---

## ❌ DELETE These Files

### Models (3 files)
- [ ] `app/Models/Permission.php` - DELETE
- [ ] `app/Models/RolePermission.php` - DELETE
- [ ] `app/Models/UserPermission.php` - DELETE

### Controllers (4 files)
- [ ] `app/Http/Controllers/PermissionController.php` - DELETE
- [ ] `app/Http/Controllers/RoleController.php` - DELETE
- [ ] `app/Http/Controllers/UserController.php` - DELETE
- [ ] `app/Http/Controllers/ZoneController.php` - DELETE

### Middleware (2 files)
- [ ] `app/Http/Middleware/CheckPermission.php` - DELETE
- [ ] `app/Http/Middleware/CheckZone.php` - DELETE

### Form Requests (6 files)
- [ ] `app/Http/Requests/StoreRoleRequest.php` - DELETE
- [ ] `app/Http/Requests/UpdateRoleRequest.php` - DELETE
- [ ] `app/Http/Requests/StorePermissionRequest.php` - DELETE
- [ ] `app/Http/Requests/StoreUserRequest.php` - DELETE
- [ ] `app/Http/Requests/UpdateUserRequest.php` - DELETE
- [ ] `app/Http/Requests/StoreZoneRequest.php` - DELETE

### Resources (4 files)
- [ ] `app/Http/Resources/RoleResource.php` - DELETE
- [ ] `app/Http/Resources/PermissionResource.php` - DELETE
- [ ] `app/Http/Resources/ZoneResource.php` - DELETE
- [ ] `app/Http/Resources/UserResource.php` - DELETE

### Services (1 file)
- [ ] `app/Services/PermissionService.php` - DELETE

### Helpers (1 file)
- [ ] `app/Helpers/RbacHelper.php` - DELETE

### Seeders (3 files)
- [ ] `database/seeders/PermissionSeeder.php` - DELETE
- [ ] `database/seeders/RolePermissionSeeder.php` - DELETE
- [ ] `database/seeders/UserSeeder.php` - DELETE

### Migrations (Optional - Delete if they exist)
- [ ] `database/migrations/2024_01_01_000002_create_permissions_table.php` - DELETE
- [ ] `database/migrations/2024_01_01_000004_create_role_permissions_table.php` - DELETE
- [ ] `database/migrations/2024_01_01_000005_create_user_permissions_table.php` - DELETE

### Documentation (Old)
- [ ] `RBAC_DOCUMENTATION.md` - DELETE
- [ ] `SETUP_GUIDE.md` - DELETE
- [ ] `IMPLEMENTATION_SUMMARY.md` - DELETE

---

## Total Files to Delete: 27 files

---

## Quick Delete Commands

### Using Git (if tracked)
```bash
git rm app/Models/Permission.php
git rm app/Models/RolePermission.php
git rm app/Models/UserPermission.php
git rm app/Http/Controllers/PermissionController.php
git rm app/Http/Controllers/RoleController.php
git rm app/Http/Controllers/UserController.php
git rm app/Http/Controllers/ZoneController.php
git rm app/Http/Middleware/CheckPermission.php
git rm app/Http/Middleware/CheckZone.php
git rm app/Http/Requests/StoreRoleRequest.php
git rm app/Http/Requests/UpdateRoleRequest.php
git rm app/Http/Requests/StorePermissionRequest.php
git rm app/Http/Requests/StoreUserRequest.php
git rm app/Http/Requests/UpdateUserRequest.php
git rm app/Http/Requests/StoreZoneRequest.php
git rm app/Http/Resources/RoleResource.php
git rm app/Http/Resources/PermissionResource.php
git rm app/Http/Resources/ZoneResource.php
git rm app/Http/Resources/UserResource.php
git rm app/Services/PermissionService.php
git rm app/Helpers/RbacHelper.php
git rm database/seeders/PermissionSeeder.php
git rm database/seeders/RolePermissionSeeder.php
git rm database/seeders/UserSeeder.php
git rm RBAC_DOCUMENTATION.md
git rm SETUP_GUIDE.md
git rm IMPLEMENTATION_SUMMARY.md
git commit -m "refactor: remove permissions system"
```

### Manual Delete
Simply delete each file/folder manually through your file explorer or IDE.

---

## ✅ File Structure After Cleanup

```
app/
├── Console/
├── Exceptions/
├── Helpers/
│   └── (EMPTY or only keep needed helpers)
├── Http/
│   ├── Controllers/
│   │   ├── AuthController.php ✅
│   │   └── FirebaseAuthController.php
│   ├── Middleware/
│   │   ├── FirebaseAuthMiddleware.php ✅
│   │   ├── Authenticate.php
│   │   └── (other default middleware)
│   ├── Requests/ (EMPTY)
│   ├── Resources/ (EMPTY)
│   └── Kernel.php ✅
├── Models/
│   ├── User.php ✅
│   ├── Role.php ✅
│   └── Zone.php ✅
├── Providers/
├── Services/ (EMPTY)
└── ...

database/
├── migrations/
│   ├── 2014_10_12_000000_create_users_table.php
│   ├── 2024_01_01_000001_create_roles_table.php
│   ├── 2024_01_01_000003_create_zones_table.php
│   ├── 2024_01_01_000006_add_rbac_columns_to_users_table.php
│   └── (new firebase migration)
└── seeders/
    ├── DatabaseSeeder.php ✅
    ├── RoleSeeder.php ✅
    └── ZoneSeeder.php ✅

routes/
└── api.php ✅
```

---

## Next Steps

1. [ ] Delete all 27 files listed in "DELETE These Files" section
2. [ ] Create new migration for firebase_uid
3. [ ] Run: `php artisan migrate`
4. [ ] Run: `php artisan db:seed`
5. [ ] Test: `curl http://localhost:8000/api/health`
6. [ ] Update React frontend
7. [ ] Deploy

---

## Commands to Run

```bash
# Create migration for users table
php artisan make:migration update_users_table_for_firebase

# Run migrations
php artisan migrate

# Seed database
php artisan db:seed

# Test health check
curl http://localhost:8000/api/health

# Clear cache
php artisan cache:clear
```

---

## After Cleanup

Once you delete all 27 files, you'll have:
- ✅ 3 models only (User, Role, Zone)
- ✅ 1 controller (AuthController)
- ✅ 1 new middleware (FirebaseAuthMiddleware)
- ✅ 2 API endpoints (health, me)
- ✅ Simple, fast backend
- ✅ Frontend handles all UI logic

---

## Status: READY FOR CLEANUP ✅

All code updates are complete. Now just delete the old files!

