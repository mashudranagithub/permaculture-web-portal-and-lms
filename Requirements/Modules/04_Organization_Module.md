# Module: Organization Management
**Phase:** 1 (Foundation)  
**Depends On:** Existing User, Role, Permission modules  
**Priority:** 🔴 Critical  

---

## 1. Overview

Organizations are the core multi-tenant unit. Each organization represents a learning institution, training center, or independent educator using the LMS platform.

**Lifecycle:**
1. Organization registers via a public registration form
2. Status is `pending` until LMS Admin approves
3. On approval → status becomes `active`, organization can login and manage content
4. LMS Admin can `suspend` or `reject` organizations at any time

---

## 2. Database Schema

### Table: `organizations`

| Column | Type | Notes |
|---|---|---|
| `id` | bigint PK | |
| `name` | string | Organization display name |
| `slug` | string unique | URL-friendly identifier |
| `email` | string unique | Primary contact email |
| `phone` | string nullable | Contact phone |
| `address` | text nullable | Physical address |
| `logo` | string nullable | Storage path |
| `website` | string nullable | |
| `description` | text nullable | About the organization |
| `status` | enum | `pending`, `active`, `suspended`, `rejected` |
| `approved_by` | FK → users | LMS Admin who approved |
| `approved_at` | timestamp nullable | |
| `rejection_reason` | text nullable | Populated on rejection |
| `settings` | json nullable | Org-level config (theme, features) |
| `timestamps` | created_at, updated_at | |
| `soft_deletes` | deleted_at | |

### Modification: `users` table

Add column:
- `organization_id` → FK → `organizations` nullable (null = LMS Admin level user)

### Modification: `courses` table

Add column:
- `organization_id` → FK → `organizations` not nullable (every course must belong to an org)

### Modification: `batches` table

Add column:
- `organization_id` → FK → `organizations` not nullable (inherited from course but stored explicitly for query performance)

---

## 3. Role Definitions

| Role Slug | Description |
|---|---|
| `lms-admin` | Super admin, full system access, manages organizations |
| `org-admin` | Manages one organization's courses, batches, students |
| `student` | Enrolled learner within an organization |

> **Note:** Existing `super-admin` role maps to `lms-admin` concept. A slug rename or alias will be required.

---

## 4. Organization Model (`app/Models/Organization.php`)

```php
class Organization extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name', 'slug', 'email', 'phone', 'address',
        'logo', 'website', 'description', 'status',
        'approved_by', 'approved_at', 'rejection_reason', 'settings'
    ];

    protected $casts = [
        'settings' => 'array',
        'approved_at' => 'datetime',
    ];

    // Relationships
    public function users(): HasMany { ... }
    public function courses(): HasMany { ... }
    public function batches(): HasMany { ... }
    public function approvedBy(): BelongsTo { ... } // → User (LMS Admin)

    // Helpers
    public function isActive(): bool { return $this->status === 'active'; }
    public function isPending(): bool { return $this->status === 'pending'; }
}
```

---

## 5. Global Scope for Data Isolation (`app/Scopes/OrganizationScope.php`)

This scope will be automatically applied to `Course`, `Batch`, and `User` (student) models when the logged-in user is an `org-admin` or `student`.

```php
class OrganizationScope implements Scope
{
    public function apply(Builder $builder, Model $model): void
    {
        $user = auth()->user();
        
        // LMS Admins see everything — no scope applied
        if (!$user || $user->hasRole('lms-admin')) {
            return;
        }

        // Org users only see their org's data
        $builder->where($model->getTable() . '.organization_id', $user->organization_id);
    }
}
```

---

## 6. Middleware

### `EnsureOrganizationApproved.php`
Blocks org-admin and student access if their organization is not `active`.

```php
public function handle($request, Closure $next)
{
    $user = $request->user();
    if ($user && $user->organization && !$user->organization->isActive()) {
        return redirect()->route('org.pending')
            ->with('error', 'Your organization is awaiting approval.');
    }
    return $next($request);
}
```

---

## 7. Controllers

### `OrganizationController.php` (LMS Admin only)
| Method | Route | Description |
|---|---|---|
| `index()` | GET /admin/organizations | List all orgs with filters |
| `create()` | GET /admin/organizations/create | Registration form |
| `store()` | POST /admin/organizations | Create + send welcome email |
| `show()` | GET /admin/organizations/{id} | Org detail + stats |
| `edit()` | GET /admin/organizations/{id}/edit | Edit form |
| `update()` | PUT /admin/organizations/{id} | Update details |
| `destroy()` | DELETE /admin/organizations/{id} | Soft delete |

### `OrgApprovalController.php` (LMS Admin only)
| Method | Route | Description |
|---|---|---|
| `queue()` | GET /admin/organizations/approvals | Pending approval list |
| `approve()` | POST /admin/organizations/{id}/approve | Approve org, notify |
| `reject()` | POST /admin/organizations/{id}/reject | Reject with reason |
| `suspend()` | POST /admin/organizations/{id}/suspend | Suspend active org |

---

## 8. UI Pages (Vue + Inertia)

### LMS Admin Views (`Pages/Organizations/`)
- **`Index.vue`** — Organization list with status badges, search, filter by status
- **`Create.vue`** — Full registration form
- **`Edit.vue`** — Edit org details
- **`Approval.vue`** — Approval queue with approve/reject actions and reason input

### Organization Self-Registration (Public)
- **`Pages/Auth/OrgRegister.vue`** — Public form: org name, email, description, logo
- Shows "pending approval" message after submission
- Email notification to LMS Admin on new registration

---

## 9. Email Notifications

| Trigger | Recipient | Template |
|---|---|---|
| New org registration | LMS Admin | "New org pending approval" |
| Org approved | Org Admin email | "Your organization has been approved" |
| Org rejected | Org Admin email | "Registration update" + reason |
| Org suspended | Org Admin email | "Account suspended" |

---

## 10. Acceptance Criteria

- [ ] Organizations can self-register via a public form
- [ ] Newly registered orgs have `status = pending`
- [ ] LMS Admin sees a pending approval queue
- [ ] LMS Admin can approve, reject (with reason), or suspend
- [ ] Approved org admin can log in and access their dashboard
- [ ] Org admin cannot see courses, batches, or students from other orgs
- [ ] Middleware blocks suspended/pending orgs from accessing the system
- [ ] `organization_id` is on users, courses, and batches tables
- [ ] OrganizationScope auto-filters all model queries by org
