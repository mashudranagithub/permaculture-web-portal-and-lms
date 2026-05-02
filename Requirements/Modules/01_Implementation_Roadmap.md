# LMS Implementation Roadmap
**Project:** Permaculture LMS v2.0  
**Last Updated:** 2026-05-02  

---

## Implementation Sequence

> Each Phase must be completed and tested before the next begins.

---

## ✅ Phase 0 — Already Done

| Task | Documentation |
|---|---|
| User & Role Management | [03_User_Role_Management.md](./03_User_Role_Management.md) |
| Course CRUD | [05_Course_Management.md](./05_Course_Management.md) |
| Curriculum Builder (Topics) | [06_Curriculum_Builder.md](./06_Curriculum_Builder.md) |
| Batch Management | [07_Batch_Management.md](./07_Batch_Management.md) |
| Auth, I18n, Media, Quiz | Integrated in above modules |

---

## 🔴 Phase 1 — Organization Foundation

**Goal:** Add multi-tenancy backbone. Everything in Phase 2+ depends on this.

### Step 1.1 — Database Migrations
- [x] `create_organizations_table`
- [x] `add_organization_id_to_users_table`
- [x] `add_organization_id_to_courses_table`
- [x] `add_organization_id_to_batches_table`

### Step 1.2 — Models & Scopes
- [x] Create `Organization` model with relationships
- [x] Create `OrganizationScope` global scope
- [x] Create `HasOrganization` trait
- [x] Register scope on `Course`, `Batch`, `User` (student queries)
- [x] Update `User` model with `organization_id`

### Step 1.3 — Middleware
- [x] Create `EnsureOrganizationApproved` middleware
- [x] Register in `Kernel.php` (bootstrap/app.php in Laravel 11)

### Step 1.4 — Controllers
- [x] `OrganizationController` (CRUD)
- [x] `OrgApprovalController` (integrated into OrganizationController)
- [x] `OrganizationRegistrationController` (Public registration)

### Step 1.5 — Policies
- [ ] `OrganizationPolicy` (who can manage orgs)
- [ ] Update `CoursePolicy`, `BatchPolicy` to check org scope

### Step 1.6 — Vue Pages
- [x] `Pages/Auth/OrgRegister.vue`
- [x] `Pages/Auth/OrgPending.vue`
- [x] `Pages/Organizations/ApprovalQueue.vue`
- [ ] `Pages/Organizations/Index.vue`
- [ ] `Pages/Organizations/Show.vue`

### Step 1.7 — Routes
- [x] Add `/admin/organizations` route group (lms-admin middleware)
- [x] Add `/org/register` public routes

### Step 1.8 — Sidebar Navigation Update
- [x] Add "Organizations" link to LMS Admin sidebar
- [x] Conditionally show nav items based on role
- [x] Show Organization Logo/Name for org users

### Step 1.9 — Seed / Migrate Existing Data
- [ ] Backfill `organization_id` on existing courses and batches
- [ ] Create a default organization for development/testing

---

## 🔴 Phase 2 — Student Panel

**Goal:** Students can log in and access their enrolled content.

### Step 2.1 — Database
- [ ] Add student-specific columns to `users` table (`student_id`, `phone`, `dob`, etc.)
- [ ] Add `enrolled_at`, `access_expires_at` to `enrollments` table
- [ ] Verify `TopicProgress` table schema

### Step 2.2 — Controllers
- [ ] `StudentDashboardController`
- [ ] `StudentCourseController`
- [ ] `StudentTopicController` (with access gate)
- [ ] `StudentBatchController` + batchmates
- [ ] `StudentProfileController`

### Step 2.3 — Policies
- [ ] `TopicPolicy::view()` — enrollment check
- [ ] `CoursePolicy::view()` — org + enrollment check

### Step 2.4 — Routes
- [ ] Add `/student/*` route group

### Step 2.5 — Vue Pages
- [ ] `Pages/Student/Dashboard.vue`
- [ ] `Pages/Student/Courses/Index.vue`
- [ ] `Pages/Student/Courses/Show.vue`
- [ ] `Pages/Student/Topics/Show.vue` (all content types)
- [ ] `Pages/Student/Batch/Show.vue`
- [ ] `Pages/Student/Profile/Edit.vue`

### Step 2.6 — Sidebar Navigation
- [ ] Student sidebar with limited nav items
- [ ] Separate AuthenticatedLayout or conditional rendering

---

## 🟡 Phase 3 — Super Admin Enhancements

**Goal:** LMS Admin has a power-user dashboard with org oversight.

### Step 3.1 — Dashboard Enhancements
- [ ] Global stats cards (all orgs)
- [ ] Pending approvals widget
- [ ] Recent activity feed

### Step 3.2 — Role Rename (Optional)
- [ ] Rename `super-admin` → `lms-admin` in DB + code
- [ ] Ensure all `hasRole()` calls are updated

### Step 3.3 — Dynamic Sidebar
- [ ] Role-based nav rendering in `AuthenticatedLayout.vue`

### Step 3.4 — Impersonation (Optional)
- [ ] `ImpersonationController` 
- [ ] Impersonation banner UI
- [ ] Session-safe stop-impersonating

---

## 🟢 Phase 4 — Forum System

**Goal:** Students can discuss course content within their org.

### Step 4.1 — Database
- [ ] `create_forum_threads_table`
- [ ] `create_forum_replies_table`
- [ ] `create_forum_reactions_table` (optional)

### Step 4.2 — Models
- [ ] `ForumThread` with OrganizationScope
- [ ] `ForumReply` with nested reply support

### Step 4.3 — Controllers
- [ ] `ForumThreadController`
- [ ] `ForumReplyController`
- [ ] `ForumModerationController`

### Step 4.4 — Policies
- [ ] `ForumThreadPolicy` (enrollment gate)
- [ ] `ForumReplyPolicy`

### Step 4.5 — Vue Pages
- [ ] `Pages/Forum/Index.vue`
- [ ] `Pages/Forum/Show.vue`
- [ ] `Pages/Forum/Create.vue`

---

## Estimated Effort

| Phase | Complexity | Estimated Days |
|---|---|---|
| Phase 1 — Organization | High | 5–7 days |
| Phase 2 — Student Panel | Medium-High | 7–10 days |
| Phase 3 — Super Admin | Low | 2–3 days |
| Phase 4 — Forum | High | 7–10 days |

---

## Risk & Considerations

| Risk | Mitigation |
|---|---|
| Existing data has no `organization_id` | Backfill migration + default org seeder |
| OrganizationScope may filter LMS Admin queries | Scope must check role before applying |
| Student routes conflict with admin routes | Use separate route prefixes (`/student/`, `/admin/`) |
| Topic access may be slow on large datasets | Add composite index: `(user_id, batch_id)` on enrollments |
