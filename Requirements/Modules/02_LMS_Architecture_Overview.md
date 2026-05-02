# LMS System Architecture Overview
**Project:** Permaculture LMS  
**Version:** 2.0 — Multi-Tenant Extension  
**Date:** 2026-05-02  

---

## 1. Current System State (Already Implemented)

| Module | Status | Notes |
|---|---|---|
| User Authentication | ✅ Done | Login, registration, avatar |
| Role Management | ✅ Done | Roles, slugs, role-user pivot |
| Permission Management | ✅ Done | Permission-role, permission-user pivot |
| Course Management | ✅ Done | Bilingual, CRUD, soft deletes |
| Curriculum / Topics | ✅ Done | Bilingual, type-based, quiz builder, media |
| Batch Management | ✅ Done | Schedules, enrollment, pricing |
| Enrollment & Payments | ✅ Done | Partial — schema exists |
| Access Control (ACL) | ✅ Done | hasRole(), hasPermission() on User model |

---

## 2. Planned Extension Modules

| # | Module | Priority | Complexity |
|---|---|---|---|
| M1 | **Organization Management** | 🔴 High | High |
| M2 | **Multi-Tenant Data Isolation** | 🔴 High | High |
| M3 | **Student Panel** | 🔴 High | Medium |
| M4 | **LMS Super Admin Enhancements** | 🟡 Medium | Low |
| M5 | **Forum / Discussion System** | 🟢 Low (Planned) | High |

---

## 3. Role Hierarchy (Revised)

```
LMS Super Admin (lms-admin)
    └── Can manage all organizations, users, system config
    └── Approves / rejects organization registrations

Organization Admin (org-admin)
    └── Manages own org: courses, batches, students
    └── Cannot see other organizations' data

Student (student)
    └── Belongs to one organization
    └── Sees only org courses + enrolled content
    └── Can view batchmates
```

---

## 4. Multi-Tenancy Strategy

**Approach:** Shared Database, Separate Organization Scope  
(Single DB with `organization_id` foreign key on all tenant-scoped tables)

**Why not separate databases?** The system is mid-development and adding `organization_id` to existing tables is a clean, maintainable approach for this scale.

### Tables That Need `organization_id`:
- `users`
- `courses`
- `batches`
- `topics` (inherited via courses)
- `enrollments`
- `payments`

### Tables That Are Global (no tenancy):
- `roles`
- `permissions`
- `organizations` (the tenant table itself)

---

## 5. Data Isolation Pattern

All queries for org-scoped data will be filtered through a **Global Scope** or a **Service Layer**:

```php
// Example: CoursePolicy
public function viewAny(User $user): bool
{
    if ($user->hasRole('lms-admin')) return true;
    return $user->organization_id !== null;
}

// Example: CourseController (org-scoped)
Course::where('organization_id', auth()->user()->organization_id)->get();
```

Laravel's **Policies** and **Global Scopes** will enforce this at the model level.

---

## 6. Implementation Phases

### Phase 1 — Foundation (Organization + Multi-Tenancy)
> See: `02_Organization_Module.md`
- Create `organizations` table and `Organization` model
- Add `organization_id` to `users`, `courses`, `batches`
- Add `OrganizationScope` global scope
- Create `OrganizationController` (CRUD for LMS Admin)
- Org registration + approval workflow

### Phase 2 — Student Panel
> See: `03_Student_Panel.md`
- Student dashboard
- Enrolled courses view
- Batch & batchmates view
- Topic/content access with enrollment check
- Progress tracking

### Phase 3 — Super Admin Enhancements
> See: `04_Super_Admin.md`
- Organization listing and approval queue
- Impersonation (view system as an org)
- System-wide statistics

### Phase 4 — Forum System (Future)
> See: `05_Forum_System.md`
- Thread + reply model per course/topic
- Student-only access scoped to enrollment
- Moderation by org-admin

---

## 7. File Structure (New Files to Create)

```
app/
  Models/
    Organization.php          [NEW]
    ForumThread.php           [NEW - Phase 4]
    ForumReply.php            [NEW - Phase 4]
  Http/
    Controllers/
      Organization/
        OrganizationController.php      [NEW]
        OrgApprovalController.php       [NEW]
      Student/
        StudentDashboardController.php  [NEW]
        StudentCourseController.php     [NEW]
        StudentBatchController.php      [NEW]
    Middleware/
      EnsureOrganizationApproved.php    [NEW]
      ScopeToOrganization.php           [NEW]
  Policies/
    CoursePolicy.php                    [UPDATE]
    BatchPolicy.php                     [UPDATE]
    OrganizationPolicy.php              [NEW]
  Scopes/
    OrganizationScope.php               [NEW]

database/
  migrations/
    xxxx_create_organizations_table.php         [NEW]
    xxxx_add_org_id_to_users_table.php          [NEW]
    xxxx_add_org_id_to_courses_table.php        [NEW]
    xxxx_add_org_id_to_batches_table.php        [NEW]

resources/
  js/
    Pages/
      Organizations/
        Index.vue     [NEW - LMS Admin view]
        Create.vue    [NEW]
        Edit.vue      [NEW]
        Approval.vue  [NEW - Approval queue]
      Student/
        Dashboard.vue       [NEW]
        Courses.vue         [NEW]
        Course/Show.vue     [NEW]
        Batch/Show.vue      [NEW]

routes/
  web.php   [UPDATE — add organization and student routes]
```
