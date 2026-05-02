# Module: User & Role Management
**Phase:** 0 (Foundation)  
**Status:** ✅ Completed  

---

## 1. Overview
The foundation of the LMS is a robust Role-Based Access Control (RBAC) system. It manages identities, authentication, and granular permissions across the platform.

---

## 2. Roles & Hierarchy

| Role | Slug | Description |
|---|---|---|
| **Super Admin** | `super-admin` | Global access, bypasses all permission checks. |
| **Admin** | `admin` | System administrator with high-level access. |
| **Organization Admin** | `org-admin` | Manages an organization's courses, batches, and students. |
| **Course Admin** | `course-admin` | Legacy role for managing course content. |
| **Instructor** | `instructor` | Teaching staff assigned to specific batches. |
| **Student** | `student` | Learners enrolled in courses. |

---

## 3. Core Features
- **User CRUD**: Full management of user accounts (Name, Email, Password, Avatar).
- **Role Management**: Define roles with unique slugs.
- **Permission System**: Granular action-based permissions (e.g., `manage-users`, `create-courses`).
- **Pivot Logic**: Users can have multiple roles and direct permissions.
- **Approval Workflow**: Users can be marked as `is_approved` to control access.
- **Bilingual Support**: UI labels and messages are fully translated (EN/BN).

---

## 4. Technical Implementation
- **Middleware**: `CheckRole` and `CheckPermission` enforce access at the route level.
- **Model Methods**: `hasRole()`, `hasPermission()` on the `User` model.
- **Frontend**: Inertia-powered management interface with searchable tables and role/permission assignment modals.

---

## 5. File References
- **Model**: `app/Models/User.php`, `app/Models/Role.php`, `app/Models/Permission.php`
- **Controller**: `app/Http/Controllers/Admin/UserController.php`
- **Vue Pages**: `resources/js/Pages/Admin/Users/`, `resources/js/Pages/Admin/Roles/`
