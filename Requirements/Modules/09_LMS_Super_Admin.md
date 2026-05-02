# Module: LMS Super Admin Enhancements
**Phase:** 3  
**Depends On:** Organization Module (M1)  
**Priority:** 🟡 Medium  

---

## 1. Overview

The LMS Super Admin (`lms-admin` role) has global, cross-organization visibility. This module formalizes and enhances those capabilities beyond the existing ACL system.

**Current State:** The `super-admin` role with `hasPermission()` bypass exists.  
**Enhancement:** Add org-management UI, global stats, and impersonation capability.

---

## 2. Role Strategy

| Role Slug | Scope | Access |
|---|---|---|
| `lms-admin` (rename from `super-admin`) | Global | All orgs, all data, system config |
| `org-admin` | Single org | Their org's data only |
| `student` | Single org, enrolled courses | Enrolled content only |

> **Migration Plan:** Rename `super-admin` slug to `lms-admin` in the roles table via a database seeder/migration update. Update all `hasRole('super-admin')` calls to `hasRole('lms-admin')`.

---

## 3. LMS Admin Dashboard Enhancements

### Global Statistics Panel
Add to Dashboard.vue or create a dedicated `LMSAdmin/Dashboard.vue`:

**Stats Cards:**
- Total Organizations (active / pending)
- Total Users (all orgs)
- Total Students (all orgs)
- Total Courses (all orgs)
- Total Revenue (all payments)
- New Registrations This Week

### Pending Approval Widget
- Quick-access widget showing count of pending org registrations
- One-click approve/reject from dashboard

---

## 4. Organization Management UI

> See detailed spec in `02_Organization_Module.md`

### Summary of LMS Admin-only pages:
- `/admin/organizations` — All organizations list
- `/admin/organizations/approvals` — Pending approval queue
- `/admin/organizations/{id}` — Org detail with stats
- `/admin/organizations/{id}/impersonate` — [Optional] View system as org admin

---

## 5. Impersonation Feature (Optional - Phase 3+)

Allows LMS Admin to "log in as" an org admin temporarily to debug or verify data without knowing their password.

```php
// ImpersonationController.php
public function impersonate(Organization $org): RedirectResponse
{
    // Store original admin ID in session
    session(['impersonating_as' => auth()->id()]);
    
    $orgAdmin = $org->users()->whereHas('roles', fn($q) => $q->where('slug', 'org-admin'))->first();
    auth()->login($orgAdmin);
    
    return redirect()->route('dashboard');
}

public function stopImpersonating(): RedirectResponse
{
    $originalId = session('impersonating_as');
    auth()->loginUsingId($originalId);
    session()->forget('impersonating_as');
    return redirect()->route('admin.organizations.index');
}
```

---

## 6. System Configuration Panel (Future)

A settings panel for LMS Admin to control:
- Platform name, logo, footer text
- Email SMTP configuration
- Feature toggles (enable/disable Forum, Payments, etc.)
- Maintenance mode

---

## 7. LMS Admin Middleware

All LMS Admin routes must be protected:

```php
Route::middleware(['auth', 'role:lms-admin'])->prefix('admin')->group(function () {
    Route::resource('organizations', OrganizationController::class);
    Route::post('organizations/{org}/approve', [OrgApprovalController::class, 'approve']);
    Route::post('organizations/{org}/reject',  [OrgApprovalController::class, 'reject']);
    Route::post('organizations/{org}/suspend', [OrgApprovalController::class, 'suspend']);
    // ... more admin routes
});
```

---

## 8. Sidebar Navigation Separation

The `AuthenticatedLayout.vue` sidebar should dynamically render different items based on role:

```javascript
// Computed sidebar sections
const sidebarItems = computed(() => {
    if (hasRole('lms-admin')) return lmsAdminNav;
    if (hasRole('org-admin')) return orgAdminNav;
    if (hasRole('student'))   return studentNav;
    return [];
});
```

| lmsAdminNav | orgAdminNav | studentNav |
|---|---|---|
| Dashboard | Dashboard | My Dashboard |
| Organizations | Courses | My Courses |
| All Users | Curriculum | My Batch |
| System Config | Batches | Batchmates |
| — | Students | My Profile |
| — | Reports | — |

---

## 9. Acceptance Criteria

- [ ] LMS Admin sees a global stats dashboard
- [ ] Pending org registrations are visible on LMS Admin dashboard
- [ ] LMS Admin can browse all organizations with status filtering
- [ ] LMS Admin can view any organization's detail page with student/course counts
- [ ] Sidebar navigation changes based on the logged-in user's role
- [ ] LMS Admin cannot accidentally use org-scoped routes
- [ ] Impersonation session is properly cleaned up on stop (optional)
